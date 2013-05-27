<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportDefinition
 *
 * @author User
 */
class ExportDefinition extends ExportElement
{

    public $id;
    public $compid;
    public $caption;
    public $description;
    public $type;
    public $content;
    public $associates = array();
    public $subordinates = array();

    public function exportData()
    {
        
    }

    public function loadDbData($compid)
    {
        global $DB;

        $defCompRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $defRecord = $DB->get_record("msm_def", array("id" => $defCompRecord->unit_id));

        $this->id = $defRecord->id;
        $this->compid = $compid;
        $this->caption = $defRecord->caption;
        $this->description = $defRecord->description;
        $this->type = $defRecord->def_type;
        $this->content = $defRecord->def_content;

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
                $associate->loadDbData($child->id);
                $this->associates[] = $associate;
            }
        }

        return $this;
    }

}

?>
