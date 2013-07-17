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
 * This script is called by an AJAX call in autorNav.js, saveMethod.js and editorUtility.js and its main role is to
 * remove the unit specified by the user and to insert data in the MSM editor to the correct database tables.
 * This script calls most of the classes in EditorCreation folder to get data from POST object of ajax call, insert
 * data into database, load from database for display and then finally for the display of the data.  This script is
 * also essential for the preview feature of the MSM as it triggeres displayPreview function in each of the classes.
 * 
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
require_once('EditorPartTheorem.php');
require_once('EditorStatementTheorem.php');
require_once('EditorAssociate.php');
require_once('EditorInfo.php');
require_once('EditorTable.php');
require_once('EditorSubordinate.php');
require_once('EditorExtraInfo.php');
require_once('EditorMedia.php');
require_once('EditorExternalLink.php');

require_once('../XMLImporter/TableCollection.php');

global $DB;

// to remove unit when triggered by Remove this Unit button
if (!empty($_POST["removeUnit"]))
{
    $idInfo = explode("-", $_POST['removeUnit']);

    $compid = $idInfo[0];

    $currentUnitRecord = $DB->get_record("msm_compositor", array("id" => $compid));

    $msmId = $currentUnitRecord->msm_id;

    $oldUnitChildRecords = $DB->get_records("msm_compositor", array("parent_id" => $compid));

    foreach ($oldUnitChildRecords as $oldchild)
    {
        $unittableid = $DB->get_record("msm_table_collection", array("tablename" => "msm_unit"))->id;

        if ($oldchild->table_id != $unittableid)
        {
            deleteOldChildRecord($oldchild->id);
        }
    }

    $DB->delete_records("msm_unit", array("id" => $idInfo[1]));
    $DB->delete_records("msm_compositor", array("id" => $compid));

    $unitNameArray = $DB->get_records("msm_unit_name", array("msmid" => $msmId), "depth");
    $topUnitName = $DB->get_record("msm_unit_name", array("msmid" => $msmId, "depth" => 0));

    $unitNameString = '';
    foreach ($unitNameArray as $unitName)
    {
        $unitNameString .= $unitName->unitname . ",";
    }
    $unitNameString .= $msmId;

    $emptyUnitContent = '';

    $emptyUnitContent .= "<div id='msm_unit_info_div'>";
    $emptyUnitContent .= "<label id='msm_unit_title_label' class='msm_unit_title_labels' for='msm_unit_title'>$topUnitName->unitname title: </label>";
    $emptyUnitContent .= "<input id='msm_unit_title' class='msm_title_input' placeholder = 'Please enter the title of this $topUnitName->unitname.' name='msm_unit_title'/>";

    $emptyUnitContent .= "<label class='msm_unit_short_title_labels' for='msm_unit_short_title'> XML hierarchy Name: </label>";
    $emptyUnitContent .= "<input class='msm_unit_short_titles' id='msm_unit_short_title' placeholder='Please enter short title for this $topUnitName->unitname' name='msm_unit_short_title' readonly='true'/>";

    $emptyUnitContent .= "<label id='msm_unit_description_label' class='msm_unit_description_labels' for='msm_unit_description_input'>Description: </label>";
    $emptyUnitContent .= "<input id='msm_unit_description_input' class='msm_unit_description_inputs' placeholder = 'Insert description to search this element in future.' name='msm_unit_description_input' readonly='true'/>";
    $emptyUnitContent .= "</div>";

    $emptyUnitContent .= "<div id='msm_editor_middle_droparea'>";
    $emptyUnitContent .= "<div id='msm_child_appending_area'>";

    $emptyUnitContent .= "</div>";
    $emptyUnitContent .= "<input id='msm_child_order' style='visibility:hidden;' name='msm_child_order'/>";

    $emptyUnitContent .= "</div>";
    $emptyUnitContent .= "<input id='msm_unit_name_input' value='$unitNameString' style='visibility:hidden;' name='msm_unit_name_input'/>";

    echo json_encode($emptyUnitContent);

    return;
}

$childOrder = $_POST['msm_child_order'];

$arrayOfChild = explode(",", $childOrder);

$lengthOfArray = sizeOf($arrayOfChild);

$msmId = $arrayOfChild[$lengthOfArray - 1];

