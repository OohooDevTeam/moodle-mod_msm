<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportTheorem
 *
 * @author User
 */
class ExportTheorem extends ExportElement
{
   public $id;
   public $compid;
   public $statements = array();
   public $description;
   public $caption;
   public $type;
   public $associates = array();
   
    public function exportData()
    {
        
    }

    public function loadDbData($compid)
    {
        global $DB;
        
        $theoremCompRecord = $DB->get_record("msm_compositor", array("id"=>$compid));
        $theoremUnitRecord = $DB->get_record("msm_theorem", array("id"=>$theoremCompRecord->unit_id));
        
        $this->id = $theoremUnitRecord->id;
        $this->compid = $compid;
        $this->caption = $theoremUnitRecord->caption;
        $this->description = $theoremUnitRecord->description;
        $this->type = $theoremUnitRecord->theorem_type;
        
        $childrenCompRecord = $DB->get_records("msm_compositor", array("parent_id"=>$this->compid), "prev_sibling_id");
        
        foreach($childrenCompRecord as $child)
        {
            $childtable = $DB->get_record("msm_table_collection", array("id"=>$child->table_id));
            
            if($childtable->tablename == "msm_statement_theorem")
            {
                $statement = new ExportStatementTheorem();
                $statement->loadDbData($child->id);
                $this->statements[] = $statement;
            }
            else if($childtable->tablename == "msm_associate")
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
