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
    public $src;
    public $width;
    public $height;
    public $description;
    public $extended_caption;
    public $imagemaps = array();    
    
    public function exportData()
    {
        
    }

    public function loadDbData($compid)
    {
        global $DB;
        
        $imgCompRecord = $DB->get_record("msm_compositor", array("id"=>$compid));
        $imgUnitRecord = $DB->get_record("msm_img", array("id"=>$imgCompRecord->unit_id));
        
        $this->id = $imgUnitRecord->id;
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