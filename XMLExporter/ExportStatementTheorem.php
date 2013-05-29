<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportStatementTheorem
 *
 * @author User
 */
class ExportStatementTheorem extends ExportElement
{

    public $id;
    public $compid;
    public $content;
    public $subordinates = array();
    public $medias = array();
    public $parts = array();

    //put your code here
    public function exportData()
    {
        $statementCreator = new DOMDocument();
        $bodyNode = $statementCreator->createElement("statement.theorem");
        $createdbodyNode = $this->createXmlContent($statementCreator, $this->content, $bodyNode);
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
