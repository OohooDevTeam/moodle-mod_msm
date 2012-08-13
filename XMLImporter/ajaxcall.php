<?php

/*
 * This php file is called via AJAX call in the jshowoff javascript file and it calls a function in Compositor class to 
 * create/update the stack with all the record IDs needed for the program to function.
 */

require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');
require_once('Compositor.php');

global $DB, $PAGE, $CFG;

$comp = new Compositor();

$string = $_POST["stackstring"];

$content = $comp->loadAndDisplay($string);

echo $content;
?>
