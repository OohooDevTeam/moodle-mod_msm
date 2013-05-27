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
    public $subordinates = array();
    public $content;
    public $medias = array();
    //put your code here
    public function exportData()
    {
        
    }

    public function loadDbData($compid)
    {
        global $DB;
        
        $paraCompRecord = $DB->get_record("msm_compositor", array("id"=>$compid));
        $paraUnitRecord = $DB->get_record("msm_para", array("id"=>$paraCompRecord->unit_id));
        
        $this->id = $paraUnitRecord->id;
        $this->compid = $compid;
        $this->content = $paraUnitRecord->para_content;
        
        $childRecords = $DB->get_records("msm_compositor", array("parent_id"=>$this->id), "prev_sibling_id");
        
        foreach($childRecords as $child)
        {
            $childtable = $DB->get_record("msm_table_collection", array("id"=>$child->table_id));
            
            if($childtable->tablename == "msm_subordinate")
            {
                $subordinate = new ExportSubordinate();
                $subordinate->loadDbData($child->id);
                $this->subordinates[] = $subordinate;
            }
            else if($childtable->tablename == "msm_media")
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
