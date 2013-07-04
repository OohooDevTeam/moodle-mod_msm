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
 * This class is representing all the ul and/or ol elements that needs to be exported as XML document.
 * ExportInContent class is called by ExportBlock class. It inherits methods from the abstract class ExportElement 
 * including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportInContent extends ExportElement {

    public $id;                             // ID of the current ul and/or ol elements in msm_content database table
    public $compid;                         // ID of the current ul and/or ol elements in msm_compositor database table
    public $content;                        // content of the current ul and/or ol elements
    public $attr;                           // extra attributes on ul and/or ol elements such as the type of bullet/numbering on the list
    public $type;                           // stating if the element is either ul or ol
    public $subordinates = array();         // ExportSubordinate objects associated with this ul and/or ol element content
    public $medias = array();               // ExportMedia objects associated with this ul and/or ol element content

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with ul and/or ol element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Molecules.xsd.  This method also calls the exportData method
     * from ExportSubordinate and ExportMedia classes.  The DOMElement object that is returned from exportData
     * calls from classes mentioned above is then appended to the ul and/or ol DOMElement and is returned to be appended to the 
     * block element.
     * 
     * @return DOMElement
     */
    public function exportData() {
        $incontentCreator = new DOMDocument();
        $incontentCreator->formatOutput = true;
        $incontentCreator->preserveWhiteSpace = false;
        $incontentNode = null;
        if ($this->type == "ordered") {
            $incontentNode = $incontentCreator->createElement("ol");
            if (!empty($this->attr)) {
                $incontentNode->setAttribute("type", $this->attr);
            }
        } else if ($this->type == "unordered") {
            $incontentNode = $incontentCreator->createElement("ul");
            if (!empty($this->attr)) {
                $incontentNode->setAttribute("bullet", $this->attr);
            }
        }

        $createdbodyNode = $this->createXmlContent($incontentCreator, $this->content, $incontentNode, $this);
        $bodyNode = $incontentCreator->importNode($createdbodyNode, true);
        return $bodyNode;
    }

    /**
     * This method is used to pull all relevant data linked with ul and/or ol elements from the database table
     * "msm_content".  It also calls the loadDbData method from the ExportSubordinate and ExportMedia classes.
     * 
     * @global moodle_database $DB
     * @param int $compid               database ID of the current ul/ol element in the msm_compositor table
     * @return \ExportInContent
     */
    public function loadDbData($compid) {
        global $DB;

        $incontentCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $incontentUnitRecord = $DB->get_record("msm_content", array("id" => $incontentCompRecord->unit_id));

        $this->id = $incontentUnitRecord->id;
        $this->compid = $compid;
        $this->attr = $incontentUnitRecord->additional_attribute;
        $this->type = $incontentUnitRecord->type;
        $this->content = $incontentUnitRecord->content;

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

}

?>
