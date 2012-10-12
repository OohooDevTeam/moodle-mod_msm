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
 * This file keeps track of upgrades to the msm module
 *
 * Sometimes, changes between versions involve alterations to database
 * structures and other major things that may break installations. The upgrade
 * function in this file will attempt to perform all the necessary actions to
 * upgrade your older installation to the current version. If there's something
 * it cannot do itself, it will tell you what you need to do.  The commands in
 * here will all be database-neutral, using the functions defined in DLL libraries.
 *
 * *
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

/**
 * Execute msm upgrade from the given old version
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_msm_upgrade($oldversion)
{
    global $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes
    // And upgrade begins here. For each one, you'll need one
    // block of code similar to the next one. Please, delete
    // this comment lines once this file start handling proper
    // upgrade code.
    // if ($oldversion < YYYYMMDD00) { //New version in version.php
    //
    // }
    // Lines below (this included)  MUST BE DELETED once you get the first version
    // of your module ready to be installed. They are here only
    // for demonstrative purposes and to show how the msm
    // iself has been upgraded.
    // For each upgrade block, the file msm/version.php
    // needs to be updated . Such change allows Moodle to know
    // that this file has to be processed.
    // To know more about how to write correct DB upgrade scripts it's
    // highly recommended to read information available at:
    //   http://docs.moodle.org/en/Development:XMLDB_Documentation
    // and to play with the XMLDB Editor (in the admin menu) and its
    // PHP generation posibilities.
    // First example, some fields were added to install.xml on 2007/04/01
    if ($oldversion < 2007040100)
    {

        // Define field course to be added to msm
        $table = new xmldb_table('msm');
        $field = new xmldb_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'id');

        // Add field course
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // Define field intro to be added to msm
        $table = new xmldb_table('msm');
        $field = new xmldb_field('intro', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'name');

        // Add field intro
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // Define field introformat to be added to msm
        $table = new xmldb_table('msm');
        $field = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0',
                        'intro');

        // Add field introformat
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // Once we reach this point, we can store the new version and consider the module
        // upgraded to the version 2007040100 so the next time this block is skipped
        upgrade_mod_savepoint(true, 2007040100, 'msm');
    }

    // Second example, some hours later, the same day 2007/04/01
    // two more fields and one index were added to install.xml (note the micro increment
    // "01" in the last two digits of the version
    if ($oldversion < 2007040101)
    {

        // Define field timecreated to be added to msm
        $table = new xmldb_table('msm');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0',
                        'introformat');

        // Add field timecreated
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // Define field timemodified to be added to msm
        $table = new xmldb_table('msm');
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0',
                        'timecreated');

        // Add field timemodified
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // Define index course (not unique) to be added to msm
        $table = new xmldb_table('msm');
        $index = new xmldb_index('courseindex', XMLDB_INDEX_NOTUNIQUE, array('course'));

        // Add index to course field
        if (!$dbman->index_exists($table, $index))
        {
            $dbman->add_index($table, $index);
        }

        // Another save point reached
        upgrade_mod_savepoint(true, 2007040101, 'msm');
    }

    // Third example, the next day, 2007/04/02 (with the trailing 00), some actions were performed to install.php,
    // related with the module
    if ($oldversion < 2007040200)
    {

        // insert here code to perform some actions (same as in install.php)

        upgrade_mod_savepoint(true, 2007040200, 'msm');
    }

    if ($oldversion < 2012051700)
    {

        // Define table msm to be created
        $table = new xmldb_table('msm');

        // Adding fields to table msm
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('intro', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        // Adding keys to table msm
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table msm
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));

        // Conditionally launch create table for msm
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012051700, 'msm');
    }
    if ($oldversion < 2012051701)
    {

        // Define table msm_compositor to be created
        $table = new xmldb_table('msm_compositor');

        // Adding fields to table msm_compositor
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('unit_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('table_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('parent_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('prev_sibling_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table msm_compositor
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_compositor
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_unit to be created
        $table = new xmldb_table('msm_unit');

        // Adding fields to table msm_unit
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('string_id', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('title', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('plain_title', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('creationdate', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('last_revision_date', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('acknowledgements', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);

        // Adding keys to table msm_unit
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_unit
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }


        // Define table msm_intro to be created
        $table = new xmldb_table('msm_intro');

        // Adding fields to table msm_intro
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('string_id', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);

        // Adding keys to table msm_intro
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_intro
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }


        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012051701, 'msm');
    }

    if ($oldversion < 2012051702)
    {

        // Define table msm_def to be created
        $table = new xmldb_table('msm_def');

        // Adding fields to table msm_def
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('def_type', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('string_id', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);

        // Adding keys to table msm_def
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_def
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_theorem to be created
        $table = new xmldb_table('msm_theorem');

        // Adding fields to table msm_theorem
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('theorem_type', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('string_id', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('textcaption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);

        // Adding keys to table msm_theorem
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_theorem
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define field def_content to be added to msm_def
        $table = new xmldb_table('msm_def');
        $field = new xmldb_field('def_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null, 'description');

        // Conditionally launch add field def_content
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }


        // Define table msm_associate to be created
        $table = new xmldb_table('msm_associate');

        // Adding fields to table msm_associate
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('description', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('target', XMLDB_TYPE_CHAR, '100', null, null, null, null);

        // Adding keys to table msm_associate
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_associate
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }


        // Define table msm_info to be created
        $table = new xmldb_table('msm_info');

        // Adding fields to table msm_info
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('info_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_info
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_info
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }


        // Define table msm_statement_theorem to be created
        $table = new xmldb_table('msm_statement_theorem');

        // Adding fields to table msm_statement_theorem
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('statement_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_statement_theorem
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_statement_theorem
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }


        // Define table msm_part_theorem to be created
        $table = new xmldb_table('msm_part_theorem');

        // Adding fields to table msm_part_theorem
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('partid', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('counter', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('equivalence_mark', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('part_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_part_theorem
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_part_theorem
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_historical to be created
        $table = new xmldb_table('msm_historical');

        // Adding fields to table msm_historical
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('historical_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_historical
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_historical
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }


        // Define table msm_summary to be created
        $table = new xmldb_table('msm_summary');

        // Adding fields to table msm_summary
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('summary_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_summary
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_summary
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_trailer to be created
        $table = new xmldb_table('msm_trailer');

        // Adding fields to table msm_trailer
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('trailer_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_trailer
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_trailer
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_proof to be created
        $table = new xmldb_table('msm_proof');

        // Adding fields to table msm_proof
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('string_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('proof_type', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('logic_type', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('proof_logic', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('proof_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_proof
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_proof
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_proof_ext to be created
        $table = new xmldb_table('msm_proof_ext');

        // Adding fields to table msm_proof_ext
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('version', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('proof_ext_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_proof_ext
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_proof_ext
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_step to be created
        $table = new xmldb_table('msm_step');

        // Adding fields to table msm_step
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('partref', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('step_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_step
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_step
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_pilot to be created
        $table = new xmldb_table('msm_pilot');

        // Adding fields to table msm_pilot
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('pilot_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_pilot
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_pilot
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012051702, 'msm');
    }

    if ($oldversion < 2012051703)
    {

        // Define table msm_subordinate to be created
        $table = new xmldb_table('msm_subordinate');

        // Adding fields to table msm_subordinate
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('hot', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);

        // Adding keys to table msm_subordinate
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_subordinate
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }


        // Define table msm_cite to be created
        $table = new xmldb_table('msm_cite');

        // Adding fields to table msm_cite
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('cite_label', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);

        // Adding keys to table msm_cite
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_cite
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_item to be created
        $table = new xmldb_table('msm_item');

        // Adding fields to table msm_item
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('item_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('citekey', XMLDB_TYPE_CHAR, '300', null, null, null, null);
        $table->add_field('position', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table msm_item
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_item
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }


        // Define table msm_external_link to be created
        $table = new xmldb_table('msm_external_link');

        // Adding fields to table msm_external_link
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('href', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('target', XMLDB_TYPE_CHAR, '500', null, null, null, null);

        // Adding keys to table msm_external_link
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_external_link
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_index_symbol to be created
        $table = new xmldb_table('msm_index_symbol');

        // Adding fields to table msm_index_symbol
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('symbol', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('symbol_type', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('sortstring', XMLDB_TYPE_CHAR, '500', null, null, null, null);

        // Adding keys to table msm_index_symbol
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_index_symbol
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_index_glossary to be created
        $table = new xmldb_table('msm_index_glossary');

        // Adding fields to table msm_index_glossary
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('term', XMLDB_TYPE_TEXT, 'small', null, null, null, null);

        // Adding keys to table msm_index_glossary
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_index_glossary
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_name to be created
        $table = new xmldb_table('msm_name');

        // Adding fields to table msm_name
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('firstname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('middlename', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('lastname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('initals', XMLDB_TYPE_CHAR, '50', null, null, null, null);

        // Adding keys to table msm_name
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_name
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_contactdata to be created
        $table = new xmldb_table('msm_contactdata');

        // Adding fields to table msm_contactdata
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('email', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('webpage', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('phone', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('address', XMLDB_TYPE_CHAR, '500', null, null, null, null);

        // Adding keys to table msm_contactdata
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_contactdata
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_preface to be created
        $table = new xmldb_table('msm_preface');

        // Adding fields to table msm_preface
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('preface_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_preface
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_preface
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_acknowledgement to be created
        $table = new xmldb_table('msm_acknowledgement');

        // Adding fields to table msm_acknowledgement
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('ack_contet', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_acknowledgement
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_acknowledgement
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_stage_date to be created
        $table = new xmldb_table('msm_stage_date');

        // Adding fields to table msm_stage_date
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('stagedate', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table msm_stage_date
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_stage_date
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012051703, 'msm');
    }

    if ($oldversion < 2012051704)
    {

        // Define table msm_para to be created
        $table = new xmldb_table('msm_para');

        // Adding fields to table msm_para
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('string_id', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('para_align', XMLDB_TYPE_CHAR, '15', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);
        $table->add_field('para_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_para
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_para
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_ol to be created
        $table = new xmldb_table('msm_ol');

        // Adding fields to table msm_ol
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('ol_type', XMLDB_TYPE_CHAR, '1', null, null, null, null);
        $table->add_field('ol_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_ol
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_ol
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_ul to be created
        $table = new xmldb_table('msm_ul');

        // Adding fields to table msm_ul
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('ul_bullet', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('ul_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_ul
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_ul
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_math_display to be created
        $table = new xmldb_table('msm_math_display');

        // Adding fields to table msm_math_display
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('string_id', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('math_display_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_math_display
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_math_display
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_table to be created
        $table = new xmldb_table('msm_table');

        // Adding fields to table msm_table
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('string_id', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('table_class', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('table_summary', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('table_title', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('table_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_table
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_table
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_comment to be created
        $table = new xmldb_table('msm_comment');

        // Adding fields to table msm_comment
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('comment_type', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('string_id', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('comment_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_comment
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_comment
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_math_array to be created
        $table = new xmldb_table('msm_math_array');

        // Adding fields to table msm_math_array
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('string_id', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('no_column', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('math_array_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_math_array
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_math_array
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_media to be created
        $table = new xmldb_table('msm_media');

        // Adding fields to table msm_media
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('string_id', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('active', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('inline', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('media_type', XMLDB_TYPE_CHAR, '20', null, null, null, null);

        // Adding keys to table msm_media
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_media
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_img to be created
        $table = new xmldb_table('msm_img');

        // Adding fields to table msm_img
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('string_id', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);
        $table->add_field('img_content', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);
        $table->add_field('extended_caption', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('image_mapping', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_img
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_img
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }


        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012051704, 'msm');
    }

    if ($oldversion < 2012051705)
    {

        // Define table msm to be created
        $table = new xmldb_table('msm');

        // Adding fields to table msm
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('course', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('intro', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('introformat', XMLDB_TYPE_INTEGER, '4', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');

        // Adding keys to table msm
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table msm
        $table->add_index('course', XMLDB_INDEX_NOTUNIQUE, array('course'));

        // Conditionally launch create table for msm
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_statement_example to be created
        $table = new xmldb_table('msm_statement_example');

        // Adding fields to table msm_statement_example
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('statement_example_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_statement_example
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_statement_example
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_part_example to be created
        $table = new xmldb_table('msm_part_example');

        // Adding fields to table msm_part_example
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('partid', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('counter', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('equivalence_mark', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('part_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_part_example
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_part_example
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_answer to be created
        $table = new xmldb_table('msm_answer');

        // Adding fields to table msm_answer
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('answer_type', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('answer_version', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('logic_type', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('answer_logic', XMLDB_TYPE_TEXT, 'small', null, null, null, null);
        $table->add_field('answer_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_answer
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_answer
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_answer_ext to be created
        $table = new xmldb_table('msm_answer_ext');

        // Adding fields to table msm_answer_ext
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('answer_ext_type', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('answer_ext_version', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);

        // Adding keys to table msm_answer_ext
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_answer_ext
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_showme to be created
        $table = new xmldb_table('msm_showme');

        // Adding fields to table msm_showme
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('textcaption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('statement_showme', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);

        // Adding keys to table msm_showme
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_showme
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_answer_showme to be created
        $table = new xmldb_table('msm_answer_showme');

        // Adding fields to table msm_answer_showme
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('answer_showme_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_answer_showme
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_answer_showme
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_exercise to be created
        $table = new xmldb_table('msm_exercise');

        // Adding fields to table msm_exercise
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('string_id', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('difficulty', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);

        // Adding keys to table msm_exercise
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_exercise
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_problem to be created
        $table = new xmldb_table('msm_problem');

        // Adding fields to table msm_problem
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('textcaption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('problem_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_problem
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_problem
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_approach to be created
        $table = new xmldb_table('msm_approach');

        // Adding fields to table msm_approach
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('approach_version', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table msm_approach
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_approach
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }


        // Define table msm_solution to be created
        $table = new xmldb_table('msm_solution');

        // Adding fields to table msm_solution
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('solution_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_solution
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_solution
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_solution_hint to be created
        $table = new xmldb_table('msm_solution_hint');

        // Adding fields to table msm_solution_hint
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('solution_hint_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_solution_hint
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_solution_hint
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_answer_exercise to be created
        $table = new xmldb_table('msm_answer_exercise');

        // Adding fields to table msm_answer_exercise
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('answer_exercise_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_answer_exercise
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_answer_exercise
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_approach_ext to be created
        $table = new xmldb_table('msm_approach_ext');

        // Adding fields to table msm_approach_ext
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('approach_ext_version', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table msm_approach_ext
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_approach_ext
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_solution_ext to be created
        $table = new xmldb_table('msm_solution_ext');

        // Adding fields to table msm_solution_ext
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);

        // Adding keys to table msm_solution_ext
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_solution_ext
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_part_exercise to be created
        $table = new xmldb_table('msm_part_exercise');

        // Adding fields to table msm_part_exercise
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('part_exercise_number', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('difficulty', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table msm_part_exercise
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_part_exercise
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_table_collection to be created
        $table = new xmldb_table('msm_table_collection');

        // Adding fields to table msm_table_collection
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('tablename', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('table_description', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);

        // Adding keys to table msm_table_collection
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_table_collection
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012051705, 'msm');
    }

    if ($oldversion < 2012051706)
    {

        // Define table msm_ext to be created
        $table = new xmldb_table('msm_ext');

        // Adding fields to table msm_ext
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('ext_type', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('ext_version', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('ext_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('ext_name', XMLDB_TYPE_CHAR, '200', null, null, null, null);

        // Adding keys to table msm_ext
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_ext
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_solution_ext to be dropped
        $table = new xmldb_table('msm_solution_ext');

        // Conditionally launch drop table for msm_solution_ext
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }
        // Define table msm_approach_ext to be dropped
        $table = new xmldb_table('msm_approach_ext');

        // Conditionally launch drop table for msm_approach_ext
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }

        // Define table msm_answer_ext to be dropped
        $table = new xmldb_table('msm_answer_ext');

        // Conditionally launch drop table for msm_answer_ext
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }

        // Define table msm_proof_ext to be dropped
        $table = new xmldb_table('msm_proof_ext');

        // Conditionally launch drop table for msm_proof_ext
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012051706, 'msm');
    }

    if ($oldversion < 2012051800)
    {

        // Define table msm_acknowledgement to be dropped
        $table = new xmldb_table('msm_acknowledgement');

        // Conditionally launch drop table for msm_acknowledgement
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012051800, 'msm');
    }

    if ($oldversion < 2012051801)
    {

        // Define table msm_summary to be dropped
        $table = new xmldb_table('msm_summary');

        // Conditionally launch drop table for msm_summary
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }

        // Define table msm_historical to be dropped
        $table = new xmldb_table('msm_historical');

        // Conditionally launch drop table for msm_historical
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }
        // Define table msm_trailer to be dropped
        $table = new xmldb_table('msm_trailer');

        // Conditionally launch drop table for msm_trailer
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }


        // Define table msm_preface to be dropped
        $table = new xmldb_table('msm_preface');

        // Conditionally launch drop table for msm_preface
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }



        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012051801, 'msm');
    }

    if ($oldversion < 2012051802)
    {

        // Define table msm_extra_info to be created
        $table = new xmldb_table('msm_extra_info');

        // Adding fields to table msm_extra_info
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('extra_info_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('extra_info_name', XMLDB_TYPE_CHAR, '300', null, null, null, null);

        // Adding keys to table msm_extra_info
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_extra_info
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012051802, 'msm');
    }

    if ($oldversion < 2012052200)
    {

        // Define table msm_ol to be dropped
        $table = new xmldb_table('msm_ol');

        // Conditionally launch drop table for msm_ol
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }

        // Define table msm_ul to be dropped
        $table = new xmldb_table('msm_ul');

        // Conditionally launch drop table for msm_ul
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }

        // Define table msm_list to be created
        $table = new xmldb_table('msm_list');

        // Adding fields to table msm_list
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('list_type', XMLDB_TYPE_CHAR, '400', null, null, null, null);
        $table->add_field('list_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('list_name', XMLDB_TYPE_CHAR, '100', null, null, null, null);

        // Adding keys to table msm_list
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_list
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012052200, 'msm');
    }

    if ($oldversion < 2012053000)
    {

        // Define table msm_name to be dropped
        $table = new xmldb_table('msm_name');

        // Conditionally launch drop table for msm_name
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }

        // Define table msm_contactdata to be dropped
        $table = new xmldb_table('msm_contactdata');

        // Conditionally launch drop table for msm_contactdata
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }

        // Define table msm_person to be created
        $table = new xmldb_table('msm_person');

        // Adding fields to table msm_person
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('firstname', XMLDB_TYPE_CHAR, '30', null, null, null, null);
        $table->add_field('middlename', XMLDB_TYPE_CHAR, '30', null, null, null, null);
        $table->add_field('lastname', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('initials', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('email', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('webpage', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('phone', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('address', XMLDB_TYPE_CHAR, '300', null, null, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '30', null, null, null, null);

        // Adding keys to table msm_person
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_person
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }


        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012053000, 'msm');
    }

    if ($oldversion < 2012053100)
    {

        // Define table msm_math_display to be dropped
        $table = new xmldb_table('msm_math_display');

        // Conditionally launch drop table for msm_math_display
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }


        // Define table msm_list to be dropped
        $table = new xmldb_table('msm_list');

        // Conditionally launch drop table for msm_list
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }

        // Define table msm_content to be created
        $table = new xmldb_table('msm_content');

        // Adding fields to table msm_content
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('additional_attribute', XMLDB_TYPE_CHAR, '300', null, null, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('type', XMLDB_TYPE_CHAR, '50', null, null, null, null);

        // Adding keys to table msm_content
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_content
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }


        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012053100, 'msm');
    }

    if ($oldversion < 2012060100)
    {

        // Define table msm_packs to be created
        $table = new xmldb_table('msm_packs');

        // Adding fields to table msm_packs
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('string_id', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('title', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);
        $table->add_field('doclabel', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('texsupport', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('literature_db', XMLDB_TYPE_CHAR, '500', null, null, null, null);

        // Adding keys to table msm_packs
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_packs
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012060100, 'msm');
    }


    if ($oldversion < 2012060101)
    {

        // Define field type to be added to msm_packs
        $table = new xmldb_table('msm_packs');
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'literature_db');

        // Conditionally launch add field type
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012060101, 'msm');
    }

    if ($oldversion < 2012060400)
    {

        // Define table msm_quiz to be created
        $table = new xmldb_table('msm_quiz');

        // Adding fields to table msm_quiz
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('textcaption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('question', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('hint', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('answer', XMLDB_TYPE_TEXT, 'big', null, null, null, null);
        $table->add_field('part', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table msm_quiz
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_quiz
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012060400, 'msm');
    }

    if ($oldversion < 2012060500)
    {

        // Define field string_id to be added to msm_quiz
        $table = new xmldb_table('msm_quiz');
        $field = new xmldb_field('string_id', XMLDB_TYPE_CHAR, '300', null, null, null, null, 'id');

        // Conditionally launch add field string_id
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012060500, 'msm');
    }

    if ($oldversion < 2012060501)
    {

        // Define table msm_solution_hint to be dropped
        $table = new xmldb_table('msm_solution_hint');

        // Conditionally launch drop table for msm_solution_hint
        if ($dbman->table_exists($table))
        {
            $dbman->drop_table($table);
        }

        // Define field solution_hint to be added to msm_approach
        $table = new xmldb_table('msm_approach');
        $field = new xmldb_field('solution_hint', XMLDB_TYPE_TEXT, 'big', null, null, null, null, 'approach_version');

        // Conditionally launch add field solution_hint
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012060501, 'msm');
    }

    if ($oldversion < 2012061500)
    {

        // Define field img_content to be dropped from msm_img
        $table = new xmldb_table('msm_img');
        $field = new xmldb_field('img_content');

        // Conditionally launch drop field img_content
        if ($dbman->field_exists($table, $field))
        {
            $dbman->drop_field($table, $field);
        }

        // Define field src to be added to msm_img
        $table = new xmldb_table('msm_img');
        $field = new xmldb_field('src', XMLDB_TYPE_CHAR, '400', null, XMLDB_NOTNULL, null, null, 'image_mapping');

        // Conditionally launch add field src
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // Define field height to be added to msm_img
        $table = new xmldb_table('msm_img');
        $field = new xmldb_field('height', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'src');

        // Conditionally launch add field height
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // Define field width to be added to msm_img
        $table = new xmldb_table('msm_img');
        $field = new xmldb_field('width', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'height');

        // Conditionally launch add field width
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012061500, 'msm');
    }

    if ($oldversion < 2012061900)
    {

        // Define field changeme to be dropped from msm_proof
        $table = new xmldb_table('msm_proof');
        $field = new xmldb_field('logic_type');

        // Conditionally launch drop field changeme
        if ($dbman->field_exists($table, $field))
        {
            $dbman->drop_field($table, $field);
        }


        // Define field proof_logic to be dropped from msm_proof
        $table = new xmldb_table('msm_proof');
        $field = new xmldb_field('proof_logic');

        // Conditionally launch drop field proof_logic
        if ($dbman->field_exists($table, $field))
        {
            $dbman->drop_field($table, $field);
        }

        // Define field caption to be dropped from msm_proof
        $table = new xmldb_table('msm_proof');
        $field = new xmldb_field('caption');

        // Conditionally launch drop field caption
        if ($dbman->field_exists($table, $field))
        {
            $dbman->drop_field($table, $field);
        }

        // Define field proof_content to be dropped from msm_proof
        $table = new xmldb_table('msm_proof');
        $field = new xmldb_field('proof_content');

        // Conditionally launch drop field proof_content
        if ($dbman->field_exists($table, $field))
        {
            $dbman->drop_field($table, $field);
        }


        // Define table msm_proof_block to be created
        $table = new xmldb_table('msm_proof_block');

        // Adding fields to table msm_proof_block
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('logic_type', XMLDB_TYPE_CHAR, '200', null, null, null, null);
        $table->add_field('proof_logic', XMLDB_TYPE_TEXT, 'medium', null, null, null, null);
        $table->add_field('caption', XMLDB_TYPE_CHAR, '500', null, null, null, null);
        $table->add_field('proof_content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_proof_block
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_proof_block
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define field hint to be dropped from msm_quiz
        $table = new xmldb_table('msm_quiz');
        $field = new xmldb_field('hint');

        // Conditionally launch drop field hint
        if ($dbman->field_exists($table, $field))
        {
            $dbman->drop_field($table, $field);
        }

        // Define field part to be dropped from msm_quiz
        $table = new xmldb_table('msm_quiz');
        $field = new xmldb_field('part');

        // Conditionally launch drop field part
        if ($dbman->field_exists($table, $field))
        {
            $dbman->drop_field($table, $field);
        }

        // Define field answer to be dropped from msm_quiz
        $table = new xmldb_table('msm_quiz');
        $field = new xmldb_field('answer');

        // Conditionally launch drop field answer
        if ($dbman->field_exists($table, $field))
        {
            $dbman->drop_field($table, $field);
        }


        // Define table msm_quiz_choice to be created
        $table = new xmldb_table('msm_quiz_choice');

        // Adding fields to table msm_quiz_choice
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('answer', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_quiz_choice
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_quiz_choice
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }



        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012061900, 'msm');
    }

//    if ($oldversion < 2012070300)
//    {
//
//        // Define field msm_id to be added to msm_compositor
//        $table = new xmldb_table('msm_compositor');
//        $field = new xmldb_field('msm_id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'id');
//
//        // Conditionally launch add field msm_id
//        if (!$dbman->field_exists($table, $field))
//        {
//            $dbman->add_field($table, $field);
//        }
//
//        // msm savepoint reached
//        upgrade_mod_savepoint(true, 2012070300, 'msm');
//    }

    if ($oldversion < 2012070303)
    {

        // Define field msm_id to be added to msm_compositor
        $table = new xmldb_table('msm_compositor');
        $field = new xmldb_field('msm_id', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'id');

        // Conditionally launch add field msm_id
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012070303, 'msm');
    }

    if ($oldversion < 2012071600)
    {

        // Define field description to be added to msm_math_array
        $table = new xmldb_table('msm_math_array');
        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, 'medium', null, null, null, null, 'math_array_content');

        // Conditionally launch add field description
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // Define field math_array_content to be dropped from msm_math_array
        $table = new xmldb_table('msm_math_array');
        $field = new xmldb_field('math_array_content');

        // Conditionally launch drop field math_array_content
        if ($dbman->field_exists($table, $field))
        {
            $dbman->drop_field($table, $field);
        }

        // Define table msm_math_row to be created
        $table = new xmldb_table('msm_math_row');

        // Adding fields to table msm_math_row
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('rowspan', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table msm_math_row
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_math_row
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define table msm_math_cell to be created
        $table = new xmldb_table('msm_math_cell');

        // Adding fields to table msm_math_cell
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('colspan', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('halign', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('valign', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('bgcolor', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('fontcolor', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('content', XMLDB_TYPE_TEXT, 'big', null, null, null, null);

        // Adding keys to table msm_math_cell
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_math_cell
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012071600, 'msm');
    }

    if ($oldversion < 2012080100)
    {

        // Define table msm_image_mapping to be created
        $table = new xmldb_table('msm_image_mapping');

        // Adding fields to table msm_image_mapping
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('shape', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('coordinates', XMLDB_TYPE_CHAR, '200', null, null, null, null);

        // Adding keys to table msm_image_mapping
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for msm_image_mapping
        if (!$dbman->table_exists($table))
        {
            $dbman->create_table($table);
        }

        // Define field image_mapping to be dropped from msm_img
        $table = new xmldb_table('msm_img');
        $field = new xmldb_field('image_mapping');

        // Conditionally launch drop field image_mapping
        if ($dbman->field_exists($table, $field))
        {
            $dbman->drop_field($table, $field);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012080100, 'msm');
    }

    if ($oldversion < 2012082700)
    {

        // Define field standalone to be added to msm_unit
        $table = new xmldb_table('msm_unit');
        $field = new xmldb_field('standalone', XMLDB_TYPE_CHAR, '10', null, null, null, null, 'id');

        // Conditionally launch add field standalone
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012082700, 'msm');
    }

    if ($oldversion < 2012082800)
    {

        // Define field type to be added to msm_answer_showme
        $table = new xmldb_table('msm_answer_showme');
        $field = new xmldb_field('type', XMLDB_TYPE_CHAR, '20', null, null, null, null, 'id');

        // Conditionally launch add field type
        if (!$dbman->field_exists($table, $field))
        {
            $dbman->add_field($table, $field);
        }

        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012082800, 'msm');
    }
    
    if ($oldversion < 2012101200)
    {
        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012101200, 'msm');
    }
    if ($oldversion < 2012101201)
    {
        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012101201, 'msm');
    }
    if ($oldversion < 2012101202)
    {
        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012101202, 'msm');
    }
     if ($oldversion < 2012101203)
    {
        // msm savepoint reached
        upgrade_mod_savepoint(true, 2012101203, 'msm');
    }



    // And that's all. Please, examine and understand the 3 example blocks above. Also
    // it's interesting to look how other modules are using this script. Remember that
    // the basic idea is to have "blocks" of code (each one being executed only once,
    // when the module version (version.php) is updated.
    // Lines above (this included) MUST BE DELETED once you get the first version of
    // yout module working. Each time you need to modify something in the module (DB
    // related, you'll raise the version and add one upgrade block here.
    // Final return of upgrade result (true, all went good) to Moodle.
    return true;
}
