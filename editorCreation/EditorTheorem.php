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
class EditorTheorem
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

}

?>
