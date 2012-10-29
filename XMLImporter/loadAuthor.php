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

$authorfilename = '../' . trim($ids[0]) . '-' . trim($ids[1]) . '-msm_authorindex.html';
if (file_exists($authorfilename))
{
    $authorfile = fopen($authorfilename, 'r');
    $content .= fread($authorfile, filesize($authorfilename));
    fclose($authorfile);
}
else
{
    echo "file " . $authorfilename . "does not exist.";
}

echo $content;
?>
