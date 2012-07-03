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
 * @package    mod
 * @subpackage msm
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
//require_once(dirname(__FILE__) . '/XMLImporter/Unit.php');

//$PAGE->requires->css('/mod/msm/MsmDisplay.css');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$m  = optional_param('n', 0, PARAM_INT);  // msm instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('msm', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $msm  = $DB->get_record('msm', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($m) {
    $msm  = $DB->get_record('msm', array('id' => $m), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, 'msm', 'view', "view.php?id={$cm->id}", $msm->name, $cm->id);

/// Print the page header

$PAGE->set_url('/mod/msm/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($msm->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

if ($PAGE->user_allowed_editing()) {
    $buttons = '<form method="get" action="' . $CFG->wwwroot . '/course/mod.php"><div>' .
               '<input type="hidden" name="update" value="' . $cm->id . '" />' .
               '<input type="submit" value="' . get_string('updatecomp', 'msm') . '" /></div></form>';
    $PAGE->set_button($buttons);
}

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('msm-'.$somevar);

// Output starts here
echo $OUTPUT->header();

echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/jquery-1.7.1.min.js'></script>\n";
echo "<script type='text/javascript' src='$CFG->wwwroot/mod/msm/js/Splitter.js'></script>\n";
echo "<link rel='stylesheet' type='text/css' href='$CFG->wwwroot/mod/msm/css/MsmDisplay.css'/>\n";


if ($msm->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('msm', $msm, $cm->id), 'generalbox mod_introbox', 'msmintro');
}

// Replace the following lines with you own code
echo $OUTPUT->heading($msm->name);

$content = '';
//$content .= "hello";
//$content .= "<div>";
$content .= "<div id = 'MySplitter'>";
$content .= "<div class = 'leftcol'>";
$content .= "left side!!";
$content .= "</div>";
$content .= "<div class = 'rightcol'>";
$content .= "right side!!";
$content .= "</div>";
$content .= "</div>";

$content .= "
    <script type='text/javascript'>
    jQuery(document).ready(function(){
        $('#MySplitter').splitter();
    });
    </script>";


// where the display method would go...

echo $OUTPUT->box($content);

// Finish the page
echo $OUTPUT->footer();


