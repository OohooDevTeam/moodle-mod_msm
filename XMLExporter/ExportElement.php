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
abstract class ExportElement {

    abstract function loadDbData($compid);

    abstract function exportData();

    function makeExportFile($DomDocument) {
        // function to create XML files and save into moodle file system
    }

    function createXmlContent($DomDocument, $content, $DomNode, $object = '') {
        $anchorArray = array();
        $contentDoc = new DOMDocument();
//       
//        $contentDoc->loadXML(utf8_encode(html_entity_decode($content)));
        $content = preg_replace("/(?s)(<img(\"[^\"]*\"|'[^']*'|[^'\">\/])*)(>)/", "$1/>", $content);
        $contentDoc->loadXML($content);
        $divNode = $contentDoc->documentElement;

        foreach ($divNode->childNodes as $child) {
            if ($child->nodeType == XML_ELEMENT_NODE) {
                if ($child->tagName == "p") {
                    if (!empty($object)) {
                        $atags = $child->getElementsByTagName("a");
                        
                        $alength = $atags->length;

//                        foreach ($anchorArray as $a) {
                        for ($i = 0; $i < $alength; $i++) {
                            $a = $atags->item(0);
                            $targetSub = null;
                            if (!empty($a)) {
                                $id = $a->getAttribute("id");
                                
                                foreach ($object->subordinates as $subordinate) {
                                    $hotInfo = explode("||", $subordinate->hot);
                                    
                                    if (trim($id) == trim($hotInfo[0])) {
                                        $targetSub = $subordinate;
                                        break;
                                    }
                                }

                                if (!empty($targetSub)) {
                                    $initsubordinateNode = $targetSub->exportData();
                                    $subordinateNode = $contentDoc->importNode($initsubordinateNode, true);
                                    if (!empty($a->parentNode)) {
                                        $a->parentNode->replaceChild($subordinateNode, $a);
                                    }
                                }
                            }
                        }

                        $imgTags = $child->getElementsByTagName("img");

                        foreach ($imgTags as $key => $img) {
                            if (isset($object->medias)) {
                                if (sizeof($object->medias) > 0) {
                                    $media = $object->medias[$key];
                                    $mediaNode = $media->exportData();
                                    $mediaElement = $contentDoc->importNode($mediaNode, true);
                                    $img->parentNode->replaceChild($mediaElement, $img);
                                }
                            }
                        }

                        $this->exportMath($child, $contentDoc);
                    }
                    $newChildNode = $this->replacePTags($DomDocument, $child);
                    $childNode = $DomDocument->importNode($newChildNode, true);
                } else {
                    $ptags = $child->getElementsByTagName("p");

                    foreach ($ptags as $p) {
                        $newpNode = $this->replacePTags($DomDocument, $p);
                        $pNode = $DomDocument->importNode($newpNode, true);
                        $p->parentNode->replaceChild($pNode, $p);
                    }

                    $litags = $child->getElementsByTagName("li");

                    foreach ($litags as $li) {
                        $liDoc = new DOMDocument();
                        $liDoc->formatOutput = true;
                        $liDoc->preserveWhiteSpace = false;
                        $newliTag = $liDoc->createElement("li");
                        $newPTag = $liDoc->createElement("para");
                        $newPbodyTag = $liDoc->createElement("para.body");
                        foreach ($li->childNodes as $lichild) {
                            $lichildNode = $liDoc->importNode($lichild, true);
                            $newPbodyTag->appendChild($lichildNode);
                        }
                        $newPTag->appendChild($newPbodyTag);
                        $newliTag->appendChild($newPTag);
                        $newLi = $contentDoc->importNode($newliTag, true);

                        $li->parentNode->replaceChild($newLi, $li);
                    }
                    if (!empty($object)) {
                        $atags = $child->getElementsByTagName("a");
                        
                        $alength = $atags->length;

//                        foreach ($anchorArray as $a) {
                        for ($i = 0; $i < $alength; $i++) {
                            $a = $atags->item(0);
                            $targetSub = null;
                            if (!empty($a)) {
                                $id = $a->getAttribute("id");

                                foreach ($object->subordinates as $subordinate) {
                                    $hotInfo = explode("||", $subordinate->hot);
                                  
                                    if (trim($id) == trim($hotInfo[0])) {
                                        $targetSub = $subordinate;
                                        break;
                                    }
                                }

                                if (!empty($targetSub)) {
                                    $initsubordinateNode = $targetSub->exportData();
                                    $subordinateNode = $contentDoc->importNode($initsubordinateNode, true);
                                    if (!empty($a->parentNode)) {
                                        $a->parentNode->replaceChild($subordinateNode, $a);
                                    }
                                   
                                }
                            }
                        }

                        // need a copy of anchored element nodelist b/c 
                        // when the anchored element is replaced with its XML counter part,
                        // it loses items in the nodelist 
//                        foreach ($atags as $atag) {
//                            $anchorArray[] = $atag;
//                        }
////                        $alength = $atags->length;
//
//                        foreach ($anchorArray as $a) {
//                            $targetSub = null;
//                            $id = $a->getAttribute("id");
//
//                            foreach ($object->subordinates as $subordinate) {
//                                $hotInfo = explode("||", $subordinate->hot);
//                                if (trim($id) == trim($hotInfo[0])) {
//                                    $targetSub = $subordinate;
//                                    break;
//                                }
//                            }
//
//                            if (!empty($targetSub)) {
//                                $initsubordinateNode = $targetSub->exportData();
//                                $subordinateNode = $contentDoc->importNode($initsubordinateNode, true);
//                                if (!empty($a->parentNode)) {
//                                    $a->parentNode->replaceChild($subordinateNode, $a);
//                                }
//                            }
//                        }
                    }

                    $imgTags = $child->getElementsByTagName("img");

                    foreach ($imgTags as $key => $img) {
                        if (isset($object->medias)) {
                            if (sizeof($object->medias) > 0) {
                                $media = $object->medias[$key];
                                $mediaNode = $media->exportData();
                                $mediaElement = $contentDoc->importNode($mediaNode, true);
                                $img->parentNode->replaceChild($mediaElement, $img);
                            }
                        }
                    }
                    $this->exportMath($child, $contentDoc);

                    $childNode = $DomDocument->importNode($child, true);
                }
                $DomNode->appendChild($childNode);
            } else { // info captions saved from imported XML does not have any tags other than top div tags --> b/c only takes textContent
                $childNode = $DomDocument->importNode($child, true);
                $DomNode->appendChild($childNode);
            }
        }
        $DomDocument->formatOutput = true;
        $DomDocument->preserveWhiteSpace = false;
        return $DomNode;
    }

