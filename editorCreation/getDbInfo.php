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


$tableID = null;

if ($dbType == "definition")
{
    $tableID = $DB->get_record("msm_table_collection", array("tablename" => "msm_def"));
}
else if ($dbType == "theorem")
{
    $tableID = $DB->get_record("msm_table_collection", array("tablename" => "msm_theorem"));
    $statementTheoremTable = $DB->get_record("msm_table_collection", array("tablename" => "msm_statement_theorem"));
    $partTheoremTable = $DB->get_record("msm_table_collection", array("tablename" => "msm_part_theorem"));
}
else if ($dbType == "comment")
{
    $tableID = $DB->get_record("msm_table_collection", array("tablename" => "msm_comment"));
}
else if ($dbType == "unit")
{
    $tableID = $DB->get_record("msm_table_collection", array("tablename" => "msm_unit"));
}

$msmString = '';

if ($refType == "Internal References")
{
    $msmString = " AND c1.msm_id=$msmId";
}
else if ($refType == "External References")
{
    $msmString = " AND c1.msm_id<>$msmId";
}

$records = array();
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
                     WHERE c1.table_id = $tableID->id" . "$msmString)
                AS comp INNER JOIN
                    (SELECT * 
                     FROM mdl_msm_def d1
                     $whereClause)
                AS def
                ON comp.unit_id = def.id                
                GROUP BY comp.unit_id";
    }
}
else if ($dbType == "comment")
{
    if ($fieldType == "title")
    {
        $whereClause = "WHERE LOWER(com1.caption) LIKE '%$matchString%' OR LOWER(com1.caption) LIKE '$matchString%' OR LOWER(com1.caption) LIKE '%$matchString'";
    }
    else if ($fieldType == "content")
    {
        $whereClause = "WHERE LOWER(com1.comment_content) LIKE '%$matchString%' OR LOWER(com1.comment_content) LIKE '$matchString%' OR LOWER(com1.comment_content) LIKE '%$matchString'";
    }
    else if ($fieldType == "description")
    {
        $whereClause = "WHERE LOWER(com1.description) LIKE '%$matchString%' OR LOWER(com1.description) LIKE '$matchString%' OR LOWER(com1.description) LIKE '%$matchString'";
    }
    else if ($fieldType == "all")
    {
        $whereClause = "WHERE LOWER(com1.comment_content) LIKE '%$matchString%' OR LOWER(com1.caption) LIKE '%$matchString%' OR LOWER(com1.description) LIKE '%$matchString%'
                        OR LOWER(com1.comment_content) LIKE '$matchString%' OR LOWER(com1.caption) LIKE '$matchString%' OR LOWER(com1.description) LIKE '$matchString%'
                        OR LOWER(com1.comment_content) LIKE '%$matchString' OR LOWER(com1.caption) LIKE '%$matchString' OR LOWER(com1.description) LIKE '%$matchString'";
    }

    if (!empty($whereClause))
    {
        $sql = "SELECT comp.id, MAX(comp.unit_id), comp.msm_id, comp.table_id, comment.caption, comment.string_id, comment.comment_type, comment.comment_content AS content, comment.description
                FROM (SELECT * 
                     FROM mdl_msm_compositor c1
                     WHERE c1.table_id = $tableID->id" . "$msmString)
                AS comp INNER JOIN
                    (SELECT * 
                     FROM mdl_msm_comment com1
                     $whereClause)
                AS comment
                ON comp.unit_id = comment.id                
                GROUP BY comp.unit_id";
    }
}
else if ($dbType == "theorem")
{
    if ($fieldType == "title")
    {
        $whereClause = "WHERE LOWER(thm.caption) LIKE '%$matchString%' OR LOWER(thm.caption) LIKE '$matchString%' OR LOWER(thm.caption) LIKE '%$matchString'
                        OR LOWER(thm.textcaption) LIKE '%$matchString%' OR LOWER(thm.textcaption) LIKE '$matchString%' OR LOWER(thm.textcaption) LIKE '%$matchString'";

        $sql = "SELECT comp.id, MAX(comp.unit_id), comp.msm_id, comp.table_id, theorem.caption, theorem.textcaption, theorem.string_id, theorem.theorem_type, theorem.description
                FROM (SELECT * 
                     FROM mdl_msm_compositor c1
                     WHERE c1.table_id = $tableID->id" . "$msmString)
                AS comp INNER JOIN
                    (SELECT * 
                     FROM mdl_msm_theorem thm
                     $whereClause)
                AS theorem
                ON comp.unit_id = theorem.id                
                GROUP BY comp.unit_id";

        $records = $DB->get_records_sql($sql);

        // finding theorem with part_theorem caption that has the matching string init when theorem caption does not
        $found = false;

        $theoremSql = "SELECT * 
                        FROM mdl_msm_compositor c1
                        WHERE c1.table_id = $tableID->id" . "$msmString";

        // eliminate records that are already in records array
        foreach ($records as $rec)
        {
            $theoremSql .= " AND c1.id <> $rec->id";
        }

        $theoremRecords = $DB->get_records_sql($theoremSql);

        foreach ($theoremRecords as $theoremrec)
        {
            $statementTheorems = $DB->get_records("msm_compositor", array("parent_id" => $theoremrec->id, "table_id" => $statementTheoremTable->id), "prev_sibling_id");

            foreach ($statementTheorems as $statement)
            {
                $partTheorems = $DB->get_records("msm_compositor", array("parent_id" => $statement->id, "table_id" => $partTheoremTable->id), "prev_sibling_id");

                foreach ($partTheorems as $part)
                {
                    $partRecord = $DB->get_record("msm_part_theorem", array("id" => $part->unit_id));

                    if (strpos($partRecord->caption, $matchString) !== false)
                    {
                        $found = true;
                        break 2;
                    }
                }
            }

            if ($found)
            {
                $joinedsql = "SELECT comp.id, MAX(comp.unit_id), comp.msm_id, comp.table_id, theorem.caption, theorem.textcaption, theorem.string_id, theorem.theorem_type, theorem.description
                FROM (SELECT * 
                     FROM mdl_msm_compositor c1
                     WHERE c1.id = $theoremrec->id)
                AS comp INNER JOIN
                    (SELECT *
                     FROM mdl_msm_theorem thm
                     WHERE thm.id = $theoremrec->unit_id)
                AS theorem
                ON comp.unit_id = theorem.id                
                GROUP BY comp.unit_id";

                $joineRec = $DB->get_record_sql($joinedsql);
                array_push($records, $joineRec);
            }
        }
    }
    else if ($fieldType == "content")
    {
        // finding theorem with part_theorem caption that has the matching string init when theorem caption does not
        $found = false;

        $theoremSql = "SELECT * 
                        FROM mdl_msm_compositor c1
                        WHERE c1.table_id = $tableID->id" . "$msmString";

        $theoremRecords = $DB->get_records_sql($theoremSql);

        foreach ($theoremRecords as $theoremrec)
        {
            $statementTheorems = $DB->get_records("msm_compositor", array("parent_id" => $theoremrec->id, "table_id" => $statementTheoremTable->id), "prev_sibling_id");

            foreach ($statementTheorems as $statement)
            {
                $stateUnitRecord = $DB->get_record("msm_statement_theorem", array("id" => $statement->unit_id));

                if (strpos($stateUnitRecord->statement_content, $matchString) !== false)
                {
                    $found = true;
                    break;
                }
                else
                {
                    $partTheorems = $DB->get_records("msm_compositor", array("parent_id" => $statement->id, "table_id" => $partTheoremTable->id), "prev_sibling_id");

                    foreach ($partTheorems as $part)
                    {
                        $partRecord = $DB->get_record("msm_part_theorem", array("id" => $part->unit_id));

                        if (strpos($partRecord->part_content, $matchString) !== false)
                        {
                            $found = true;
                            break 2;
                        }
                    }
                }
            }

            if ($found)
            {
                $joinedsql = "SELECT comp.id, MAX(comp.unit_id), comp.msm_id, comp.table_id, theorem.caption, theorem.textcaption, theorem.string_id, theorem.theorem_type, theorem.description
                FROM (SELECT * 
                     FROM mdl_msm_compositor c1
                     WHERE c1.id = $theoremrec->id)
                AS comp INNER JOIN
                    (SELECT *
                     FROM mdl_msm_theorem thm
                     WHERE thm.id = $theoremrec->unit_id)
                AS theorem
                ON comp.unit_id = theorem.id                
                GROUP BY comp.unit_id";

                $joineRec = $DB->get_record_sql($joinedsql);
                array_push($records, $joineRec);
            }
        }
    }
    else if ($fieldType == "description")
    {
        $whereClause = "WHERE LOWER(thm.description) LIKE '%$matchString%' OR LOWER(thm.description) LIKE '$matchString%' OR LOWER(thm.description) LIKE '%$matchString'";

        $sql = "SELECT comp.id, MAX(comp.unit_id), comp.msm_id, comp.table_id, theorem.caption, theorem.textcaption, theorem.string_id, theorem.comment_type, theorem.description
                FROM (SELECT * 
                     FROM mdl_msm_compositor c1
                     WHERE c1.table_id = $tableID->id" . "$msmString)
                AS comp INNER JOIN
                    (SELECT * 
                     FROM mdl_msm_theorem thm
                     $whereClause)
                AS theorem
                ON comp.unit_id = theorem.id                
                GROUP BY comp.unit_id";

        $records = $DB->get_records_sql($sql);
    }
    else if ($fieldType == "all")
    {
        $whereClause = "WHERE LOWER(thm.textcaption) LIKE '%$matchString%' OR LOWER(thm.caption) LIKE '%$matchString%' OR LOWER(thm.description) LIKE '%$matchString%'
                        OR LOWER(thm.textcaption) LIKE '$matchString%' OR LOWER(thm.caption) LIKE '$matchString%' OR LOWER(thm.description) LIKE '$matchString%'
                        OR LOWER(thm.textcaption) LIKE '%$matchString' OR LOWER(thm.caption) LIKE '%$matchString' OR LOWER(thm.description) LIKE '%$matchString'";

        $sql = "SELECT comp.id, MAX(comp.unit_id), comp.msm_id, comp.table_id, theorem.caption, theorem.textcaption, theorem.string_id, theorem.theorem_type, theorem.description
                FROM (SELECT * 
                     FROM mdl_msm_compositor c1
                     WHERE c1.table_id = $tableID->id" . "$msmString)
                AS comp INNER JOIN
                    (SELECT * 
                     FROM mdl_msm_theorem thm
                     $whereClause)
                AS theorem
                ON comp.unit_id = theorem.id                
                GROUP BY comp.unit_id";

        $records = $DB->get_records_sql($sql);

        $found = false;

        $theoremSql = "SELECT * 
                        FROM mdl_msm_compositor c1
                        WHERE c1.table_id = $tableID->id" . "$msmString";

        // eliminate records that are already in records array
        foreach ($records as $rec)
        {
            $theoremSql .= " AND c1.id <> $rec->id";
        }

        $theoremRecords = $DB->get_records_sql($theoremSql);

        foreach ($theoremRecords as $theoremrec)
        {
            $statementTheorems = $DB->get_records("msm_compositor", array("parent_id" => $theoremrec->id, "table_id" => $statementTheoremTable->id), "prev_sibling_id");

            foreach ($statementTheorems as $statement)
            {
                $stateUnitRecord = $DB->get_record("msm_statement_theorem", array("id" => $statement->unit_id));

                if (strpos($stateUnitRecord->statement_content, $matchString) !== false)
                {
                    $found = true;
                    break;
                }
                else
                {
                    $partTheorems = $DB->get_records("msm_compositor", array("parent_id" => $statement->id, "table_id" => $partTheoremTable->id), "prev_sibling_id");

                    foreach ($partTheorems as $part)
                    {
                        $partRecord = $DB->get_record("msm_part_theorem", array("id" => $part->unit_id));

                        if (strpos($partRecord->part_content, $matchString) !== false)
                        {
                            $found = true;
                            break 2;
                        }
                        else if (strpos($partRecord->caption, $matchString) !== false)
                        {
                            $found = true;
                            break 2;
                        }
                    }
                }
            }

            if ($found)
            {
                $joinedsql = "SELECT comp.id, MAX(comp.unit_id), comp.msm_id, comp.table_id, theorem.caption, theorem.textcaption, theorem.string_id, theorem.theorem_type, theorem.description
                FROM (SELECT * 
                     FROM mdl_msm_compositor c1
                     WHERE c1.id = $theoremrec->id)
                AS comp INNER JOIN
                    (SELECT * 
                     FROM mdl_msm_theorem thm
                     WHERE thm.id = $theoremrec->unit_id)
                AS theorem
                ON comp.unit_id = theorem.id                
                GROUP BY comp.unit_id";

                $joineRec = $DB->get_record_sql($joinedsql);
                array_push($records, $joineRec);
            }
        }
    }
}

