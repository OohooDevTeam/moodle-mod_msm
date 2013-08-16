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
 * This class is representing all the intro elements that needs to be exported as XML document. 
 * ExportIntro class is called by ExportUnit class. It inherits methods from the abstract class
 * ExportElement including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportIntro extends ExportElement
{

    public $id;                     // ID of the current intro element in msm_intro database table
    public $compid;                 // ID of the current intro element in msm_compositor database table
    public $msmid;                  // msm instance ID
    public $blocks = array();       // ExportBlock objects associated with the current intro element
    public $caption;                // title associated with the current intro element

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with intro element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Unit.xsd.  This method also calls the exportData method
     * from ExportBlock class.  The DOMElement object that is returned from exportData calls from classes 
     * mentioned above is then appended to the intro DOMElement and is returned to be appended to the Unit element.
     * 
     * @return DOMElement
     */

    public function exportData()
    {
        $introCreator = new DOMDocument();
        $introCreator->formatOutput = true;
        $introCreator->preserveWhiteSpace = false;
        $introNode = $introCreator->createElement("intro");
        $introNode->setAttribute("id", "$this->msmid-$this->compid");

        $titleNode = null;

        if (!empty($this->caption))
        {
            $captionNode = $introCreator->createElement("caption");
            $createdTitleNode = $this->createXmlTitle($introCreator, $this->caption, $captionNode);
            $titleNode = $introCreator->importNode($createdTitleNode, true);
        }

        foreach ($this->blocks as $key => $block)
        {
            $blockNode = null;
            if (($key == 0) && ($titleNode != null))
            {
                $blockNode = $block->exportData($titleNode);
            }
            else
            {
                $blockNode = $block->exportData();
            }
            $newblockNode = $introCreator->importNode($blockNode, true);

            $introNode->appendChild($newblockNode);
        }
        return $introNode;
    }

    /**
     * This method is used to pull all relevant data linked with intro elements from the database table
     * "msm_intro".  It also calls the loadDbData method from the ExportBlock class.
     * 
     * @global moodle_database $DB
     * @param int $compid               ID of the current intro element in msm_compositor database table
     * @return \ExportIntro
     */
    public function loadDbData($compid)
    {
        global $DB;

        $introCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $introUnitRecord = $DB->get_record("msm_intro", array("id" => $introCompRecord->unit_id));

        $this->id = $introUnitRecord->id;
        $this->compid = $compid;
        $this->msmid = $introCompRecord->msm_id;
        $this->caption = $introUnitRecord->intro_caption;

        $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->compid), "prev_sibling_id");

        foreach ($childRecords as $child)
        {
            $childtable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));

            if ($childtable->tablename == "msm_block")
            {
                $block = new ExportBlock();
                $block->loadDbData($child->id);
                $this->blocks[] = $block;
            }
        }
        return $this;
    }

}

?>
