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

$symbolfilename = $CFG->dataroot. '/cache/MSM/' . trim($ids[0]) . '-' . trim($ids[1]) . '-msm_symbolindex.html';
if (file_exists($symbolfilename))
{
    $symbolfile = fopen($symbolfilename, 'r');
    $content .= fread($symbolfile, filesize($symbolfilename));
    fclose($symbolfile);
}
else
{
    echo "file " . $symbolfilename . "does not exist.";
}

echo $content;
?>