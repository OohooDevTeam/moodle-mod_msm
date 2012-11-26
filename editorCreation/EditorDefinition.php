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
class EditorDefinition
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
}

?>
