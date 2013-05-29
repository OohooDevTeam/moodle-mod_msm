<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportDefinition
 *
 * @author User
 */
class ExportDefinition extends ExportElement
{

    public $id;
    public $compid;
    public $caption;
    public $description;
    public $type;
    public $content;
    public $associates = array();
    public $subordinates = array();

    public function exportData()
    {
        $defCreator = new DOMDocument();
        $defCreator->formatOutput = true;
        $defCreator->preserveWhiteSpace = false;
        $defNode = $defCreator->createElement("def");
        $defNode->setAttribute("type", $this->type);
        $defNode->setAttribute("id", $this->compid);

        if (!empty($this->caption))
        {
            $captionNode = $defCreator->createElement("caption");
            $captionText = $defCreator->createTextNode($this->caption);
            $captionNode->appendChild($captionText);
            $defNode->appendChild($captionNode);
        }

        if (!empty($this->description))
        {
            $descriptionNode = $defCreator->createElement("description");
            $descriptionText = $defCreator->createTextNode($this->description);
            $descriptionNode->appendChild($descriptionText);
            $defNode->appendChild($descriptionNode);
        }

        $defbodyNode = $defCreator->createElement("def.body");
        $createdbodyNode = $this->createXmlContent($defCreator, $this->content, $defbodyNode, $this);
        $bodyNode = $defCreator->importNode($createdbodyNode, true);

        $defNode->appendChild($bodyNode);

        if (!empty($this->associates))
        {
            foreach ($this->associates as $associate)
            {
                $associateNode = $associate->exportData();
                $newassociateNode = $defCreator->importNode($associateNode, true);
                $defNode->appendChild($newassociateNode);
            }
        }

        return $defNode;
    }

    public function loadDbData($compid)
    {
        global $DB;

        $defCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $defRecord = $DB->get_record("msm_def", array("id" => $defCompRecord->unit_id));

        $this->id = $defRecord->id;
        $this->compid = $compid;
        $this->caption = $defRecord->caption;
        $this->description = $defRecord->description;
        $this->type = $defRecord->def_type;
        $this->content = $defRecord->def_content;

        $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->compid), 'prev_sibling_id');

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));
            if ($childTable->tablename == "msm_subordinate")
            {
                $subordinate = new ExportSubordinate();
                $subordinate->loadDbData($child->id);
                $this->subordinates[] = $subordinate;
            }
            else if ($childTable->tablename == "msm_associate")
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
