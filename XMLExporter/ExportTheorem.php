<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportTheorem
 *
 * @author User
 */
class ExportTheorem extends ExportElement
{
    public $id;
    public $compid;
    public $statements = array();
    public $description;
    public $caption;
    public $type;
    public $associates = array();

    public function exportData()
    {
        $theoremCreator = new DOMDocument();
        $theoremCreator->formatOutput = true;
        $theoremCreator->preserveWhiteSpace = false;
        $theoremNode = $theoremCreator->createElement("theorem");
        $theoremNode->setAttribute("id", $this->compid);
        $theoremNode->setAttribute("type", $this->type);

        if (!empty($this->caption))
        {
            $captionNode = $theoremCreator->createElement("caption");
            $captionText = $theoremCreator->createTextNode($this->caption);
            $captionNode->appendChild($captionText);
            $theoremNode->appendChild($captionNode);
        }

        if (!empty($this->description))
        {
            $descriptionNode = $theoremCreator->createElement("description");
            $descriptionText = $theoremCreator->createTextNode($this->description);
            $descriptionNode->appendChild($descriptionText);
            $theoremNode->appendChild($descriptionNode);
        }

        if (!empty($this->statements))
        {
            foreach ($this->statements as $statement)
            {
                $statementNode = $statement->exportData();
                $newstatementNode = $theoremCreator->importNode($statementNode, true);
                $theoremNode->appendChild($newstatementNode);
            }
        }

        if (!empty($this->associates))
        {
            foreach ($this->associates as $associate)
            {
                $associateNode = $associate->exportData();
                $newassociateNode = $theoremCreator->importNode($associateNode, true);
                $theoremNode->appendChild($newassociateNode);
            }
        }
        return $theoremNode;
    }

    public function loadDbData($compid)
    {
        global $DB;

        $theoremCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $theoremUnitRecord = $DB->get_record("msm_theorem", array("id" => $theoremCompRecord->unit_id));

        $this->id = $theoremUnitRecord->id;
        $this->compid = $compid;
        $this->caption = $theoremUnitRecord->caption;
        $this->description = $theoremUnitRecord->description;
        $this->type = $theoremUnitRecord->theorem_type;

        $childrenCompRecord = $DB->get_records("msm_compositor", array("parent_id" => $this->compid), "prev_sibling_id");

        foreach ($childrenCompRecord as $child)
        {
            $childtable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));

            if ($childtable->tablename == "msm_statement_theorem")
            {
                $statement = new ExportStatementTheorem();
                $statement->loadDbData($child->id);
                $this->statements[] = $statement;
            }
            else if ($childtable->tablename == "msm_associate")
            {
                $associate = new ExportAssociate();
                $associate->loadDbData($child->id);
                $this->associates[] = $associate;
            }
        }

        return $this;
    }

}

?>
