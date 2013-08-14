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
 * This script is called by an AJAX call in editorUtility.js and its main role is to
 * load the correct unit when triggered.  The possible triggers can be when the user clicks on 
 * a specific node of a jsTree on right panel to open an already created unit, when the user
 * triggers the edit function from view.php page, when the user decides to cancel all the changes
 * made to the unit, or when the user triggers to go to view.php by pressing the "done" button in MSM
 * editor panel.
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
require_once("EditorMedia.php");
require_once('EditorExternalLink.php');


require_once('../XMLImporter/TableCollection.php');

global $DB;

// user triggered a specific node of a jstree to view a particular unit
if (isset($_POST['id']))
{
// id passed in form of an array with id index and value of msm_unit-#compid-#unitid
    $unitidInfo = explode('-', $_POST['id']);

    $unitData = new EditorUnit();
    $unitData->loadData($unitidInfo[1]);

    $htmlContent = '';
    $htmlContent .= $unitData->displayData();

    echo json_encode($htmlContent);
}
// user has triggered the edit mode from the view.php
else if (isset($_POST['mode']))
{
    $unitCompidInfo = explode("-", $_POST['currentUnit']);
    $unitCompid = $unitCompidInfo[0];

    $unitChildElementRecords = $DB->get_records("msm_compositor", array("parent_id" => $unitCompid), "prev_sibling_id");

    // need to copy unitChildElementRecords to have index as incrementing numbers from zero to n instead of it being compositor id
    $unitChildElements = array();
    $unitTable = $DB->get_record("msm_table_collection", array("tablename" => "msm_unit"));
    $personTable = $DB->get_record("msm_table_collection", array("tablename" => "msm_person"));

    foreach ($unitChildElementRecords as $childRecord)
    {
        if (($childRecord->table_id != $unitTable->id) && ($childRecord->table_id != $personTable->id))
        {
            $unitChildElements[] = $childRecord;
        }
    }
    $childOrderArray = explode(",", $_POST['childOrder']);

    $indexElement = null;
    foreach ($childOrderArray as $key => $value)
    {
        if ($value == $_POST["currentElement"])
        {
            $indexElement = $key;
        }
    }

    $currentElement = $unitChildElements[$indexElement];
    $currentElementTable = $DB->get_record("msm_table_collection", array("id" => $currentElement->table_id))->tablename;

    switch ($currentElementTable)
    {
        case "msm_intro":
            $intro = new EditorIntro();
            $intro->loadData($currentElement->id);
            echo json_encode($intro);
            break;
        case "msm_block":
            $block = new EditorBlock();
            $block->loadData($currentElement->id);
            echo json_encode($block);
            break;
        case "msm_def":
            $def = new EditorDefinition();
            $def->loadData($currentElement->id);
            echo json_encode($def);
            break;
        case "msm_theorem":
            $theorem = new EditorTheorem();
            $theorem->loadData($currentElement->id);
            echo json_encode($theorem);
            break;
        case "msm_comment":
            $comment = new EditorComment();
            $comment->loadData($currentElement->id);
            echo json_encode($comment);
            break;
        case "msm_extra_info":
            $extraInfo = new EditorExtraInfo();
            $extraInfo->loadData($currentElement->id);
            echo json_encode($extraInfo);
            break;
    }
}
// when triggered to view.php from MSM editor panel
else if (isset($_POST['tree_content']))
{
    $doc = new DOMDocument();
    $doc->loadHTML($_POST['tree_content']);

    $standaloneIds = $_POST["standalone_content"];
    $standaloneIdInfos = explode(",", $standaloneIds);

    $rootElement = $doc->documentElement;

    $ulElement = $rootElement->childNodes->item(0);

    processTreeContent($ulElement, 0, 0);

    // updating standalone units to change the standalone status in the msm_unit table
    foreach ($standaloneIdInfos as $idInfo)
    {
        if (!empty($idInfo))
        {
            $idsets = explode("-", $idInfo);

            $idPair = $idsets[1] . "-" . $idsets[2];
            $unit = new EditorUnit();
            $unit->updateUnitStructure($idPair, '', '');
        }
    }

    $liElements = $ulElement->getElementsByTagName('li');

    $compidArray = array();

    foreach ($liElements as $liEl)
    {
        $id = $liEl->getAttribute("id");

        $string = explode("-", $id);

        $compidArray[] = $string[1];
    }

    $idPairs = array();

    foreach ($compidArray as $compid)
    {
        $elementRecord = $DB->get_record('msm_compositor', array('id' => $compid));
        $idPairs[] = $elementRecord->msm_id . "-" . $compid;
    }

    echo json_encode($idPairs[0]);
}
// user triggered to discard any changes made to the unit
else if (isset($_POST["cancelUnit"])) // from cancelUnit js function
{
    $unitidInfo = explode('-', $_POST['cancelUnit']);

    $unitData = new EditorUnit();
    $unitData->loadData($unitidInfo[0]);

    $htmlContent = '';
    $htmlContent .= $unitData->displayData();

    echo json_encode($htmlContent);
}
// for displaying chosen reference material for loading the existing subordinate for editting
else if (isset($_POST["loadRefId"])) 
{
    $refId = $_POST["loadRefId"];

    $compRecord = $DB->get_record("msm_compositor", array("id" => $refId));
    $tableRecord = $DB->get_record("msm_table_collection", array("id" => $compRecord->table_id));

    switch ($tableRecord->tablename)
    {
        case "msm_def":
            $definition = new EditorDefinition();
            $definition->loadData($refId);
            $refHtml = $definition->displayPreview();
            echo json_encode($refHtml);
            break;
        case "msm_comment":
            $comment = new EditorComment();
            $comment->loadData($refId);
            $refHtml = $comment->displayPreview();
            echo json_encode($refHtml);
            break;
        case "msm_theorem":
            $theorem = new EditorTheorem();
            $theorem->loadData($refId);
            $refHtml = $theorem->displayPreview();
            echo json_encode($refHtml);
            break;
        case "msm_unit":
            $unit = new EditorUnit();
            $unit->loadData($refId);
            $refHtml = $unit->displayPreview();
            echo json_encode($refHtml);
            break;
    }
}

