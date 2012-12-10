<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorStatementTheorem
 *
 * @author User
 */
class EditorStatementTheorem extends EditorElement
{

    public $position;
    public $id;
    public $compid;
    public $errorArray = array();
    public $content;
    public $children = array();

    function __construct()
    {
        $this->tablename = 'msm_statement_theorem';
    }

    public function getFormData($idNumber, $position)
    {
        $this->position = $position;
        if ($_POST['msm_theorem_content_input-' . $idNumber] != '')
        {
            $this->content = $_POST['msm_theorem_content_input-' . $idNumber];
        }
        else
        {
            $this->errorArray[] = 'msm_theorem_content_input-' . $idNumber;
        }

//        $partmatch1 = '/^msm_theorem_part_title_' . $idNumber . '-.*/';
        $partmatch = "/^msm_theorem_part_content-$idNumber-.*/";

        $i = 0;

        foreach ($_POST as $id => $content)
        {
            if (preg_match($partmatch, $id))
            {
//                $indexNumber = explode("-", $id);
                $partTheorem = new EditorPartTheorem();
                $partTheorem->getFormData($id, $i);
                $this->children[] = $partTheorem;
                $i++;
            }
        }

        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;

        $data = new stdClass();
        $data->statement_content = $this->content;

        $this->id = $DB->insert_record($this->tablename, $data);

        $compData = new stdClass();
        $compData->msm_id = $msmid;
        $compData->unit_id = $this->id;
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => $this->tablename))->id;
        $compData->parent_id = $parentid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);

        $sibling_id = 0;

        foreach ($this->children as $partTheorem)
        {
            $partTheorem->insertData($this->compid, $sibling_id, $msmid);
            $sibling_id = $partTheorem->compid;
        }
    }

}

?>
