<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('../../../config.php');
require_once($CFG->dirroot . '/mod/msm/lib.php');
require_once('EditorElement.php');
require_once('EditorDefinition.php');
require_once('EditorTheorem.php');
require_once('EditorImage.php');
require_once('EditorUnit.php');
require_once('../XMLImporter/TableCollection.php');

global $DB;

$childOrder = $_POST['msm_child_order'];

$arrayOfChild = explode(",", $childOrder);

$lengthOfArray = sizeOf($arrayOfChild);

$msmId = $arrayOfChild[$lengthOfArray - 1];

$unitcontent = array();

$hasError = false;
$errorArray = array();

$DB->delete_records('msm_table_collection');
$tableCollection = new TableCollection();
$tableCollection->insertTablename();

$unit = new EditorUnit();
$unit->getFormData('', '');

for ($i = 0; $i < $lengthOfArray - 1; $i++)
{
    $childIdInfo = explode("-", $arrayOfChild[$i]);

    switch ($childIdInfo[0])
    {
        case "copied_msm_def":
            $definition = new EditorDefinition();
            $definition->getFormData($childIdInfo[1], $i);
            $unitcontent[] = $definition;
            break;

        case "copied_msm_theorem":
            $theorem = new EditorTheorem();
            $theorem->getFormData($childIdInfo[1], $i);
            $unitcontent[] = $theorem;
            break;

        case "copied_msm_intro":
            echo "intro";
            break;
        case "copied_msm_content":
            echo "content";
            break;
        case "copied_msm_pic":
            echo "pic";
            break;
        default:
            echo $childIdInfo[0];
            break;
    }
}

foreach ($unitcontent as $unitchild)
{
    if (!empty($unitchild->errorArray))
    {
        $hasError = true;
        foreach ($unitchild->errorArray as $errorid)
        {
            $errorArray[] = $errorid;
        }
    }
}

if ($hasError)
{
    echo json_encode($errorArray);
}
else
{
    $unit->insertData(0, 0, $msmId);

    // need code fo insert unit information to unitdatabase before procesing the child so that
    // the parentid exists when the child elements are being inserted to the db

    $siblingCompid = 0;
    foreach ($unitcontent as $element)
    {
        $element->insertData($unit->compid, $siblingCompid, $msmId);
        $siblingCompid = $element->compid;
    }

    echo json_encode("true");
}
?>
