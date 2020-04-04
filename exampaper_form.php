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
 * @package   quiz_exampaper
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
class quiz_exampaper_settings_form extends moodleform {

    protected function definition() {
        global $PAGE;
        
        $mform = $this->_form;
        
        $mform->addElement('header', 'preferencespage',
                get_string('reportwhattoinclude', 'quiz'));

        $this->standard_attempt_fields($mform);

        $mform->addElement('submit', 'submitbutton',
                get_string('showreport', 'quiz'));

        $mform->addElement('header', 'preferencegradescaletype',
                get_string('exampapergradescaletypedisplayoptions', 'quiz_exampaper'));

        $this->other_attempt_fields($mform);

        $mform->addElement('submit', 'savegradescaletype',
                get_string('exampapersavegradescaletype', 'quiz_exampaper'));

        $mform->addElement('header', 'preferencesuser',
                get_string('exampapercolontitlesdisplayoptions', 'quiz_exampaper'));

        $this->standard_preference_fields($mform);
        $this->other_preference_fields($mform);

        $mform->addElement('submit', 'savecolontitles',
                get_string('exampapersavecolontitles', 'quiz_exampaper'));
        $mform->addElement('cancel', 'resetcolontitles',
                get_string('exampaperesetcolontitles', 'quiz_exampaper'));
        $PAGE->requires->event_handler('#id_resetcolontitles', 'click', 'M.util.show_confirm_dialog',
                    array('message' => get_string('exampaperesetcolontitlescofirmation', 'quiz_exampaper')));
    }

    protected function standard_attempt_fields(MoodleQuickForm $mform) {
    
        $mform->addElement('select', 'attempts', get_string('reportattemptsfrom', 'quiz'), array(
                    quiz_attempts_report::ENROLLED_WITH    => get_string('reportuserswith', 'quiz'),
                    quiz_attempts_report::ENROLLED_WITHOUT => get_string('reportuserswithout', 'quiz'),
                    quiz_attempts_report::ENROLLED_ALL     => get_string('reportuserswithorwithout', 'quiz'),
                    quiz_attempts_report::ALL_WITH        => get_string('reportusersall', 'quiz'),
                 ));

        $stategroup = array(
            $mform->createElement('advcheckbox', 'stateinprogress', '',
                    get_string('stateinprogress', 'quiz')),
            $mform->createElement('advcheckbox', 'stateoverdue', '',
                    get_string('stateoverdue', 'quiz')),
            $mform->createElement('advcheckbox', 'statefinished', '',
                    get_string('statefinished', 'quiz')),
            $mform->createElement('advcheckbox', 'stateabandoned', '',
                    get_string('stateabandoned', 'quiz')),
        );
        $mform->addGroup($stategroup, 'stateoptions',
                get_string('reportattemptsthatare', 'quiz'), array(' '), false);
        $mform->setDefault('stateinprogress', 1);
        $mform->setDefault('stateoverdue',    1);
        $mform->setDefault('statefinished',   1);
        $mform->setDefault('stateabandoned',  1);
        $mform->disabledIf('stateinprogress', 'attempts', 'eq', quiz_attempts_report::ENROLLED_WITHOUT);
        $mform->disabledIf('stateoverdue',    'attempts', 'eq', quiz_attempts_report::ENROLLED_WITHOUT);
        $mform->disabledIf('statefinished',   'attempts', 'eq', quiz_attempts_report::ENROLLED_WITHOUT);
        $mform->disabledIf('stateabandoned',  'attempts', 'eq', quiz_attempts_report::ENROLLED_WITHOUT);

        if (quiz_report_can_filter_only_graded($this->_customdata['quiz'])) {
            $gm = html_writer::tag('span',
                    quiz_get_grading_option_name($this->_customdata['quiz']->grademethod),
                    array('class' => 'highlight'));
            $mform->addElement('advcheckbox', 'onlygraded', '',
                    get_string('reportshowonlyfinished', 'quiz', $gm));
            $mform->disabledIf('onlygraded', 'attempts', 'eq', quiz_attempts_report::ENROLLED_WITHOUT);
            $mform->disabledIf('onlygraded', 'statefinished', 'notchecked');
        }
    }

    protected function other_attempt_fields(MoodleQuickForm $mform) {
        $mform->addElement('select', 'gradescaletype', get_string('gradescaletypefrom', 'quiz_exampaper'), array(
                    quiz_exampaper_report::GRADESCALE_DIFCREDIT => get_string('difcreditlabel', 'quiz_exampaper'),
                    quiz_exampaper_report::GRADESCALE_TESTEXAM => get_string('testexamlabel', 'quiz_exampaper'),
                    quiz_exampaper_report::GRADESCALE_ESSAYEXAM => get_string('essayexamlabel', 'quiz_exampaper'),
                 ));
    }

    protected function standard_preference_fields(MoodleQuickForm $mform) {
        $mform->addElement('hidden', 'slotmarks', 0);
        $mform->setType('slotmarks', PARAM_INT);
        $mform->addElement('hidden', 'pagesize', quiz_attempts_report::DEFAULT_PAGE_SIZE);
        $mform->setType('pagesize', PARAM_INT);
        //$mform->addElement('hidden', 'attempts', quiz_attempts_report::ENROLLED_ALL);
        //$mform->setType('attempts', PARAM_TEXT);
    }

    protected function other_preference_fields(MoodleQuickForm $mform) {
		// cheader editor.
        $mform->addElement('editor', 'cheader',
                get_string('exampapercheader', 'quiz_exampaper'), array('rows' => 5), array('maxfiles' => EDITOR_UNLIMITED_FILES,
                        'noclean' => true, 'enable_filemanagement' => true));
        $mform->setType('cheader', PARAM_RAW);

		// cfooter editor.
        $mform->addElement('editor', 'cfooter',
                get_string('exampapercfooter', 'quiz_exampaper'), array('rows' => 5), array('maxfiles' => EDITOR_UNLIMITED_FILES,
                        'noclean' => true, 'enable_filemanagement' => true));
        $mform->setType('cfooter', PARAM_RAW);        
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['attempts'] != quiz_attempts_report::ENROLLED_WITHOUT && !(
                $data['stateinprogress'] || $data['stateoverdue'] || $data['statefinished'] || $data['stateabandoned'])) {
            $errors['stateoptions'] = get_string('reportmustselectstate', 'quiz');
        }
        
        return $errors;
    }
}