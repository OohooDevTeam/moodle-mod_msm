<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorImage
 *
 * @author User
 */
class EditorImage extends EditorElement
{
    public $position;
    public $id;
    public $compid;
    public $src;
    public $description;
    public $caption;
    public $height;
    public $width;
    
    function __construct()
    {
        $this->tablename = 'msm_img';
    }

    public function getFormData($idNumber, $position)
    {
        $this->position = $position;
        
        return $this;
    }

    public function insertData($parentid, $siblingid, $msmid)
    {
        global $DB;
        
        $data = new stdClass();
        
        $compData = new stdClass();
    }

    public function displayData()
    {
        
    }

    public function loadData($compid)
    {
        
    }
    
    
}

?>
