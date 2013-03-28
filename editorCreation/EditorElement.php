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

    abstract function getFormData($idNumber);

    abstract function insertData($parentid, $siblingid, $msmid);

    abstract function loadData($compid);

    abstract function displayData();

    function processContent($oldContent)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($oldContent);
//
        $rootElement = $doc->getElementsByTagName('body')->item(0);

        $newContent = array();

        foreach ($rootElement->childNodes as $key => $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if (($child->tagName == "p") || (preg_match('/h\d/', $child->tagName) === 1))
                {
                    $para = new EditorPara();
                    $para->getFormData($child);
                    $newContent[] = $para;
                }
                else if (($child->tagName == "ol") || ($child->tagName == "ul"))
                {
                    $inContent = new EditorInContent();
                    $inContent->getFormData($child);
                    $newContent[] = $inContent;
                }
                else if ($child->tagName == "table")
                {
                    $table = new EditorTable();
                    $table->getFormData($child);
                    $newContent[] = $table;
                }
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
            $hotword->getFormData($a);
            $subordinates[] = $hotword;
        }

        return $subordinates;
    }
}

?>
