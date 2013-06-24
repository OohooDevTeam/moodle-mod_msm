<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportAssociate
 *
 * @author User
 */
class ExportAssociate extends ExportElement
{
    public $id;
    public $compid;
    public $msmid;
    public $description;
    public $info;
    public $ref;

    //put your code here
    public function exportData()
    {
        $associateCreator = new DOMDocument();
        $associateCreator->formatOutput = true;
        $associateCreator->preserveWhiteSpace = false;
        $associateNode = $associateCreator->createElement("associate");
        $associateNode->setAttribute("type", $this->description);

        if (!empty($this->info))
        {
            $infoNode = $this->info->exportData();
            $newinfoNode = $associateCreator->importNode($infoNode, true);
            $associateNode->appendChild($newinfoNode);
        }

        if (!empty($this->ref))
        {
            switch (get_class($this->ref))
            {
                case "ExportDefinition":
                    $refNode = $associateCreator->createElement("definition.ref");
                    $refNode->setAttribute("definitionID", $this->msmid . "-" . $this->ref->compid);
                    break;
                case "ExportTheorem":
                    $refNode = $associateCreator->createElement("theorem.ref");
                    $refNode->setAttribute("theoremID", $this->msmid . "-" . $this->ref->compid);
                    break;
                case "ExportComment":
                    $refNode = $associateCreator->createElement("comment.ref");
                    $refNode->setAttribute("commentID", $this->msmid . "-" . $this->ref->compid);
                    break;
            }
            $associateNode->appendChild($refNode);
            $this->ref->exportData("ref");
        }

        return $associateNode;
    }

    public function loadDbData($compid)
    {
        global $DB;

        $associateCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $associateUnitRecord = $DB->get_record("msm_associate", array("id" => $associateCompRecord->unit_id));

        $this->id = $associateUnitRecord->id;
        $this->compid = $compid;
        $this->msmid = $associateCompRecord->msm_id;
        $this->description = $associateUnitRecord->description;

        $refCompRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->compid));

        foreach ($refCompRecords as $refCompRecord)
        {
            if (!empty($refCompRecord))
            {
                $refTable = $DB->get_record("msm_table_collection", array("id" => $refCompRecord->table_id));

                switch ($refTable->tablename)
                {
                    case "msm_def":
                        $def = new ExportDefinition();
                        $def->loadDbData($refCompRecord->id);
                        $this->ref = $def;
                        break;
                    case "msm_theorem":
                        $theorem = new ExportTheorem();
                        $theorem->loadDbData($refCompRecord->id);
                        $this->ref = $theorem;
                        break;
                    case "msm_comment":
                        $comment = new ExportComment();
                        $comment->loadDbData($refCompRecord->id);
                        $this->ref = $comment;
                        break;
                    case "msm_info":
                        $info = new ExportInfo();
                        $info->loadDbData($refCompRecord->id);
                        $this->info = $info;
                        break;
//                    case "msm_unit":
//                        break;
                }
            }
        }
//        }

        return $this;
    }

}

?>
