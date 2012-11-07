<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/mod/msm/authoring_form.php');
require_once($CFG->dirroot . '/mod/msm/lib.php');


//$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$m = optional_param('mid', 0, PARAM_INT);  // msm instance ID - it should be named as the first character of the module
//if ($id)
//{
//    $cm = get_coursemodule_from_id('msm', $id, 0, false, MUST_EXIST);
//    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
//    $msm = $DB->get_record('msm', array('id' => $cm->instance), '*', MUST_EXIST);
//}
if ($m)
{
    $msm = $DB->get_record('msm', array('id' => $m), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);
}
else
{
    error('You must specify a course_module ID');
}

//$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

require_login($course->id, true, $cm);

add_to_log($course->id, 'createbook', 'createbook', 'view.php?id=' . $cm->id, $msm->id);
$PAGE->set_url('/mod/msm/authoringTool.php', array('mid' => $msm->id));

echo $OUTPUT->header();

$mform = new mod_msm_authoring_form();
echo $OUTPUT->box($mform->display());

echo $OUTPUT->footer();
?>
