<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportPara
 *
 * @author User
 */
class ExportPara extends ExportElement {

    public $id;
    public $compid;
    public $align;
    public $subordinates = array();
    public $content;
    public $medias = array();

    //put your code here
    public function exportData() {
        $paraCreator = new DOMDocument();
        $paraCreator->formatOutput = true;
        $paraCreator->preserveWhiteSpace = false;
        $paraNode = $this->processParaContent();
        $newparaNode = $paraCreator->importNode($paraNode, true);
//        $paraNode = $paraCreator->createElement("para");
//        if (!empty($this->align))
//        {
//            $paraNode->setAttribute("align", $this->align);
//        }
//        else
//        {
//            $paraNode->setAttribute("align", "left");
//        }
//        $paraNode->setAttribute("id", $this->compid);       
//
//        $content = $this->processParaContent();
//        $contentElement = $paraCreator->importNode($content, true);
//
//        $parabodyNode->appendChild($contentNode);
//
//        $paraNode->appendChild($parabodyNode);

        return $newparaNode;
    }

    public function loadDbData($compid) {
        global $DB;

        $paraCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $paraUnitRecord = $DB->get_record("msm_para", array("id" => $paraCompRecord->unit_id));

        $this->id = $paraUnitRecord->id;
        $this->compid = $compid;
        $this->align = $paraUnitRecord->para_align;
        $this->content = $paraUnitRecord->para_content;

        $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->compid), "prev_sibling_id");

        foreach ($childRecords as $child) {
            $childtable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));

            if ($childtable->tablename == "msm_subordinate") {
                $subordinate = new ExportSubordinate();
                $subordinate->loadDbData($child->id);
                $this->subordinates[] = $subordinate;
            } else if ($childtable->tablename == "msm_media") {
                $media = new ExportMedia();
                $media->loadDbData($child->id);
                $this->medias[] = $media;
            }
        }

        return $this;
    }

//
    function processParaContent() {
        $contentDoc = new DOMDocument();
        $contentDoc->formatOutput = true;
        $contentDoc->preserveWhiteSpace = false;
        $contentDoc->loadXML(utf8_encode(html_entity_decode($this->content)));
        $contentNode = $contentDoc->documentElement;

        $atags = $contentNode->getElementsByTagName("a");

        $alength = $atags->length;

//                        foreach ($anchorArray as $a) {
        for ($i = 0; $i < $alength; $i++) {
            $a = $atags->item(0);
            $targetSub = null;
            if (!empty($a)) {
                $id = $a->getAttribute("id");

                foreach ($this->subordinates as $subordinate) {
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
        $imgTags = $contentNode->getElementsByTagName("img");

        foreach ($imgTags as $key => $img) {
            if (isset($this->medias)) {
                if (sizeof($this->medias) > 0) {
                    $media = $this->medias[$key];
                    $mediaNode = $media->exportData();
                    $mediaElement = $contentDoc->importNode($mediaNode, true);
                    $img->parentNode->replaceChild($mediaElement, $img);
                }
            }
        }

        $this->exportMath($contentNode, $contentDoc);

        $newpNode = $this->replacePTags($contentDoc, $contentNode);

        return $newpNode;
    }

}

?>
