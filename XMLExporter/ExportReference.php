<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportReference
 *
 * @author User
 */
class ExportReference extends ExportElement
{

    public $id;
    public $compid;
    public $msmid; // if external reference, it will be the msm_id of the original content
    public $type;
    public $ref;

    public function exportData()
    {
        $refCreator = new DOMDocument();
        $refCreator->formatOutput = true;
        $refCreator->preserveWhiteSpace = false;
        
        $refTypeNode = null;
        if ($this->type == "internal")
        {
            $refTypeNode = $refCreator->createElement("companion");
        }
        else
        {
            $refTypeNode = $refCreator->createElement("external.ref");
        }

        switch (get_class($this->ref))
        {
            case "ExportDefinition":
                $refNode = $refCreator->createElement("definition.ref");
                $refNode->setAttribute("definitionID", $this->msmid . "-" . $this->compid);
                break;
            case "ExportTheorem":
                $refNode = $refCreator->createElement("theorem.ref");
                $refNode->setAttribute("theoremID", $this->msmid . "-" . $this->compid);
                break;
            case "ExportComment":
                $refNode = $refCreator->createElement("comment.ref");
                $refNode->setAttribute("commentID", $this->msmid . "-" . $this->compid);
                break;
            // need to add ExportUnit later when dealing with internal/exteral references
        }
        $refTypeNode->appendChild($refNode);
        
        $this->ref->exportData("ref");
        
        return $refTypeNode;
    }

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
