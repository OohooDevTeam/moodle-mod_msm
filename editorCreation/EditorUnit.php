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

    public function getFormData($idNumber, $position)
    {
        global $DB;
        $this->position = $position;
        $this->errorArray = array();

        $this->title = $_POST['msm_unit_title'];
        $this->description = $_POST['msm_unit_descripton_input'];

        $this->unitName = $DB->get_record('msm_unit_name', array('msmid'=>$msmid, 'depth'=>0))->id;
//        $unitName = $_POST['msm_unit_name_input'];
//        $eachUnit = explode(",", $unitName);
//        $currentType = '';
//        foreach ($eachUnit as $key => $idNameCombo)
//        {
//            $unitIdNamePair = explode("|", $idNameCombo);
//
//            if ($key == 0)
//            {
//                $currentType = $unitIdNamePair[1];
//                $this->unitNames[] = $unitIdNamePair[1];
//            }
//            else if ($key >= 2)
//            {
//                $this->unitNames[] = $unitIdNamePair[1];
//            }
//        }
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
        
//        print_object($this->unitNames[0]);
//        $data->compchildtype = $this->unitNames[0];
        
        $data->compchildtype =  $this->unitName;       
       
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
