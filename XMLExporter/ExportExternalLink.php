<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportExternalLink
 *
 * @author User
 */
class ExportExternalLink extends ExportElement
{
    public $id;
    public $compid;
    public $href;
    public $target;
    public $type;
    public $info;
    
    public function exportData()
    {
        
    }

    public function loadDbData($compid)
    {
        global $DB;
        
        $externalLinkCompRecord = $DB->get_record("msm_compositor", array("id"=>$compid));
        $externalLinkRecord = $DB->get_record("msm_external_link", array("id"=>$externalLinkCompRecord->unit_id));
        
        $this->compid = $externalLinkCompRecord->id;
        $this->id = $externalLinkRecord->id;
        
        $this->href = $externalLinkRecord->href;
        $this->target = $externalLinkRecord->target;
        $this->type = $externalLinkRecord->type;
        
        $childRecord = $DB->get_record("msm_compsitor", array("parent_id"=>$this->compid));
        
        if(!empty($childRecord))
        {
            $info = new ExportInfo();
            $info->loadDbData($childRecord->id);
            $this->info = $info;
        }
        
        return $this;
    }
}

?>
