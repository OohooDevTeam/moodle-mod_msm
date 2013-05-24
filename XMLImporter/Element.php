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

                        $content = preg_replace('/^<math xmlns=(.+)>/', '<math>', $content);

                        $content = preg_replace('/<math>\s+<latex>/', '$', $content);
                        $content = preg_replace('/<\/latex>\s+<\/math>/', '$', $content);
                        $content = preg_replace('/<math>\s+<latex\/>\s+<\/math>/', '', $content);
                        // replacing \RNr[...] to \RNr{...}
                        // need to escape twice because it is parsed twice 
                        $content = preg_replace('/\\\\(RNr|CNr|QNr|ZNr|NNr|IdMtrx|Id)\[(.*?)\]/', '\\\\$1{$2}', $content);
                        // to change \RNr to \RNr{}
                        $content = preg_replace('/\\\\(RNr|CNr|QNr|ZNr|NNr|IdMtrx|Id)(\$|\\\\|:|\s|\.|=)/', '\\\\$1{}$2', $content);
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
//        $mlength = $medias->length;

        $mlength = $medias->length;

        //to eliminate any nested subordinates from being counted when getting the length of the subordinates
        foreach ($medias as $m)
        {
            if (isset($m->parentNode->parentNode->parentNode))
            {
                if ($m->parentNode->parentNode->parentNode->nodeType == XML_ELEMENT_NODE)
                {
                    if (($m->parentNode->parentNode->parentNode->tagName == 'info') && ($DomElement->tagName != 'info'))
                    {
                        $mlength--;
                    }
                }
            }
        }

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
        for ($i = 0; $i < $matharraylength; $i++)
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
        for ($i = 0; $i < $tablelength; $i++)
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
            // need to process subordinates separately in table/math.array to prevent duplicate data from being in database
            // eg) if proof.block had both subordinate in para and table, the same subordinates with parentid for table element would be also present with parentid of proof.block
            if (($DomElement->tagName == 'table') || ($DomElement->tagName == 'math.array'))
            {
                if ($s->parentNode->parentNode->parentNode->tagName != 'info')
                {
                    $length++;
                }
            }
            else
            {
                if (($s->parentNode->parentNode->parentNode->tagName != 'info') && ($s->parentNode->parentNode->parentNode->tagName != 'cell'))
                {
                    $length++;
                }
            }
        }
        for ($i = 0; $i < $length; $i++)
        {
            $position = $position + 1;
            $subordinate = new Subordinate($this->xmlpath);

            $subordinate->loadFromXml($subordinates->item($i), $position);
            if (!empty($subordinate->position))
            {
                $arrayOfSubordinates[] = $subordinate;
            }
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

        // to eliminate the caption of info element --> could not eliminate it from being included in the DomElement argument
        $infocaption = $DomElement->getElementsByTagName('info.caption');

        if ($infocaption->length > 0)
        {
            $infocaption->item(0)->parentNode->removeChild($infocaption->item(0));
        }

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
            $position++;
            // replacing the entire subordinate element with just the hot element of the subordinate to show only the hot element 
            $hot = $subordinates->item(0)->getElementsByTagName('hot')->item(0);
            $hot->setAttribute("id", $position);
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
            $string = str_replace('<row', '<tr', $string);
            $string = str_replace('</row>', '</tr>', $string);

            $string = str_replace('<cell', '<td', $string);
            $string = str_replace('</cell>', '</td>', $string);

            $string = str_replace('<para.body', '<span', $string);
            $string = str_replace('</para.body>', '</span>', $string);

            $string = str_replace('<para', '<p', $string);
            $string = str_replace('</para>', '</p>', $string);

            $string = str_replace('<strong', '<b', $string);
            $string = str_replace('</strong>', '</b>', $string);

            $string = str_replace('<emphasis', '<i', $string);
            $string = str_replace('</emphasis>', '</i>', $string);

            $string = str_replace('<hot', '<a', $string);
            $string = str_replace('</hot>', '</a>  ', $string);

            $string = preg_replace('/<math xmlns=(.+)>/', '<math>', $string);
            $string = preg_replace('/<math.display xmlns=(.+)>\s+<latex>/', '$$', $string);

            $string = preg_replace('/<math.display>\s+<latex>/', '$$', $string);
            $string = preg_replace('/<\/latex>\s+<\/math.display>/', '$$', $string);
            $string = preg_replace('/<math.display>\s+<latex\/>\s+<\/math.display>/', '', $string);

            $string = preg_replace('/<math>\s+<latex>/', '$', $string);
            $string = preg_replace('/<\/latex>\s+<\/math>/', '$', $string);
            $string = preg_replace('/<math>\s+<latex\/>\s+<\/math>/', '', $string);

            // ? needed to make it ungreedy
            $string = preg_replace('/\\\\(RNr|CNr|QNr|ZNr|NNr|IdMtrx|Id)\[(.*?)\]/', '\\\\$1{$2}', $string);
            $string = preg_replace('/\\\\(RNr|CNr|QNr|ZNr|NNr|IdMtrx|Id)(\$|\\\\|:|\s|\.|=)/', '\\\\$1{}$2', $string);
//            $string = preg_replace('/\\\\dfrac/', '\\\\\frac', $string);
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
            if (!empty($indexauthor->position))
            {
                $arrayOfIndexAuthor[] = $indexauthor;
            }
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
            if (!empty($indexglossary->position))
            {
                $arrayOfIndexGlossary[] = $indexglossary;
            }
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
            if (!empty($indexsymbol->position))
            {
                $arrayOfIndexSymbol[] = $indexsymbol;
            }
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
                            break;

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
                            break;

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
                            break;

                        case('unit'):
                            $parsedID = $element->getAttribute('unitid');

                            if ($parsedID == $elementID)
                            {
                                $path = $filepath . '/' . $file;
                                return $path;
                            }
                            break;

                        case('subunit'):
                            if ($element->tagName == 'unit')
                            {
                                $units = $element->getElementsByTagName('unit');

                                foreach ($units as $unit)
                                {
                                    $subunitID = $unit->getAttribute('unitid');
                                    if ($subunitID == $elementID)
                                    {
                                        $path = $filepath . '/' . $file;
                                        return $path;
                                    }
                                }
                            }
                            break;

                        case('showmepack'):
                            $parsedID = $element->getAttribute('id');

                            if ($parsedID == $elementID)
                            {
                                $path = $filepath . '/' . $file;
                                return $path;
                            }
                            break;

                        case('quizpack'):
                            $parsedID = $element->getAttribute('id');

                            if ($parsedID == $elementID)
                            {
                                $path = $filepath . '/' . $file;
                                return $path;
                            }
                            break;

                        case('examplepack'):
                            $parsedID = $element->getAttribute('id');

                            if ($parsedID == $elementID)
                            {
                                $path = $filepath . '/' . $file;
                                return $path;
                            }
                            break;

                        case('exercisepack'):
                            $parsedID = $element->getAttribute('id');

                            if ($parsedID == $elementID)
                            {
                                $path = $filepath . '/' . $file;
                                return $path;
                            }
                            break;
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
     * @param int $msmid            Represents the record ID from the msm table which defines the
     *                              moodle module instance
     */
    function insertToCompositor($elementid, $tablename, $msmid, $parentid = '', $siblingid = '')
    {
        global $DB;

        $compdata = new stdClass();
        $compdata->msm_id = $msmid;
        $compdata->unit_id = $elementid;
        $compdata->table_id = $DB->get_record('msm_table_collection', array('tablename' => $tablename))->id;
        $compdata->parent_id = $parentid;
        $compdata->prev_sibling_id = $siblingid;

        $compid = $DB->insert_record('msm_compositor', $compdata);

        return $compid;
    }

    /**
     * This recursive method is called if a duplicate record is read in XML.  Its purpose is to find all the child elements of the first
     * record of the duplicate record and to copy it onto the duplicate record in compositor table.  This is to remove any duplicate records in the
     * data tables but still to keep the data structure consistent with the XML files.
     * 
     * @global moodle_database $DB
     * @param int $elementRecord   A reference element that possibly has child elements that can be copied to other subunits without child elements
     * @param int $currentUnitID   The reference element that just has been read as a duplicate which needs alll its child element ids to be copied
     * @return boolean             returns false if no child has been copied/ returns true if child elements have been copied
     */
    function grabSubunitChilds($elementRecord, $currentUnitID, $msmid, $isRef = false)
    {
        global $DB;

//        echo "in grabSubunitChilds";

        if (!$isRef)
        {
            $childSibling = 0;
            // checking if there are child elements to be copied
            $childElements = $DB->get_records('msm_compositor', array('parent_id' => $elementRecord->id), 'prev_sibling_id');

            // checking if the following record is a duplicate or not
            // if it is the original record, it will already have a child elemnts associated with it
            $existingchildElements = $DB->get_records('msm_compositor', array('parent_id' => $currentUnitID), 'prev_sibling_id');

            // no child element detected to be copied
            if (empty($childElements))
            {
                return false;
            }
            // child elements exist and also the current reference element record does not have any child (meaning its not the original record)
            else if ((!empty($childElements)) && (empty($existingchildElements)))
            {
                foreach ($childElements as $child)
                {
                    $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;
                    $childCompID = $this->insertToCompositor($child->unit_id, $childtablename, $msmid, $currentUnitID, $childSibling);
                    $childSibling = $childCompID;
                    $this->grabSubunitChilds($child, $childCompID, $msmid);
                }
                return true;
            }
            // child element exist but the current record is the original record and already has child elements associated with it
            else if ((!empty($childElements)) && (!empty($existingchildElements)))
            {
                return false;
            }
        }
        // preventing theorem ref from having self-reference to itself or reference another reference that references this theorem back
        // if above is allowed, it creates an infinite loop
        else if ($isRef)
        {
            $theoremTable = $DB->get_record('msm_table_collection', array('tablename' => 'msm_theorem'))->id;
            $currentRecordTable = $DB->get_record('msm_compositor', array('id' => $currentUnitID))->table_id;

            if ($theoremTable == $currentRecordTable)
            {
                $childSibling = 0;
                // checking if there are child elements to be copied
                $childElements = $DB->get_records('msm_compositor', array('parent_id' => $elementRecord->id), 'prev_sibling_id');

                // checking if the following record is a duplicate or not
                // if it is the original record, it will already have a child elemnts associated with it
                $existingchildElements = $DB->get_records('msm_compositor', array('parent_id' => $currentUnitID), 'prev_sibling_id');

                // no child element detected to be copied
                if (empty($childElements))
                {
                    return false;
                }
                // child elements exist and also the current reference element record does not have any child (meaning its not the original record)
                else if ((!empty($childElements)) && (empty($existingchildElements)))
                {
                    foreach ($childElements as $child)
                    {
                        $childtablename = $DB->get_record('msm_table_collection', array('id' => $child->table_id))->tablename;
                        if ($childtablename == 'msm_statement_theorem')
                        {
//                            echo "child is statementTheorem";
                            $childCompID = $this->insertToCompositor($child->unit_id, $childtablename, $msmid, $currentUnitID, $childSibling);
                            $childSibling = $childCompID;
                            $this->grabSubunitChilds($child, $childCompID, $msmid, false);
                        }
                    }
                    return true;
                }
                // child element exist but the current record is the original record and already has child elements associated with it
                else if ((!empty($childElements)) && (!empty($existingchildElements)))
                {
                    return false;
                }
            }
        }
    }

    /**
     * 
     * @param Object $object
     * @param String $XMLcontent
     * @return null|String
     */
    function displayContent($object, $XMLcontent, $isindex = false)
    {
        global $DB;
        $content = '';
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = true;

        print_object($XMLcontent);
        
        if(!$doc->loadXML($XMLcontent))
        {
            $doc->loadHTML($XMLcontent);
        }

        $tables = $doc->getElementsByTagName('table');
        $imgs = $doc->getElementsByTagName('img');
        $hottags = $doc->getElementsByTagName('a');
        $matharrays = $doc->getElementsByTagName('math.array');

        $newElementdoc = new DOMDocument();

        if ((empty($tables)) && (empty($imgs)) && (empty($hottags)) && (empty($matharrays)))
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

                    // this subordinate element has reference child elements
                    if (!empty($subordinate->childs))
                    {
                        if (!empty($subordinate->infos[0]))
                        {
                            $newtag = '';
                            if (!$isindex)
                            {
                                $newtag = "<a id='activehottag-" . $subordinate->infos[0]->compid . "' class='activehottag' onmouseover='infoopen(" . $subordinate->infos[0]->compid . ")'>";
                            }
                            else
                            {
                                $newtag = "<span>";
                            }

                            $rawhotString = explode(',', $subordinate->hot);

                            // there are other commas in the content 
                            // therefore, get the position value from index 0 then concatenate the rest of the string
                            if (sizeof($rawhotString) >= 3)
                            {
                                $positionvalue = $rawhotString[0];

                                $rawhotContent = '';

                                for ($i = 1; $i < sizeof($rawhotString) - 1; $i++)
                                {
                                    $rawhotContent .= $rawhotString[$i] . ',';
                                }
                                $rawhotContent .= $rawhotString[sizeof($rawhotString) - 1];
                            }
                            // only comma in the hot string is the one separating the position value and the rest of the hot content
                            else
                            {
                                $positionvalue = $rawhotString[0];
                                $rawhotContent = $rawhotString[1];
                            }

                            if (is_string($subordinate->hot))
                            {

                                $newtag .= $rawhotContent;
                            }
                            else
                            {
                                $newtag .= $this->getContent($rawhotContent);
                            }
                            if (!$isindex)
                            {
                                $newtag .= "</a>";
                            }
                            else
                            {
                                $newtag .= "</span>";
                            }

                            $hottagid = $hottag->getAttribute('id');

                            if ($positionvalue == $hottagid)
                            {

                                $newElementdoc->loadXML($newtag);

                                $hottag->parentNode->replaceChild($doc->importNode($newElementdoc->documentElement, true), $hottag);
                                $XMLcontent = $doc->saveXML();

                                if (!$isindex)
                                {
                                    $content .= $subordinate->infos[0]->displayhtml();
                                    $content .= "<div class='refcontent' id='refcontent-" . $subordinate->infos[0]->compid . "' style='display:none;'>";
                                    foreach ($subordinate->childs as $child)
                                    {
                                        $content .= $child->displayhtml();
                                    }
                                    $content .= "</div>";
                                }
                            }
                        }
                    }
                    else
                    {
                        if (!empty($subordinate->infos[0]))
                        {
                            $newtag = '';
                            if (!$isindex)
                            {
                                $newtag .= "<a id='hottag-" . $subordinate->infos[0]->compid . "' class='hottag' onmouseover='popup(" . $subordinate->infos[0]->compid . ")'>";
                            }
                            else
                            {
                                $newtag = "<span>";
                            }
                            $rawhotString = explode(',', $subordinate->hot);

                            // there are other commas in the content 
                            // therefore, get the position value from index 0 then concatenate the rest of the string
                            if (sizeof($rawhotString) >= 3)
                            {
                                $positionvalue = $rawhotString[0];

                                $rawhotContent = '';

                                for ($i = 1; $i < sizeof($rawhotString) - 1; $i++)
                                {
                                    $rawhotContent .= $rawhotString[$i] . ',';
                                }
                                $rawhotContent .= $rawhotString[sizeof($rawhotString) - 1];
                            }
                            // only comma in the hot string is the one separating the position value and the rest of the hot content
                            else
                            {
                                $positionvalue = $rawhotString[0];
                                $rawhotContent = $rawhotString[1];
                            }

                            if (is_string($subordinate->hot))
                            {
                                $newtag .= $rawhotContent;
                            }
                            else
                            {
                                $newtag .= $this->getContent($rawhotContent);
                            }

                            if (!$isindex)
                            {
                                $newtag .= "</a>";
                            }
                            else
                            {
                                $newtag .= "</span>";
                            }

                            $hottagid = $hottag->getAttribute('id');

                            if (trim($positionvalue) == trim($hottagid))
                            {
                                $newtag = str_replace('<?xml version="1.0"?>', '', $newtag);
                                @$newElementdoc->loadXML($newtag);

                                $hottag->parentNode->replaceChild($doc->importNode($newElementdoc->documentElement, true), $hottag);

                                $XMLcontent = $doc->saveXML();

                                if (!$isindex)
                                {
                                    $content .= $subordinate->infos[0]->displayhtml();
                                }
                            }
                        }
                    }

                    if (!empty($subordinate->external_links[0]))
                    {
                        $newtag = '';
                        if (!$isindex)
                        {
                            $newtag = "<a href='" . $subordinate->external_links[0]->href . "' id='hottag-" . $subordinate->external_links[0]->compid . "' class='externallink' target='" . $subordinate->external_links[0]->target . "' onmouseover='popup(" . $subordinate->external_links[0]->compid . ")'>";
                        }
                        else
                        {
                            $newtag = "<span>";
                        }
                        $rawhotString = explode(',', $subordinate->hot);

                        // there are other commas in the content 
                        // therefore, get the position value from index 0 then concatenate the rest of the string
                        if (sizeof($rawhotString) >= 3)
                        {
                            $positionvalue = $rawhotString[0];

                            $rawhotContent = '';

                            for ($i = 1; $i < sizeof($rawhotString) - 1; $i++)
                            {
                                $rawhotContent .= $rawhotString[$i] . ',';
                            }
                            $rawhotContent .= $rawhotString[sizeof($rawhotString) - 1];
                        }
                        // only comma in the hot string is the one separating the position value and the rest of the hot content
                        else
                        {
                            $positionvalue = $rawhotString[0];
                            $rawhotContent = $rawhotString[1];
                        }

                        if (is_string($subordinate->hot))
                        {
                            $newtag .= $rawhotContent;
                        }
                        else
                        {
                            $newtag .= $this->getContent($rawhotContent);
                        }

                        if (!$isindex)
                        {
                            $newtag .= "</a>";
                        }
                        else
                        {
                            $newtag .= "</span>";
                        }

                        $newElementdoc->loadXML($newtag);

                        $hottag->parentNode->replaceChild($doc->importNode($newElementdoc->documentElement, true), $hottag);
                        $XMLcontent = $doc->saveXML();

                        if ((!empty($subordinate->external_links[0]->infos[0])) && (!$isindex))
                        {
                            $content .= $subordinate->external_links[0]->infos[0]->displayhtml();
                        }
                    }
                }
            }

            $tablelength = $tables->length;

            for ($i = 0; $i < $tablelength; $i++)
            {
                if (!empty($object->tables[$i]))
                {
                    $table = $object->tables[$i];
                    $newtableString = $table->displayhtml();
                    @$newElementdoc->loadXML($newtableString);
                    $tables->item($i)->parentNode->replaceChild($doc->importNode($newElementdoc->documentElement, true), $tables->item($i));
                }
                $XMLcontent = $doc->saveXML();
            }
            // could not use foreach matharrays...etc because when replaceChild is executed, it seems like the 
            // the length of the matharrays decrease as well.
            $matharraylength = $matharrays->length;

            for ($i = 0; $i < $matharraylength; $i++)
            {
                if (!empty($object->matharrays[$i]))
                {

                    $matharray = $object->matharrays[$i];
                    $newmarrayString = $matharray->displayhtml();

                    @$newElementdoc->loadXML($newmarrayString);

                    $matharrays->item(0)->parentNode->replaceChild($doc->importNode($newElementdoc->documentElement, true), $matharrays->item(0));
                }
                $XMLcontent = $doc->saveXML();
            }


            foreach ($imgs as $key => $img)
            {
                if (!empty($object->medias[$key]))
                {
                    $media = $object->medias[$key];

                    if (!empty($media->childs[0]))
                    {
                        $image = $media->childs[0];

                        $newtag = '';
                        $newtag .= $media->displayhtml($isindex);
                        // there can be only one xml declaration for the loadXML to work
                        // so if there are other xml declarations were added, remove them
                        $newtag = str_replace('<?xml version="1.0"?>', '', $newtag);
                        @$newElementdoc->loadXML($newtag);

                        $img->parentNode->replaceChild($doc->importNode($newElementdoc->documentElement, true), $img);
                        $XMLcontent = $doc->saveXML();
                    }
                }
            }
            $content .= $XMLcontent;
            $content = str_replace('<?xml version="1.0"?>', '', $content);
            return $content;
        }
    }

}

?>
