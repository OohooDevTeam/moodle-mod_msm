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
else if (isset($_POST['mode']))
{
    $unitCompidInfo = explode("-", $_POST['currentUnit']);
    $unitCompid = $unitCompidInfo[0];
    
    $unitChildElementRecords = $DB->get_records("msm_compositor", array("parent_id"=>$unitCompid), "prev_sibling_id");
    
    // need to copy unitChildElementRecords to have index as incrementing numbers from zero to n instead of it being compositor id
    $unitChildElements = array();
    foreach($unitChildElementRecords as $childRecord)
    {
        $unitChildElements[] = $childRecord;
    }
    
//    print_object($unitChildElements);
    
    $childOrderArray = explode(",", $_POST['childOrder']);
    
    $indexElement = null;
    foreach($childOrderArray as $key=>$value)
    {
        if($value == $_POST["currentElement"])
        {
            $indexElement = $key;
        }
    }
    
    $currentElement = $unitChildElements[$indexElement];
    $currentElementTable = $DB->get_record("msm_table_collection", array("id"=>$currentElement->table_id))->tablename;
    
    switch($currentElementTable)
    {
        case "msm_intro":
            break;
        case "msm_block":
            $block = new EditorBlock();
            $block->loadData($currentElement->id);
            echo json_encode($block);
            break;
        case "msm_def":
            break;
        case "msm_theorem":
            break;
        case "msm_comment":
            break;
    }
    
}
else if (isset($_POST['tree_content']))
{
    $doc = new DOMDocument();
    $doc->loadHTML($_POST['tree_content']);

    $rootElement = $doc->documentElement;

    $ulElement = $rootElement->childNodes->item(0)->childNodes->item(0);

    processTreeContent($ulElement, 0, 0);

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

    echo json_encode($idPairs);
}
else if ($_POST["cancelUnit"]) // from cancelUnit js function
{
    $unitidInfo = explode('-', $_POST['cancelUnit']);

    $unitData = new EditorUnit();
    $unitData->loadData($unitidInfo[0]);

    $htmlContent = '';
    $htmlContent .= $unitData->displayData();

    echo json_encode($htmlContent);
}

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