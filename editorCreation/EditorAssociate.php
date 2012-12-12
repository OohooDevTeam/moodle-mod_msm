<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorAssociate
 *
 * @author User
 */
class EditorAssociate extends EditorElement
{
    public $id;
    public $compid;
    public $errorArray = array();
    public $type; // in db--> description of the associate
    public $infos = array();
    
    function __construct()
    {
        $this->tablename = 'msm_associate';
    }

    // idNumber = parent_number-currentelement_number
    public function getFormData($idNumber, $position)
    {
        $this->type = $_POST['msm_associate_dropdown-' . $idNumber];
        
        $parentid = explode("-", $idNumber);
        
        $infomatch = "/^msm_info_content-$parentid[0]-.*/";
        
        $i = 0;
        
//        echo "associate";
//        print_object($_POST);
        
        foreach($_POST as $id=>$value)
        {
            if(preg_match($infomatch, $id))
            {
                print_object($id);
                $idInfo = explode("-", $id);
                $indexNumber = $idInfo[1] . "-" . $idInfo[2];
                $info = new EditorInfo();
                $info->getFormData($indexNumber, $i);
                $this->infos[] = $info;
                
                $i++;
            }
        }
        
//        if($_POST['msm_associate_reftype-' . $idNumber] != "None")
//        {
//            print_object($_POST);
//        }
        
        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;
        
        $data = new stdClass();
        $data->description = $this->type;
        
        $this->id = $DB->insert_record($this->tablename, $data);
        
        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename'=>$this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;
        
        $this->compid = $DB->insert_record('msm_compositor', $compData);
        
        $sibling_id = 0;
        foreach($this->infos as $info)
        {
            $info->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $info->compid;
        }
    }
}

?>
