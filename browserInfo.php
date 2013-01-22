<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

echo $_SERVER['HTTP_USER_AGENT'] . "\n\n";

$browser = get_browser(null, true);

print_r($browser);

?>
