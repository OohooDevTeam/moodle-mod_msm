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
 * Library of interface functions and constants for module msm
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the msm specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod
 * @subpackage msm
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/** example constant */
//define('NEWMODULE_ULTIMATE_ANSWER', 42);
////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function msm_supports($feature)
{
    switch ($feature)
    {
        case FEATURE_MOD_INTRO: return false;
        case FEATURE_MOD_ARCHETYPE: return MOD_ARCHETYPE_RESOURCE;
        case FEATURE_GROUPS: return false;
        case FEATURE_GROUPINGS: return false;
        case FEATURE_GROUPMEMBERSONLY: return false;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return false;
        case FEATURE_GRADE_HAS_GRADE: return false;
        case FEATURE_GRADE_OUTCOMES: return false;
        case FEATURE_BACKUP_MOODLE2: return true;
        case FEATURE_SHOW_DESCRIPTION: return true;
        default: return null;
    }
}

/**
 * Saves a new instance of the msm into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $msm An object from the form in mod_form.php
 * @param mod_msm_mod_form $mform
 * @return int The id of the newly inserted msm record
 */
function msm_add_instance(stdClass $msm, mod_msm_mod_form $mform = null)
{
    global $CFG, $DB;

    require_once("$CFG->libdir/resourcelib.php");
    require_once("XMLImporter/Unit.php");
    require_once("XMLImporter/Compositor.php");
    require_once("XMLImporter/TableCollection.php");

    $msm->timecreated = time();

    $courseid = $msm->course;
    //only one instance in every system
    $sysctx = get_context_instance(CONTEXT_SYSTEM);


    //temporary delete records.
//    $DB->delete_records('msm_unit');
//    $DB->delete_records('msm_def');
//    $DB->delete_records('msm_table_collection');
//    $DB->delete_records('msm');
//    $DB->delete_records('msm_intro');
//    $DB->delete_records('msm_theorem');
//    $DB->delete_records('msm_associate');
//    $DB->delete_records('msm_info');
//    $DB->delete_records('msm_extra_info');
//    $DB->delete_records('msm_theorem');
//    $DB->delete_records('msm_proof');
//    $DB->delete_records('msm_proof_block');
//    $DB->delete_records('msm_statement_theorem');
//    $DB->delete_records('msm_comment');
//    $DB->delete_records('msm_para');
//    $DB->delete_records('msm_subordinate');
//    $DB->delete_records('msm_person');
//    $DB->delete_records('msm_content');
//    $DB->delete_records('msm_index_glossary');
//    $DB->delete_records('msm_index_symbol');
//    $DB->delete_records('msm_table');
//    $DB->delete_records('msm_answer');
//    $DB->delete_records('msm_answer_exercise');
//    $DB->delete_records('msm_answer_showme');
//    $DB->delete_records('msm_packs');
//    $DB->delete_records('msm_problem');
//    $DB->delete_records('msm_exercise');
//    $DB->delete_records('msm_example');
//    $DB->delete_records('msm_showme');
//    $DB->delete_records('msm_quiz');
//    $DB->delete_records('msm_quiz_choice');
//    $DB->delete_records('msm_part_exercise');
//    $DB->delete_records('msm_part_example');
//    $DB->delete_records('msm_part_theorem');
//    $DB->delete_records('msm_ext');
//    $DB->delete_records('msm_approach');
//    $DB->delete_records('msm_solution');
//    $DB->delete_records('msm_statement_example');
//    $DB->delete_records('msm_media');
//    $DB->delete_records('msm_img');
//    $DB->delete_records('msm_pilot');
//    $DB->delete_records('msm_step');
//    $DB->delete_records('msm_external_link');
//    $DB->delete_records('msm_cite');
//    $DB->delete_records('msm_item');
//    $DB->delete_records('msm_compositor');


    if ($msm->id = $DB->insert_record('msm', $msm))
    {
        $table_collection = new TableCollection();
        $talbeid = $table_collection->insertTablename();

        $parser = new DOMDocument();
        //define('parser', $parser);
        @$parser->load(dirname(__FILE__) . '/newXML/LinearAlgebraRn/LinearAlgebraInRn.xml');
        //$parser->load(dirname(__FILE__) . '/newXML/Calculus/Analysis/Analysis.xml');

        $unit = new Unit(dirname(__FILE__) . '/newXML/LinearAlgebraRn/', $parser);
        //$unit = new Unit(dirname(__FILE__) . '/newXML/Calculus/Analysis/', $parser);
        $position = 1;
       
        $unit->loadFromXml($parser->documentElement, $position);
        
        
        
//        $unit->saveIntoDb($unit->position, $msm->id);
        
        
        
        //inserting the top element unit
//        $data = new stdClass();
//        $data->unit_id = $unit->id;
//        $data->table_id = $DB->get_record('msm_table_collection', array('tablename'=>'msm_unit'))->id;
//        $data->parent_id = null;
//        $data->prev_sibling_id = null;
//        
//        $id = $DB->insert_record('msm_compositor', $data);
//
//        $compositor = new Compositor();
//        $compositor->loadFromUnit($unit);
    }

//    echo "done";
//    die;
    # You may have to add extra stuff in here #
    return $msm->id;
}

