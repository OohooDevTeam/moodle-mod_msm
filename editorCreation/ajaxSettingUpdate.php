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
$prevstring = $_POST["prevstackstring"];
$currentString = $_POST["currentvalue"];
$functionstring = $_POST["functionname"];

$content = $comp->loadAndDisplay($prevstring, $string, $currentString, $functionstring);

echo $content;

?>
