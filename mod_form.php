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
 * The main msm configuration form
 *
 * It uses the standard core Moodle formslib. For more info about them, please
 * visit: http://docs.moodle.org/en/Development:lib/formslib.php
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

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/lib/formslib.php');
//require_once('editorCreation/authoringTool.php');

/**
 * Module instance settings form
 */
class mod_msm_mod_form extends moodleform_mod
{

    /**
     * Defines forms elements
     */
    public function definition()
    {
        global $CFG;

        $mform = $this->_form;

        $config = get_config('msm');

        //-------------------------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are showed

        $mform->addElement('header', 'general', get_string('msm', 'msm'));
//        $mform->addElement('header', 'general', get_string('general', 'form'));
        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('msmname', 'msm'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags))
        {
            $mform->setType('name', PARAM_TEXT);
        }
        else
        {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        // Adding the standard "intro" and "introformat" fields
        $this->add_intro_editor();

        //-------------------------------------------------------------------------------
        // Adding the rest of msm settings, spreeading all them into this fieldset
        // or adding more fieldsets ('header' elements) if needed for better logic

        $msm_types = array();
        $msm_types[0] = 'Lecture';
        $msm_types[1] = 'Book';
        $msm_types[2] = 'Work Book';
        $msm_types[3] = 'Others';

        $selectattr = array('onchange' =>
            "javascript:
                var currentSelect = document.getElementById('id_comptype').value;
                
                switch(currentSelect)
                {                        
                    case '0':
                        document.getElementById('id_toplevel').value = 'Lecture';
                        document.getElementById('id_childlevel1').value = 'Part';
                        document.getElementById('id_childlevel2').value = 'Topic';
                        document.getElementById('id_childlevel3').value = 'Section';
                        document.getElementById('id_childlevel4').value = 'Subsection';
                        break;
                        
                    case '1':
                        document.getElementById('id_toplevel').value = 'Book';
                        document.getElementById('id_childlevel1').value = 'Book Part';
                        document.getElementById('id_childlevel2').value = 'Chapter';
                        document.getElementById('id_childlevel3').value = 'Section';
                        document.getElementById('id_childlevel4').value = 'Subsection';
                        break;
                        
                    case '2':
                        document.getElementById('id_toplevel').value = 'Work Book';
                        document.getElementById('id_childlevel1').value = 'Book Part';
                        document.getElementById('id_childlevel2').value = 'Chapter';
                        document.getElementById('id_childlevel3').value = 'Section';
                        document.getElementById('id_childlevel4').value = 'Subsection';
                        break;
                        
                    case '3':
                        document.getElementById('id_toplevel').value = 'Please specify the name of this container.';
                        document.getElementById('id_childlevel1').value = 'Please specify the name of this container.';
                        document.getElementById('id_childlevel2').value = 'Please specify the name of this container.';
                        document.getElementById('id_childlevel3').value = 'Please specify the name of this container.';
                        document.getElementById('id_childlevel4').value = 'Please specify the name of this container.';
                        break;
                    
                }
                "
        );

        $mform->addElement('select', 'comptype', get_string('msmtype', 'msm'), $msm_types, $selectattr);
        $mform->addHelpButton('comptype', 'msmtype', 'msm');

        $mform->addElement('header', 'settinggeneral', get_string('levelsetting', 'msm'));
        $mform->addHelpButton('settinggeneral', 'levelsetting', 'msm');
        $mform->addElement('text', 'toplevel', get_string('toplevel', 'msm'), array('size' => '64', 'value' => 'Lecture'));
        $mform->setType('toplevel', PARAM_TEXT);
        $mform->addElement('text', 'standalone', get_string('standalone', 'msm'), array('size' => '64', 'value' => 'Simplepage'));
        $mform->setType('standalone', PARAM_TEXT);
        $mform->addElement('text', 'childlevel1', get_string('childlevel1', 'msm'), array('size' => '64', 'value' => 'Part'));
        $mform->setType('childlevel1', PARAM_TEXT);
        $mform->addElement('text', 'childlevel2', get_string('childlevel2', 'msm'), array('size' => '64', 'value' => 'Topic'));
        $mform->setType('childlevel2', PARAM_TEXT);
        $mform->addElement('text', 'childlevel3', get_string('childlevel3', 'msm'), array('size' => '64', 'value' => 'Section'));
        $mform->setType('childlevel3', PARAM_TEXT);
        $mform->addElement('text', 'childlevel4', get_string('childlevel4', 'msm'), array('size' => '64', 'value' => 'Subsection'));
        $mform->setType('childlevel4', PARAM_TEXT);

        $mform->addRule('toplevel', null, 'required', null, 'client');
        $mform->addRule('standalone', null, 'required', null, 'client');
        $mform->addRule('childlevel1', null, 'required', null, 'client');
        $mform->addRule('childlevel2', null, 'required', null, 'client');
        $mform->addRule('childlevel3', null, 'required', null, 'client');
        $mform->addRule('childlevel4', null, 'required', null, 'client');

        $repeatedArray = array();
        $repeatedArray[] = $mform->createElement('text', 'additionalChild', get_string('moreChild', 'msm'), array('size' => '64', 'placeholder' => 'Please specify the name of this container.'));

        $repeatno = 0;
        $repeateloptions = array();
        $repeateloptions['additionalChild']['default'] = '';
        $mform->setType('additionalChild', PARAM_TEXT);
        $this->repeat_elements($repeatedArray, $repeatno, $repeateloptions, 'option_repeats', 'option_add_fields', 1, get_string('addchildbutton', 'msm'));

        $mform->addElement('header', 'importgeneral', get_string('import', 'msm'));
        $mform->addHelpButton('importgeneral', 'import', 'msm');

        $mform->addElement('filemanager', 'importElement', get_string('importElement', 'msm'), null,
                    array('subdirs'=>0, 'maxfiles' => 1, 'accepted_types' => array('.zip') ));

        $this->standard_coursemodule_elements();

//        $this->add_action_buttons(true, get_string('msmsubmit', 'msm'), get_string('importbutton', 'msm'));        

        $this->add_action_buttons();
    }

}

