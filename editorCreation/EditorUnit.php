<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorUnit
 *
 * @author User
 */
class EditorUnit extends EditorElement
{

    public $id;
    public $compid;
    public $title;
    public $description;
    public $position;
    public $unitName;

    public function __construct()
    {
        $this->tablename = 'msm_unit';
    }

    // idNumber contains the msm id number
    public function getFormData($idNumber, $position)
    {
        global $DB;
        $this->position = $position;
        $this->errorArray = array();

        $this->title = $_POST['msm_unit_title'];
        $this->description = $_POST['msm_unit_description_input'];
       
        $this->unitName = $DB->get_record('msm_unit_name', array('msmid' => $idNumber, 'depth' => 0))->id;

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;        

        $data = new stdClass();
        $data->title = $this->title;
        $data->description = $this->description;
        // when saving the unit for the first time, no structure is given(ie no subunit is specified until user structures the unit in hierarchy column)
        // so the default value is the name of the top unit specified by the user
        $data->compchildtype = $this->unitName;
        
        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $this->compid = $DB->insert_record('msm_compositor', $compData);
    }

}

?>
