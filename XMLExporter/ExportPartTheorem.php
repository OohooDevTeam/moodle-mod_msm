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
    public $content;
    public $caption;
    public $eqMark;
    public $subordinates = array();
    public $medias = array();
    //put your code here
    public function exportData()
    {
        
    }

    public function loadDbData($compid)
    {
        global $DB;
        
        $partCompRecord = $DB->get_record("msm_compositor", array("id"=>$compid));
        $partUnitRecord = $DB->get_record("msm_part_theorem", array("id"=>$partCompRecord->unit_id));
        
        $this->id = $partUnitRecord->id;
        $this->compid = $compid;
        $this->content = $partUnitRecord->part_content;
        $this->eqMark = $partUnitRecord->equivalence_mark;
        $this->caption = $partUnitRecord->caption;
        
        $childRecords = $DB->get_records("msm_compositor", array("parent_id"=>$this->compid), "prev_sibling_id");
        
        foreach($childRecords as $child)
        {
            $childTable = $DB->get_record("msm_table_collection", array("id"=>$child->table_id));
            
            if($childTable->tablename == "msm_subordinate")
            {
                $subordinate = new ExportSubordinate();
                $subordinate->loadDbData($child->id);
                $this->subordinates[] = $subordinate;
            }
            else if($childTable->tablename == "msm_media")
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
