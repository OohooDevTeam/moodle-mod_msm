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
        
    }

}

?>