$msm = $DB->get_record('msm', array('id' => $msmId), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

$unitcontent = array(); // all the children of the unit such as def/comment/theorem...etc
// these two variables are used to check for null/empty content errors
$hasError = false;
$errorArray = array();

$DB->delete_records('msm_table_collection');
$tableCollection = new TableCollection();
$tableCollection->insertTablename();
$unit = new EditorUnit();
$unit->getFormData($msmId);

for ($i = 0; $i < $lengthOfArray - 1; $i++)
{
    $childIdInfo = explode("-", $arrayOfChild[$i]);

    switch ($childIdInfo[0])
    {
        case "copied_msm_def":
            $definition = new EditorDefinition();
            $definition->getFormData($childIdInfo[1]);
            $unitcontent[] = $definition;
            break;

        case "copied_msm_theorem":
            $theorem = new EditorTheorem();
            $theorem->getFormData($childIdInfo[1]);
            $unitcontent[] = $theorem;
            break;

        case "copied_msm_comment":
            $comment = new EditorComment();
            $comment->getFormData($childIdInfo[1]);
            $unitcontent[] = $comment;
            break;

        case "copied_msm_extra_info":
            $extraInfo = new EditorExtraInfo();
            $extraInfo->getFormData($childIdInfo[1]);
            $unitcontent[] = $extraInfo;
            break;

        case "copied_msm_intro":
            $intro = new EditorIntro();
            $intro->getFormData($childIdInfo[1]);
            $unitcontent[] = $intro;
            break;

        case "copied_msm_body":
            $body = new EditorBlock();
            $body->getFormData($arrayOfChild[$i]);
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
    // intro does not have errorArray property but its content blocks do
    if ((get_class($unitchild) == "EditorIntro") || (get_class($unitchild) == "EditorExtraInfo"))
    {
        foreach ($unitchild->blocks as $introContent)
        {
            if (!empty($introContent->errorArray))
            {
                $hasError = true;
                foreach ($introContent->errorArray as $introerrorid)
                {
                    $errorArray[] = $introerrorid;
                }
            }
        }
    }

    if (get_class($unitchild) == "EditorBlock")
    {
        if (!empty($unitchild->errorArray))
        {
            $hasError = true;
            foreach ($unitchild->errorArray as $blockerrorid)
            {
                $errorArray[] = $blockerrorid;
            }
        }
    }

    if (get_class($unitchild) == 'EditorTheorem')
    {
        foreach ($unitchild->contents as $statementTheorem)
        {
            if (!empty($statementTheorem->errorArray))
            {
                $hasError = true;
                foreach ($statementTheorem->errorArray as $statementerrorid)
                {
                    $errorArray[] = $statementerrorid;
                }
            }
            foreach ($statementTheorem->children as $partTheorem)
            {
                if (!empty($partTheorem->errorArray))
                {
                    $hasError = true;
                    foreach ($partTheorem->errorArray as $partErrorid)
                    {
                        $errorArray[] = $partErrorid;
                    }
                }
            }
        }
    }
    if ((get_class($unitchild) != 'EditorIntro') && (get_class($unitchild) != 'EditorBlock') && (get_class($unitchild) != 'EditorExtraInfo'))
    {
        foreach ($unitchild->children as $associate)
        {
            foreach ($associate->infos as $info)
            {
                if (!empty($info->errorArray))
                {
                    $hasError = true;
                    foreach ($info->errorArray as $infoError)
                    {
                        $errorArray[] = $infoError;
                    }
                }
            }
        }
    }
    if (!empty($unitchild->errorArray))
    {
        $hasError = true;
        foreach ($unitchild->errorArray as $errorid)
        {
            $errorArray[] = $errorid;
        }
    }
}

// there was an empty content detected 
if ($hasError)
{
    // return array with all id of input fields/text fields that are missing content
    echo json_encode($errorArray);
}
else
{
    if (empty($_POST["msm_mode_info"]))
    {
        // when the new unit is triggered to save, if there are existing child elements
        // with this unit, then delete these children and insert new records of child elements (?notsure)
        if (!empty($_POST['msm_currentUnit_id']))
        {
            $idInfo = explode("-", $_POST['msm_currentUnit_id']);

            $compid = $idInfo[0];

            $unit->updateDbRecord($compid);

            $currentUnitRecord = $DB->get_record("msm_compositor", array("id" => $compid));

            $oldUnitChildRecords = $DB->get_records("msm_compositor", array("parent_id" => $compid, "msm_id" => $msmId));

            foreach ($oldUnitChildRecords as $oldchild)
            {
                $unittableid = $DB->get_record("msm_table_collection", array("tablename" => "msm_unit"))->id;

                // first condition: to prevent from deleting the unit when it is editted
                // second condition: to prevent from deleting reference materials from other compoitions from being deleted
                if ($oldchild->table_id != $unittableid)
                {
                    deleteOldChildRecord($oldchild->id, $msmId);
                }
                else
                {
                    // update the parent id of the child so that it corresponds to parent id of the current unit element
                    $updateData = new stdClass();
                    $updateData->id = $oldchild->id;
                    $updateData->msm_id = $oldchild->msm_id;
                    $updateData->table_id = $oldchild->table_id;
                    $updateData->unit_id = $oldchild->unit_id;
//                    $updateData->parent_id = $currentUnitRecord->parent_id;
//                    $updateData->prev_sibling_id = $oldchild->prev_sibling_id;  

                    $updateData->parent_id = 0;
                    $updateData->prev_sibling_id = 0;

                    $DB->update_record("msm_compositor", $updateData);
                }
            }
        }
        else
        {
            $unit->insertData(0, 0, $msmId);
        }
        // need code fo insert unit information to unitdatabase before procesing the child so that
        // the parentid exists when the child elements are being inserted to the db

        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, "mod_msm", "editor", $msm->id);
        $fileExists = false;
        foreach ($files as $file)
        {
            $fileExt = explode(".", $file->get_filename());

            if (sizeof($fileExt) > 1)
            {
                if ($fileExt != "zip")
                {
                    $fileExists = true;
                }
            }
        }

        $fileoptions = json_decode($_POST["msm_file_options"])->image;

        if ($fileExists)
        {
            file_prepare_draft_area($fileoptions->itemid, $context->id, "mod_msm", $fileoptions->env, $msm->id);
        }
//        else
//        {
//            file_save_draft_area_files($fileoptions->itemid, $context->id, "mod_msm", $fileoptions->env, $msm->id, null);
//        }

        $siblingCompid = 0;

        foreach ($unitcontent as $element)
        {
            $element->insertData($unit->compid, $siblingCompid, $msmId);
            $siblingCompid = $element->compid;
        }
        // need both compid(in case the same unit was inserted multiple times in the composition) and the id of the unit

        if (!empty($_POST['msm_currentUnit_id']))
        {
            if (!empty($unit->short_name))
            {
                echo json_encode($unit->short_name . "-" . $unit->compid . "-" . $unit->id . "|" . $_POST['msm_currentUnit_id']);
            }
            else
            {
                echo json_encode($unit->compid . "-" . $unit->id . "|" . $_POST['msm_currentUnit_id']);
            }
//            
        }
        else
        {
            if (!empty($unit->short_name))
            {
                echo json_encode($unit->short_name . "-" . $unit->compid . "-" . $unit->id);
            }
            else
            {
                echo json_encode($unit->compid . "-" . $unit->id);
            }
        }
    }
    // user triggered the view button on navigation menu to get a preview of the unit
    else if (!empty($_POST['msm_mode_info']))
    {
        $previewHtml = '';

        $previewHtml .= $unit->displayPreview();

        foreach ($unitcontent as $key => $unitchild)
        {
            $previewHtml .= $unitchild->displayPreview($key);
        }

        echo json_encode($previewHtml);
    }
}

/**
 * This method is used to search for all the child elements associated with the unit specified by the $compid and
 * it deletes all the child and any child elements associated with it to prevent having duplicate data associated
 * with the same unit.
 * 
 * @global moodle_datbase $DB
 * @param integer $compid
 */
function deleteOldChildRecord($compid, $msm_id)
{
    global $DB;

    if ($compid != 0)
    {
        $compRecord = $DB->get_record("msm_compositor", array("id" => $compid));
        $compTableRecord = $DB->get_record("msm_table_collection", array("id" => $compRecord->table_id));
        $childElements = $DB->get_records("msm_compositor", array("parent_id" => $compid));

        foreach ($childElements as $child)
        {
            $childTable = $DB->get_record("msm_table_collection", array("id" => $child->table_id));
            // reference materials
            if ((($compTableRecord->tablename == "msm_associate") || ($compTableRecord->tablename == "msm_subordinate")) && ($childTable->tablename != "msm_info"))
            {
                $sql = "SELECT * FROM mdl_msm_compositor WHERE msm_id<>$msm_id AND table_id=$child->table_id AND unit_id=$child->unit_id";
                $records = $DB->get_records_sql($sql);

                if (!empty($records))
                {
                    $DB->delete_records("msm_compositor", array("id" => $child->id));
                    continue;
                }
                else
                {
                    deleteOldChildRecord($child->id, $msm_id);
                }
            }
            else
            {
                deleteOldChildRecord($child->id, $msm_id);
            }
        }

        $childRecord = $DB->get_record("msm_compositor", array('id' => $compid));
        $childTablename = $DB->get_record("msm_table_collection", array("id" => $childRecord->table_id))->tablename;

        $DB->delete_records($childTablename, array("id" => $childRecord->unit_id));
        $DB->delete_records("msm_compositor", array("id" => $compid));
    }
}

?>