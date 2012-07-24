<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once('TestNode.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');

$doc = new DOMDocument;

$testNode = new TestNode();

$doc->loadXML($testNode->buildTree()->saveXML());

$newstack = array();

foreach($testNode->trasverse($doc->documentElement) as $stackElement)
{
    array_push($newstack, $stackElement);
}

$newstack = array_reverse($newstack);
print_object($newstack);
?>