    function exportMath($DomElement, $DomDocument) {
        $mathSpans = $DomElement->getElementsByTagName("span");

        // when each of the spans with className of matheditor is replaced, the size of the NodeList above
        // decreases so need a temporary array that is a copy of the NodeList above
        $tempArray = array();
        foreach ($mathSpans as $mathspan) {
            $tempArray[] = $mathspan;
        }

        foreach ($tempArray as $math) {
            if ($math->getAttribute("class") == "matheditor") {
                $mathNode = $DomDocument->createElement("math");
                $latexNode = $DomDocument->createElement("latex");

                $modifiedMathText = preg_replace("/\((.*?)\)/", "$1", trim($math->textContent));
                $innerMathText = preg_replace("/^\\\(.*?)\\\?$/", "$1", $modifiedMathText);

                $innerTextNode = $DomDocument->createTextNode($innerMathText);
                $latexNode->appendChild($innerTextNode);
                $mathNode->appendChild($latexNode);

                $math->parentNode->replaceChild($mathNode, $math);
            }
        }
    }

    function replacePTags($DomDocument, $DomElement) {
        $paraNode = $DomDocument->createElement("para");
        $style = $DomElement->getAttribute("style");
        $align = '';

        $styleProperites = explode(";", $style);
        foreach ($styleProperites as $property) {
            $propertyValue = explode(":", $property);

            if ($propertyValue[0] == "text-align") {
                $align = $propertyValue[1];
            }
        }

        if (!empty($align)) {
            $paraNode->setAttribute("align", trim($align));
        } else {
            $paraNode->setAttribute("align", "left");
        }
        $parabody = $DomDocument->createElement("para.body");

        foreach ($DomElement->childNodes as $child) {
            $childNode = $DomDocument->importNode($child, true);
            $paraContentNode = $childNode->cloneNode(true);
            $parabody->appendChild($paraContentNode);
        }
        $paraNode->appendChild($parabody);

        return $paraNode;
    }

    function createXMLFile($obj, $elementContent) {
        global $DB, $CFG;

        $msmRecord = $DB->get_record("msm", array("id" => $obj->msmid));
        $msmtrimName = preg_replace("/\s+/", '', $msmRecord->name);
        $CompDir = $CFG->dataroot . "/temp/msmtempfiles/$msmtrimName$msmRecord->id/standalones/";

        $elementType = '';
        switch (get_class($obj)) {
            case "ExportDefinition":
                $elementType = "definition";
                break;
            case "ExportTheorem":
                $elementType = "theorem";
                break;
            case "ExportComment":
                $elementType = "comment";
                break;
        }

        if (file_exists($CompDir)) {
            if (!empty($obj->caption)) {
                $filename = $CompDir . $obj->caption . "-$elementType" . $obj->compid . ".xml";
            } else if (!empty($obj->type)) {
                $filename = $CompDir . $obj->type . "-$elementType" . $obj->compid . ".xml";
            }
        } else {
            if (mkdir($CompDir)) {
                if (!empty($obj->caption)) {
                    $filename = $CompDir . $obj->caption . "-$elementType" . $obj->compid . ".xml";
                } else if (!empty($obj->type)) {
                    $filename = $CompDir . $obj->type . "-$elementType" . $obj->compid . ".xml";
                }
            } else {
                echo "error with creating standalone folder";
            }
        }

        if ($xmlfile = fopen($filename, "w")) {
            fwrite($xmlfile, $elementContent);
            fclose($xmlfile);
        } else {
            echo json_encode("error");
        }
    }

    //need a function to look for subordinate and math
    // need a function to convert some HTML stuff to XML
}

?>
