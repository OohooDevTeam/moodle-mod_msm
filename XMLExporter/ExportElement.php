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

    function createXmlContent($DomDocument, $content, $DomNode, $object = '')
    {
        $contentDoc = new DOMDocument();
        $contentDoc->formatOutput = true;
        $contentDoc->preserveWhiteSpace = false;
        $contentDoc->loadXML(utf8_encode(html_entity_decode($content)));
        $divNode = $contentDoc->documentElement;

        foreach ($divNode->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                if ($child->tagName == "p")
                {

                    if (!empty($object))
                    {
                        $atags = $child->getElementsByTagName("a");

                        foreach ($atags as $a)
                        {
                            $targetSub = null;
                            $id = $a->getAttribute("id");

                            foreach ($object->subordinates as $subordinate)
                            {
                                $hotInfo = explode("||", $subordinate->hot);
                                if (trim($id) == trim($hotInfo[0]))
                                {
                                    $targetSub = $subordinate;
                                    break;
                                }
                            }
                            if (!empty($targetSub))
                            {
                                $initsubordinateNode = $targetSub->exportData();

                                $subordinateNode = $contentDoc->importNode($initsubordinateNode, true);
                                $a->parentNode->replaceChild($subordinateNode, $a);
                            }
                        }

                        $imgTags = $child->getElementsByTagName("img");

                        foreach ($imgTags as $key => $img)
                        {
                            if (isset($this->medias))
                            {
                                $media = $this->medias[$key];
                                $mediaNode = $media->exportData();
                                $mediaElement = $contentDoc->importNode($mediaNode, true);
                                $img->parentNode->replaceChild($mediaElement, $img);
                            }
                        }
                    }
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
                        $liDoc->formatOutput = true;
                        $liDoc->preserveWhiteSpace = false;
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
                    if (!empty($object))
                    {
                        $atags = $child->getElementsByTagName("a");

                        foreach ($atags as $a)
                        {
                            $targetSub = null;
                            $id = $a->getAttribute("id");

                            foreach ($object->subordinates as $subordinate)
                            {
                                $hotInfo = explode("||", $subordinate->hot);
                                if (trim($id) == trim($hotInfo[0]))
                                {
                                    $targetSub = $subordinate;
                                    break;
                                }
                            }
                            $initsubordinateNode = $targetSub->exportData();
                            $subordinateNode = $contentDoc->importNode($initsubordinateNode, true);

                            $a->parentNode->replaceChild($subordinateNode, $a);
                        }
                    }

                    $imgTags = $child->getElementsByTagName("img");

                    foreach ($imgTags as $key => $img)
                    {
                        if (isset($this->medias))
                        {
                            $media = $this->medias[$key];
                            $mediaNode = $media->exportData();
                            $mediaElement = $contentDoc->importNode($mediaNode, true);
                            $img->parentNode->replaceChild($mediaElement, $img);
                        }
                    }

                    $childNode = $DomDocument->importNode($child, true);
                }
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

        foreach ($DomElement->childNodes as $child)
        {
            $childNode = $DomDocument->importNode($child, true);
            $parabody->appendChild($childNode);
        }
//        $parabodyText = $DomDocument->createTextNode($DomElement->textContent);
//        $parabody->appendChild($parabodyText);
        $paraNode->appendChild($parabody);

        return $paraNode;
    }

    //need a function to look for subordinate and math
    // need a function to convert some HTML stuff to XML
}

?>
