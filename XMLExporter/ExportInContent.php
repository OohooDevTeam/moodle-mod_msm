<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportInContent
 *
 * @author User
 */
class ExportInContent extends ExportElement
{

    public $id;
    public $compid;
    public $content;
    public $attr;
    public $type;
    public $medias = array();
    public $subordinates = array();

    public function exportData()
    {
        $incontentCreator = new DOMDocument();

        $incontentNode = null;
        if ($this->type == "ordered")
        {
            $incontentNode = $incontentCreator->createElement("ol");
            if (!empty($this->attr))
            {
                $incontentNode->setAttribute("type", $this->attr);
            }
        }
        else if ($this->type == "unordered")
        {
            $incontentNode = $incontentCreator->createElement("ul");
            if (!empty($this->attr))
            {
                $incontentNode->setAttribute("bullet", $this->attr);
            }
        }

        $patterns = array();
        $patterns[0] = "/<ul.*?>/";
        $patterns[1] = "/<\/ul>/";
        $patterns[2] = "/<ol.*?>/";
        $patterns[3] = "/<\/ol>/";
        $replacements = array();
        $replacements[0] = '';
        $replacements[1] = '';
        $replacements[2] = '';
        $replacements[3] = '';
        $modifiedContent = preg_replace($patterns, $replacements, $this->content);
        $incontentText = $incontentCreator->createTextNode($modifiedContent);
        $incontentNode->appendChild($incontentText);

        return $incontentNode;
    }

    public function loadDbData($compid)
    {
        global $DB;

        $incontentCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $incontentUnitRecord = $DB->get_record("msm_content", array("id" => $incontentCompRecord->unit_id));

        $this->id = $incontentUnitRecord->id;
        $this->compid = $compid;
        $this->attr = $incontentUnitRecord->additional_attribute;
        $this->type = $incontentUnitRecord->type;
        $this->content = $incontentUnitRecord->content;

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
