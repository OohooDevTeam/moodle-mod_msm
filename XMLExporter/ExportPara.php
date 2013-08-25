<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                       **
 * @subpackage  msm                                                       **
 * @name        msm                                                       **
 * @copyright   University of Alberta                                     **
 * @link        http://ualberta.ca                                        **
 * @author      Ga Young Kim                                              **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * *************************************************************************
 */

/**
 * This class is representing all the paragraph elements(ie. <p>) that needs to be exported as XML document.
 * ExportPara class is called by ExportDefinition, ExportTheorem and ExportComment classes.
 * It inherits methods from the abstract class ExportElement including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportPara extends ExportElement {

    public $id;                             // ID of the current paragraph element in msm_para database table
    public $compid;                         // ID of the current paragraph element in msm_compositor database table
    public $align;                          // alignment specification for the paragraph element (eg. left, center, right)
    public $subordinates = array();         // ExportSubordinate objects associated with the content of the paragraph element
    public $content;                        // content associated with the paragraph element
    public $medias = array();               // ExportMedia objects associated with the content of the paragraph element

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with paragraph element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Molecules.xsd.  This method calls a processParaContent function
     * that processes all para contents and calls the exportData method from ExportSubordinate and ExportMedia clases.
     * The DOMElement object that is returned from exportData calls from classes mentioned above is then appended to
     * the content of the paragraph element which is then replaced with para.body DOMElement which is appended to para DOMElement.
     * The para DOMElement is returned to be appended to the Block elements.
     * 
     * @return DOMElement
     */

    public function exportData() {
        $paraCreator = new DOMDocument();
        $paraCreator->formatOutput = true;
        $paraCreator->preserveWhiteSpace = false;
        $paraNode = $this->processParaContent();
        $newparaNode = $paraCreator->importNode($paraNode, true);

        return $newparaNode;
    }

    /**
     * This method is used to pull all relevant data linked with paragraph elements from the database table
     * "msm_para".  It also calls the loadDbData method from the ExportSubordinate and ExportMedia classes.
     * 
     * @global moodle_database $DB
     * @param int $compid               ID of the current paragraph elmeent in msm_compositor database table
     * @return \ExportPara
     */
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

    /**
     * This method only exists within the ExportPara class and is a similar function to the 
     * createXmlContent function in ExportElement in a sense that it processes content to 
     * replace all image and anchored elements to its XML equivalent form and replace all 
     * b and em tags to strong and emphasis tags, respectively to match the schema specified by
     * ../NewSchemas/Molecules.xsd.  The processed content is then appended to new para.body XML element
     * which is then appended to new para XML element.  The new para XML element is then returned to be
     * ultimately appended to the block element.
     * 
     * @return DOMElement
     */
    private function processParaContent() {
        $contentDoc = new DOMDocument();
        $contentDoc->formatOutput = true;
        $contentDoc->preserveWhiteSpace = false;
        $content = $this->content;        
        $content = str_replace("<em>", "<emphasis>", $content);
        $content = str_replace("<b>", "<strong>", $content);
        $content = str_replace("</em>", "</emphasis>", $content);
        $content = str_replace("</b>", "</strong>", $content);
        
        $contentDoc->loadXML(utf8_encode(html_entity_decode($content)));
        $contentNode = $contentDoc->documentElement;

        // replace all anchored elements to subordinate XML elements
        $atags = $contentNode->getElementsByTagName("a");

        $alength = $atags->length;

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

        // replace all image elements to media XML elements
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

        // replace all mathjax code to <span class="matheditor">math content </span> format
        $this->exportMath($contentNode, $contentDoc);

        // replace p HTML tags to <para><para.body> XML elements
        $newpNode = $this->replacePTags($contentDoc, $contentNode);

        return $newpNode;
    }

}

?>
