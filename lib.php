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
    @set_time_limit(60 * 60); // 1 hour should be enough. You can always change this value
    raise_memory_limit(MEMORY_HUGE);

    global $CFG, $DB;

    require_once("$CFG->libdir/resourcelib.php");


    require_once("XMLImporter/Unit.php");
    require_once("XMLImporter/Compositor.php");
    require_once("XMLImporter/TableCollection.php");

    $msm->timecreated = time();
//
    $courseid = $msm->coursemodule;
//    //only one instance in every system
    $sysctx = get_context_instance(CONTEXT_SYSTEM);

    if ($msm->id = $DB->insert_record('msm', $msm))
    {
        $DB->set_field('course_modules', 'instance', $msm->id, array('id' => $courseid));
        $context = context_module::instance($courseid);

        // matching all the property defining the unit names
        $match = '/^(top|child)level(\d+)*$/';
        $depth = 0;
        $unitnameIdArray = array();

        foreach ($msm as $property => $value)
        {
            if (preg_match($match, trim($property)))
            {
                $unitNameTableData = new stdClass();
                $unitNameTableData->msmid = $msm->id;
                $unitNameTableData->unitname = $value;
                $unitNameTableData->depth = $depth;
                $unitnameIdArray[$depth] = $DB->insert_record('msm_unit_name', $unitNameTableData);
                $depth++;
            }
            else if (trim($property) == "standalone")
            {
                $unitNameTableData = new stdClass();
                $unitNameTableData->msmid = $msm->id;
                $unitNameTableData->unitname = $value;
                $unitNameTableData->depth = -1;
                $unitnameIdArray[$depth] = $DB->insert_record('msm_unit_name', $unitNameTableData);
            }
            else if (trim($property) == 'additionalChild')
            {
                foreach ($value as $moreChild)
                {
                    if (!empty($moreChild))
                    {
                        $unitNameTableData = new stdClass();
                        $unitNameTableData->msmid = $msm->id;
                        $unitNameTableData->unitname = $moreChild;
                        $unitNameTableData->depth = $depth;
                        $unitnameIdArray[$depth] = $DB->insert_record('msm_unit_name', $unitNameTableData);
                        $depth++;
                    }
                }
            }
        }


        $tableRecords = $DB->count_records('msm_table_collection');

        if ($tableRecords == 0)
        {
            $table_collection = new TableCollection();
            $tableid = $table_collection->insertTablename();
        }

        // importing newly created XML files
        $draftitemid = file_get_submitted_draft_itemid('importElement');

        if (!empty($draftitemid))
        {
            file_save_draft_area_files($draftitemid, $context->id, 'mod_msm', 'editor', $msm->importElement);            
            $fs = get_file_storage();
            $files = $fs->get_area_files($context->id, "mod_msm", 'editor', $draftitemid);

            if (sizeof($files) > 0)
            {
                foreach ($files as $file)
                {
                    $filename = $file->get_filename();
                    if ($filename != ".")
                    {
                        $temppath = "$CFG->dataroot/temp/msmtempfiles/";

                        if (!file_exists($temppath))
                        {
                            mkdir($temppath);
                        }

                        $fileInfo = explode(".", $filename);
                        if ($fileInfo[sizeof($fileInfo) - 1] == "zip")
                        {
                            $path = $file->copy_content_to_temp($msm->name . $msm->id);

                            $zip = new ZipArchive();
                            if ($zip->open($path))
                            {
                                $zip->extractTo($temppath);
                                $zip->close();

                                unlink($path);
                                $file->delete();
                            }
                            if (file_exists("$CFG->dataroot/temp/$msm->name$msm->id/"))
                            {
                                rmdir("$CFG->dataroot/temp/$msm->name$msm->id");
                            }
                        }
                    }
                }

                $newXMLfilepath = $CFG->dataroot . "/temp/msmtempfiles/";
                $msmdir = scandir($newXMLfilepath);
                $msmdirpath = $newXMLfilepath . $msmdir[2];

                $xmlfiles = scandir($msmdirpath . "/");

                $xmlpath = $msmdirpath . "/";
                foreach ($xmlfiles as $xmlfile)
                {
                    $xmlfilepath = $xmlpath . $xmlfile;
                    if (is_file($xmlfilepath))
                    {
                        $xmlpath .= $xmlfile;
                        break;
                    }
                }

                $newXMLparser = new DOMDocument();
                $newXMLparser->load($xmlpath);

                $importedunit = new Unit($msmdirpath, $newXMLparser);
                $position = 1;
                $importedunit->loadFromXml($newXMLparser->documentElement, $position);
                
                $importedunit->updateUnitNames($unitnameIdArray, $msm->id, 0);

                $importedunit->saveIntoDb($importedunit->position, $msm->id);
            }
        }

        /********************************************************************************************
         *                         Part of Code to import the legacy material                       *
        *********************************************************************************************/
        
        // uncomment if want to import legacy material and if want to import
        // other legacy material, just change the pathing
        
//        $parser = new DOMDocument();
//        
//        @$parser->load(dirname(__FILE__) . '/newXML/LinearAlgebraRn/LinearAlgebraInRn.xml');
////        @$parser->load(dirname(__FILE__) . '/newXML/Calculus/Analysis/Analysis.xml');
//
//        $unit = new Unit(dirname(__FILE__) . '/newXML/LinearAlgebraRn/', $parser);
////        $unit = new Unit(dirname(__FILE__) . '/newXML/Calculus/Analysis/', $parser);
//        $position = 1;
//
//        $unit->loadFromXml($parser->documentElement, $position);
//        
//        $unit->saveIntoDb($unit->position, $msm->id);
        
         // *********************************************************************************************

        $deletePath = $CFG->dataroot . "/temp/msmtempfiles/";
        if (file_exists($deletePath))
        {
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($deletePath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path)
            {
                if ($path->isFile())
                {
                    unlink($path->getPathname());
                }
                else if(is_dir($path))
                {
                    $existingFiles = scandir($path);
                    
                    if(sizeof($existingFiles)<=2)
                    {
                        rmdir($path);
                    }
                    else
                    {
                        continue;
                    }
                }
            }
            rmdir($deletePath);
        }
    }
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

    $msm->timemodified = time();
    $msm->id = $msm->instance;

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
    
    $CompRecords = $DB->get_records("msm_compositor", array("msm_id"=>$id), "table_id");
    
    foreach($CompRecords as $record)
    {
        $tablename = $DB->get_record("msm_table_collection", array("id"=>$record->table_id));
        $DB->delete_records($tablename->tablename, array("id"=>$record->unit_id));
    }
    $DB->delete_records("msm_compositor", array("msm_id"=>$id));

    $DB->delete_records('msm', array('id' => $id));

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
    global $CFG, $DB;

    require_once("XMLImporter/Unit.php");
    
    $path = "$CFG->dataroot/cache/MSM";
    
    if(!file_exists($path))
    {
        mkdir($path);
    }

    $msmRecords = $DB->get_records('msm');

    foreach ($msmRecords as $msm)
    {
        $symbol = new MathIndex();
        $symboldata = $symbol->makeSymbolPanel($msm->id);
        $courseid = $DB->get_record('msm', array('id' => $msm->id))->course;
        $filename = $path . "/" . $courseid . "-" . $msm->id . "-msm_symbolindex.html";

        $symbolfile = fopen($filename, 'w') or die('Cannot open file: ' . $filename);
        fwrite($symbolfile, $symboldata);

        fclose($symbolfile);
        unset($symbol);

        $glossary = new MathIndex();
        $glossarydata = $glossary->makeGlossaryPanel($msm->id);
        $filename = $path . "/" . $courseid . "-" . $msm->id . "-msm_glossaryindex.html";

        $glossaryfile = fopen($filename, 'w') or die('Cannot open file: ' . $filename);
        fwrite($glossaryfile, $glossarydata);

        fclose($glossaryfile);
        unset($glossary);

        $author = new MathIndex();
        $authordata = $author->makeAuthorPanel($msm->id);
        $filename = $path . "/" . $courseid . "-" . $msm->id . "-msm_authorindex.html";

        $authorfile = fopen($filename, 'w') or die('Cannot open file: ' . $filename);
        fwrite($authorfile, $authordata);

        fclose($authorfile);
        unset($author);
    }
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
    global $CFG, $DB;

    //The following code is for security
    require_course_login($course, true, $cm);

    if ($context->contextlevel != CONTEXT_MODULE)
    {
        return false;
    }

    $fileareas = array('editor');
    if (!in_array($filearea, $fileareas))
    {
        return false;
    }

    $msmid = (int) array_shift($args);

    //Now gather file information
    $fs = get_file_storage();
    $relativepath = implode('/', $args);
    $fullpath = "/$context->id/mod_msm/$filearea/$msmid/$relativepath";

    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory())
    {
        return false;
    }

    // finally send the file
    send_stored_file($file, 0, 0, $forcedownload);
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
    global $DB, $PAGE;
    
    $keys = $msmnode->get_children_key_list();
    $beforekey = null;
    $i = array_search('modedit', $keys);
    if ($i === false and array_key_exists(0, $keys)) {
        $beforekey = $keys[0];
    } else if (array_key_exists($i + 1, $keys)) {
        $beforekey = $keys[$i + 1];
    }
    
      $msm = $DB->get_record('msm', array('id' => $PAGE->cm->instance), '*', MUST_EXIST);
    
     if (has_capability('mod/msm:view', $PAGE->cm->context)) {
        $node = navigation_node::create(get_string('editmsm', 'msm'),
                new moodle_url('/mod/msm/authoringTool.php', array('mid'=>$msm->id)),
                navigation_node::TYPE_SETTING);
        $msmnode->add_node($node, $beforekey);
    }
}
