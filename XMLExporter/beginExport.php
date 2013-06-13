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
require_once("ExportExternalLink.php");

global $DB, $CFG;

if (isset($_POST["mode"]))
{
    $deletePath = $CFG->dataroot . "/temp/msmtempfiles/";
    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($deletePath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path)
    {
        if ($path->isFile())
        {
            unlink($path->getPathname());
        }
        else
        {
            rmdir($path->getPathname());
        }
    }
    rmdir($deletePath);
    return true;
}

$msm_id = $_POST["msm_id"];

$msm = $DB->get_record('msm', array('id' => $msm_id), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

$unittable = $DB->get_record("msm_table_collection", array("tablename" => "msm_unit"));

$topUnits = $DB->get_records("msm_compositor", array("msm_id" => $msm_id, "table_id" => $unittable->id, "parent_id" => 0, "prev_sibling_id" => 0));

$unitObjects = array();

if (sizeof($topUnits) == 0)
{
    echo json_encode("empty");
    return;
}

foreach ($topUnits as $topUnit)
{
    $unit = new ExportUnit();
    $unit->loadDbData($topUnit->id);
    $unitObjects[] = $unit;
}

$parentDir = $CFG->dataroot . "/temp/msmtempfiles/";
$msmRecord = $DB->get_record("msm", array("id" => $msm_id));

$msmtrimName = preg_replace("/\s+/", '', $msmRecord->name);
$CompDir = "$parentDir/$msmtrimName$msmRecord->id/";

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
            echo json_encode("error");
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
                echo json_encode("error");
            }
        }
    }
    else
    {
        echo json_encode("error");
    }
}
zipXmlFiles($parentDir, "$msmtrimName$msmRecord->id");

$fs = get_file_storage();

$filename = $msmtrimName . $msmRecord->id . ".zip";

$existingfile = $fs->get_file($context->id, 'mod_msm', 'editor', $msm->id, "/", $filename);

if ($existingfile)
{
    $existingfile->delete();
//    $fs->delete_area_files($context->id, 'mod_msm', 'editor', $existingfile->get_itemid());
}

$file_record = array('contextid' => $context->id, 'component' => 'mod_msm', 'filearea' => 'editor',
    'itemid' => $msm->id, 'filepath' => "/", 'filename' => $filename, 'timecreated' => time(), 'timemodified' => time());

$pathname = str_replace("/", "\\", $parentDir . $filename);
$servedfile = $fs->create_file_from_pathname($file_record, $pathname);

$url = "{$CFG->wwwroot}/pluginfile.php/{$servedfile->get_contextid()}/mod_msm/editor";
$filename = $servedfile->get_filename();
$fileurl = $url . $servedfile->get_filepath() . $servedfile->get_itemid() . '/' . $filename;
$downloadlink = html_writer::link($fileurl, "Click here to download $filename");

echo json_encode($downloadlink);

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
                $fileInfo = explode(".", $filename);

                $ext = $fileInfo[sizeof($fileInfo) - 1];

                if (($ext == "jpg") || ($ext == "png") || ($ext == "gif") || ($ext == "jpeg") || ($ext == "bmp"))
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
                    continue;
                }
            }
            else
            {
                if (mkdir($picFilePath))
                {
                    $fileInfo = explode(".", $filename);

                    $ext = $fileInfo[sizeof($fileInfo) - 1];

                    if (($ext == "jpg") || ($ext == "png") || ($ext == "gif") || ($ext == "jpeg") || ($ext == "bmp"))
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
                        continue;
                    }
                }
                else
                {
                    echo json_encode("error");
                }
            }
        }
    }
}

function exportToFile($parentPath, $unitObj, $msmid, $isTop)
{
    $topunitDocument = $unitObj->exportData();
    $filename = null;

    $trimmedTag = preg_replace('/\s+/', '', $unitObj->unittag);
    if (!empty($unitObj->shortname))
    {
        $unitname = preg_replace('/\s+/', '', $unitObj->shortname);
    }

    if ($unitObj->standalone == "true")
    {
        $directoryname = $parentPath . "standalones/";
        if (file_exists($directoryname))
        {
            if (!empty($unitObj->shortname))
            {
                $filename = "$directoryname$trimmedTag$unitObj->compid-$unitname.xml";
            }
            else
            {
                $filename = "$directoryname$trimmedTag$unitObj->compid-$unitObj->id.xml"; // default if no name is given --> id from msm_unit
            }
        }
        else
        {
            if (mkdir($directoryname))
            {
                if (!empty($unitObj->shortname))
                {
                    $filename = "$directoryname$trimmedTag$unitObj->compid-$unitname.xml";
                }
                else
                {
                    $filename = "$directoryname$trimmedTag$unitObj->compid-$unitObj->id.xml"; // default if no name is given --> id from msm_unit
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
                $filename = "$parentPath$trimmedTag$unitObj->compid-$unitname.xml";
            }
            else
            {
                $filename = "$parentPath$trimmedTag$unitObj->compid-$unitObj->id.xml"; // default if no name is given --> id from msm_unit
            }
        }
        else
        {
            $filepath = $parentPath . "NestedUnits/";

            if (file_exists($filepath))
            {
                if (!empty($unitObj->shortname))
                {
                    $filename = "$filepath$trimmedTag$unitObj->compid-$unitname.xml";
                }
                else
                {
                    $filename = "$filepath$trimmedTag$unitObj->compid-$unitObj->id.xml"; // default if no name is given --> id from msm_unit
                }
            }
            else
            {
                if (mkdir($filepath))
                {
                    if (!empty($unitObj->shortname))
                    {
                        $filename = "$filepath$trimmedTag$unitObj->compid-$unitname.xml";
                    }
                    else
                    {
                        $filename = "$filepath$trimmedTag$unitObj->compid-$unitObj->id.xml"; // default if no name is given --> id from msm_unit
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

function zipXmlFiles($source, $zipfilename)
{
    $destination = $source . $zipfilename . ".zip";

    if (!extension_loaded('zip') || !file_exists($source))
    {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE))
    {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file)
        {
            $file = str_replace('\\', '/', $file);

            // Ignore "." and ".." folders
            if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..')))
            {
                continue;
            }

            $file = realpath($file);
            $file = str_replace('\\', '/', $file); // realpath function gives pathnames with backslashes instead of forward slashes so replace them for string comparision with $source

            if (is_dir($file) === true)
            {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            }
            else if (is_file($file) === true)
            {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    }
    else if (is_file($source) === true)
    {
        $zip->addFromString(basename($source), file_get_contents($source));
    }
    $zip->close();
}
?>

