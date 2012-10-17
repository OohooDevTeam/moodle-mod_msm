<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('Unit.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');

$string = $_POST["moduleinfo"];

$ids = explode(',', $string);

$content = '';

$glossryfilename = '../' . trim($ids[0]) . '-' . trim($ids[1]) . '-msm_glossaryindex.html';
if (file_exists($glossryfilename))
{
    $glossaryfile = fopen($glossryfilename, 'r');
    $content .= fread($glossaryfile, filesize($glossryfilename));
    fclose($glossaryfile);
}
else
{
    echo "file " . $glossryfilename . "does not exist.";
    die;
}

echo $content;
?>
