<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                      **
 * @subpackage  msm                                                      **
 * @name        msm                                                      **
 * @copyright   University of Alberta                                    **
 * @link        http://ualberta.ca                                       **
 * @author      Ga Young Kim                                             **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * *************************************************************************
 * ************************************************************************ */
require_once("Subordinate.php");
require_once("Media.php");
require_once("MathImg.php");

/**
 * The Element class is an abstract class that each of the other classes in the XMLImporter folder inherits from.
 * This class has an abstract method - loadFromXml.  It also contains several functions that were
 * refactored from the child classses.  For more information about the mentioned method, see the comments above
 * the method declaration.
 */
abstract class Element
{

    public $xmlpath; // this property of the class stores the filepath of the current XML file being parsed
    public $id;

    // constructor for the class
    function __construct($xmlpath = '')
    {
        $this->xmlpath = $xmlpath;
    }

    /**
     * This function is an abstract method that is implemented by each class
     * that extends from this class.  It essentially parses the given XML file
     *  to retrive data to be inserted into the database tables
     */
    abstract function loadFromXml($DomElement, $position = '');

    /**
     *  this function was created to retrieve the first item in the nodelist generated from
     *  result of getElementsByTagName method
     * 
     * @param DOMNodeList $nodeList
     * @return string 
     */
    function getDomAttribute($nodeList)
    {
        if ($nodeList->length > 0)
        {
            $e = $nodeList->item(0);

            return $e->nodeValue;
        }
        else
        {
            return '';
        }
    }

