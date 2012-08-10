<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Element
 *
 * @author User
 */
require_once("Subordinate.php");
require_once("Media.php");
require_once("MathImg.php");

abstract class Element
{

    public $xmlpath;
    public $id;

    // constructor for the class
    function __construct($xmlpath = '')
    {
        $this->xmlpath = $xmlpath;
    }

    // abstract method that is implemented by each class 
    // This function essentially parses the given XML file to retrive data

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
     *
     * @global moodle_database $DB
     * @param DOMElement $DomElement
     * @param String $propertyName
     * @return int/boolean 
     */
    function checkForRecord($DomElement, $propertyName = '')
    {
        global $DB;

        if (property_exists(get_class($DomElement), 'string_id'))
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
        else if (!empty($DomElement->$propertyName))
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
            echo "debugging checkForRecord";
            print_object($DomElement->tagName);
            return false;
        }
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position
     * @param String $xmlpath
     * @return String 
     */
    function getContent($DomElement, $position = '', $xmlpath = '')
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
                    if ($child->tagName == 'math')
                    {
                        $content .= $doc->saveXML($child);

                        $content = str_replace('<math>', '', $content);
                        $content = str_replace('</math>', '', $content);

                        $content = str_replace('<latex>', '$', $content);
                        $content = str_replace('</latex>', '$', $content);
                    }
                }
                else // child is not a node
                {
                    $content .= $doc->saveXML($child);
                }
            }
        }
        return $content;
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position
     * @return array 
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
     *
     * @param DOMElement $DomElement
     * @param int $position
     * @return array 
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
//            $hot = $subordinates->item(0)->getElementsByTagName('hot')->item(0);

            $position = $position + 1;
            $subordinate = new Subordinate($this->xmlpath);
            $subordinate->loadFromXml($subordinates->item($i), $position);
            $arrayOfSubordinates[] = $subordinate;
        }
        return $arrayOfSubordinates;
    }

    /**
     *
     * @param DOMElement $DomElement
     * @param int $position
     * @return array 
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

        $indexauthors = $DomElement->getElementsByTagName('index.author');
        $ialength = $indexauthors->length;
        for ($i = 0; $i < $ialength; $i++)
        {
            $indexauthors->item(0)->parentNode->removeChild($indexauthors->item(0));
        }

        $indexglossarys = $DomElement->getElementsByTagName('index.glossary');
        $iglength = $indexglossarys->length;
        for ($i = 0; $i < $iglength; $i++)
        {
            $indexglossarys->item(0)->parentNode->removeChild($indexglossarys->item(0));
        }

        $indexsymbols = $DomElement->getElementsByTagName('index.symbol');
        $islength = $indexsymbols->length;
        for ($i = 0; $i < $islength; $i++)
        {
            $indexsymbols->item(0)->parentNode->removeChild($indexsymbols->item(0));
        }
//
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
     *
     * @param DOMElement $DomElement
     * @param int $position
     * @return array 
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
     *
     * @param DOMElement $DomElement
     * @param int $position
     * @return array 
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
     *
     * @param DOMElement $DomElement
     * @param int $position
     * @return array 
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
     *
     * @param DOMElement $elementID
     * @param String $filepath
     * @return string|null 
     */
    function findFile($elementID, $filepath)
    {
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
                    $path = $this->findFile($elementID, $inputpath);
                }
                else if ((sizeof($ext) > 1) && ($ext[1] == 'xml'))
                {
                    $Domparser = new DOMDocument();
                    @$Domparser->load($filepath . '/' . $file);

                    $element = $Domparser->documentElement;

                    $parsedID = $element->getAttribute('id');

                    if ($parsedID == $elementID)
                    {
                        $path = $filepath . '/' . $file;
                        return $path;
                    }
                    else
                    {
                        continue;
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
     *
     * @global moodle_database $DB
     * @param int $elementid
     * @param int $parentid
     * @param int $siblingid 
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

    function displaySubordinate($object, $XMLcontent)
    {
        global $DB;
        $content = '';
        $recursivecontent='';
        $doc = new DOMDocument();
        @$doc->loadXML($XMLcontent);

        $tables = $doc->getElementsByTagName('table');
        $imgs = $doc->getElementsByTagName('img');
        $hottags = $doc->getElementsByTagName('a');

        if ((empty($tables)) && (empty($imgs)) && (empty($hottags)))
        {
            return null;
        }
        else
        {
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

                        $content .= '<div id="dialog-' . $subordinate->infos[0]->compid . '" class="dialogs" title="' . $subordinate->infos[0]->caption . '">';

                        $recursivecontent = $this->displaySubordinate($subordinate->infos[0], $subordinate->infos[0]->info_content);

                        $content .= $recursivecontent;

                        $content .= "</div>";
                    }
                }
            }

            $content .= $XMLcontent;
            return $content;
        }
    }

}

?>
