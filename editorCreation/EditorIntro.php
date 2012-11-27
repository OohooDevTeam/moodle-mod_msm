<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorIntro
 *
 * @author User
 */
class EditorIntro extends EditorElement
{
    public $id;
    public $compid;
    public $content;
    public $position;
    public $title;
    
    function __construct()
    {
        $this->tablename = "msm_intro";
    }

    public function getFormData($idNumber, $position)
    {
        
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        $data = new stdClass();
        $data->
        
        $compData = new stdClass();
    }
    
}

?>
