<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorTheorem
 *
 * @author User
 */
class EditorTheorem extends EditorElement
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
        $this->tablename = 'msm_theorem';
    }

    public function getFormData($idNumber, $position)
    {
        $this->type = $_POST['msm_theorem_type_dropdown-' . $idNumber];
        $this->description = $_POST['msm_theorem_descripton_input-' . $idNumber];
        $this->associateType = $_POST['msm_theorem_associate_dropdown-' . $idNumber];
        $this->title = $_POST['msm_theorem_title_input-' . $idNumber];
        $this->position = $position;

        $this->errorArray = array();

        if ($_POST['msm_theorem_content_input-' . $idNumber] != '')
        {
            $this->content = $_POST['msm_theorem_content_input-' . $idNumber];
        }
        else
        {
            $this->errorArray[] = 'msm_theorem_content_input-' . $idNumber . '_ifr';
        }

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $compData = new stdClass();

        $data->theorem_type = $this->type;
        $data->caption = $this->title;
        $data->description = $this->description;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record("msm_table_collection", array("tablename" => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record("msm_compositor", $compData);

        $sibling_id = 0;
        if (!empty($this->content))
        {
            $statementData = new stdClass();
            $statementData->statement_content = $this->content;

            $statementTheorem = new stdClass();
            $statementTheorem->id = $DB->insert_record('msm_statement_theorem', $statementData);

            $statementCompData = new stdClass();
            $statementCompData->msm_id = $msmid;
            $statementCompData->unit_id = $statementTheorem->id;
            $statementCompData->table_id = $DB->get_record('msm_table_collection', array('tablename' => 'msm_statement_theorem'))->id;
            $statementCompData->parent_id = $this->compid;
            $statementCompData->prev_sibling_id = $sibling_id;

            $statementTheorem->compid = $DB->insert_record('msm_compositor', $statementCompData);

            $sibling_id = $statementTheorem->compid;
        }
    }

}

?>
