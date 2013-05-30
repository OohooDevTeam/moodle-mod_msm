<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportPartTheorem
 *
 * @author User
 */
class ExportPartTheorem extends ExportElement
{

    public $id;
    public $compid;
    public $counter;
    public $content;
    public $caption;
    public $eqMark;
    public $subordinates = array();
    public $medias = array();

    //put your code here
    public function exportData()
    {
        $partCreator = new DOMDocument();
        $partCreator->formatOutput = true;
        $partCreator->preserveWhiteSpace = false;
        $partNode = $partCreator->createElement("part.theorem");
        $partNode->setAttribute("partid", $this->id);

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

    public function loadDbData($compid)
    {
        global $DB;

        $partCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $partUnitRecord = $DB->get_record("msm_part_theorem", array("id" => $partCompRecord->unit_id));

        $this->id = $partUnitRecord->id;
        $this->compid = $compid;
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
