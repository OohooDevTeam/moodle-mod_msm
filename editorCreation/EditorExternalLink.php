<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorExternalLink
 *
 * @author User
 */
class EditorExternalLink extends EditorElement
{

    public $id;
    public $compid;
    public $href;
    public $type;
    public $target;
    public $info;

    public function __construct()
    {
        $this->tablename = "msm_external_link";
    }

    public function getFormData($idNumber)
    {
        $this->type = null;
        $this->target = "_blank";
        $allSubordinateValues = $_POST['msm_unit_subordinate_container'];
//
        $tempallSubordinates = explode("//|", $allSubordinateValues);

        // copying the array from string processing above (due to it ending in comma, the last
        // element is empty)
        $allSubordinates = array();
         for ($i = 0; $i < sizeof($tempallSubordinates); $i++)
        {
            $allSubordinates[] = $tempallSubordinates[$i];
        }

        foreach ($allSubordinates as $index => $subordinate)
        {
            $idValuePair = explode("||", $subordinate);

            if (strpos($idValuePair[0], $idNumber) !== false)
            {
                if (strpos($idValuePair[0], 'url') !== false)
                {
                    if ($idValuePair[0] == 'msm_subordinate_url-' . $idNumber)
                        {
                            // converting &gt;..etc back to html characters
                            $this->href = $idValuePair[1];
                        }
                }
            }
        }
        
        $info = new EditorInfo();
        $info->getFormData($idNumber . "|sub");
        $this->info = $info;
        
        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;
        
        $data = new stdClass();
        $data->type = $this->type;
        $data->href = $this->href;
        $data->target = $this->target;
        
        $this->id = $DB->insert_record($this->tablename, $data);
        
        $compdata = new stdClass();
        $compdata->unit_id = $this->id;
        $compdata->msm_id = $msmid;
        $compdata->table_id = $DB->get_record("msm_table_collection", array("tablename"=>$this->tablename))->id;
        $compdata->parent_id = $parentid;
        $compdata->prev_sibling_id = $siblingid;
        
        $this->compid = $DB->insert_record("msm_compositor", $compdata);
        
        $sibling_id = 0;
        if(!empty($this->info))
        {
            $this->info->insertData($this->compid, $sibling_id, $msmid);
        }
    }

    public function loadData($compid)
    {
        global $DB;
       
        $linkCompRecord = $DB->get_record("msm_compositor", array("id"=>$compid));
        
        $this->compid = $compid;
        $this->id = $linkCompRecord->unit_id;
        
        $linkRecord = $DB->get_record($this->tablename, array("id"=>$this->id));
        
        $this->href = $linkRecord->href;
        $this->type = $linkRecord->type;
        $this->target = $linkRecord->target;
        
        $childRecord = $DB->get_record("msm_compositor", array("parent_id"=>$this->compid));
        
        $childTable = $DB->get_record("msm_table_collection", array("id"=>$childRecord->table_id));
        
        if($childTable->tablename == "msm_info")
        {
            $info = new EditorInfo();
            $info->loadData($childRecord->id);
            $this->info = $info;
        }
    }

}

?>
