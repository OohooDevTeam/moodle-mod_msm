<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('../../../config.php');
require_once($CFG->dirroot . '/mod/msm/lib.php');
require_once("ExportElement.php");
require_once("ExportUnit.php");
require_once("ExportDefinition.php");
require_once("ExportComment.php");
require_once("ExportTheorem.php");
require_once("ExportIntro.php");
require_once("ExportBlock.php");
require_once("ExportExtraInfo.php");
require_once("ExportAssociate.php");
require_once("ExportStatementTheorem.php");
require_once("ExportPara.php");
require_once("ExportInContent.php");
require_once("ExportTable.php");
require_once("ExportPartTheorem.php");
require_once("ExportInfo.php");
require_once("ExportSubordinate.php");
require_once("ExportMedia.php");
require_once("ExportImage.php");

global $DB, $CFG;

$msm_id = $_POST["msm_id"];

$msm = $DB->get_record('msm', array('id' => $msm_id), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

$unittable = $DB->get_record("msm_table_collection", array("tablename" => "msm_unit"));

$topUnits = $DB->get_records("msm_compositor", array("msm_id" => $msm_id, "table_id" => $unittable->id, "parent_id" => 0, "prev_sibling_id" => 0));

$unitObjects = array();

foreach ($topUnits as $topUnit)
{
    $unit = new ExportUnit();
    $unit->loadDbData($topUnit->id);
    $unitObjects[] = $unit;
}

$parentDir = $CFG->dataroot . "/temp/msmtempfiles/";
$msmRecord = $DB->get_record("msm", array("id" => $msm_id));
$CompDir = "$parentDir/$msmRecord->name$msmRecord->id/";

if (file_exists($parentDir))
{
    if (file_exists($CompDir))
    {
        foreach ($unitObjects as $unitobject)
        {
            exportToFile($CompDir, $unitobject, $msm_id, true);
        }

        exportAllImages($CompDir, $context, $msm_id);
    }
    else
    {
        if (mkdir($CompDir))
        {
            foreach ($unitObjects as $unitobject)
            {
                exportToFile($CompDir, $unitobject, $msm_id, true);
            }

            exportAllImages($CompDir, $context, $msm_id);
        }
        else
        {
            echo "error with making comp folder in temp directory";
        }
    }
}
else
{
    if (mkdir($parentDir))
    {
        if (file_exists($CompDir))
        {
            foreach ($unitObjects as $unitobject)
            {
                exportToFile($CompDir, $unitobject, $msm_id, true);
            }

            exportAllImages($CompDir, $context, $msm_id);
        }
        else
        {
            if (mkdir($CompDir))
            {
                foreach ($unitObjects as $unitobject)
                {
                    exportToFile($CompDir, $unitobject, $msm_id, true);
                }

                exportAllImages($CompDir, $context, $msm_id);
            }
            else
            {
                echo "error with making comp folder in temp directory";
            }
        }
    }
    else
    {
        echo "error making msmtemp folder in temp directory";
    }
}

$downloadParam = array();
//$downloadParam["source"] = $parentDir;
$downloadParam["zipfilename"] = "$msmRecord->name$msmRecord->id";

echo json_encode($downloadParam);

//echo json_encode(zipXmlFiles($parentDir, "$msmRecord->name$msmRecord->id"));



function exportAllImages($parentPath, $cntxt, $msmId)
{
    $fs = get_file_storage();

    foreach ($fs->get_area_files($cntxt->id, "mod_msm", "editor") as $file)
    {
        $filename = $file->get_filename();
        $picFilePath = "$parentPath/pics/";

        if ($filename != ".")
        {
            if (file_exists($picFilePath))
            {
                $newpath = $picFilePath . $filename;

                if ($imgfile = fopen($newpath, "w"))
                {
                    fwrite($imgfile, $file->get_content());
                    fclose($imgfile);
                }
                else
                {
                    echo json_encode("error");
                }
            }
            else
            {
                if (mkdir($picFilePath))
                {
                    $newpath = $picFilePath . $filename;

                    if ($imgfile = fopen($newpath, "w"))
                    {
                        fwrite($imgfile, $file->get_content());
                        fclose($imgfile);
                    }
                    else
                    {
                        echo json_encode("error");
                    }
                }
                else
                {
                    echo "error at pic directory creation";
                }
            }
        }
    }
}

function exportToFile($parentPath, $unitObj, $msmid, $isTop)
{
    $topunitDocument = $unitObj->exportData();
    $filename = null;

    if ($unitObj->standalone == "true")
    {
        $directoryname = $parentPath . "standalones/";
        if (file_exists($directoryname))
        {
            if (!empty($unitObj->shortname))
            {
                $unitname = preg_replace('/\s+/', '', $unitObj->shortname);
                $filename = "$directoryname$unitObj->unittag$unitObj->compid-$unitname.xml";
            }
            else
            {
                $filename = "$directoryname$unitObj->unittag$unitObj->compid-$unitObj->id.xml"; // default if no name is given --> id from msm_unit
            }
        }
        else
        {
            if (mkdir($directoryname))
            {
                if (!empty($unitObj->shortname))
                {
                    $unitname = preg_replace('/\s+/', '', $unitObj->shortname);
                    $filename = "$directoryname$unitObj->unittag$unitObj->compid-$unitname.xml";
                }
                else
                {
                    $filename = "$directoryname$unitObj->unittag$unitObj->compid-$unitObj->id.xml"; // default if no name is given --> id from msm_unit
                }
            }
            else
            {
                echo "error in making the standalone directory!";
            }
        }
    }
    else
    {
        if ($isTop)
        {
            if (!empty($unitObj->shortname))
            {
                $unitname = preg_replace('/\s+/', '', $unitObj->shortname);
                $filename = "$parentPath$unitObj->unittag$unitObj->compid-$unitname.xml";
            }
            else
            {
                $filename = "$parentPath$unitObj->unittag$unitObj->compid-$unitObj->id.xml"; // default if no name is given --> id from msm_unit
            }
        }
        else
        {
            $filepath = $parentPath . "Nested Units/";

            if (file_exists($filepath))
            {
                if (!empty($unitObj->shortname))
                {
                    $unitname = preg_replace('/\s+/', '', $unitObj->shortname);
                    $filename = "$filepath$unitObj->unittag$unitObj->compid-$unitname.xml";
                }
                else
                {
                    $filename = "$filepath$unitObj->unittag$unitObj->compid-$unitObj->id.xml"; // default if no name is given --> id from msm_unit
                }
            }
            else
            {
                if (mkdir($filepath))
                {
                    if (!empty($unitObj->shortname))
                    {
                        $unitname = preg_replace('/\s+/', '', $unitObj->shortname);
                        $filename = "$filepath$unitObj->unittag$unitObj->compid-$unitname.xml";
                    }
                    else
                    {
                        $filename = "$filepath$unitObj->unittag$unitObj->compid-$unitObj->id.xml"; // default if no name is given --> id from msm_unit
                    }
                }
                else
                {
                    echo "error making parent directory";
                }
            }
        }
    }

    if ($xmlfile = fopen($filename, "w"))
    {
        fwrite($xmlfile, $topunitDocument->saveXML());
        fclose($xmlfile);
    }
    else
    {
        echo json_encode("error");
    }

    foreach ($unitObj->unitchildren as $subunit)
    {
        exportToFile($parentPath, $subunit, $msmid, false);
    }
}

?>

