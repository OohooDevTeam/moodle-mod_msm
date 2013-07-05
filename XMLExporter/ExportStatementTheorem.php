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
 * This class is representing all the statement theorem elements that needs to be exported as XML document.
 * ExportStatementTheorem class is called by ExportTheorem class. It inherits methods from the abstract class
 * ExportElement including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportStatementTheorem extends ExportElement
{

    public $id;                             // ID of the current statement theorem in the msm_statement_theorem database table
    public $compid;                         // ID of the current statement theorem in the msm_compositor database table
    public $content;                        // content associated with the current statement theorem element
    public $subordinates = array();         // ExportSubordinate objects associated with the current statement theorem
    public $medias = array();               // ExportMedia objects associated with the current statement theorem
    public $parts = array();                // ExportPartTheorem objects associated with the current statement theorem

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with statement theorem element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Theorem.xsd.  This method also calls the exportData method
     * from ExportPartTheorem, ExportSubordinate, and ExportMedia classes.  The DOMElement object 
     * that is returned from exportData calls from classes mentioned above is then appended to the statement.theorem DOMElement
     * and is returned to be appended to the theorem element.
     * 
     * @return DOMElement
     */
    public function exportData()
    {
        $statementCreator = new DOMDocument();
        $statementCreator->formatOutput = true;
        $statementCreator->preserveWhiteSpace = false;
        $bodyNode = $statementCreator->createElement("statement.theorem");
        $createdbodyNode = $this->createXmlContent($statementCreator, $this->content, $bodyNode, $this);
        $statementNode = $statementCreator->importNode($createdbodyNode, true);

        if (!empty($this->parts))
        {
            foreach ($this->parts as $part)
            {
                $partNode = $part->exportData();
                $newpartNode = $statementCreator->importNode($partNode, true);
                $statementNode->appendChild($newpartNode);
            }
        }

        return $statementNode;
    }

    /**
     * This method is used to pull all relevant data linked with statement theorem elements from the database table
     * "msm_statement_theorem".  It also calls the loadDbData method from the ExportPartTheorem, ExportSubordinate and ExportMedia classes.
     * 
     * @global moodle_database $DB
     * @param int $compid                         ID of the current statement theorem in the msm_compositor database table 
     * @return \ExportStatementTheorem
     */
    public function loadDbData($compid)
    {
        global $DB;

        $statementCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $statementUnitRecord = $DB->get_record("msm_statement_theorem", array("id" => $statementCompRecord->unit_id));

        $this->id = $statementUnitRecord->id;
        $this->compid = $compid;
        $this->content = $statementUnitRecord->statement_content;

        $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->compid), "prev_sibling_id");

        foreach ($childRecords as $child)
        {
            $childtable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));

            switch ($childtable->tablename)
            {
                case "msm_subordinate":
                    $subordinate = new ExportSubordinate();
                    $subordinate->loadDbData($child->id);
                    $this->subordinates[] = $subordinate;
                    break;
                case "msm_media":
                    $media = new ExportMedia();
                    $media->loadDbData($child->id);
                    $this->medias[] = $media;
                    break;
                case "msm_part_theorem":
                    $part = new ExportPartTheorem();
                    $part->loadDbData($child->id);
                    $this->parts[] = $part;
                    break;
            }
        }

        return $this;
    }

}

?>
