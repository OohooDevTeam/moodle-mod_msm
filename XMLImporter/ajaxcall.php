<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');
require_once('Compositor.php');

global $DB, $PAGE, $CFG;

$comp = new Compositor();

$string = $_POST["stackstring"];
//
//echo "ajax";
//print_object($string);
//echo "string above";
//$displaycontent = $_POST["displaystring"];

$content = $comp->loadAndDisplay($string);
//echo "in ajaxcall";
//print_object($content);

echo $content;
?>
