<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('../../../config.php');
require_once($CFG->dirroot . '/mod/msm/lib.php');
require_once('EditorDefinition.php');
require_once('EditorTheorem.php');
require_once('EditorImage.php');
require_once('EditorUnit.php');
require_once('../XMLImporter/TableCollection.php');

global $DB;

$childOrder = $_POST['msm_child_order'];

$arrayOfChild = explode(",", $childOrder);

$lengthOfArray = sizeOf($arrayOfChild);

$msmId = $arrayOfChild[$lengthOfArray-1];

//$theorems = array();
//$defs = array();
//$images = array();
//$intros = array();
//$msmbodys = array();

$unitcontent = array();

$hasError = false;
$errorArray = array();

$tableCollection = new TableCollection();
$tableCollection->insertTablename();

$unit = new EditorUnit();

if($_POST['msm_unit_title'] != '')
{
    $unit->title = $_POST['msm_unit_title'];
}
else
{
    $hasError = true;
    $errorArray[] = 'msm_unit_title';
}

if($_POST['msm_unit_descripton_input'] != '')
{
    $unit->description = $_POST['msm_unit_descripton_input'];
}

for($i=0; $i < $lengthOfArray-1; $i++)
{
    $childIdInfo = explode("-", $arrayOfChild[$i]);
    
    switch($childIdInfo[0])
    {
        case "copied_msm_def":
            $definition = new EditorDefinition();
            $definition->type = $_POST['msm_def_type_dropdown-'.$childIdInfo[1]];
            $definition->associateType = $_POST['msm_def_associate_dropdown-'.$childIdInfo[1]];
            $definition->description = $_POST['msm_def_descripton_input-' . $childIdInfo[1]];
            $definition->position = $i;
            
            if($_POST['msm_def_title_input-'.$childIdInfo[1]] != '')
            {
                $definition->title = $_POST['msm_def_title_input-'.$childIdInfo[1]];
            }
            else
            {
                $hasError = true;
                $errorArray[] = 'msm_def_title_input-'.$childIdInfo[1];
                
            }
            
            if($_POST['msm_def_content_input-'.$childIdInfo[1]] != '')
            {
                $definition->content = $_POST['msm_def_content_input-'.$childIdInfo[1]];
            }
            else
            {
                $hasError = true;
                $errorArray[] = 'msm_def_content_input-' . $childIdInfo[1] . "_ifr";
                
            }
            
            $unitcontent[] = $definition;
            
            break;
        case "copied_msm_theorem":
            echo "theorem";
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
    
    if($hasError)
    {
        echo json_encode($errorArray);
    }
    else
    {
        $unitData = new stdClass();
        $unitData->title = $unit->title;
        $unitData->description = $unit->description;
        
        $unit->id = $DB->insert_record($unit->tablename, $unitData);
        $unitCompData = new stdClass();
        $unitCompData->unit_id = $unit->id;
        $unitCompData->parent_id = 0;
        $unitCompData->prev_sibling_id = 0;
        $unitCompData->table_id = $DB->get_record('msm_table_collection', array('tablename'=>$unit->tablename))->id;
        $unit->compid = $DB->insert_record('msm_compositor', $unitCompData);
        // need code fo insert unit information to unitdatabase before procesing the child so that
        // the parentid exists when the child elements are being inserted to the db
        
        $siblingCompid = 0;
        foreach($unitcontent as $element)
        {
            $className = get_class($element);
            $data = new stdClass();
            $compData = new stdClass();
            
            if($className == 'EditorDefinition')
            {
                $data->def_type = $element->type;
                $data->caption = $element->title;
                $data->def_content = $element->content;
                
                $element->id = $DB->insert_record($element->tablename, $data);
                
                $compData->unit_id = $element->id;
                $compData->table_id = $DB->get_record('msm_table_collection', array('tablename'=>$element->tablename))->id;
                $compData->parent_id = $unit->compid;
                $compData->prev_sibling_id = $siblingCompid;
                
                $element->compid = $DB->insert_record('msm_compositor', $compData);
                $siblingCompid = $element->compid;
            }
        }
        
        echo json_encode("true");
    }
}

?>
