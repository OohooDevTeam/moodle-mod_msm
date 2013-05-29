<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportElement
 *
 * @author User
 */
abstract class ExportElement
{

    abstract function loadDbData($compid);

    abstract function exportData();

    function makeExportFile($DomDocument)
    {
        // function to create XML files and save into moodle file system
    }

    function createXmlContent($DomDocument, $content, $DomNode)
    {
        $contentDoc = new DOMDocument();
        $contentDoc->formatOutput = true;
        $contentDoc->loadXML(utf8_encode(html_entity_decode($content)));
        $divNode = $contentDoc->documentElement;

        foreach ($divNode->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == "p")
                {
                    $newChildNode = $this->replacePTags($DomDocument, $child);
                    $childNode = $DomDocument->importNode($newChildNode, true);
                }
                else
                {
                    $ptags = $child->getElementsByTagName("p");

                    foreach ($ptags as $p)
                    {
                        $newpNode = $this->replacePTags($DomDocument, $p);
                        $pNode = $DomDocument->importNode($newpNode, true);
                        $p->parentNode->replaceChild($pNode, $p);
                    }

                    $litags = $child->getElementsByTagName("li");

                    foreach ($litags as $li)
                    {
                        $liDoc = new DOMDocument();
                        $newliTag = $liDoc->createElement("li");
                        $newPTag = $liDoc->createElement("para");
                        $newPbodyTag = $liDoc->createElement("para.body");
                        foreach ($li->childNodes as $lichild)
                        {
                            $lichildNode = $liDoc->importNode($lichild, true);
                            $newPbodyTag->appendChild($lichildNode);
                        }
                        $newPTag->appendChild($newPbodyTag);
                        $newliTag->appendChild($newPTag);
                        $newLi = $contentDoc->importNode($newliTag, true);
                      
                        $li->parentNode->replaceChild($newLi, $li);
                    }
                    $childNode = $DomDocument->importNode($child, true);
                }
                $DomDocument->formatOutput = true;
                $DomNode->appendChild($childNode);
            }
        }

        return $DomNode;
    }

    function replacePTags($DomDocument, $DomElement)
    {
        $paraNode = $DomDocument->createElement("para");
        $align = $DomElement->getAttribute("align");
        if (!empty($align))
        {
            $paraNode->setAttribute("align", $align);
        }
        else
        {
            $paraNode->setAttribute("align", "left");
        }
        $parabody = $DomDocument->createElement("para.body");
        $parabodyText = $DomDocument->createTextNode($DomElement->textContent);
        $parabody->appendChild($parabodyText);
        $paraNode->appendChild($parabody);

        return $paraNode;
    }

    //need a function to look for subordinate and math
    // need a function to convert some HTML stuff to XML
}

?>
