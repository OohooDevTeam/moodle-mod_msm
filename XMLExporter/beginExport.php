<?php

/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                       **
 * @subpackage  msm                                                       **
 * @name        msm                                                       **
 * @copyright   University of Alberta                                     **
 * @link        http://ualberta.ca                                        **
 * @author      Ga Young Kim                                              **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * *************************************************************************
 */
/**
 * This PHP script is called by AJAX call when user triggers the export button
 * from the navigation menu which starts the process of retrieving the data in the
 * database then create XML document to be exported.  Also this script is triggered by another 
 * AJAX call when user closes the download zip file popup which triggers removal of the 
 * temporary folders in moodledata/tmp folder.  
 * 
 * This file contains methods such as exportAllImages which exports images that are
 * associated with this composition, exportToFile which creates all the diretories
 * and xml files that represents the compositions and zipXmlFiles takes the 
 * msmstempfiles folder and creates a zip file for the user to donwload
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
require_once("ExportReference.php");

global $DB, $CFG;

// AJAX call triggered from the close download popup which is for deleting all temp
// directories and files
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

// this part of the code is triggered when the user clicks the export button in the navigation menu 
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

// create force download
$file_record = array('contextid' => $context->id, 'component' => 'mod_msm', 'filearea' => 'editor',
    'itemid' => $msm->id, 'filepath' => "/", 'filename' => $filename, 'timecreated' => time(), 'timemodified' => time());

$pathname = str_replace("/", "\\", $parentDir . $filename);
$servedfile = $fs->create_file_from_pathname($file_record, $pathname);

$url = "{$CFG->wwwroot}/pluginfile.php/{$servedfile->get_contextid()}/mod_msm/editor";
$filename = $servedfile->get_filename();
$fileurl = $url . $servedfile->get_filepath() . $servedfile->get_itemid() . '/' . $filename;
$downloadlink = html_writer::link($fileurl, "Click here to download $filename");

echo json_encode($downloadlink);

/**
 * This method is called to copy all image files used in the composition to be in pics/ directory
 * to be exported in zip file.  It retrieves the files from moodle file storage area and
 * copy and files into temporary directory pics.
 * 
 * @param string $parentPath            filepath of the parent directory
 * @param Object $cntxt                 moodle context object
 * @param int $msmId                    MSM instance ID
 */
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

/**
 * This method is used to create new XML files for each unit in the composition and for
 * each of the reference materials used.  If the file is reference material then it goes into
 * standalones temporary directory and if it is nested unit that is main part of the composition, then
 * it goes into NestedUnits temporary directory.
 * 
 * @param string $parentPath                filepath of parent directory
 * @param Object $unitObj                   ExportUnit object
 * @param int $msmid                        MSM intance ID
 * @param bool $isTop                       flag to indicate top unit(it needs to be in folder above NestedUnit)
 */
function exportToFile($parentPath, $unitObj, $msmid, $isTop)
{
    $topunitDocument = $unitObj->exportData();
    $filename = null;

    $trimmedTag = preg_replace('/\s+/', '', $unitObj->unittag);
    if (!empty($unitObj->shortname))
    {
        $unitname = preg_replace('/\s+/', '', $unitObj->shortname);
    }

    // dealing with reference materials
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
        if ($isTop) // this unit is top unit which needs to be placed in parent directory of NestedUnits
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
        else  // this unit is nested unit which needs to be placed in NestedUnits
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

/**
 * This file takes the temporary file with name equivalent to MSM instance and
 * compresses the file into a zip file that would be prompted to the user for download.
 * 
 * @param string $source            filepath for parent directory of temporary file 
 * @param string $zipfilename       filename that is created from MSM instance name and database ID
 * @return boolean
 */
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

    // need file path to only contain forward slash
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