/**
 * Updates an instance of the msm in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $msm An object from the form in mod_form.php
 * @param mod_msm_mod_form $mform
 * @return boolean Success/Fail
 */
function msm_update_instance(stdClass $msm, mod_msm_mod_form $mform = null)
{
    global $CFG, $DB;
//
//    require_once("$CFG->libdir/resourcelib.php");
//    require_once("XMLImporter/Unit.php");

    $msm->timemodified = time();
    $msm->id = $msm->instance;

//    $tablenames = $DB->get_record('msm_table_collection', array('tablename' => '*'));
//
//    if (!empty($tablenames))
//    {
//
//        foreach ($tablenames as $key => $tablename)
//        {
//            $DB->delete_records($tablename);
//        }
//    }
//
//    $DB->set_field('course_modules', 'instance', $msm->id, array('id' => $courseid));
//    $context = get_context_instance(CONTEXT_MODULE, $courseid);
//
//    if ($msm->id = $DB->insert_record('msm', $msm))
//    {
//        $parser = new DOMDocument();
//        //define('parser', $parser);
//        @$parser->load(dirname(__FILE__) . '/newXML/LinearAlgebraRn/LinearAlgebraInRn.xml');
//        //$parser->load(dirname(__FILE__).'/../xml/Calculus/Calculus.xml');
//
//        $unit = new Unit(dirname(__FILE__) . '/newXML/LinearAlgebraRn/', $parser);
//        //$book = new Book(dirname(__FILE__).'/../xml/Calculus/', $parser);
//        $unit->loadFromXml($parser->documentElement);
//
//
//        $id = $unit->saveIntoDb();
//        die;
//    }

    return $DB->update_record('msm', $msm);
}

/**
 * Removes an instance of the msm from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function msm_delete_instance($id)
{
    global $DB;

    if (!$msm = $DB->get_record('msm', array('id' => $id)))
    {
        return false;
    }

    $DB->delete_records('msm', array('id' => $msm->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function msm_user_outline($course, $user, $mod, $msm)
{

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $msm the module instance record
 * @return void, is supposed to echp directly
 */
function msm_user_complete($course, $user, $mod, $msm)
{
    
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in msm activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function msm_print_recent_activity($course, $viewfullnames, $timestart)
{
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link msm_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function msm_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid = 0, $groupid = 0)
{
    
}

/**
 * Prints single activity item prepared by {@see msm_get_recent_mod_activity()}

 * @return void
 */
function msm_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames)
{
    
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 * */
function msm_cron()
{
    return true;
}

/**
 * Returns an array of users who are participanting in this msm
 *
 * Must return an array of users who are participants for a given instance
 * of msm. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $msmid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function msm_get_participants($msmid)
{
    return false;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function msm_get_extra_capabilities()
{
    return array();
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of msm?
 *
 * This function returns if a scale is being used by one msm
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $msmid ID of an instance of this module
 * @return bool true if the scale is used by the given msm instance
 */
function msm_scale_used($msmid, $scaleid)
{
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('msm', array('id' => $msmid, 'grade' => -$scaleid)))
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of msm.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean true if the scale is used by any msm instance
 */
function msm_scale_used_anywhere($scaleid)
{
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('msm', array('grade' => -$scaleid)))
    {
        return true;
    }
    else
    {
        return false;
    }
}

/**
 * Creates or updates grade item for the give msm instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $msm instance object with extra cmidnumber and modname property
 * @return void
 */
function msm_grade_item_update(stdClass $msm)
{
    global $CFG;
    require_once($CFG->libdir . '/gradelib.php');

    /** @example */
    $item = array();
    $item['itemname'] = clean_param($msm->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax'] = $msm->grade;
    $item['grademin'] = 0;

    grade_update('mod/msm', $msm->course, 'mod', 'msm', $msm->id, 0, null, $item);
}

/**
 * Update msm grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $msm instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function msm_update_grades(stdClass $msm, $userid = 0)
{
    global $CFG, $DB;
    require_once($CFG->libdir . '/gradelib.php');

    /** @example */
    $grades = array(); // populate array of grade objects indexed by userid

    grade_update('mod/msm', $msm->course, 'mod', 'msm', $msm->id, 0, $grades);
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function msm_get_file_areas($course, $cm, $context)
{
    return array();
}

/**
 * Serves the files from the msm file areas
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return void this should never return to the caller
 */
function msm_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload)
{
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE)
    {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    send_file_not_found();
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding msm nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the msm module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function msm_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm)
{
    
}

/**
 * Extends the settings navigation with the msm settings
 *
 * This function is called when the context for the page is a msm module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $msmnode {@link navigation_node}
 */
function msm_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $msmnode = null)
{
    
}
