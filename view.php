<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of msm
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 * 
 * *************************************************************************
 * *                              MSM                                     **
 * *************************************************************************
 * @package     mod                                                      **
 * @subpackage  msm                                                      **
 * @name        msm                                                      **
 * @copyright   University of Alberta                                    **
 * @link        http://ualberta.ca                                       **
 * @author      Ga Young Kim                                             **
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later **
 * *************************************************************************
 * ************************************************************************ */
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/XMLImporter/Compositor.php');
//$PAGE->requires->css('/mod/msm/MsmDisplay.css');

global $PAGE, $OUTPUT;

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$m = optional_param('n', 0, PARAM_INT);  // msm instance ID - it should be named as the first character of the module

if ($id)
{
    $cm = get_coursemodule_from_id('msm', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $msm = $DB->get_record('msm', array('id' => $cm->instance), '*', MUST_EXIST);
}
elseif ($m)
{
    $msm = $DB->get_record('msm', array('id' => $m), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);
}
else
{
    error('You must specify a course_module ID or an instance ID');
}

$rootcomp = $DB->get_record('msm_compositor', array('msm_id' => $msm->id), '*', MUST_EXIST);

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, 'msm', 'view', "view.php?id={$cm->id}", $msm->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/msm/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($msm->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

if ($PAGE->user_allowed_editing())
{
    $buttons = '<form method="get" action="' . $CFG->wwwroot . '/course/mod.php"><div>' .
            '<input type="hidden" name="update" value="' . $cm->id . '" />' .
            '<input type="submit" value="' . get_string('updatecomp', 'msm') . '" /></div></form>';
    $PAGE->set_button($buttons);
}
//
//$compositor = new Compositor();
//$compositor = $compositor->loadFromDb($msm->id);
//$compositor = $compositor->loadFromDb(1); // db broken for now due to no update method... later use above method
// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('msm-'.$somevar);
// Output starts here

echo $OUTPUT->header();

echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jquery-1.7.1.min.js'></script>";

echo "<link rel='stylesheet' href='$CFG->wwwroot/mod/msm/development-bundle/themes/ui-lightness/jquery.ui.all.css'/>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/jquery-1.7.1.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/external/jquery.bgiframe-2.1.2.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.core.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.widget.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.mouse.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.draggable.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.position.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.resizable.js'></script>";
echo "<script src='$CFG->wwwroot/mod/msm/development-bundle/ui/jquery.ui.dialog.js'></script>";

// these js files need to be after the development-bundle
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/Splitter.js'></script>";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/MsmDisplay.css'/>";

echo "<link rel='stylesheet' href='$CFG->wwwroot/mod/msm/css/jshowoff.css' type='text/css'/>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jquery.jshowoff.js'></script>";

//echo " <script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jImageMaster/dist/jquery.imagemapster.js'></script>";

echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/popup.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/showRightPage.js'></script>";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/infoopen.js'></script>";
//echo "<script type ='text/javascript' src='$CFG->wwwroot/mod/msm/js/jimagemapster.js'></script>";


//echo "<script type='text/javascript' src='http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML'></script>";

echo "<script type='text/javascript' src='http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML,$CFG->wwwroot/mod/msm/js/Mathjaxconfig.js'></script>";
//echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/Mathjaxconfig.js'></script>";


if ($msm->intro)
{ // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('msm', $msm, $cm->id), 'generalbox mod_introbox', 'msmintro');
}

// Replace the following lines with you own code
echo $OUTPUT->heading($msm->name);

$content = '';
//$content .= "hello";
//$content .= "<div>";
$content .= "<div id = 'MySplitter' padding:10px;>";

$content .= "<div class = 'leftcol' style='min-width: 542px;'>";

$content .= "<div class = 'leftbox'>";
$content .= "<div id='features'>";


$compositor = new Compositor();
$string = '';

// the stack that is created below is in reverse order
$stack = $compositor->makeStack($rootcomp);

$stack = array_reverse($stack); // needed to access the contents in proper order

foreach ($stack as $key => $record)
{
    $string .= $record->id . "/" . $record->unit_id . "/" . $record->parent_id . "/" . $record->prev_sibling_id . ",";
}


$content.= $compositor->loadAndDisplay($string, 0);
//print_object($stack);

$content .= "</div>";

$content .= "</div>";
$content .= "<div class='controller'>";
$content .= "</div>";
$content .= "</div>";

$content .= "<div class = 'rightcol' style='min-width: 542px;'>";
$content .= "<div class = 'rightbox'>";
$content .= "<p>";
$content .= "right side!!";
$content .= "</p>";
$content .= "</div>";
$content .= "</div>";

$content .= "</div>";

// need to have it in this order or dialog breaks

// if implementing jImageMapster, need to insert the jquery code in the space between dialogs and jshowoff
$content .= "
    <script type='text/javascript'>
    jQuery(document).ready(function(){       
         $('.dialogs').dialog({
              autoOpen: false,
              width: 'auto'
         }); 

         $('#features').jshowoff({
              autoplay:false,
              links:false                  
         });
         
        $('#MySplitter').splitter();
        
        
    });
    </script>";


// where the display method would go...

echo $OUTPUT->box($content);

// Finish the page
echo $OUTPUT->footer();


