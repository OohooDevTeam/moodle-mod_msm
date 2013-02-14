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
require_once('EditorPartTheorem.php');
require_once('EditorStatementTheorem.php');
require_once('EditorAssociate.php');
require_once('EditorInfo.php');
require_once('EditorTable.php');
require_once('EditorSubordinate.php');

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
    if (get_class($unitchild) == "EditorIntro")
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
    if ((get_class($unitchild) != 'EditorIntro') && (get_class($unitchild) != 'EditorBlock'))
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

if ($hasError)
{
    echo json_encode($errorArray);
}
else
{
    if (!empty($_POST['msm_currentUnit_id']))
    {
        $idInfo = explode("-", $_POST['msm_currentUnit_id']);

        $compid = $idInfo[0];

        $unit->updateDbRecord($compid);

        $currentUnitRecord = $DB->get_record("msm_compositor", array("id" => $compid));

        $oldUnitChildRecords = $DB->get_records("msm_compositor", array("parent_id" => $compid));

        foreach ($oldUnitChildRecords as $oldchild)
        {
            $unittableid = $DB->get_record("msm_table_collection", array("tablename" => "msm_unit"))->id;

            if ($oldchild->table_id != $unittableid)
            {
                deleteOldChildRecord($oldchild->id);
            }
            else
            {
                // update the parent id of the child so that it corresponts to parent id of the current unit element
                $updateData = new stdClass();
                $updateData->id = $oldchild->id;
                $updateData->msm_id = $oldchild->msm_id;
                $updateData->table_id = $oldchild->table_id;
                $updateData->unit_id = $oldchild->unit_id;
                $updateData->parent_id = $currentUnitRecord->parent_id;
                $updateData->prev_sibling_id = $oldchild->prev_sibling_id;

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

    $siblingCompid = 0;

    foreach ($unitcontent as $element)
    {
        $element->insertData($unit->compid, $siblingCompid, $msmId);
        $siblingCompid = $element->compid;
    }

    // need both compid(in case the same unit was inserted multiple times in the composition) and the id of the unit

    if (!empty($_POST['msm_currentUnit_id']))
    {
        echo json_encode($unit->compid . "-" . $unit->id . "|" . $_POST['msm_currentUnit_id']);
    }
    else
    {
        echo json_encode($unit->compid . "-" . $unit->id);
    }
}

function deleteOldChildRecord($compid)
{
    global $DB;

    if ($compid != 0)
    {
        $childElements = $DB->get_records("msm_compositor", array("parent_id" => $compid));

        foreach ($childElements as $child)
        {
            deleteOldChildRecord($child->id);
        }

        $childRecord = $DB->get_record("msm_compositor", array('id' => $compid));
        $childTablename = $DB->get_record("msm_table_collection", array("id" => $childRecord->table_id))->tablename;

        $DB->delete_records($childTablename, array("id" => $childRecord->unit_id));
        $DB->delete_records("msm_compositor", array("id" => $compid));
    }
}

?>