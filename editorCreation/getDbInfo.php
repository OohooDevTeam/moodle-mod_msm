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
require_once('EditorExtraInfo.php');
require_once('EditorMedia.php');
require_once('EditorExternalLink.php');

require_once('../XMLImporter/TableCollection.php');

global $DB;

$msmId = $_POST["msmId"];

$msm = $DB->get_record('msm', array('id' => $msmId), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);

require_login($course, true, $cm);

$dbType = $_POST["param"][0]["value"];
$matchString = $_POST["param"][1]["value"];
$fieldType = $_POST["param"][2]["value"];

print_object($dbType);
print_object($matchString);
print_object($fieldType);

// need to include msm ID 
// also need to be given info to know if this is internal or external referencing
if ($dbType == "definition")
{
    if ($fieldType == "title")
    {
        $sql = "SELECT * FROM mdl_msm_def WHERE caption LIKE '%$matchString%'";
    }
    else if ($fieldType == "content")
    {
        $sql = "SELECT * FROM mdl_msm_def WHERE def_content LIKE '%$matchString%'";
    }
    else if($fieldType == "description")
    {
        $sql = "SELECT * FROM mdl_msm_def WHERE description LIKE '%$matchString%'";
    }
    else if ($fieldType == "all")
    {
        $sql = "SELECT * FROM mdl_msm_def WHERE def_content LIKE '%$matchString%' OR caption LIKE '%$matchString%' OR description LIKE '%$matchString%'";
    }
}
else if ($dbType == "theorem") // complication --> title can be from theorem/or part theorem and contents can be either statement/part theorem.
{
     if ($fieldType == "title")
    {
        $sql = "SELECT * FROM mdl_msm_theorem WHERE 'caption' LIKE '%$matchString%'";
    }
//    else if ($fieldType == "content")
//    {
//        $sql = "SELECT * FROM mdl_msm_theorem WHERE 'def_content' LIKE '%$matchString%'";
//    }
    else if($fieldType == "description")
    {
        $sql = "SELECT * FROM mdl_msm_theorem WHERE 'description' LIKE '%$matchString%'";
    }
//    else if ($fieldType == "both")
//    {
//        $sql = "SELECT * FROM mdl_msm_theorem WHERE 'def_content' LIKE '%$matchString%' OR 'caption' LIKE '%$matchString%'";
//    }
}
else if ($dbType == "unit") // complication --> when searching through unit, need to gather all unit's children and search through there.
{
    
}

    $records = $DB->get_records_sql($sql);


print_object($records);

?>
