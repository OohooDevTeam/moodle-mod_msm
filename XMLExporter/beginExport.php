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

global $DB;

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

foreach ($unitObjects as $unitobject)
{
    exportToFile($unitobject, $msm_id, true);
}

exportAllImages($context, $msm_id);

echo json_encode("success");

function exportAllImages($cntxt, $msmId)
{
    $fs = get_file_storage();

    foreach ($fs->get_area_files($cntxt->id, "mod_msm", "editor") as $file)
    {
        $filename = $file->get_filename();
        $picFilePath = "../testExport/pics/";

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

//
//        if ($file = $fs->get_file_by_hash(sha1($srcEnd)))
//        {
//            
//        }
//    }
}

function exportToFile($unitObj, $msmid, $isTop)
{
    global $DB;

    $topunitDocument = $unitObj->exportData();
    $msmRecord = $DB->get_record("msm", array("id" => $msmid));
    $filename = null;
    $parentDirectory = "../testExport/$msmRecord->name$msmRecord->id/";

    if ($unitObj->standalone == "true")
    {
        if (file_exists($parentDirectory))
        {
            $directoryname = $parentDirectory . "standalones/";
            if (file_exists($directoryname))
            {
                if (!empty($unitObj->shortname))
                {
                    $filename = "$directoryname$unitObj->unittag$unitObj->compid-$unitObj->shortname.xml";
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
                        $filename = "$directoryname$unitObj->unittag$unitObj->compid-$unitObj->shortname.xml";
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
            if (mkdir($parentDirectory))
            {
                $directoryname = $parentDirectory . "standalones/";
                if (file_exists($directoryname))
                {
                    if (!empty($unitObj->shortname))
                    {
                        $filename = "$directoryname$unitObj->unittag$unitObj->compid-$unitObj->shortname.xml";
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
                            $filename = "$directoryname$unitObj->unittag$unitObj->compid-$unitObj->shortname.xml";
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
                echo "error making parent directory";
            }
        }
    }
    else
    {
        if (file_exists($parentDirectory))
        {
            if ($isTop)
            {
                if (!empty($unitObj->shortname))
                {
                    $filename = "$parentDirectory$unitObj->unittag$unitObj->compid-$unitObj->shortname.xml";
                }
                else
                {
                    $filename = "$parentDirectory$unitObj->unittag$unitObj->compid-$unitObj->id.xml"; // default if no name is given --> id from msm_unit
                }
            }
            else
            {
                $filepath = $parentDirectory . "Nested Units/";

                if (file_exists($filepath))
                {
                    if (!empty($unitObj->shortname))
                    {
                        $filename = "$filepath$unitObj->unittag$unitObj->compid-$unitObj->shortname.xml";
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
                            $filename = "$filepath$unitObj->unittag$unitObj->compid-$unitObj->shortname.xml";
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
        else
        {
            if (mkdir($parentDirectory))
            {
                if (!empty($unitObj->shortname))
                {
                    $filename = "$parentDirectory$unitObj->unittag$unitObj->compid-$unitObj->shortname.xml";
                }
                else
                {
                    $filename = "$parentDirectory$unitObj->unittag$unitObj->compid-$unitObj->id.xml"; // default if no name is given --> id from msm_unit
                }
            }
            else
            {
                echo "error making parent directory";
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
        exportToFile($subunit, $msmid, false);
    }
}
?>

