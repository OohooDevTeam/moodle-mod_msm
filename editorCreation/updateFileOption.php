<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once('../../../config.php');
require_once($CFG->dirroot . '/mod/msm/lib.php');
//require_once($CFG->dirroot . '/repository/lib.php');
require_once("$CFG->libdir/filelib.php");

$options['maxbytes'] = $CFG->maxbytes;

$msmid = $_POST["msmid"];
$msm = $DB->get_record('msm', array('id' => $msmid), '*', MUST_EXIST);
$course = $DB->get_record('course', array('id' => $msm->course), '*', MUST_EXIST);
$cm = get_coursemodule_from_instance('msm', $msm->id, $course->id, false, MUST_EXIST);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

require_login($course, true, $cm);
$PAGE->set_context($context);

$draftitemid = file_get_unused_draft_itemid();

//The options
$fpoptions = array();

$args = new stdClass();
// need these three to filter repositories list
$args->accepted_types = array('web_image');
$args->return_types = (FILE_INTERNAL | FILE_EXTERNAL);
$args->context = $context;
$args->env = 'filepicker';

// advimage plugin
$image_options = initialise_filepicker($args);
$image_options->context = $context->id;
$image_options->client_id = uniqid();
$image_options->maxbytes = $options['maxbytes'];
$image_options->env = 'editor';
$image_options->itemid = $draftitemid;

// moodlemedia plugin
$args->accepted_types = array('video', 'audio');
$media_options = initialise_filepicker($args);
$media_options->context = $context->id;
$media_options->client_id = uniqid();
$media_options->maxbytes = $options['maxbytes'];
$media_options->env = 'editor';
$media_options->itemid = $draftitemid;

// advlink plugin
$args->accepted_types = '*';
$link_options = initialise_filepicker($args);
$link_options->context = $context->id;
$link_options->client_id = uniqid();
$link_options->maxbytes = $options['maxbytes'];
$link_options->env = 'editor';
$link_options->itemid = $draftitemid;

$fpoptions['image'] = $image_options;
$fpoptions['media'] = $media_options;
$fpoptions['link'] = $link_options;

echo json_encode($fpoptions);
?>
