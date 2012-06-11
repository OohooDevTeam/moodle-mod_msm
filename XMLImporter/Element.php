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
        $content->content = '';
        $content->subordinates = array();
        $subordinates = array();
        $doc = new DOMDocument();

        if (!is_null($DomElement))
        {
            $element = $doc->importNode($DomElement, true);

            foreach ($element->childNodes as $child)
            {
                if ($child->nodeType == XML_ELEMENT_NODE)
                {
                    if($child->tagName == 'para.body')
                    {
                        foreach($child->childNodes as $subChild)
                        {
                            if($subChild->nodeType == XML_ELEMENT_NODE)
                            {
                                if($subChild->tagName != 'subordinate')
                                {
                                    $content->content .= $doc->saveXML($subChild);
                                }
                                else // child of para.body is subordinate
                                {
                                    $position = $position+1;
                                    $subordinate = new Subordinate($xmlpath);
                                    $subordinate->loadFromXml($subChild, $position);
                                    $content->subordinates[] = $subordinate;
                                    
                                    //need to append hot element (child element of subordinate) to content
                                    
                                   $hot = $subChild->getElementsByTagName('hot')->item(0);
                                   
                                   $content->content .= $doc->saveXML($hot);
                                }
                            }
                            else //child of para.body element is not an element node
                            {
                                $content->content .= $doc->saveXML($subChild);
                            }
                        }
                    }
                    else if($child->tagName == 'math')
                    {
                        $content->content .= $doc->saveXML($child);
                    }
                }
                else // child is not a node
                {
                    $content->content .= $doc->saveXML($child);
                }
            }
        }
        return $content;
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
