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
   
   function makeExportFile($DomDocument)
   {
       // function to create XML files and save into moodle file system
   }
   
   //need a function to look for subordinate and math
   // need a function to convert some HTML stuff to XML
}

?>
