<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('../../../config.php');
require_once($CFG->dirroot . '/mod/msm/lib.php');

$childOrder = $_POST['msm_child_order'];

$arrayOfChild = explode(",", $childOrder);

$lengthOfArray = sizeOf($arrayOfChild);

$msmId = $arrayOfChild[$lengthOfArray-1];

for($i=0; $i < $lengthOfArray-1; $i++)
{
    $childIdInfo = explode("-", $arrayOfChild[$i]);
    
    switch($childIdInfo[0])
    {
        case "copied_msm_def":
            echo "def";
            break;
        case "copied_msm_theorem":
            echo "theorem";
            break;
        default:
            echo $childInfo[0];
            break;
    }
}

?>
