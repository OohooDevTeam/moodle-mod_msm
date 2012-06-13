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

    // this function was created to retrieve the first item in the nodelist generated from
    // result of getElementsByTagName method
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

    function getContent($DomElement, $position = '', $xmlpath = '')
    {
        $content = '';
        $subordinates = array();
        $doc = new DOMDocument();

        if (!is_null($DomElement))
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

    function processSubordinate($DomElement, $position)
    {
        $elementcontent = new stdClass();
        $elementcontent->content = array();
        $elementcontent->subordinates = array();
        $elementcontent->indexauthors = array();
        $elementcontent->indexglossarys = array();
        $elementcontent->indexsymbols = array();
        $doc = new DOMDocument();

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
            $subordinate->loadFromXml($subordinates->item(0), $position);
            $elementcontent->subordinates[] = $subordinate;

            $subordinates->item(0)->parentNode->replaceChild($hot, $subordinates->item(0));
        }


        $indexauthors = $DomElement->getElementsByTagName('index.author');
        $ialength = $indexauthors->length;
        for ($i = 0; $i < $ialength; $i++)
        {
            $position = $position + 1;
            $indexauthor = new MathIndex($this->xmlpath);
            $indexauthor->loadFromXml($indexauthors->item(0), $position);
            $elementcontent->indexauthors[] = $indexauthor;

            $indexauthors->item(0)->parentNode->removeChild($indexauthors->item(0));
        }

        $indexglossarys = $DomElement->getElementsByTagName('index.glossary');
        $iglength = $indexglossarys->length;
        for ($i = 0; $i < $iglength; $i++)
        {
            $position = $position + 1;
            $indexglossary = new MathIndex($this->xmlpath);
            $indexglossary->loadFromXml($indexglossarys->item(0), $position);
            $elementcontent->indexglossarys[] = $indexglossary;

            $indexglossarys->item(0)->parentNode->removeChild($indexglossarys->item(0));
        }

        $indexsymbols = $DomElement->getElementsByTagName('index.symbol');
        $islength = $indexsymbols->length;
        for ($i = 0; $i < $islength; $i++)
        {
            $position = $position + 1;
            $indexsymbol = new MathIndex($this->xmlpath);
            $indexsymbol->loadFromXml($indexsymbols->item(0), $position);
            $elementcontent->indexsymbols[] = $indexsymbol;

            $indexsymbols->item(0)->parentNode->removeChild($indexsymbols->item(0));
        }

        $element = $doc->importNode($DomElement, true);
        $elementcontent->content[] = $doc->saveXML($element);
        
        return $elementcontent;
    }

    // abstract method that is implemented by each class 
    // This function saves the data retrieved from loadFromXml method to the appropriate 
    // database table
    // abstract function saveIntoDb($position);

    /* The method is used to process strings in content to convert them to
     * corresonding HTML elements, to allow conversion of math symbols by mathjax, or to 
     * insert the proper path for images.
     * 
     * The $content is passed to be processed and $DomElement and $xmlpath are needed to
     * create the correct pathing of the images.
     * 
     * @param string $content
     * @param DOMElement $DomElement
     * @param string $xmlpath
     */
}

?>
