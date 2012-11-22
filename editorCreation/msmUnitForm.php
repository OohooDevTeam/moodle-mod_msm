<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('../../../config.php');
require_once($CFG->dirroot . '/mod/msm/lib.php');

$childIds = $_POST['msm_child_order'];

$eachId = explode(",", $childIds);

//iterating through each child basically
foreach($eachId as $id)
{
    $idInfo = explode("-", $id);
    $indexNumber = $idInfo[1];
    echo $indexNumber;
}

?>
