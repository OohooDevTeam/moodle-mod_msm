<?php
/**
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                       **
 * @subpackage  msm                                                       **
 * @name        msm                                                       **
 * @copyright   University of Alberta                                     **
 * @link        http://ualberta.ca                                        **
 * @author      Ga Young Kim                                              **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later  **
 * *************************************************************************
 * ************************************************************************* */

/**
 * This script is called by the navMenu.js file by an AJAX call when the user clicks the author 
 * index menu in view.php.  This script finds the HTML file with all the HTML code for creating the
 * index tree using jquery jsTree plugin.
 */
require_once('Unit.php');
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.php');
require_once(dirname(dirname(__FILE__)) . '/lib.php');

global $CFG;

$string = $_POST["moduleinfo"];

$ids = explode(',', $string);

$content = '';

$authorfilename =  $CFG->dataroot. '/cache/MSM/' . trim($ids[0]) . '-' . trim($ids[1]) . '-msm_authorindex.html';
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
