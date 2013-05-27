<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportBlock
 *
 * @author User
 */
class ExportBlock extends ExportElement
{
    public $id;
    public $compid;
    public $caption;
    public $content = array();
    
    //put your code here
    public function exportData()
    {
        
    }

    public function loadDbData($compid)
    {
        global $DB;
        
        $blockCompRecord = $DB->get_record("msm_compositor", array("id"=>$compid));
        $blockUnitRecord = $DB->get_record("msm_block", array("id"=>$blockCompRecord->unit_id));
        
        $this->id = $blockUnitRecord->id;
        $this->compid = $compid;
        $this->caption = $blockUnitRecord->block_caption;
        
        $childRecords = $DB->get_records("msm_compositor", array("parent_id"=>$this->compid), "prev_sibling_id");
        
        foreach($childRecords as $child)
        {
            $childTable = $DB->get_record("msm_table_collection", array("id"=>$child->table_id));
            
            switch($childTable->tablename)
            {
                case "msm_para":
                    $para = new ExportPara();
                    $para->loadDbData($child->id);
                    $this->content[] = $para;
                    break;
                case "msm_content":
                    $incontent = new ExportInContent();
                    $incontent->loadDbData($child->id);
                    $this->content[] = $incontent;
                    break;
                case "msm_table":
                    $table = new ExportTable();
                    $table->loadDbData($child->id);
                    $this->content[] = $table;
                    break;
            }
        }
        
        return $this;
    }
}

?>
