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
 * This class is representing all the table elements that needs to be exported as XML document.
 * ExportTable class is called by ExportBlock classes.  It inherits methods from the abstract class
 * ExportElement including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportTable extends ExportElement
{

    public $id;                             // ID of the current table element in the msm_table database table
    public $compid;                         // ID of the current table element in the msm_compositor database table
    public $content;                        // content associated with the current table element
    public $class;                          // class name associated with the current table element
    public $summary;                        // summary associated with the current table element
    public $title;                          // title associated with the current table element
    public $subordinates = array();         // ExportSubordinate objects associated with the table element
    public $medias = array();               // ExportMedia objects associated with the table element

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with table element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Molecules.xsd.  This method also calls the exportData method
     * from ExportSubordinate and ExportMedia clasess.  The DOMElement object that is returned from exportData 
     * calls from classes mentioned above replaces the image and anchored elements in the content of the table element
     * and the table element is returned to replace the existing table element with properly structured XML document.
     * 
     * @todo need to add subordinate and media processing in the table content export
     * 
     * @return DOMElement
     */

    public function exportData()
    {
        $tableCreator = new DOMDocument();
        $tableCreator->formatOutput = true;
        $tableCreator->preserveWhiteSpace = false;
        $tableCreator->loadXML($this->content);

        $tableNode = $tableCreator->documentElement;

        return $tableNode;
    }

    /**
     * This method is used to pull all relevant data linked with table elements from the database table
     * "msm_table".  It also calls the loadDbData method from the ExportSubordinate and ExportMedia classes.
     * 
     * @global moodle_database $DB
     * @param int $compid                   ID of the current table element in msm_compositor database table
     * @return \ExportTable
     */
    public function loadDbData($compid)
    {
        global $DB;

        $tableCompRecord = $DB->get_record("msm_compositor", array("id" => compid));
        $tableUnitRecord = $DB->get_record("msm_table", array("id" => $tableCompRecord->unit_id));

        $this->id = $tableUnitRecord->id;
        $this->compid = $compid;
        $this->content = $tableUnitRecord->table_content;
        $this->class = $tableUnitRecord->table_class;
        $this->summary = $tableUnitRecord->table_summary;
        $this->title = $tableUnitRecord->title;

        $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->id), "prev_sibling_id");

        foreach ($childRecords as $child)
        {
            $childtable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));

            if ($childtable->tablename == "msm_subordinate")
            {
                $subordinate = new ExportSubordinate();
                $subordinate->loadDbData($child->id);
                $this->subordinates[] = $subordinate;
            }
            else if ($childtable->tablename == "msm_media")
            {
                $media = new ExportMedia();
                $media->loadDbData($child->id);
                $this->medias[] = $media;
            }
        }

        return $this;
    }

}

?>
