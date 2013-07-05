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
 * This class is representing all the part theorem elements that needs to be exported as XML document.
 * ExportPartTheorem class is called by ExportStatementTheorem class.  It inherits methods from the abstract class ExportElement
 * including the abstract methods exportData and loadDbData.
 * For more information on the other inherited methods, go to ExportElement class document.
 *
 * @author Ga Young Kim
 */
class ExportPartTheorem extends ExportElement
{

    public $id;                             // ID of the current part theorem element in the msm_part_theorem database table
    public $compid;                         // ID of the current part theorem element in the msm_compositor database table
    public $msmid;                          // MSM instance ID
    public $counter;                        // type of list numbering/bullets used for display
    public $content;                        // content of the part theorem
    public $caption;                        // title of the part theorem
    public $eqMark;                         // indicates which part theorem is equivalent to each other  
    public $subordinates = array();         // ExportSubordinate objects that are associated with this part theorem element
    public $medias = array();               // ExportMedia objects that are associated with this part theorem element

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with part theorem element into properly structured XML document.
     * It follows the XML schema in ../NewSchemas/Theorem.xsd.  This method also calls the exportData method
     * from ExportSubordinate and ExportMedia clases.  The DOMElement object that is returned from exportData
     * calls from classes mentioned above is then appended to the content of the part theorem DOMElement
     * and is returned to be appended to the statement.theorem element.
     * 
     * @return DOMElement
     */
    public function exportData()
    {
        $partCreator = new DOMDocument();
        $partCreator->formatOutput = true;
        $partCreator->preserveWhiteSpace = false;
        $partNode = $partCreator->createElement("part.theorem");
        $partNode->setAttribute("partid", "$this->msmid-$this->compid");

        if (!empty($this->counter))
        {
            $partNode->setAttribute("counter", $this->counter);
        }

        if (!empty($this->eqMark))
        {
            $partNode->setAttribute("equivalence.mark", $this->eqMark);
        }

        if (!empty($this->caption))
        {
            $captionNode = $partCreator->createElement("caption");
            $captionText = $partCreator->createTextNode($this->caption);
            $captionNode->appendChild($captionText);
            $partNode->appendChild($captionNode);
        }

        $partbodyNode = $partCreator->createElement("part.body");
        $createdbodyNode = $this->createXmlContent($partCreator, $this->content, $partbodyNode, $this);
        $bodyNode = $partCreator->importNode($createdbodyNode, true);
        $partNode->appendChild($bodyNode);

        return $partNode;
    }

    /**
     * This method is used to pull all relevant data linked with part theorem elements from the database table
     * "msm_part_theorem".  It also calls the loadDbData method from the ExportSubordinate and ExportMedia classes.
     * 
     * @global moodle_database $DB
     * @param int $compid                   ID of the current part theorme element in the msm_compositor
     * @return \ExportPartTheorem   
     */
    public function loadDbData($compid)
    {
        global $DB;

        $partCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $partUnitRecord = $DB->get_record("msm_part_theorem", array("id" => $partCompRecord->unit_id));

        $this->id = $partUnitRecord->id;
        $this->compid = $compid;
        $this->msmid = $partCompRecord->msm_id;
        $this->content = $partUnitRecord->part_content;
        $this->counter = $partUnitRecord->counter;
        $this->eqMark = $partUnitRecord->equivalence_mark;
        $this->caption = $partUnitRecord->caption;

        $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->compid), "prev_sibling_id");

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));

            if ($childTable->tablename == "msm_subordinate")
            {
                $subordinate = new ExportSubordinate();
                $subordinate->loadDbData($child->id);
                $this->subordinates[] = $subordinate;
            }
            else if ($childTable->tablename == "msm_media")
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
