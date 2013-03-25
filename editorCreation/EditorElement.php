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

    function previewSubordinate($content, $subordinateArray)
    {
        $doc = new DOMDocument();
        $doc->loadHTML($content);

        $aElements = $doc->getElementsByTagName('a');
        foreach ($aElements as $key => $a)
        {
            $hotwordId = $a->getAttribute("id");

            $hotwordIdInfo = explode("-", $hotwordId);
            $index = '';
            for ($i = 1; $i < sizeof($hotwordIdInfo) - 1; $i++)
            {
                $index .= $hotwordIdInfo[$i] . "-";
            }
            $index .= $hotwordIdInfo[sizeof($hotwordIdInfo) - 1];
            $hotwordText = $a->textContent;

            $targetSub = null;

            foreach ($subordinateArray as $subordinate)
            {
                $subInfo = explode(",", $subordinate->hot);

                if ((trim($subInfo[0]) == trim($hotwordId)) && (trim($hotwordText) == trim($subInfo[1])))
                {
                    $targetSub = $subordinate;
                    break;
                }
            }

//            $subordinateDoc = new DOMDocument();
            $subHTML = $targetSub->displayPreview($index);

            $doc->loadHTML($subHTML);
            $div = $doc->getElementsByTagName("div")->item(0);
            $divNode = $doc->importNode($div, true);
           
             $aParentNode = $doc->importNode($a->parentNode, true);

            $aParentNode->appendChild($divNode);
            
            $doc->appendChild($aParentNode);
        }
        return $doc->saveHTML();
    }

}

?>
