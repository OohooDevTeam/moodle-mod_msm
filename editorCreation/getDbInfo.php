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
$currentId = $_POST["current_id"];

$msm = $DB->get_record('msm', array('id' => $currentId), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);

require_login($course, true, $cm); // for security measures

$dbType = $_POST["param"][0]["value"];
$matchString = trim(strtolower($_POST["param"][1]["value"]));
$fieldType = $_POST["param"][2]["value"];

$deftableID = $DB->get_record("msm_table_collection", array("tablename" => "msm_def"));

$msmString = '';

if (!empty($msmId))
{
    $msmString = " AND c1.msm_id=$msmId";
}

$whereClause = '';

// need to include msm ID 
// also need to be given info to know if this is internal or external referencing
if ($dbType == "definition")
{
    if ($fieldType == "title")
    {
        $whereClause = "WHERE LOWER(d1.caption) LIKE '%$matchString%' OR LOWER(d1.caption) LIKE '$matchString%' OR LOWER(d1.caption) LIKE '%$matchString'";
    }
    else if ($fieldType == "content")
    {
        $whereClause = "WHERE LOWER(d1.def_content) LIKE '%$matchString%' OR LOWER(d1.def_content) LIKE '$matchString%' OR LOWER(d1.def_content) LIKE '%$matchString'";
    }
    else if ($fieldType == "description")
    {
        $whereClause = "WHERE LOWER(d1.description) LIKE '%$matchString%' OR LOWER(d1.description) LIKE '$matchString%' OR LOWER(d1.description) LIKE '%$matchString'";
    }
    else if ($fieldType == "all")
    {
        $whereClause = "WHERE LOWER(d1.def_content) LIKE '%$matchString%' OR LOWER(d1.caption) LIKE '%$matchString%' OR LOWER(d1.description) LIKE '%$matchString%'
                        OR LOWER(d1.def_content) LIKE '$matchString%' OR LOWER(d1.caption) LIKE '$matchString%' OR LOWER(d1.description) LIKE '$matchString%'
                        OR LOWER(d1.def_content) LIKE '%$matchString' OR LOWER(d1.caption) LIKE '%$matchString' OR LOWER(d1.description) LIKE '%$matchString'";
    }

    if (!empty($whereClause))
    {
        $sql = "SELECT comp.id, MAX(comp.unit_id), comp.msm_id, comp.table_id, def.caption, def.string_id, def.def_type, def.def_content AS content, def.description
                FROM (SELECT * 
                     FROM mdl_msm_compositor c1
                     WHERE c1.table_id = $deftableID->id" . "$msmString)
                AS comp INNER JOIN
                    (SELECT * 
                     FROM mdl_msm_def d1
                     $whereClause)
                AS def
                ON comp.unit_id = def.id                
                GROUP BY comp.unit_id";
    }
}
//else if ($dbType == "theorem") // complication --> title can be from theorem/or part theorem and contents can be either statement/part theorem.
//{
//    if ($fieldType == "title")
//    {
//        $sql = "SELECT * FROM mdl_msm_theorem WHERE 'caption' LIKE '%$matchString%'";
//    }
//    else if ($fieldType == "content")
//    {
//        $sql = "SELECT * FROM mdl_msm_theorem WHERE 'def_content' LIKE '%$matchString%'";
//    }
//    else if ($fieldType == "description")
//    {
//        $sql = "SELECT * FROM mdl_msm_theorem WHERE 'description' LIKE '%$matchString%'";
//    }
//    else if ($fieldType == "both")
//    {
//        $sql = "SELECT * FROM mdl_msm_theorem WHERE 'def_content' LIKE '%$matchString%' OR 'caption' LIKE '%$matchString%'";
//    }
//}
//else if ($dbType == "unit") // complication --> when searching through unit, need to gather all unit's children and search through there.
//{
//    
//}

$records = null;

if (!empty($sql))
{
    $records = $DB->get_records_sql($sql);
}
else
{
    echo "error in retrieving records using SQL queries";
}

$html = "<table id='msm_search_result_table'>";
$html .= "<tr>";
$html .= "<th class='msm_search_result_table_cells'> Select </th>";
$html .= "<th class='msm_search_result_table_cells'> Type </th>";
$html .= "<th class='msm_search_result_table_cells'> Title </th>";
$html .= "<th class='msm_search_result_table_cells'> Content </th>";
$html .= "<th class='msm_search_result_table_cells'> Description </th>";
$html .= "</tr>";

foreach ($records as $rec)
{
    $html .= "<tr>";
    $html .= "<td class='msm_search_result_table_cells'><input type='checkbox' id='msm_search_select-" . $rec->id . "' name='msm_search_select-" . $rec->id . "'/></td>";
    if ($deftableID->tablename == "msm_def")
    {
        if(!empty($rec->def_type))
        {
            $html .= "<td class='msm_search_result_table_cells'>$rec->def_type</td>";
        }
        else
        {
            $html .= "<td class='msm_search_result_table_cells'>Definition</td>";
        }
        
    }
    $html .= "<td class='msm_search_result_table_cells'>$rec->caption</td>";

    $content = preg_replace("/<math.array(.*?)>/", "<table$1>", $rec->content);
    $content = preg_replace("/<\/math.array>/", "</table>", $content);
    $html .= "<td class='msm_search_result_table_cells'>$content</td>";
    $html .= "<td class='msm_search_result_table_cells'>$rec->description</td>";
    $html .= "</tr>";
}
$html .= "</table>";

echo json_encode($html);
?>
