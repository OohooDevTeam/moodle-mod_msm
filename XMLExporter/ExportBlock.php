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
 * This class is representing all the block elements that needs to be exported as XML document.  ExportBlock
 * class is called by ExportUnit, ExportExtraInfo and ExportIntro.
 * It inherits methods from the abstract class ExportElement including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportBlock extends ExportElement
{

    public $id;                     // database ID of current block element in msm_block database table
    public $compid;                 // database ID of current block element in msm_compositor database table
    public $caption;                // title associated with this block element
    public $content = array();      // array of other content class objects such as ExportPara, ExportInContent and/or ExportTable objects

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with block element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Molecules.xsd.  This method also calls the exportData method
     * from ExportPara, ExportInContent, and ExportTable clases.  The DOMElement object that is returned from exportData
     * calls from classes mentioned above is then appended to the block DOMElement and is returned to be appended to the 
     * one of the following elements: ExtraInfo, Intro or Unit elements
     * 
     * @param DOMElement $captionNode       // passed from ExportIntro if the intro element has a title associated with it
     * @return DOMElement
     */
    public function exportData($captionNode = '')
    {
        $blockCreator = new DOMDocument();
        $blockCreator->formatOutput = true;
        $blockCreator->preserveWhiteSpace = false;
        $blockNode = $blockCreator->createElement("block");

        if (!empty($captionNode))
        {
            $impCaption = $blockCreator->importNode($captionNode, true);
            $blockNode->appendChild($impCaption);
        }
        else if (!empty($this->caption))
        {
            $captionNode = $blockCreator->createElement("caption");
            $captionText = $blockCreator->createTextNode($this->caption);
            $captionNode->appendChild($captionText);
            $blockNode->appendChild($captionNode);
        }

        $blockbodyNode = $blockCreator->createElement("block.body");

        foreach ($this->content as $content)
        {
            $contentNode = $content->exportData();
            $newcontentNode = $blockCreator->importNode($contentNode, true);
            $blockbodyNode->appendChild($newcontentNode);
        }
        $blockNode->appendChild($blockbodyNode);

        return $blockNode;
    }

    /**
     * This method is used to pull all relevant data linked with block elements from the database table
     * "msm_block".  It also calls the loadDbData method from the ExportPara, ExportInContent, and/or ExportTable classes.
     * 
     * @global moodle_database $DB
     * @param int $compid               database ID of the current block element in the msm_compositor table
     * @return \ExportBlock
     */
    public function loadDbData($compid)
    {
        global $DB;

        $blockCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $blockUnitRecord = $DB->get_record("msm_block", array("id" => $blockCompRecord->unit_id));

        $this->id = $blockUnitRecord->id;
        $this->compid = $compid;
        $this->caption = $blockUnitRecord->block_caption;

        $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->compid), "prev_sibling_id");

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));

            switch ($childTable->tablename)
            {
                case "msm_para":
                    $para = new ExportPara();
                    $para->loadDbData($child->id);
                    $this->content[] = $para;
                    break;
                case "msm_content":
                    $incontent = new ExportInContent();
                    $incontent->loadDbData($child->id);
                    $this->content[] = $incontent;
                    break;
                case "msm_table":
                    $table = new ExportTable();
                    $table->loadDbData($child->id);
                    $this->content[] = $table;
                    break;
            }
        }

        return $this;
    }

}

?>
