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
 * Base class for the settings form for {@link quiz_attempts_report}s.
 *
 * @package   mod_quiz
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');


/**
 * Base class for the settings form for {@link quiz_attempts_report}s.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class quiz_exampaper_settings_form extends moodleform {

    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'preferencespage',
                get_string('reportwhattoinclude', 'quiz'));

        $this->standard_attempt_fields($mform);
        $this->other_attempt_fields($mform);

        $mform->addElement('header', 'preferencesuser',
                get_string('reportdisplayoptions', 'quiz'));

        $this->standard_preference_fields($mform);
        $this->other_preference_fields($mform);

        $mform->addElement('submit', 'submitbutton',
                get_string('savecolontitles', 'exampaper'));
    }

    protected function standard_attempt_fields(MoodleQuickForm $mform) {
		// cheader.
        $mform->addElement('text', 'cheader', get_string('cheader'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('cheader', PARAM_TEXT);
        } else {
            $mform->setType('cheader', PARAM_CLEANHTML);
        }
        $mform->addRule('cheader', null, 'required', null, 'client');
        $mform->addRule('cheader', get_string('maximumchars', '', 1255), 'maxlength', 1255, 'client');

		// cfooter.
        $mform->addElement('text', 'cfooter', get_string('cfooter'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('cfooter', PARAM_TEXT);
        } else {
            $mform->setType('cfooter', PARAM_CLEANHTML);
        }
        $mform->addRule('cfooter', null, 'required', null, 'client');
        $mform->addRule('cfooter', get_string('maximumchars', '', 1255), 'maxlength', 1255, 'client');
    }

    protected function other_attempt_fields(MoodleQuickForm $mform) {
    }

    protected function standard_preference_fields(MoodleQuickForm $mform) {
    }

    protected function other_preference_fields(MoodleQuickForm $mform) {
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        return $errors;
    }
}
