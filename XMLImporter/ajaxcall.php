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
$counter = $_POST["input"];

print_object($string);
print_object($counter);

$content = $comp->loadAndDisplay($string, $counter);

//echo $content;

?>
