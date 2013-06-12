<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportInfo
 *
 * @author User
 */
class ExportInfo extends ExportElement
{

    public $id;
    public $compid;
    public $caption;
    public $content;
    public $subordinates = array();
    public $medias = array();

    public function exportData()
    {
        $infoCreator = new DOMDocument();
        $infoCreator->formatOutput = true;
        $infoCreator->preserveWhiteSpace = false;
        $infoNode = $infoCreator->createElement("info");

        if (!empty($this->caption))
        {
            $captionNode = $infoCreator->createElement("info.caption");            
            $captionText = $infoCreator->createTextNode($this->caption);
            $captionNode->appendChild($captionText);

//            $newcaptionNode = $this->createXmlContent($infoCreator, "<div>$this->caption</div>", $captionNode, '');
//            $newCaption = $infoCreator->importNode($newcaptionNode, true);
            $infoNode->appendChild($captionNode);
        }

        $createdbodyNode = $this->createXmlContent($infoCreator, $this->content, $infoNode, $this);

        $bodyNode = $infoCreator->importNode($createdbodyNode, true);

        return $bodyNode;
    }

    public function loadDbData($compid)
    {
        global $DB;

        $infoCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $infoUnitRecord = $DB->get_record("msm_info", array("id" => $infoCompRecord->unit_id));

        $this->id = $infoUnitRecord->id;
        $this->compid = $compid;
        $this->caption = trim($infoUnitRecord->caption);
        $this->content = $infoUnitRecord->info_content;

        $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->compid), "prev_sibling_id");

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
