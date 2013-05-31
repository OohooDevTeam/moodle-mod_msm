<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('../../../config.php');
require_once($CFG->dirroot . '/mod/msm/lib.php');
// modified code from stackoverflow http://stackoverflow.com/questions/1334613/how-to-recursively-zip-a-directory-in-php
//function zipXmlFiles($source, $zipfilename)
//{
global $CFG;
// need source and zipfilename
$source = $CFG->dataroot . "/temp/msmtempfiles/";
$zipfilename = $_GET["zipfilename"];
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

if ($zip->close())
{
    $filesize = filesize($destination);
    header('Content-Description: Donwloading XML files');
    header('Content-Type: application/zip');
    header("Content-Length: $filesize");
    header('Content-Disposition: attachment; filename="' . $zipfilename . '.zip"');
    ob_clean();
    flush();
    readfile($destination);
    unlink($destination);
}
//}
?>
