<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ExportElement
 *
 * @author User
 */
abstract class ExportElement
{
   abstract function loadDbData($compid);
   
   abstract function exportData();
}

?>
