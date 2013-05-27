<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportAssociate
 *
 * @author User
 */
class ExportAssociate extends ExportElement
{
    public $id;
    public $compid;
    public $description;
    public $info;
    public $ref;
    //put your code here
    public function exportData()
    {
        
    }

    public function loadDbData($compid)
    {
        global $DB;
        
        $associateCompRecord = $DB->get_record("msm_compositor", array("id"=>$compid));
        $associateUnitRecord = $DB->get_record("msm_associate", array("id"=>$associateCompRecord->unit_id));
        
        $this->id = $associateUnitRecord->id;
        $this->compid = $compid;
        $this->description = $associateUnitRecord->description;
        
        $infoTable = $DB->get_record("msm_table_collection", array("tablename"=>"msm_info"));
        $infoChildRecord = $DB->get_record("msm_compositor", array("parent_id"=>$this->compid, "table_id"=>$infoTable->id));
        
        if(!empty($infoChildRecord))
        {
            $info = new ExportInfo();
            $info->loadDbData($infoChildRecord->id);
            $this->info = $info;
            
//            $refCompRecord = $DB->get_record("msm_compositor", array("parent_id"=>$info->compid));
//            
//            if(!empty($refCompRecord))
//            {
//                $refTable = $DB->get_record("msm_table_collection", array("id"=>$refCompRecord->table_id));
//                
//                switch($refTable->tablename)
//                {
//                    case "msm_def":
//                        break;
//                    case "msm_theorem":
//                        break;
//                    case "msm_comment":
//                        break;
//                    case "msm_unit":
//                        break;
//                }
//            }
        }
        
        return $this;
    }
}

?>
