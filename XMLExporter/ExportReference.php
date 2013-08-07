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
 * ExportReference class is representing any internal/external references that exist in the
 * contents in the editor.  These references are made as a child element of associate or subordinate
 * elements and needs to have a <type.ref typeID=#/> in place of the reference and have the
 * reference exported as a standalone material.  These reference materials can be an instance of
 * ExportDefinition/ExportComment/ExportTheorem classes.
 */
class ExportReference extends ExportElement
{

    public $id;                 // database ID of the current comment element in reference material's respective database table
    public $compid;             // database ID of the current comment element in msm_compositor database table
    public $msmid;              // if external reference, it will be the msm_id of the original content
                                // if internal reference, it will be the instance ID of the current MSM module
    public $type;               // internal/external references
    public $ref;                // ExportDefinition/ExportComment/ExportTheorem objects used as a reference material

    /**
     * This method is an abstract method declared by the abstract class ExportElement.  Its role is to
     * convert all database data associated with internal/external element into properly structured XML document.
     * This method calls the exportData method from reference material object and creates a tag for the reference
     * material with its MSM instance ID and compoistor ID to find the standalone XML file later during import.
     * 
     * @return DOMElement
     */
    public function exportData()
    {
        $refCreator = new DOMDocument();
        $refCreator->formatOutput = true;
        $refCreator->preserveWhiteSpace = false;

        $refTypeNode = null;
        $msmId = '';

        $existingCompid = $this->ref->exportData("ref");

        if ($this->type == "internal")
        {
            $refTypeNode = $refCreator->createElement("companion");
            $msmId = $this->msmid;
        }
        else
        {
            $refTypeNode = $refCreator->createElement("external.ref");
            $msmId = $this->ref->msmid;
        }

        switch (get_class($this->ref))
        {
            case "ExportDefinition":
                $refNode = $refCreator->createElement("definition.ref");
                if (!empty($existingCompid))
                {
                    $refNode->setAttribute("definitionID", $msmId . "-" . $existingCompid);
                }
                else
                {
                    $refNode->setAttribute("definitionID", $msmId . "-" . $this->compid);
                }
                break;
            case "ExportTheorem":
                $refNode = $refCreator->createElement("theorem.ref");
                if (!empty($existingCompid))
                {
                    $refNode->setAttribute("theoremID", $msmId . "-" . $existingCompid);
                }
                else
                {
                    $refNode->setAttribute("theoremID", $msmId . "-" . $this->compid);
                }
                break;
            case "ExportComment":
                $refNode = $refCreator->createElement("comment.ref");
                if (!empty($existingCompid))
                {
                    $refNode->setAttribute("commentID", $msmId . "-" . $existingCompid);
                }
                else
                {
                    $refNode->setAttribute("commentID", $msmId . "-" . $this->compid);
                }
                break;
        }
        $refTypeNode->appendChild($refNode);
        return $refTypeNode;
    }

    /**
     * This method is used to pull all relevant data linked with internal/external elements from the reference mateterial's respective database tables
     * (msm_def/msm_comment/msm_theorem).  It also calls the loadDbData method from the ExportDefinition/ExportTheorem/ExportComment classes.
     * 
     * @global moodle_database $DB
     * @param integer $compid                           database ID of the current reference material in msm_compositor table
     */
    public function loadDbData($compid)
    {
        global $DB;

        $otherMsmId = 0;

        $referenceCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $referenceTable = $DB->get_record("msm_table_collection", array("id" => $referenceCompRecord->table_id));

        $this->id = $referenceCompRecord->unit_id;
        $this->compid = $compid;

        $sql = "SELECT * FROM mdl_msm_compositor WHERE unit_id=$referenceCompRecord->unit_id AND table_id=$referenceTable->id AND id<>$compid";
        $otherSameRecords = $DB->get_records_sql($sql);

        foreach ($otherSameRecords as $otherRec)
        {
            $otherRecParent = $DB->get_record("msm_compositor", array("id" => $otherRec->parent_id));
            $parentTable = $DB->get_record("msm_table_collection", array("id" => $otherRecParent->table_id));

            if (($parentTable->tablename != "msm_subordinate") && ($parentTable->tablename != "msm_associate"))
            {
                $otherMsmId = $otherRecParent->msm_id;
                break;
            }
        }

        if ($otherMsmId == $referenceCompRecord->msm_id)
        {
            $this->type = "internal";
        }
        else
        {
            $this->type = "external";
        }

        $this->msmid = $otherMsmId;

        switch ($referenceTable->tablename)
        {
            case "msm_def":
                $def = new ExportDefinition();
                $def->loadDbData($referenceCompRecord->id);
                $this->ref = $def;
                break;
            case "msm_theorem":
                $theorem = new ExportTheorem();
                $theorem->loadDbData($referenceCompRecord->id);
                $this->ref = $theorem;
                break;
            case "msm_comment":
                $comment = new ExportComment();
                $comment->loadDbData($referenceCompRecord->id);
                $this->ref = $comment;
                break;
            case "msm_unit":
                break;
        }
    }

}

?>
