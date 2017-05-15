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
 * Class to store the options for a {@link quiz_exampaper_report}.
 *
 * @package   quiz_exampaper
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/report/attemptsreport_options.php');


/**
 * Class to store the options for a {@link quiz_exampaper_report}.
 *
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_exampaper_options extends mod_quiz_attempts_report_options {

    /** @var bool whether to show only attempt that need regrading. */
    public $onlyregraded = false;

    /** @var bool whether to show marks for each question (slot). */
    public $slotmarks = false;
    
    //tdmu-force display all enrolled users
    public $attempts = quiz_attempts_report::ENROLLED_ALL;
    
    public $pagesize = 30;
    
    public $cheadertext = 'TDMU header';
    public $cheaderformat = 1;
    public $cfootertext = 'TDMU footer';
    public $cfoterformat = 1;

    protected function get_url_params() {
        $params = parent::get_url_params();
        $params['onlyregraded'] = $this->onlyregraded;
        $params['slotmarks']    = $this->slotmarks;
        $params['attempts']     = $this->attempts;
        $params['pagesize']     = $this->pagesize;
        
//        $params['cheader']['text']    = $this->cheadertext;
//        $params['cheader']['format']  = $this->cheaderformat;
//        $params['cfooter']['text']    = $this->cfootertext;
//        $params['cfooter']['format']  = $this->cfoterformat;
        
        return $params;
    }

    public function get_initial_form_data() {
        global $DB;
        
        $toform = parent::get_initial_form_data();
        $toform->onlyregraded = $this->onlyregraded;
        $toform->slotmarks    = $this->slotmarks;
        $toform->attempts     = $this->attempts;
        $toform->pagesize     = $this->pagesize;

        $saved_colontitles = $DB->get_record('quiz_exampaper_colontitles', array('quizid'=>$this->quiz->id));
        $a= new stdClass();
        $a->groupname = groups_get_group_name($this->group);

        if ($saved_colontitles) {
            $this->cheadertext    = $saved_colontitles->cheader;
            $this->cfootertext    = $saved_colontitles->cfooter;
            $this->cheaderformat  = $saved_colontitles->cheaderformat;
            $this->cfoterformat   = $saved_colontitles->cfooterformat;            
        } else {
            $this->cheadertext = get_string('exampapercheaderdefault', 'quiz_exampaper', $a);
            $this->cfootertext = get_string('exampapercfooterdefault', 'quiz_exampaper', $a);
        }
        
        $toform->cheader['text']    = $this->cheadertext;
        $toform->cfooter['text']    = $this->cfootertext;
        $toform->cheader['format']  = $this->cheaderformat;
        $toform->cfooter['format']  = $this->cfoterformat;
        
        return $toform;
    }

    public function setup_from_form_data($fromform) {
        parent::setup_from_form_data($fromform);

        $this->onlyregraded = !empty($fromform->onlyregraded);
        $this->slotmarks    = $fromform->slotmarks;
        $this->pagesize     = $fromform->pagesize;
        $this->attempts     = $fromform->attempts;
        
        $this->cheadertext    = $fromform->cheader['text'];
        $this->cfootertext    = $fromform->cfooter['text'];
        $this->cheaderformat  = $fromform->cheader['format'];
        $this->cfoterformat   = $fromform->cfooter['format'];
    }

    public function setup_from_params() {
        parent::setup_from_params();

        $this->onlyregraded = optional_param('onlyregraded', $this->onlyregraded, PARAM_BOOL);
        $this->slotmarks    = optional_param('slotmarks', $this->slotmarks, PARAM_BOOL);
        $this->pagesize     = optional_param('pagesize', $this->pagesize, PARAM_INT);
        $this->attempts     = optional_param('attempts', $this->attempts, PARAM_TEXT);
        
        $this->cheadertext    = optional_param('cheader[text]', $this->cheadertext, PARAM_RAW);
        $this->cfootertext    = optional_param('cfooter[text]', $this->cfootertext, PARAM_RAW);
        $this->cheaderformat  = optional_param('cheader[format]', $this->cheaderformat, PARAM_INT);        
        $this->cfoterformat   = optional_param('cfooter[format]', $this->cfoterformat, PARAM_INT);
    }

    public function setup_from_user_preferences() {
        parent::setup_from_user_preferences();

        $this->slotmarks = get_user_preferences('quiz_exampaper_slotmarks', $this->slotmarks);
    }

    public function update_user_preferences() {
        parent::update_user_preferences();

        if (quiz_has_grades($this->quiz)) {
            set_user_preference('quiz_exampaper_slotmarks', $this->slotmarks);
        }
    }

    public function resolve_dependencies() {
        parent::resolve_dependencies();

        if ($this->attempts == quiz_attempts_report::ENROLLED_WITHOUT) {
            $this->onlyregraded = false;
        }

        if (!$this->usercanseegrades) {
            $this->slotmarks = false;
        }

        // We only want to show the checkbox to delete attempts
        // if the user has permissions and if the report mode is showing attempts.
        $this->checkboxcolumn = has_any_capability(
                array('mod/quiz:regrade', 'mod/quiz:deleteattempts'), context_module::instance($this->cm->id))
                && ($this->attempts != quiz_attempts_report::ENROLLED_WITHOUT);
    }
}
