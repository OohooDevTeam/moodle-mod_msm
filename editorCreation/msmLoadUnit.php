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
    //print_object($_POST);
// id passed in form of an array with id index and value of msm_unit-#compid-#unitid
    $unitidInfo = explode('-', $_POST['id']);

    $unitData = new EditorUnit();
    $unitData->loadData($unitidInfo[1]);

    $htmlContent = '';
    $htmlContent .= $unitData->displayData();

    echo json_encode($htmlContent);
}
else if (isset($_POST['tree_content']))
{
    $doc = new DOMDocument();
    $doc->loadHTML($_POST['tree_content']);

    $rootElement = $doc->documentElement;
    
    $ulElement = $rootElement->childNodes->item(0)->childNodes->item(0);
    
    processTreeContent($ulElement, 0);
    
    $midString = parse_url($_SERVER['PATH_INFO'], PHP_URL_QUERY);
    
    print_object($midString);
    
    $midInfo = explode("=", $midString);
    
    echo json_encode($midInfo[1]);
}

function processTreeContent($DomElement, $parentNode)
{
    $parent = $parentNode;
    
    if ($DomElement->hasChildNodes())
    {
        foreach ($DomElement->childNodes as $child)
        {
            if ($child->nodeType == XML_ELEMENT_NODE)
            {
                switch ($child->tagName)
                {
                    case "ul":
                        processTreeContent($child, $parent);
                        break;
                    case "li":
                        processTreeContent($child, $parent);
                        break;
                    case "a":
                        if ($child->hasChildNodes())
                        {
                            foreach($child->childNodes as $grandChild)
                            {
                                if($grandChild->nodeType == XML_TEXT_NODE)
                                {
                                    $unit = new EditorUnit();
                                    $updateId = $unit->updateCompRecord($grandChild->nodeValue, $parent);
                                    
                                    if(!empty($child->nextSibling))
                                    {
                                        if($child->nextSibling->nodeType == XML_ELEMENT_NODE)
                                        {
                                            if($child->nextSibling->tagName == "ul")
                                            {
                                                $parent = $updateId;
                                            }
                                        }
                                    }
//                                    else
//                                    {
//                                        
//                                    }
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