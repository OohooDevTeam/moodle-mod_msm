<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportImage
 *
 * @author User
 */
class ExportImage extends ExportElement
{

    public $id;
    public $compid;
    public $msmid;
    public $msm_name;
    public $src;
    public $width;
    public $height;
    public $description;
    public $extended_caption;
    public $imagemaps = array();

    public function exportData()
    {
        $imgCreator = new DOMDocument();
        $imgNode = $imgCreator->createElement("img");
        $imgNode->setAttribute("id", "$this->msmid-$this->compid");

        $srcInfo = explode("/", $this->src);

        $modifiedsrc = explode("||", $srcInfo[sizeof($srcInfo) - 1]);

        $src = "$this->msm_name/pics/" . $modifiedsrc[0];

        $imgNode->setAttribute("src", $src);
        $imgNode->setAttribute("height", $this->height);
        $imgNode->setAttribute("width", $this->width);

        return $imgNode;
    }

    public function loadDbData($compid)
    {
        global $DB;

        $imgCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $imgUnitRecord = $DB->get_record("msm_img", array("id" => $imgCompRecord->unit_id));
        $msmData = $DB->get_record("msm", array("id"=>$imgCompRecord->msm_id));

        $this->id = $imgUnitRecord->id;
        $this->msmid = $imgCompRecord->msm_id;
        $this->msm_name = preg_replace("/\s+/", '', $msmData->name) . $imgCompRecord->msm_id;
        $this->compid = $compid;
        $this->src = $imgUnitRecord->src;
        $this->description = $imgUnitRecord->description;
        $this->extended_caption = $imgUnitRecord->extended_caption;
        $this->height = $imgUnitRecord->height;
        $this->width = $imgUnitRecord->width;
        // add code for image mapping when available

        return $this;
    }

}

?>
