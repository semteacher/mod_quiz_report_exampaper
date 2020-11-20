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
 * Quiz exampaper report upgrade script.
 *
 * @package   quiz_exampaper
 * @copyright 2008 Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Quiz exampaper report upgrade function.
 * @param number $oldversion
 */
function xmldb_quiz_exampaper_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();
    // Moodle v2.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v2.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion < 2017042500) {

        // Define table quiz_exampaper_colontitles to be created.
        $table = new xmldb_table('quiz_exampaper_colontitles');

        // Adding fields to table quiz_exampaper_colontitles.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('quizid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cheader', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cheaderformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cfooter', XMLDB_TYPE_TEXT, 'medium', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cfooterformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table quiz_exampaper_colontitles.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
        $table->add_key('quizid', XMLDB_KEY_FOREIGN, array('quizid'), 'quiz', array('id'));

        // Conditionally launch create table for quiz_exampaper_colontitles.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Quiz exampaper savepoint reached.
        upgrade_mod_savepoint(true, 2017042500, 'quiz', 'exampaper');
    }
    
    if ($oldversion < 2020040602) {

        $table = new xmldb_table('quiz_exampaper_colontitles');

        // Define field questioncategoryid to be added to quiz_slots.
        $field = new xmldb_field('gradescaletype', XMLDB_TYPE_CHAR, '10', null, XMLDB_NOTNULL, null, null, null, 'testexam');
        // Conditionally launch add field questioncategoryid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Quiz exampaper savepoint reached.
        upgrade_mod_savepoint(true, 2020040602, 'quiz', 'exampaper');        
    }

    return true;
}
