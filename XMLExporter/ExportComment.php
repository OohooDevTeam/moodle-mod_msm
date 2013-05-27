<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportComment
 *
 * @author User
 */
class ExportComment extends ExportElement
{
    public $id;
    public $compid;
    public $caption;
    public $description;
    public $type;
    public $content;
    public $associate = array();
    public $subordinates = array();
    
    public function exportData()
    {
        
    }

    public function loadDbData($compid)
    {
        global $DB;
        
        $commentCompRecord = $DB->get_record("msm_compositor", array("id"=>$compid));
        $commentRecord = $DB->get_record("msm_comment", array("id"=>$commentCompRecord->unit_id));
        
        $this->id = $commentRecord->id;
        $this->compid = $compid;
        $this->caption = $commentRecord->caption;
        $this->description = $commentRecord->description;
        $this->type = $commentRecord->comment_type;
        $this->content = $commentRecord->comment_content;
        
        $childRecords = $DB->get_records("msm_compositor", array("parent_id" => $this->compid), 'prev_sibling_id');

        foreach ($childRecords as $child)
        {
            $childTable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));
            if ($childTable->tablename == "msm_subordinate")
            {
                $subordinate = new ExportSubordinate();
                $subordinate->loadDbData($child->id);
                $this->subordinates[] = $subordinate;
            }
            else if ($childTable->tablename == "msm_associate")
            {
                $associate = new ExportAssociate();
                $associate->loadDbData($asso->id);
                $this->associates[] = $associate;
            }
        }
        
        return $this;
    }
}

?>
