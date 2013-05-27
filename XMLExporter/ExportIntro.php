<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportIntro
 *
 * @author User
 */
class ExportIntro extends ExportElement
{
    public $id;
    public $compid;
    public $blocks = array();
    public $caption;
    
    public function exportData()
    {
        
    }

    public function loadDbData($compid)
    {
        global $DB;
        
        $introCompRecord = $DB->get_record("msm_compositor", array("id"=>$compid));
        $introUnitRecord = $DB->get_record("msm_intro", array("id"=>$introCompRecord->unit_id));
        
        $this->id = $introUnitRecord->id;
        $this->compid = $compid;
        $this->caption = $introUnitRecord->intro_caption;
        
        $childRecords = $DB->get_records("msm_compositor", array("parent_id"=>$this->compid), "prev_sibling_id");
        
        foreach($childRecords as $child)
        {
            $childtable = $DB->get_record("msm_table_collection", array("id"=>$child->table_id));
            
            if($childtable->tablename == "msm_block")
            {
                $block = new ExportBlock();
                $block->loadDbData($child->id);
                $this->blocks[] = $block;
            }
        }
        return $this;
    }
}

?>
