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
 * This class is representing all the extra info elements(ackenowledgements, preface, summary, trailer and historical notes) 
 * that needs to be exported as XML document.  ExportExtraInfo class is called only by ExportUnit class. It inherits methods
 * from the abstract class ExportElement including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportExtraInfo extends ExportElement
{

    public $id;                     //ID of the current extra info element from the msm_extra_info database table
    public $compid;                 //ID of the current extra info element from the msm_compositor database table
    public $name;                   //the type of the extra info element as one of the following:  historical notes, 
                                    //summary, preface, ackenowledgements and/or trailer 
    public $blocks = array();       // ExportBlock objects that are assciated with this extra info element

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with extra info element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Unit.xsd.  This method also calls the exportData method
     * from the ExportBlock class.  The DOMElement object that is returned from exportData
     * calls from classes mentioned above is then appended to one of the folllowing DOMElements depending on
     * the value in name property of the class: preface/trailer/ackenowledgements/summary/historical.notes.
     * One of the DOMElements above is returned to be appended to the Unit element
     * 
     * @return DOMElement
     */

    public function exportData()
    {
        $extraInfoCreator = new DOMDocument();
        $extraInfoCreator->formatOutput = true;
        $extraInfoCreator->preserveWhiteSpace = false;
        $extraInfoNode = $extraInfoCreator->createElement(strtolower($this->name));

        foreach ($this->blocks as $key => $block)
        {
            $blockNode = null;
            $blockNode = $block->exportData();
            $newblockNode = $extraInfoCreator->importNode($blockNode, true);

            $extraInfoNode->appendChild($newblockNode);
        }
        return $extraInfoNode;
    }

    /**
     * This method is used to pull all relevant data linked with block elements from the database table
     * "msm_extra_info".  It also calls the loadDbData method from the ExporBlock classes.
     * 
     * @global moodle_database $DB
     * @param int $compid               ID of this extra info element in msm_compositor database table
     * @return \ExportExtraInfo
     */
    public function loadDbData($compid)
    {
        global $DB;

        $extraCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $extraUnitRecord = $DB->get_record("msm_extra_info", array("id" => $extraCompRecord->unit_id));

        $this->id = $extraUnitRecord->id;
        $this->compid = $compid;

        switch ($extraUnitRecord->extra_info_name)
        {
            case "Historical Notes":
                $this->name = "historical.notes";
                break;
            default:
                $this->name = strtolower($extraUnitRecord->extra_info_name);
                break;
        }
        $this->name = $extraUnitRecord->extra_info_name;

        $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->compid), "prev_sibling_id");

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));

            if ($childTable->tablename == "msm_block")
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
