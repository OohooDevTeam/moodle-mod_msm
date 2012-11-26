<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EditorElement
 *
 * @author User
 */
abstract class EditorElement
{
    abstract function getFormData($idNumber, $position);
    
    abstract function insertData($parentid, $siblingid, $msmid);
}

?>
