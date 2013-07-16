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
$refType = $_POST["refereceType"];

$msm = $DB->get_record('msm', array('id' => $msmId), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);

require_login($course, true, $cm); // for security measures

$dbType = $_POST["param"][0]["value"];
$matchString = trim(strtolower($_POST["param"][1]["value"]));
$fieldType = $_POST["param"][2]["value"];

$deftableID = $DB->get_record("msm_table_collection", array("tablename" => "msm_def"));

$msmString = '';

if ($refType == "Internal Reference")
{
    $msmString = " AND c1.msm_id=$msmId";
}
else if ($refType == "External Reference")
{
    $msmString = " AND c1.msm_id<>$msmId";
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

$html = displaySearchResult($records, $deftableID);

echo json_encode($html);

function displaySearchResult($records, $tableRecords)
{
    $displayString = '';
    $displayString .= "<table id='msm_search_result_table'>";
    $displayString .= "<tr>";
    $displayString .= "<th class='msm_search_result_table_cells'> Select </th>";
    $displayString .= "<th class='msm_search_result_table_cells'> Type </th>";
    $displayString .= "<th class='msm_search_result_table_cells'> Title </th>";
    $displayString .= "<th class='msm_search_result_table_cells'> Content </th>";
    $displayString .= "<th class='msm_search_result_table_cells'> Description </th>";
    $displayString .= "</tr>";

    foreach ($records as $rec)
    {
        $displaySubordinate = displaySearchSubordinate($rec);

        $displayString .= "<tr>";
        $displayString .= "<td class='msm_search_result_table_cells'><input type='checkbox' id='msm_search_select-" . $rec->id . "' name='msm_search_select-" . $rec->id . "'/></td>";
        if ($tableRecords->tablename == "msm_def")
        {
            if (!empty($rec->def_type))
            {
                $displayString .= "<td class='msm_search_result_table_cells'>$rec->def_type</td>";
            }
            else
            {
                $displayString .= "<td class='msm_search_result_table_cells'>Definition</td>";
            }
        }
        $displayString .= "<td class='msm_search_result_table_cells'>$rec->caption</td>";

        $content = preg_replace("/<math.array(.*?)>/", "<table$1>", $rec->content);
        $content = preg_replace("/<\/math.array>/", "</table>", $content);

        $displayString .= "<td class='msm_search_result_table_cells'>" . $content . $displaySubordinate . "</td>";
        $displayString .= "<td class='msm_search_result_table_cells'>$rec->description</td>";
        $displayString .= "</tr>";
    }
    $displayString .= "</table>";

    return $displayString;
}

function displaySearchSubordinate($record)
{
    global $DB;
    
    $subordinateString = '';

    $subordinateTable = $DB->get_record("msm_table_collection", array("tablename" => "msm_subordinate"));
    $childSubs = $DB->get_records("msm_compositor", array("parent_id" => $record->id, "table_id" => $subordinateTable->id), "prev_sibling_id");

    foreach ($childSubs as $sub)
    {
        $subordinate = new EditorSubordinate();
        $subordinate->loadData($sub->id);
        
        $subordinateString .= $subordinate->displayPreview();
    }
    
    return $subordinateString;
}

?>