//else if ($dbType == "unit") // complication --> when searching through unit, need to gather all unit's children and search through there.
//{
//    
//}

if (empty($records))
{
    if (!empty($sql))
    {
        $records = $DB->get_records_sql($sql);
    }
    else
    {
        echo "error in retrieving records using SQL queries";
    }
}

$html = displaySearchResult($records, $tableID);

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
        else if ($tableRecords->tablename == "msm_comment")
        {
            if (!empty($rec->comment_type))
            {
                $displayString .= "<td class='msm_search_result_table_cells'>$rec->comment_type</td>";
            }
            else
            {
                $displayString .= "<td class='msm_search_result_table_cells'>Comment</td>";
            }
        }
        else if ($tableRecords->tablename == "msm_theorem")
        {
            if (!empty($rec->theorem_type))
            {
                $displayString .= "<td class='msm_search_result_table_cells'>$rec->theorem_type</td>";
            }
            else
            {
                $displayString .= "<td class='msm_search_result_table_cells'>Theorem</td>";
            }
        }
        $displayString .= "<td class='msm_search_result_table_cells'>$rec->caption</td>";

        if (($tableRecords->tablename == "msm_def") || ($tableRecords->tablename == "msm_comment"))
        {
            $displaySubordinate = displaySearchSubordinate($rec);

            $content = preg_replace("/<math.array(.*?)>/", "<table$1>", $rec->content);
            $content = preg_replace("/<\/math.array>/", "</table>", $content);

            $displayString .= "<td class='msm_search_result_table_cells'>" . $content . $displaySubordinate . "</td>";
        }
        else if ($tableRecords->tablename == "msm_theorem")
        {
            $theoremContent = displaySearchTheorem($rec);
            $displayString .= "<td class='msm_search_result_table_cells'>" . $theoremContent . "</td>";
        }

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

function displaySearchTheorem($searchTheoremRec)
{
    $content = '';

    $theorem = new EditorTheorem();
    $theorem->loadData($searchTheoremRec->id);

    foreach ($theorem->contents as $statement)
    {
        $content .= $statement->displayPreview($statement->id);
    }

    $newcontent = preg_replace("/<math.array(.*?)>/", "<table$1>", $content);
    $newcontent = preg_replace("/<\/math.array>/", "</table>", $newcontent);

    return $newcontent;
}

?>
