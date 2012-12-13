<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorInfo
 *
 * @author User
 */
class EditorInfo extends EditorElement
{
    public $id;
    public $compid;
    public $caption;
    public $content;   
    public $errorArray = array();
    
    function __construct()
    {
        $this->tablename = 'msm_info';
    }

    // idNumber --> parentid-currentelementid
    public function getFormData($idNumber, $position)
    {
        $this->caption = $_POST['msm_info_title-' . $idNumber];
        
        if($_POST['msm_info_content-' . $idNumber] != '')
        {
            $this->content = $_POST['msm_info_content-' . $idNumber];
        }
        else
        {
            $this->errorArray[] = 'msm_info_content-' . $idNumber . '_ifr';
        }
        
        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;
        
        $data = new stdClass();
        $data->caption = $this->caption;
        $data->info_content = $this->content;
        
        $this->id = $DB->insert_record($this->tablename, $data);
        
        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename'=>$this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;
        
        $this->compid = $DB->insert_record('msm_compositor', $compData);
    }
}

?>
