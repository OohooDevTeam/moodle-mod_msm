<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');
require_once('Compositor.php');

global $DB, $PAGE, $CFG;

$comp = new Compositor();

$parentid = $_POST["parentid"];

$siblingid = $_POST["siblingid"];

//$unittableid = $Db->get_record('msm_table_collection', array('tablename'=>'msm_unit'));
//
//$childElements = $DB->get_records('msm_compositor', array('parent_id'=>$parentid, 'table_id'=>$unittableid),'prev_sibling_id');
//
//if(!empty($childElements))
//{
//    $numberofChild = $childElements->size();
//}



$content = $comp->loadAndDisplay($parentid, $prevSiblingid);
echo $content;


//$PAGE->set_url('/mod/msm/view.php', array('id' => $cm->id,'parentid' => $rootcomp->parent_id, 'siblingid' => $rootcomp->prev_sibling_id))

// $currentURL = $PAGE->url;

//$content = $comp->loadAndDisplay($_GET['parentid'],$_GET['siblingid'],$_GET['instanceid']);

//echo $content;
?>
