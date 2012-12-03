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
require_once('EditorComment.php');
require_once('EditorImage.php');
require_once('EditorUnit.php');
require_once('EditorIntro.php');
require_once('EditorBlock.php');
require_once('EditorPara.php');
require_once('EditorInContent.php');

require_once('../XMLImporter/TableCollection.php');

global $DB;

$hasError = false;
$errorArray = array();
//$copiedValue = array();
//if (isset($_POST['msm_setting_save']))
//{
//    if (isset($_POST['msm_structure_input_top']))
//    {
//        $topCompName = $_POST['msm_structure_input_top'];
//        $copiedValue[] = "msm_structure_input_top|" . $topCompName;
//    }
//    else
//    {
//        $hasError = true;
//        $errorArray = 'msm_structure_input_top';
//    }
//
//    $match = '/^msm_structure_input_child-.*/';
//
//    foreach ($_POST as $id => $inputs)
//    {
//        if (preg_match($match, $id))
//        {
//            if (!empty($inputs))
//            {
//                $copiedValue[] = $id . "|" . $inputs;
//            }
//            else
//            {
//                $hasError = true;
//                $errorArray = $id;
//            }
//        }
//    }
//
//    if ($hasError)
//    {
//        echo json_encode($errorArray);
//    }
//    else
//    {
//        $newInputValue = '';
//        for ($i = 0; $i < sizeof($copiedValue) - 1; $i++)
//        {
//            $newInputValue .= $copiedValue[$i] . ",";
//        }
//        $newInputValue .= $copiedValue[sizeof($copiedValue) - 1];
//    }
//}
if (isset($_POST['msm_editor_save']))
{
    $childOrder = $_POST['msm_child_order'];

    $arrayOfChild = explode(",", $childOrder);

    $lengthOfArray = sizeOf($arrayOfChild);

    $msmId = $arrayOfChild[$lengthOfArray - 1];

    $unitcontent = array();
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

            case "copied_msm_comment":
                $comment = new EditorComment();
                $comment->getFormData($childIdInfo[1], $i);
                $unitcontent[] = $comment;
                break;

            case "copied_msm_intro":
                $intro = new EditorIntro();
                $intro->getFormData($childIdInfo[1], $i);
                $unitcontent[] = $intro;
                break;

            case "copied_msm_body":
                $body = new EditorBlock();
                $body->getFormData($arrayOfChild[$i], $i);
                $unitcontent[] = $body;
                break;
            default:
                echo $childIdInfo[0];
                break;
        }
    }


// unit cannot get an error since both title and description can be null
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
}
?>