    /**
     *  checkForRecord method is used to check if there is already a record in the database table which is identical to the one the class
     * is about to insert.  It compares the string_id of the instance of the class to records in the database or a specified property
     * of the class that matches a field of the database table to identify a record with the same values. (ie representing a duplicate record)
     * The method then returns the record ID in the database table if there is already a record in the database table with the same either string_id
     * or the value of the specified property or returns false.
     * 
     * @global moodle_database $DB
     * @param object $DomElement    Represents the instance of a class that called the method
     * @param String $propertyName      Represents the name of the property to be used to identify record in the database table
     * @return int/boolean              If a duplicate record is found, returns the ID number of the record.  Otherwise returns false.
     */
    function checkForRecord($DomElement, $propertyName = '')
    {
        global $DB;

        // if no property name is specified, then default is to check with string_id
        if (!empty($propertyName))
        {
            if (isset($DomElement->$propertyName))
            {
                $foundID = $DB->get_record($DomElement->tablename, array($propertyName => $DomElement->$propertyName));

                if (!empty($foundID))
                {
                    return $foundID;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        // $propertyName is defined
        else
        {
            if (isset($DomElement->string_id))
            {
                if (!empty($DomElement->string_id))
                {
                    $foundID = $DB->get_record($DomElement->tablename, array('string_id' => $DomElement->string_id));
                    
                    if (!empty($foundID))
                    {
                        return $foundID;
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
        }
    }

    /**
     * This method is used to process the math elements in the title XML elements.  It parses through the title element of the XML file
     * then if it encounters a child node with tag name of math, it preserves the math and its associated latex tags and their values while
     * the rest of the value of the title element is also preserved.  The result is a parsed string with preserved XML title structure.
     * 
     * @param DOMElement $DomElement    An XML element that has the values to be processed (usually title element)
     * @return string                   A parsed title XML element that has been converted as a string
     */
    function getContent($DomElement)
    {
        $content = '';
        $doc = new DOMDocument();

        if (!empty($DomElement))
        {
            $element = $doc->importNode($DomElement, true);

            foreach ($element->childNodes as $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    // if the child node is math, then convert the XML to a string then replace the tags with appropriate
                    // mathjax inline math configuration(ie, '$')
                    if ($child->tagName == 'math')
                    {
                        $content .= $doc->saveXML($child);

                        $content = str_replace('<math>', '', $content);
                        $content = str_replace('</math>', '', $content);

                        $content = str_replace('<latex>', '$', $content);
                        $content = str_replace('</latex>', '$', $content);
                    }
                }
                // child is not an element node but a text node
                else
                {
                    $content .= $doc->saveXML($child);
                }
            }
        }
        return $content;
    }

    /**
     * This method is to process the media elements in the XML elements.  This method is
     * called by most of the classes in XMLImporter folder to process the corresponding content
     * part of the element to extract all the media elements.
     * 
     * @param DOMElement $DomElement    The element that is considered as a main content body of the XML file(eg. def.body, block.body...etc)
     * @param int $position             This number is used to preserve the structure of the XML during parsing process
     * @return array                    This is an array with all the media elements in the given DOMElement
     */
    function processMedia($DomElement, $position)
    {
        $arrayOfMedia = array();

        $position = $position + 1;

        $medias = $DomElement->getElementsByTagName('media');
        $mlength = $medias->length;
        for ($i = 0; $i < $mlength; $i++)
        {
            $position = $position + 1;
            $media = new Media($this->xmlpath);
            $media->loadFromXml($medias->item($i), $position);
            $arrayOfMedia[] = $media;
        }
        return $arrayOfMedia;
    }

    /**
     * This method processes the math.array elements in an segment of an XML file
     * specified by the parameter DomElement.  This method is called in most of the 
     * classes in XMLImporter folder to process its content parts.
     * 
     * @param DOMElement $DomElement    DOMElement that is a main content part of the element represented by the class
     * @param int $position             Represents the position of this XML element in relation to the other XML elements
     * @return array                    Represents all the math.array elements that were present in the DOMElement specified
     */
    function processMathArray($DomElement, $position)
    {
        $arrayOfMathArray = array();

        $position = $position + 1;

        $matharrays = $DomElement->getElementsByTagName('math.array');
        $matharraylength = $matharrays->length;
        for ($i = 1; $i < $matharraylength; $i++)
        {
            $position = $position + 1;
            $matharray = new MathArray($this->xmlpath);
            $matharray->loadFromXml($matharrays->item($i), $position);
            $arrayOfMathArray[] = $matharray;
        }

        return $arrayOfMathArray;
    }

    /**
     * This method processes the table elements in an segment of an XML file
     * specified by the parameter DomElement.  This method is called in most of the 
     * classes in XMLImporter folder to process its content parts.
     * 
     * @param DOMElement $DomElement    DOMElement that is a main content part of the element represented by the class
     * @param int $position             Represents the position of this XML element in relation to the other XML elements
     * @return array                    Represents all the table elements that were present in the DOMElement specified
     */
    function processTable($DomElement, $position)
    {
        $arrayOfTable = array();

        $position = $position + 1;

        $tables = $DomElement->getElementsByTagName('table');
        $tablelength = $tables->length;
        for ($i = 1; $i < $tablelength; $i++)
        {
            $position = $position + 1;
            $table = new Table($this->xmlpath);
            $table->loadFromXml($tables->item($i), $position);
            $arrayOfTable[] = $table;
        }

        return $arrayOfTable;
    }

    /**
     * This method processes the subordinate elements in an segment of an XML file
     * specified by the parameter DomElement.  This method is called in most of the 
     * classes in XMLImporter folder to process its content parts.
     * 
     * @param DOMElement $DomElement    DOMElement that is a main content part of the element represented by the class
     * @param int $position             Represents the position of this XML element in relation to the other XML elements
     * @return array                    Represents all the subordinate elements that were present in the DOMElement specified
     */
    function processSubordinate($DomElement, $position)
    {
        $arrayOfSubordinates = array();

        $position = $position + 1;
        $subordinates = $DomElement->getElementsByTagName('subordinate');
        $length = 0;

        //to eliminate any nested subordinates from being counted when getting the length of the subordinates
        foreach ($subordinates as $s)
        {
            if ($s->parentNode->parentNode->parentNode->tagName != 'info')
            {
                $length++;
            }
        }
        for ($i = 0; $i < $length; $i++)
        {
            $position = $position + 1;
            $subordinate = new Subordinate($this->xmlpath);
            $subordinate->loadFromXml($subordinates->item($i), $position);
            $arrayOfSubordinates[] = $subordinate;
        }
        return $arrayOfSubordinates;
    }

    /**
     * This method is to process the XML element considered to contain the main contents (such as def.body, block.body...etc).
     * It is called from most of the classes in the XMLImporter folder to precess its contents.  This method replaces any subordinate
     * elements and its child nodes with hot element(which depicts the hot taged word that activates the dialog box to display the info element),
     * deletes any index elements, replaces the media elements with the img element with src attribute to be replaced by the properly processed
     * media element later before rendering and replaces certain XML elements such as row/cell with equivalent HTML tags such as tr/td.  It also 
     * replaces any math tags with the inline mathjax conversion delimiter('$') and replace math.display with display mathjax conversion ('$$').
     * 
     * @param DOMElement $DomElement    DOMElement that is a main content part of the element represented by the class
     * @param int $position             Represents the position of this XML element in relation to the other XML elements
     * @return array                    Represents all the content of the element that is represented by the class that called the method
     */
    function processContent($DomElement, $position)
    {
        $content = array();

        $position = $position + 1;

        $subordinates = $DomElement->getElementsByTagName('subordinate');

        $length = 0;

        //to eliminate any nested subordinates from being counted when getting the length of the subordinates
        foreach ($subordinates as $s)
        {
            if ($s->parentNode->parentNode->parentNode->tagName != 'info')
            {
                $length++;
            }
        }
        for ($i = 0; $i < $length; $i++)
        {
            // replacing the entire subordinate element with just the hot element of the subordinate to show only the hot element 
            $hot = $subordinates->item(0)->getElementsByTagName('hot')->item(0);
            $subordinates->item(0)->parentNode->replaceChild($hot, $subordinates->item(0));
        }

        // remove index.author elements along with its child nodes from content
        $indexauthors = $DomElement->getElementsByTagName('index.author');
        $ialength = $indexauthors->length;
        for ($i = 0; $i < $ialength; $i++)
        {
            $indexauthors->item(0)->parentNode->removeChild($indexauthors->item(0));
        }

        // remove index.glossary elements along with its child nodes from content
        $indexglossarys = $DomElement->getElementsByTagName('index.glossary');
        $iglength = $indexglossarys->length;
        for ($i = 0; $i < $iglength; $i++)
        {
            $indexglossarys->item(0)->parentNode->removeChild($indexglossarys->item(0));
        }

        // remove index.symbol elements along with its child nodes from content
        $indexsymbols = $DomElement->getElementsByTagName('index.symbol');
        $islength = $indexsymbols->length;
        for ($i = 0; $i < $islength; $i++)
        {
            $indexsymbols->item(0)->parentNode->removeChild($indexsymbols->item(0));
        }

        // replace media elements with img element with src attribute
        $medias = $DomElement->getElementsByTagName('media');
        $mlength = $medias->length;
        for ($i = 0; $i < $mlength; $i++)
        {
            foreach ($medias->item(0)->childNodes as $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    if ($child->tagName == 'img')
                    {
                        $imgsrc = $child;
                    }
                }
            }

            if (!empty($imgsrc))
            {
                $medias->item(0)->parentNode->replaceChild($imgsrc, $medias->item(0));
            }
        }

        // converting XML content into string for further XML tag processing
        $doc = new DOMDocument();
        $element = $doc->importNode($DomElement, true);
        $content[] = $doc->saveXML($element);

        $resultcontent = array();

        foreach ($content as $key => $string)
        {
//            $string = str_replace('<caption>', '<captions>', $string);
//            $string = str_replace('</caption>', '</captions>', $string);

            $string = str_replace('<row', '<tr', $string);
            $string = str_replace('</row>', '</tr>', $string);

            $string = str_replace('<cell', '<td', $string);
            $string = str_replace('</cell>', '</td>', $string);

//            $string = preg_replace('/^<math.array xmlns=(.+)/', '<table class="matharray"', $string);
//            $string = str_replace('<math.array', '<table', $string);
//            $string = str_replace('</math.array>', '</table>', $string);

            $string = str_replace('<para.body', '<p', $string);
            $string = str_replace('</para.body>', '</p>', $string);

            $string = str_replace('<strong', '<b', $string);
            $string = str_replace('</strong>', '</b>', $string);

            $string = str_replace('<emphasis', '<i', $string);
            $string = str_replace('</emphasis>', '</i>', $string);

            $string = str_replace('<hot>', '<a href="">', $string);
            $string = str_replace('</hot>', '</a>  ', $string);

            $string = str_replace('<math>', '$', $string);
            $string = preg_replace('/^<math xmlns=(.+)>/', '$', $string);
            $string = str_replace('</math>', '$', $string);

            $string = str_replace('<math.display>', '$$', $string);
            $string = preg_replace('/^<math.display xmlns=(.+)>/', '$$', $string);
            $string = str_replace('</math.display>', '$$', $string);

            $string = str_replace('<latex>', '', $string);
            $string = str_replace('</latex>', '', $string);

            $resultcontent[] = $string;
        }
        return $resultcontent;
    }

    /**
     * This method processes the index.author elements in an segment of an XML file
     * specified by the parameter DomElement.  This method is called in most of the 
     * classes in XMLImporter folder to process its content parts.
     * 
     * @param DOMElement $DomElement    DOMElement that is a main content part of the element represented by the class
     * @param int $position             Represents the position of this XML element in relation to the other XML elements
     * @return array                    Represents all the index.author elements that were present in the DOMElement specified
     */
    function processIndexAuthor($DomElement, $position)
    {
        $position = $position + 1;
        $arrayOfIndexAuthor = array();

        $indexauthors = $DomElement->getElementsByTagName('index.author');
        $ialength = $indexauthors->length;
        for ($i = 0; $i < $ialength; $i++)
        {
            $position = $position + 1;
            $indexauthor = new MathIndex($this->xmlpath);
            $indexauthor->loadFromXml($indexauthors->item($i), $position);
            $arrayOfIndexAuthor[] = $indexauthor;
        }
        return $arrayOfIndexAuthor;
    }

    /**
     * This method processes the index.glossary elements in an segment of an XML file
     * specified by the parameter DomElement.  This method is called in most of the 
     * classes in XMLImporter folder to process its content parts.
     * 
     * @param DOMElement $DomElement    DOMElement that is a main content part of the element represented by the class
     * @param int $position             Represents the position of this XML element in relation to the other XML elements
     * @return array                    Represents all the index.glossary elements that were present in the DOMElement specified
     */
    function processIndexGlossary($DomElement, $position)
    {
        $position = $position + 1;
        $arrayOfIndexGlossary = array();

        $indexglossarys = $DomElement->getElementsByTagName('index.glossary');
        $iglength = $indexglossarys->length;
        for ($i = 0; $i < $iglength; $i++)
        {
            $position = $position + 1;
            $indexglossary = new MathIndex($this->xmlpath);
            $indexglossary->loadFromXml($indexglossarys->item($i), $position);
            $arrayOfIndexGlossary[] = $indexglossary;
        }
        return $arrayOfIndexGlossary;
    }

    /**
     * This method processes the index.symbol elements in an segment of an XML file
     * specified by the parameter DomElement.  This method is called in most of the 
     * classes in XMLImporter folder to process its content parts.
     * 
     * @param DOMElement $DomElement    DOMElement that is a main content part of the element represented by the class
     * @param int $position             Represents the position of this XML element in relation to the other XML elements
     * @return array                    Represents all the index.symbol elements that were present in the DOMElement specified
     */
    function processIndexSymbols($DomElement, $position)
    {
        $position = $position + 1;
        $arrayOfIndexSymbol = array();

        $indexsymbols = $DomElement->getElementsByTagName('index.symbol');
        $islength = $indexsymbols->length;
        for ($i = 0; $i < $islength; $i++)
        {
            $position = $position + 1;
            $indexsymbol = new MathIndex($this->xmlpath);
            $indexsymbol->loadFromXml($indexsymbols->item($i), $position);
            $arrayOfIndexSymbol[] = $indexsymbol;
        }
        return $arrayOfIndexSymbol;
    }

    /**
     * This method is used to find the file path with the specified reference ID in form of a 
     * string given by the DOMElement's attribute 'id'(or 'unitid').  It parses each of the files in the 
     * starting file path given by $filepath and if it encounters another direcotry, it parses the files in
     * that directory.  It parses for the id attribute that matches the DOMElement ID given by $elemetnID.
     * If the there is a match then it returns the file path to the current file, while if it finds no match, then it returns null.
     * 
     * @param DOMAttribute $elementID   Represents the string ID that is unque to each DOMElement
     * @param string $filepath          Represents the file path
     * @param string $reftype           This variable specified which XML element reference has called the function
     * @return string|null              If the XML file with the specified string ID is found, the file path to this file is returned.
     *                                  Otherwise, null is returned
     */
    function findFile($elementID, $filepath, $reftype)
    {
        $defIDs = array();
        $commentIDs = array();

        $dirOrFiles = scandir($filepath);

        foreach ($dirOrFiles as $key => $file)
        {
            // first two items in the array $dirOrFiles refers to the current and parent directories
            // which is not useful in this case
            if ($key > 1)
            {
                $ext = explode('.', $file);

                if (sizeof($ext) <= 1) // it's a directory
                {
                    $inputpath = $filepath . '/' . $file;
                    $path = $this->findFile($elementID, $inputpath, $reftype);
                }
                else if ((sizeof($ext) > 1) && ($ext[1] == 'xml'))
                {
                    $Domparser = new DOMDocument();
                    @$Domparser->load($filepath . '/' . $file);

                    $element = $Domparser->documentElement;

                    //depending on the element that the reference was for, different actions are taken
                    switch ($reftype)
                    {
                        // for comment and definition references, it cannot just parse the top element
                        // because they are nested elements.  
                        case('comment'):
                            if ($element->tagName == 'unit')
                            {
                                $comments = $element->getElementsByTagName('comment');

                                foreach ($comments as $comment)
                                {
                                    $commentID = $comment->getAttribute('id');
                                    if ($commentID == $elementID)
                                    {
                                        $path = $filepath . '/' . $file;
                                        return $path;
                                    }
                                }
                            }

                        case('def'):
                            if ($element->tagName == 'unit')
                            {
                                $defs = $element->getElementsByTagName('def');

                                foreach ($defs as $def)
                                {
                                    $defID = $def->getAttribute('id');
                                    if ($defID == $elementID)
                                    {
                                        $path = $filepath . '/' . $file;
                                        return $path;
                                    }
                                }
                            }

                        // for theorem/unit/and all the pack elements,
                        // the parser just looks for the id attribute in the 
                        // root element of the file
                        case('theorem'):
                            $parsedID = $element->getAttribute('id');

                            if ($parsedID == $elementID)
                            {
                                $path = $filepath . '/' . $file;
                                return $path;
                            }

                        case('unit'):
                            $parsedID = $element->getAttribute('unitid');

                            if ($parsedID == $elementID)
                            {
                                $path = $filepath . '/' . $file;
                                return $path;
                            }

                        case('showmepack'):
                            $parsedID = $element->getAttribute('id');

                            if ($parsedID == $elementID)
                            {
                                $path = $filepath . '/' . $file;
                                return $path;
                            }

                        case('quizpack'):
                            $parsedID = $element->getAttribute('id');

                            if ($parsedID == $elementID)
                            {
                                $path = $filepath . '/' . $file;
                                return $path;
                            }

                        case('examplepack'):
                            $parsedID = $element->getAttribute('id');

                            if ($parsedID == $elementID)
                            {
                                $path = $filepath . '/' . $file;
                                return $path;
                            }

                        case('exercisepack'):
                            $parsedID = $element->getAttribute('id');

                            if ($parsedID == $elementID)
                            {
                                $path = $filepath . '/' . $file;
                                return $path;
                            }
                    }
                }
                else if ((sizeof($ext) > 1) && ($ext[1] != 'xml'))
                {
                    continue;
                }
            }
            if (!empty($path))
            {
                return $path;
            }
        }

        // base case where no match is found at the end of loop
        if (empty($path))
        {
            return null;
        }
        else
        {
            return $path;
        }
    }

    /**
     * This method is called by most of the classes to insert the new records to compositor table
     * after inserting the record to the element specific database table.(eg msm_unit, msm_def...etc).
     * 
     * @global moodle_database $DB
     * @param int $elementid        Represents the record ID from the database tables
     * @param int $parentid         Represents the record ID from the compositor tables that 
     *                              was the parent element in the XML file.
     * @param int $siblingid        Represents the record ID from the compositor tables that 
     *                              was the previous sibling element in the XML file.
     */
    function insertToCompositor($elementid, $tablename, $parentid = '', $siblingid = '')
    {
        global $DB;

        $compdata = new stdClass();
        $compdata->unit_id = $elementid;
        $compdata->table_id = $DB->get_record('msm_table_collection', array('tablename' => $tablename))->id;
        $compdata->parent_id = $parentid;
        $compdata->prev_sibling_id = $siblingid;

        $compid = $DB->insert_record('msm_compositor', $compdata);

        return $compid;
    }

    /**
     * 
     * @global moodle_database $DB
     * @param object $object
     * @param XML $XMLcontent
     * @return string|null
     */
    function displaySubordinate($object, $XMLcontent)
    {
        global $DB;
        $content = '';
        $recursivecontent = '';
        $doc = new DOMDocument();
        @$doc->loadXML($XMLcontent);

        $tables = $doc->getElementsByTagName('table');
        $imgs = $doc->getElementsByTagName('img');
        $hottags = $doc->getElementsByTagName('a');

        if ((empty($tables)) && (empty($imgs)) && (empty($hottags)))
        {
            echo "null?";
            return null;
        }
        else
        {
            foreach ($hottags as $key => $hottag)
            {
                if (!empty($object->subordinates[$key]))
                {
                    $subordinate = $object->subordinates[$key];
                    if (!empty($subordinate->infos[0]))
                    {
                        $newtag = '';
                        $newtag = "<a id='hottag-" . $subordinate->infos[0]->compid . "' class='hottag' onmouseover='popup(" . $subordinate->infos[0]->compid . ")'>";

                        if (is_string($subordinate->hot))
                        {
                            $newtag .= $subordinate->hot;
                        }
                        else
                        {
                            $newtag .= $this->getContent($subordinate->hot);
                        }
                        $newtag .= "</a>";

                        $hotString = $doc->saveXML($hottag);

                        $XMLcontent = str_replace($hotString, $newtag, $XMLcontent);

                        $content .= $subordinate->infos[0]->displayhtml();

//                        $content .= '<div id="dialog-' . $subordinate->infos[0]->compid . '" class="dialogs" title="' . $subordinate->infos[0]->caption . '">';
//
//                        $recursivecontent = $this->displaySubordinate($subordinate->infos[0], $subordinate->infos[0]->info_content);
//
//                        $content .= $recursivecontent;
//
//                        $content .= "</div>";
                    }
                    if (!empty($subordinate->external_links[0]))
                    {
                        $newtag = '';
                        $newtag = "<a href='" . $subordinate->external_links[0]->href . "'' id='hottag-" . $subordinate->external_links[0]->compid . "' class='hottag' onmouseover='popup(" . $subordinate->external_links[0]->compid . ")'>";

                        if (is_string($subordinate->hot))
                        {
                            $newtag .= $subordinate->hot;
                        }
                        else
                        {
                            $newtag .= $this->getContent($subordinate->hot);
                        }
                        $newtag .= "</a>";

                        $hotString = $doc->saveXML($hottag);

                        $XMLcontent = str_replace($hotString, $newtag, $XMLcontent);

                        if (!empty($subordinate->external_links[0]->infos[0]))
                        {
                            $content .= '<div id="dialog-' . $subordinate->external_links[0]->compid . '" class="dialogs" title="' . $subordinate->external_links[0]->infos[0]->caption . '">';

                            $recursivecontent = $this->displaySubordinate($subordinate->external_links[0]->infos[0], $subordinate->external_links[0]->infos[0]->info_content);

                            $content .= $recursivecontent;

                            $content .= "</div>";
                        }
                    }
                }
            }

            foreach ($tables as $table)
            {
                $newtabletag = '';

                $trs = $table->getElementsByTagName('tr');
                $border = $table->getAttribute('border');
                $cellpadding = $table->getAttribute('cellpadding');

                if (empty($border))
                {
                    $border = 0;
                }
                if (empty($cellpadding))
                {
                    $cellpadding = 0;
                }

                $newtabletag .= "<table class='mathtable' border='" . $border . "' cellpadding='" . $cellpadding . "'>";

                foreach ($trs as $tr)
                {
                    $newtabletag .= "<tr>";
                    foreach ($tr->childNodes as $grandChild)
                    {
                        if ($grandChild->nodeType == XML_ELEMENT_NODE)
                        {
                            if ($grandChild->tagName == 'td')
                            {
                                $newtabletag .= "<td style='border-width:" . $border . "px !important;'>";
                                foreach ($grandChild->childNodes as $tablecontent)
                                {
                                    $newtabletag .= $doc->saveXML($tablecontent);
                                }
                                $newtabletag .= "</td>";
                            }
                        }
                        else
                        {
                            $newtabletag .= $doc->saveXML($grandChild);
                        }
                    }
                    $newtabletag .= "</tr>";
                }

                $newtabletag .= "</table>";

                $tableString = $doc->saveXML($table);

                $XMLcontent = str_replace($tableString, $newtabletag, $XMLcontent);
            }


            foreach ($imgs as $key => $img)
            {
                $newtag = '';

                $src = $img->getAttribute('src');

                $sql = "src LIKE '%" . $src . "%'";

                // array_shift(array_values($array)) grabs the first item of the array --> since the get_records
                // return an array indexed by the id number of the record, need to grab the first item this way

                if ($DB->count_records_select('msm_img', $sql) > 1)
                {
                    $imgRecord = $DB->get_records_select('msm_img', $sql);
                    $imgparentid = $DB->get_record('msm_compositor', array('unit_id' => array_shift(array_values($imgRecord))->id, 'table_id' => 16))->parent_id;
                }
                else
                {
                    $imgRecord = $DB->get_record_select('msm_img', $sql);
                    $imgparentid = $DB->get_record('msm_compositor', array('unit_id' => $imgRecord->id, 'table_id' => 16))->parent_id;
                }



                if (!empty($imgRecord))
                {
                    $mediaRecord = $DB->get_record('msm_compositor', array('id' => $imgparentid));

                    if (!empty($mediaRecord))
                    {
                        $media = new Media();
                        $media->loadFromDb($mediaRecord->unit_id, $mediaRecord->id);

                        $newtag .= $media->displayhtml();

                        $imgString = $doc->saveXML($img);

                        $XMLcontent = str_replace($imgString, $newtag, $XMLcontent);
                    }
                }
            }
//            $XMLcontent = preg_replace("/\s\s{2,}/", " ", $XMLcontent);

            $content .= $XMLcontent;
            return $content;
        }
    }

}

?>
