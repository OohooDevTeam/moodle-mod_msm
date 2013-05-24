<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                       **
 * @subpackage  msm                                                       **
 * @name        msm                                                       **
 * @copyright   University of Alberta                                     **
 * @link        http://ualberta.ca                                        **
 * @author      Ga Young Kim                                              **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************* */
/**
 * This script is called by an AJAX call in saveSettings.js which is called when user saves 
 * any changes made to the MSM settngs menu.  This script's main function is to update the
 * msm_unit_name table with new type of composition and/or new names of the subunits.
 * 
 */
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');

global $DB;

$userInputArray = array(); // changed values by user
// following two variables are used to detect null/empty content error
$hasError = false;
$errorArray = array();

if (isset($_POST['msm_structure_input_alone']))
{
    $aloneCompName = $_POST['msm_structure_input_alone'];
    array_push($userInputArray, $aloneCompName);
}

if (isset($_POST['msm_structure_input_top']))
{
    $topCompName = $_POST['msm_structure_input_top'];
    array_push($userInputArray, $topCompName);
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
// if there is a null/empty content error, return the id of the input field with empty content 
// which is passed to a part of a code where it highlights the input field with missing content
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
            // for the case where user deleted initially set sub unit (ie. there are less nesting units available than before setting update)
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
                $newRecord->depth = $index-1;

                $id = $DB->insert_record('msm_unit_name', $newRecord);
            }
        }
    }

    foreach ($copiedArray as $key => $dbValue)
    {
        if ($key > sizeof($userInputArray) - 1)
        {
            $DB->delete_records('msm_unit_name', array('id' => $dbValue->id));
        }
    }

    $newString = '';

    for ($i = 0; $i < sizeof($userInputArray) - 1; $i++)
    {
        $newString .= $userInputArray[$i] . ",";
    }
    $newString .= $userInputArray[sizeof($userInputArray) - 1];


    // new value to be put into the hidden input field with all unit names
    echo json_encode($newString);
}
?>
