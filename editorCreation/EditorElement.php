<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorElement
 *
 * @author User
 */
abstract class EditorElement
{

    abstract function getFormData($idNumber, $position);

    abstract function insertData($parentid, $siblingid, $msmid);
    abstract function loadData($compid);
    abstract function displayData();

    function processContent($oldContent)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($oldContent);
//
        $rootElement = $doc->getElementsByTagName('body')->item(0);
//        
//        $rootElement = $doc->documentElement;

        $newContent = array();

        foreach ($rootElement->childNodes as $key => $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if (($child->tagName == "p")||(preg_match('/h\d/', $child->tagName)=== 1))
                {
                    $para = new EditorPara();
                    $para->getFormData($child, $key);
                    $newContent[] = $para;
                }
                else if (($child->tagName == "ol") || ($child->tagName == "ul"))
                {
                    $inContent = new EditorInContent();
                    $inContent->getFormData($child, $key);
                    $newContent[] = $inContent;
                }
                else if ($child->tagName == "table")
                {
                    $table = new EditorTable();
                    $table->getFormData($child, $key);
                    $newContent[] = $table;
                }
//                else
//                {
//                    print_object($child->tagName);
//                    print_object($child->nodeValue);
//                }
            }
        }

        return $newContent;
    }

    function processSubordinate($content)
    {
        $subordinates = array();
        $htmlParser = new DOMDocument;

        $htmlParser->loadHTML($content);

        $aElements = $htmlParser->getElementsByTagName('a');
        foreach ($aElements as $key => $a)
        {
            $hotword = new EditorSubordinate();
            $hotword->getFormData($a, $key);
            $subordinates[] = $hotword;
        }
        
        return $subordinates;
    }

}

?>
