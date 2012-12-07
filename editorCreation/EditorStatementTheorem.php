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

    function __construct()
    {
        $this->tablename = 'msm_statement_theorem';
    }

    public function getFormData($idNumber, $position)
    {
        
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
        $compData->table_id = $DB->get_record('msm_table_collection', array('tablename' => 'msm_statement_theorem'))->id;
        $compData->parent_id = $this->compid;
        $compData->prev_sibling_id = $siblingid;

        $this->compid = $DB->insert_record('msm_compositor', $compData);
    }

}

?>
