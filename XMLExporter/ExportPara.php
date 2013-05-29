<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportPara
 *
 * @author User
 */
class ExportPara extends ExportElement
{

    public $id;
    public $compid;
    public $align;
    public $subordinates = array();
    public $content;
    public $medias = array();

    //put your code here
    public function exportData()
    {
        $paraCreator = new DOMDocument();

        $paraNode = $paraCreator->createElement("para");
        if (!empty($this->align))
        {
            $paraNode->setAttribute("align", $this->align);
        }
        else
        {
            $paraNode->setAttribute("align", "left");
        }
        $paraNode->setAttribute("id", $this->compid);

        $parabodyNode = $paraCreator->createElement("para.body");

        $contentDoc = new DOMDocument();
        $contentDoc->loadXML(utf8_encode(html_entity_decode($this->content)));
        $topNode = $contentDoc->documentElement;
        $contentNode = $paraCreator->createTextNode($topNode->textContent);
        $parabodyNode->appendChild($contentNode);

        $paraNode->appendChild($parabodyNode);

        return $paraNode;
    }

    public function loadDbData($compid)
    {
        global $DB;

        $paraCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $paraUnitRecord = $DB->get_record("msm_para", array("id" => $paraCompRecord->unit_id));

        $this->id = $paraUnitRecord->id;
        $this->compid = $compid;
        $this->align = $paraUnitRecord->para_align;
        $this->content = $paraUnitRecord->para_content;

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