/**
 * This function is used to parse the HTML string generated from the
 * jsTree that is used to describe the structural relationship between 
 * units in a composition when the user changes the tree structure(ie. changing
 * the unit structure). This function finds the list items which has an id 
 * stirng of "unit_composition_id-unit_table_id" pair and calls the updateUnitStructure
 * function from EditorUnit class to update records in msm_compositor.
 * 
 * @param DOMElement $DomElement
 * @param Integer $parentId
 * @param Integer $siblingId
 */
function processTreeContent($DomElement, $parentId, $siblingId)
{
    if ($DomElement->hasChildNodes())
    {
        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                switch ($child->tagName)
                {
                    case "ul":
                        processTreeContent($child, $parentId, $siblingId);
                        break;
                    case "li":
                        $childId = $child->getAttribute("id");
                        $childIdInfo = explode("-", $childId);

                        $idPair = $childIdInfo[1] . "-" . $childIdInfo[2];
                        $unit = new EditorUnit();
                        $unit->updateUnitStructure($idPair, $parentId, $siblingId);

                        $siblingId = $childIdInfo[1];

                        if ($child->hasChildNodes())
                        {
                            foreach ($child->childNodes as $grandChild)
                            {
                                if ($grandChild->nodeType == XML_ELEMENT_NODE)
                                {
                                    if ($grandChild->tagName == "ul")
                                    {
                                        processTreeContent($grandChild, $childIdInfo[1], 0);
                                    }
                                }
                            }
                        }

                        break;
                }
            }
        }
    }
}

?>