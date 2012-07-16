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
    
//    function processMathArray($DomElement, $position)
//    {
//        $arrayOfMathArray = array();
//        
//        $position = $position+1;
//        
//        $matharrays = $DomElement->getElementByTagName('math.array');
//        $matharraylength = $matharrays->length;
//        for($i=1; $i < $matharraylength; $i++)
//        {
//            $position = $position+1;
//            $matharray = new MathArray($this->xmlpath);
//            $matharray->loadFromXml($matharrays->item($i), $position);
//            $arrayOfMathArray[] = $matharray;
//        }
//    }

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
            $hot = $subordinates->item(0)->getElementsByTagName('hot')->item(0);

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
//        $content = '';

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

        $doc = new DOMDocument();
        $element = $doc->importNode($DomElement, true);
        $content[] = $doc->saveXML($element);

        $resultcontent = array();

        foreach ($content as $key => $string)
        {
            $string = str_replace('<caption>', '<captions>', $string);
            $string = str_replace('</caption>', '</captions>', $string);

            $string = str_replace('<row>', '<tr>', $string);
            $string = str_replace('</row>', '</tr>', $string);

            $string = str_replace('<cell>', '<td>', $string);
            $string = str_replace('</cell>', '</td>', $string);

            $string = str_replace('<para.body', '<p', $string);
            $string = str_replace('</para.body>', '</p>', $string);
            
            $string = str_replace('<strong', '<b', $string);
            $string = str_replace('</strong>', '</b>', $string);
            
            $string = str_replace('<emphasis', '<i', $string);
            $string = str_replace('</emphasis>', '</i>', $string);

            $string = str_replace('<hot>', '<a href="">', $string);
            $string = str_replace('</hot>', '</a>  ', $string);
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

    // abstract method that is implemented by each class 
    // This function saves the data retrieved from loadFromXml method to the appropriate 
    // database table
    // abstract function saveIntoDb($position);
}

?>
