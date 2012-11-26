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
class EditorUnit
{

    public $id;
    public $compid;
    public $title;
    public $description;
    public $position;

    public function __construct()
    {
        $this->tablename = 'msm_unit';
    }

}

?>
