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

    public function getFormData($idNumber, $position)
    {
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        
    }
    
    
}

?>
