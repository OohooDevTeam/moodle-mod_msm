<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorPartTheorem
 *
 * @author User
 */
class EditorPartTheorem extends EditorElement
{
    public $position;
    public $id;
    public $compid;
    public $content;
    public $caption;
    public $errorArray = array();
    
    
    function __construct()
    {
        $this->tablename = 'msm_part_theorem';
    }

    // idNumber is actually a id name from HTML form
    public function getFormData($idNumber, $position)
    {
        $this->position = $position;    
        
        $idInfo = explode("-", $idNumber);
       
        $this->caption = $_POST['msm_theorem_part_title-' . $idInfo[1] . '-' . $idInfo[2]];
        
        if($_POST['msm_theorem_part_content-' . $idInfo[1] . '-' . $idInfo[2]] != '')
        {
            $this->content = $_POST['msm_theorem_part_content-' . $idInfo[1] . '-' . $idInfo[2]];
        }
        else
        {
            $this->errorArray[] = 'msm_theorem_part_content-' . $idInfo[1] . '-' . $idInfo[2] . '_ifr';
        }
        
        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;
        
        $data = new stdClass();
        $data->partid = null;
        $data->counter = null;
        $data->equivalence_mark = null;
        $data->caption = $this->caption;
        $data->part_content = $this->content;
        
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
