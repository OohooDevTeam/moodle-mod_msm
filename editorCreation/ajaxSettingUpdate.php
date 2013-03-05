<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');

global $DB;

$userInputArray = array();
$hasError = false;
$errorArray = array();

if (isset($_POST['msm_structure_input_top']))
{
    $topCompName = $_POST['msm_structure_input_top'];
    $userInputArray[] = $topCompName;
}
else
{
    $hasError = true;
    $errorArray[] = 'msm_structure_input_top';
}

$match = '/^msm_structure_input_child-.*/';

foreach ($_POST as $id => $inputs)
{
    if (preg_match($match, $id))
    {
        if (!empty($inputs))
        {
            $userInputArray[] = $inputs;
        }
        else
        {
            $hasError = true;
            $errorArray[] = $id;
        }
    }
}

if ($hasError)
{
    echo json_encode($errorArray);
}
else
{
    $existingValues = $DB->get_records('msm_unit_name', array('msmid' => $_POST['msm_instance_id']), 'depth');

    $copiedArray = array();

    // to make the indices of the array to be incremental starting at zero instead of reflecting the db id number
    foreach ($existingValues as $dataRecord)
    {
        $copiedArray[] = $dataRecord;
    }


    foreach ($userInputArray as $index => $userInput)
    {
        if (!empty($userInput))
        {
            if ($index < sizeof($copiedArray))
            {
                if ($copiedArray[$index]->unitname != $userInput)
                {
                    $newRecord = new stdClass();
                    $newRecord->id = $copiedArray[$index]->id;
                    $newRecord->unitname = $userInput;
                    $newRecord->msmid = $_POST['msm_instance_id'];
                    $newRecord->depth = $copiedArray[$index]->depth;

                    $DB->update_record('msm_unit_name', $newRecord);
                }
            } // case where userInput has more unit names than current db table records (ie. user added another child name)
            else if ($index >= sizeof($copiedArray))
            {
                // insert the newly added unit name
                $newRecord = new stdClass();
                $newRecord->unitname = $userInput;
                $newRecord->msmid = $_POST['msm_instance_id'];
                $newRecord->depth = $index;

                $id = $DB->insert_record('msm_unit_name', $newRecord);
            }
        }
    }

    foreach ($copiedArray as $key => $dbValue)
    {
        if ($key > sizeof($userInputArray)-1)
        {
            $DB->delete_records('msm_unit_name', array('id' => $dbValue->id));
        }
    }
    
    $newString = '';    
    
    for ($i = 0; $i < sizeof($userInputArray)-1; $i++)
    {
        $newString .= $userInputArray[$i] . ",";
    }
    $newString .= $userInputArray[sizeof($userInputArray)-1];
    

    echo json_encode($newString);
}
?>
