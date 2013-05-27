<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');
require_once("ExportElement.php");
require_once("ExportUnit.php");
require_once("ExportDefinition.php");
require_once("ExportComment.php");
require_once("ExportTheorem.php");
require_once("ExportIntro.php");
require_once("ExportBlock.php");
require_once("ExportExtraInfo.php");
require_once("ExportAssociate.php");
require_once("ExportStatementTheorem.php");
require_once("ExportPara.php");
require_once("ExportInContent.php");
require_once("ExportTable.php");
require_once("ExportPartTheorem.php");
require_once("ExportInfo.php");
require_once("ExportSubordinate.php");
require_once("ExportMedia.php");
require_once("ExportImage.php");

global $DB;

$msmid = $_POST["msm_id"];

$unittable = $DB->get_record("msm_table_collection", array("tablename"=>"msm_unit"));

$topUnits = $DB->get_records("msm_compositor", array("msm_id"=>$msmid, "table_id"=>$unittable->id, "parent_id"=>0, "prev_sibling_id"=>0));

$unitObjects = array();

foreach($topUnits as $topUnit)
{
    $unit = new ExportUnit();
    $unit->loadDbData($topUnit->id);
    $unitObjects[] = $unit;
}

print_object($unitObjects);
die;

foreach($unitObjects as $unitObj)
{
   $topunitElement = $unitObj->exportData();
   createExportFiles($topunitElement);
   
}

function createExportFiles($DomElement)
{
    
}

?>