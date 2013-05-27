<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportExtraInfo
 *
 * @author User
 */
class ExportExtraInfo extends ExportElement
{
    public $id;
    public $compid;
    public $name;
    public $blocks = array();
    
    public function exportData()
    {
        
    }

    public function loadDbData($compid)
    {
        global $DB;
        
        $extraCompRecord = $DB->get_record("msm_compositor", array("id"=>$compid));
        $extraUnitRecord = $DB->get_record("msm_extra_info", array("id"=>$extraCompRecord->unit_id));
        
        $this->id = $extraUnitRecord->id;
        $this->compid = $compid;
        $this->name = $extraUnitRecord->extra_info_name;
        
        $childRecords = $DB->get_records("msm_compositor", array("parent_id"=>$this->compid), "prev_sibling_id");
        
        foreach($childRecords as $child)
        {
            $childTable = $DB->get_record("msm_table_collection", array("id"=>$child->table_id));
            
            if($childTable->tablename == "msm_block")
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
