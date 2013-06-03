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

$string['createbook'] = "Create Book";
$string['createcontent'] = "Create Composition";
$string['invalidcoursemoduleid'] = "Invalid Course Module ID";
$string['modulename'] = 'msm';
$string['modulenameplural'] = 'msms';
$string['modulename_help'] = 'The msm module enables the teachers to create their own electronic teaching materials (eg. textbook, lecture...etc) and also allowing the students to learn in interactive manner.  This module can contain media elements as well as interactive exercises, tests and quizes and have note taking tools for the students.
A msm module may be used

* To create/display reading material for specified course
* As a staff departmental handbook
* To create evaluating materials for students
* For students to create notes';
$string['msmfieldset'] = 'Custom example fieldset';
$string['msmname'] = 'Title of Composition';
$string['msmname_help'] = 'This is the content of the help tooltip associated with the msmname field. Markdown syntax is supported.';
$string['msm'] = 'Math Suite for Moodle';
$string['pluginadministration'] = 'msm administration';
$string['pluginname'] = 'msm';
$string['updatecomp'] = 'Update this composition';
$string['msm:addinstance'] = 'MSM Creation Tool';
$string['msmtype'] = 'Type of Composition';
$string['msmtype_help'] = 'Replace with explanation of each types...';

$string['levelsetting'] = 'Names for the Hierarchical Elements';
$string['levelsetting_help'] = 'This setting specifies the name of each hierarchical elements that will make up this composition.
    The values stated in the input fields are the default value for each type in the select menu above.';
$string['toplevel'] = 'Name of top level container';
$string['standalone'] = 'Name of reference/stand-alone materials';
$string['childlevel1'] = 'Name of first child element';
$string['childlevel2'] = 'Name of second child element';
$string['childlevel3'] = 'Name of third child element';
$string['childlevel4'] = 'Name of fourth child element';
$string['moreChild'] = 'Name of additional child element';
$string['addchildbutton'] = '(+) Add more child elements';

$string['msmsubmit'] = ' Save and Proceed ';

$string['import'] = 'Import from Existing XML';
$string['import_help'] = 'Please either upload the zip file produced from export from MSM editor or create a zip file with all the XML files.  If you are making your own zip file, please make sure that the file containeing
                          the top XML element(ie. < unit >) is a directly below the top directory and contains the reference tags that links each child XML files to the top XML file.';
$string['importElement'] = 'Please specify the XML file to import.';


