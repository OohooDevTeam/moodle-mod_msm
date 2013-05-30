<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportMedia
 *
 * @author User
 */
class ExportMedia extends ExportElement
{
    public $id;
    public $compid;
    public $img;
    public $active;
    public $inline;
    public $type;
    //put your code here
    public function exportData()
    {
        $mediaCreator = new DOMDocument();
        $mediaNode = $mediaCreator->createElement("media");
        $mediaNode->setAttribute("id", $this->compid);
        $mediaNode->setAttribute("active", $this->active);
        $mediaNode->setAttribute("inline", $this->inline);
        $mediaNode->setAttribute("type", $this->type);
        
        if(!empty($this->img))
        {
            $imgNode = $this->img->exportData();
            $newimgNode = $mediaCreator->importNode($imgNode, true);
            $mediaNode->appendChild($newimgNode);
        }
        
        return $mediaNode;
    }

    public function loadDbData($compid)
    {
        global $DB;
        
        $mediaCompRecord = $DB->get_record("msm_compositor", array("id"=>$compid));
        $mediaUnitRecord = $DB->get_record("msm_media", array("id"=>$mediaCompRecord->unit_id));
        
        $this->id = $mediaUnitRecord->id;
        $this->compid = $compid;
        $this->active = $mediaUnitRecord->active;
        $this->inline = $mediaUnitRecord->inline;
        $this->type = $mediaUnitRecord->media_type;
        
        $imgRecord = $DB->get_record("msm_compositor", array("parent_id"=>$this->compid));
        
        if(!empty($imgRecord))
        {
            $image = new ExportImage();
            $image->loadDbData($imgRecord->id);
            $this->img = $image;
        }
        
        return $this;
    }
}

?>
