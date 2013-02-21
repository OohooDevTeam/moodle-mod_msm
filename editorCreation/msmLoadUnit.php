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
else if (isset($_POST['childElementId']))
{    
    $unitCompidInfo = explode("-", $_POST['currentUnit']);
    $unitCompid = $unitCompidInfo[0];
    
    $childElements = $DB->get_records("msm_compositor", array("id"=>$unitCompid), "prev_sibling_id");
    // copy to have incremental index instead of db id
    $copiedElements = array();    
    foreach($childElements as $child)
    {
        $copiedElements[] = $child;
    }

    $intromatch = "/^msm_intro_content_input-\d+$/";
    $introchildmatch = "/^msm_intro_child_content-\d+$/";
    $bodymatch = "/^msm_body_content_input-\d+$/";
    $defmatch = "/^msm_def_content_input-\d+$/";
    $defrefmatch = "/^msm_defref_content_input-\d+$/";
    $theoremmatch = "/^msm_theorem_content_input-.+$/";
    $theoremrefmatch = "/^msm_theoremref_content_input-.+$/";
    $theoremcontentmatch = "/^msm_theorem_part_content-.+$/";
    $theoremrefcontentmatch = "/^msm_theoremref_part_content-.+$/";
    $theorempartmatch = "/^msm_theorem_part_content-.+$/";
    $theoremrefpartmatch = "/^msm_theoremref_part_content-.+$/";
    $commentmatch = "/^msm_comment_content_input-\d+$/";
    $commentrefmatch = "/^msm_commentref_content_input-.+$/";
    $infotitlematch = "/^msm_info_title-.+$/";
    $infocontentmatch = "/^msm_info_content-.+$/";
    
    if(preg_match($intromatch, $_POST["childElementId"]))
    {
        $intro = new EditorIntro();
        $intro->loadData($copiedElements[$i]->id);
        
        echo json_encode($intro);
    }
    else if((preg_match($introchildmatch, $_POST["childElementId"]))||(preg_match($bodymatch, $_POST["childElementId"])))
    {
        
    }
    else if((preg_match($defmatch, $_POST["childElementId"]))||(preg_match($defrefmatch, $_POST["childElementId"])))
    {
        
    }
    else if((preg_match($theoremmatch, $_POST["childElementId"]))||(preg_match($theoremrefmatch, $_POST["childElementId"])))
    {
        
    }
    else if((preg_match($theoremcontentmatch, $_POST["childElementId"]))||(preg_match($theoremrefcontentmatch, $_POST["childElementId"])))
    {
        
    }
    else if((preg_match($theorempartmatch, $_POST["childElementId"]))||(preg_match($theoremrefpartmatch, $_POST["childElementId"])))
    {
        
    }
    else if((preg_match($commentmatch, $_POST["childElementId"]))||(preg_match($commentrefmatch, $_POST["childElementId"])))
    {
        
    }
    else if(preg_match($infotitlematch, $_POST["childElementId"]))
    {
        
    }
    else if(preg_match($infocontentmatch, $_POST["childElementId"]))
    {
        
    } 
    
}
else if (isset($_POST['tree_content']))
{
    $doc = new DOMDocument();
    $doc->loadHTML($_POST['tree_content']);

    $rootElement = $doc->documentElement;

    $ulElement = $rootElement->childNodes->item(0)->childNodes->item(0);

    processTreeContent($ulElement, 0, 0);

    $aElements = $ulElement->getElementsByTagName('a');

    $compidArray = array();

    foreach ($aElements as $aEl)
    {
        if ($aEl->hasChildNodes())
        {
            foreach ($aEl->childNodes as $child)
            {
                if ($child->nodeType == XML_TEXT_NODE)
                {
                    $string = explode("-", $child->wholeText);
                    $compidArray[] = $string[0];
                }
            }
        }
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