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

for($i=0; $i < $lengthOfArray-1; $i++)
{
    $childIdInfo = explode("-", $arrayOfChild[$i]);
    
    switch($childIdInfo[0])
    {
        case "copied_msm_def":
            $definition = new EditorDefinition();
            $definition->type = $_POST['msm_def_type_dropdown-'.$childIdInfo[1]];
            $definition->associateType = $_POST['msm_def_associate_dropdown-'.$childIdInfo[1]];
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
        foreach($unitcontent as $element)
        {
            $className = get_class($element);
            $data = new stdClass();
            
            if($className == 'EditorDefinition')
            {
                $data->def_type = $element->type;
                $data->caption = $element->title;
                $data->def_content = $element->content;
                
                $id = $DB->insert_record($element->tablename, $data);
            }
        }
        
        echo json_encode("true");
    }
}

?>
