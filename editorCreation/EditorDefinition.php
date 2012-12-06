<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorDefinition
 *
 * @author User
 */
class EditorDefinition extends EditorElement
{

    public $id;
    public $compid;
    public $type;
    public $title;
    public $content;
    public $associateType;
    public $tablename;
    public $position;
    public $description;

    public function __construct()
    {
        $this->tablename = 'msm_def';
    }

    function getFormData($idNumber, $position)
    {
        $this->type = $_POST['msm_def_type_dropdown-' . $idNumber];
        $this->associateType = $_POST['msm_def_associate_dropdown-' . $idNumber];
        $this->description = $_POST['msm_def_descripton_input-' . $idNumber];
        $this->title = $_POST['msm_def_title_input-' . $idNumber];
        $this->position = $position;

        $this->errorArray = array();

        if ($_POST['msm_def_content_input-' . $idNumber] != '')
        {
            $this->content = $_POST['msm_def_content_input-' . $idNumber];
        }
        else
        {
            $this->errorArray[] = 'msm_def_content_input-' . $idNumber . '_ifr';
        }

        return $this;
    }

    function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $data->def_type = $this->type;
        $data->caption = $this->title;
        $data->def_content = $this->content;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();

        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);
    }

}

?>
