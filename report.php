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
 * This file defines the quiz exampaper report class.
 *
 * @package   quiz_exampaper
 * @copyright 1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @copyright 2020 Andrii Semenets (semteacher@tdmu.edu.ua)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/report/attemptsreport.php');
require_once($CFG->dirroot . '/mod/quiz/report/exampaper/exampaper_options.php');
require_once($CFG->dirroot . '/mod/quiz/report/exampaper/exampaper_form.php');
require_once($CFG->dirroot . '/mod/quiz/report/exampaper/exampaper_table.php');
//require_once($CFG->dirroot . '/mod/quiz/report/exampaper/reportlib.php');


/**
 * Quiz report subclass for the exampaper (grades) report.
 *
 * @copyright 1999 onwards Martin Dougiamas and others {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_exampaper_report extends quiz_attempts_report {

    /** @var string TNMU constant used for the options, means multichoice test exam. */
    const GRADESCALE_TESTEXAM = 'testexam';
    /** @var string TNMU constant used for the options, means essay test exam. */
    const GRADESCALE_ESSAYEXAM = 'essayexam';
    /** @var string TNMU constant used for the options, means multichoice differential credit. */
    const GRADESCALE_DIFCREDIT = 'difcredit';
    
    public function display($quiz, $cm, $course) {
        global $DB, $OUTPUT, $PAGE;
        
        list($currentgroup, $studentsjoins, $groupstudentsjoins, $allowedjoins) = $this->init(
                'exampaper', 'quiz_exampaper_settings_form', $quiz, $cm, $course);

        $options = new quiz_exampaper_options('exampaper', $quiz, $cm, $course);

        if ($fromform = $this->form->get_data()) {
            $options->process_settings_from_form($fromform);
        } else {
            $options->process_settings_from_params();
        }

        $this->form->set_data($options->get_initial_form_data());

        //tdmu-force display all enrolled users
        //$options->attempts = self::ENROLLED_ALL;
        
        // Load the required questions.
        $questions = quiz_report_get_significant_questions($quiz);

        // Prepare for downloading, if applicable.
        $courseshortname = format_string($course->shortname, true,
                array('context' => context_course::instance($course->id)));
        $coursesection = $DB->get_record('course_sections', array('id'=>$cm->section));
        if ($coursesection) {
            $coursesectionname = format_string($coursesection->name, true,
                array('context' => context_course::instance($course->id)));
        } else {
            $coursesectionname = '';
        }
        $table = new quiz_exampaper_table($quiz, $this->context, $this->qmsubselect,
                $options, $groupstudentsjoins, $studentsjoins, $questions, $options->get_url());
        //$filename = quiz_report_download_filename(get_string('exampaperfilename', 'quiz_exampaper'),
        //        $courseshortname, $quiz->name);
        $filename = $this->quiz_report_download_filename_extended(get_string('exampaperfilename', 'quiz_exampaper'),
                $courseshortname, $coursesectionname, $quiz->name, $options->group);
        $table->is_downloading($options->download, $filename,
                $courseshortname . ' ' . format_string($quiz->name, true));
        if ($table->is_downloading()) {
            raise_memory_limit(MEMORY_EXTRA);
        }

        $this->hasgroupstudents = false;
        if (!empty($groupstudentsjoins->joins)) {
            $sql = "SELECT DISTINCT u.id
                      FROM {user} u
                    $groupstudentsjoins->joins
                     WHERE $groupstudentsjoins->wheres";
            $this->hasgroupstudents = $DB->record_exists_sql($sql, $groupstudentsjoins->params);
        }
        $hasstudents = false;
        if (!empty($studentsjoins->joins)) {
            $sql = "SELECT DISTINCT u.id
                    FROM {user} u
                    $studentsjoins->joins
                    WHERE $studentsjoins->wheres";
            $hasstudents = $DB->record_exists_sql($sql, $studentsjoins->params);
        }
        if ($options->attempts == self::ALL_WITH) {
            // This option is only available to users who can access all groups in
            // groups mode, so setting allowed to empty (which means all quiz attempts
            // are accessible, is not a security porblem.
            $allowedjoins = new \core\dml\sql_join();
        }

        $this->course = $course; // Hack to make this available in process_actions.
        $this->process_actions($quiz, $cm, $currentgroup, $groupstudentsjoins, $allowedjoins, $options->get_url());

        // Start output.
        if (!$table->is_downloading()) {
            // Only print headers if not asked to download data.
            $this->print_header_and_tabs($cm, $course, $quiz, $this->mode);
        }

        if ($groupmode = groups_get_activity_groupmode($cm)) {
            // Groups are being used, so output the group selector if we are not downloading.
            if (!$table->is_downloading()) {
                groups_print_activity_menu($cm, $options->get_url());
            }
        }

        // Print information on the number of existing attempts.
        if (!$table->is_downloading()) {
            // Do not print notices when downloading.
            if ($strattemptnum = quiz_num_attempt_summary($quiz, $cm, true, $currentgroup)) {
                echo '<div class="quizattemptcounts">' . $strattemptnum . '</div>';
            }
        }

        $hasquestions = quiz_has_questions($quiz->id);
        if (!$table->is_downloading()) {
            if (!$hasquestions) {
                echo quiz_no_questions_message($quiz, $cm, $this->context);
            } else if (!$hasstudents) {
                echo $OUTPUT->notification(get_string('nostudentsyet'));
            } else if ($currentgroup && !$this->hasgroupstudents) {
                echo $OUTPUT->notification(get_string('nostudentsingroup'));
            }

            // Print the display options.
            $this->form->display();
        }

        $hasstudents = $hasstudents && (!$currentgroup || $this->hasgroupstudents);
        if ($hasquestions && ($hasstudents || $options->attempts == self::ALL_WITH)) {
            // Construct the SQL.
            list($fields, $from, $where, $params) = $table->base_sql($allowedjoins);

            $table->set_count_sql("SELECT COUNT(1) FROM $from WHERE $where", $params);

            // Test to see if there are any regraded attempts to be listed.
            $fields .= ", COALESCE((
                                SELECT MAX(qqr.regraded)
                                  FROM {quiz_exampaper_regrades} qqr
                                 WHERE qqr.questionusageid = quiza.uniqueid
                          ), -1) AS regraded";
            if ($options->onlyregraded) {
                $where .= " AND COALESCE((
                                    SELECT MAX(qqr.regraded)
                                      FROM {quiz_exampaper_regrades} qqr
                                     WHERE qqr.questionusageid = quiza.uniqueid
                                ), -1) <> -1";
            }
            $table->set_sql($fields, $from, $where, $params);

            if (!$table->is_downloading()) {
                // Output the regrade buttons.
                if (has_capability('mod/quiz:regrade', $this->context)) {
                    $regradesneeded = $this->count_question_attempts_needing_regrade(
                            $quiz, $groupstudentsjoins);
                    if ($currentgroup) {
                        $a= new stdClass();
                        $a->groupname = groups_get_group_name($currentgroup);
                        $a->coursestudents = get_string('participants');
                        $a->countregradeneeded = $regradesneeded;
                        $regradealldrydolabel =
                                get_string('regradealldrydogroup', 'quiz_overview', $a);
                        $regradealldrylabel =
                                get_string('regradealldrygroup', 'quiz_overview', $a);
                        $regradealllabel =
                                get_string('regradeallgroup', 'quiz_overview', $a);
                    } else {
                        $regradealldrydolabel =
                                get_string('regradealldrydo', 'quiz_overview', $regradesneeded);
                        $regradealldrylabel =
                                get_string('regradealldry', 'quiz_overview');
                        $regradealllabel =
                                get_string('regradeall', 'quiz_overview');
                    }
                    $displayurl = new moodle_url($options->get_url(), array('sesskey' => sesskey()));
                    //tdmu-disable regrading
//                    echo '<div class="mdl-align">';
//                    echo '<form action="'.$displayurl->out_omit_querystring().'">';
//                    echo '<div>';
//                    echo html_writer::input_hidden_params($displayurl);
//                    echo '<input type="submit" class="btn btn-secondary" name="regradeall" value="'.$regradealllabel.'"/>';
//                    echo '<input type="submit" class="btn btn-secondary m-l-1" name="regradealldry" value="' .
//                            $regradealldrylabel . '"/>';
//                    if ($regradesneeded) {
//                        echo '<input type="submit" class="btn btn-secondary m-l-1" name="regradealldrydo" value="' .
//                                $regradealldrydolabel . '"/>';
//                    }
//                    echo '</div>';
//                    echo '</form>';
//                    echo '</div>';
                }
                // Print information on the grading method.
                if ($strattempthighlight = quiz_report_highlighting_grading_method(
                        $quiz, $this->qmsubselect, $options->onlygraded)) {
                    echo '<div class="quizattemptcounts">' . $strattempthighlight . '</div>';
                }
            }

            // Define table columns.
            $columns = array();
            $headers = array();

            if (!$table->is_downloading() && $options->checkboxcolumn) {
            //tdmu-disable checkbox column
//                $columns[] = 'checkbox';
//                $headers[] = null;
            }

            $this->add_user_columns($table, $columns, $headers);
            $this->add_state_column($columns, $headers);
            $this->add_time_columns($columns, $headers);

            $this->add_grade_columns($quiz, $options->usercanseegrades, $columns, $headers, false);

            if (!$table->is_downloading() && has_capability('mod/quiz:regrade', $this->context) &&
                    $this->has_regraded_questions($from, $where, $params)) {
                $columns[] = 'regraded';
                $headers[] = get_string('regrade', 'quiz_overview');
            }

            if ($options->slotmarks) {
            //tdmu-disable slotmarks
//                foreach ($questions as $slot => $question) {
//                    // Ignore questions of zero length.
//                    $columns[] = 'qsgrade' . $slot;
//                    $header = get_string('qbrief', 'quiz', $question->number);
//                    if (!$table->is_downloading()) {
//                        $header .= '<br />';
//                    } else {
//                        $header .= ' ';
//                    }
//                    $header .= '/' . quiz_rescale_grade($question->maxmark, $quiz, 'question');
//                    $headers[] = $header;
//                }
            }

            $this->set_up_table_columns($table, $columns, $headers, $this->get_base_url(), $options, false);
            $table->set_attribute('class', 'generaltable generalbox grades');
//var_dump($options);
            $table->out($options->pagesize, true);
        }

        return true;
    }

    /**
     * Extends parent function processing any submitted actions.
     *
     * @param object $quiz
     * @param object $cm
     * @param int $currentgroup
     * @param \core\dml\sql_join $groupstudentsjoins (joins, wheres, params)
     * @param \core\dml\sql_join $allowedjoins (joins, wheres, params)
     * @param moodle_url $redirecturl
     */
    protected function process_actions($quiz, $cm, $currentgroup, \core\dml\sql_join $groupstudentsjoins,
            \core\dml\sql_join $allowedjoins, $redirecturl) {
        parent::process_actions($quiz, $cm, $currentgroup, $groupstudentsjoins, $allowedjoins, $redirecturl);

        if (empty($currentgroup) || $this->hasgroupstudents) {
            if (optional_param('regrade', 0, PARAM_BOOL) && confirm_sesskey()) {
                if ($attemptids = optional_param_array('attemptid', array(), PARAM_INT)) {
                    $this->start_regrade($quiz, $cm);
                    $this->regrade_attempts($quiz, false, $groupstudentsjoins, $attemptids);
                    $this->finish_regrade($redirecturl);
                }
            }
        }

        if (optional_param('regradeall', 0, PARAM_BOOL) && confirm_sesskey()) {
            $this->start_regrade($quiz, $cm);
            $this->regrade_attempts($quiz, false, $groupstudentsjoins);
            $this->finish_regrade($redirecturl);

        } else if (optional_param('regradealldry', 0, PARAM_BOOL) && confirm_sesskey()) {
            $this->start_regrade($quiz, $cm);
            $this->regrade_attempts($quiz, true, $groupstudentsjoins);
            $this->finish_regrade($redirecturl);

        } else if (optional_param('regradealldrydo', 0, PARAM_BOOL) && confirm_sesskey()) {
            $this->start_regrade($quiz, $cm);
            $this->regrade_attempts_needing_it($quiz, $groupstudentsjoins);
            $this->finish_regrade($redirecturl);
        }

        if (optional_param('savecolontitles', '', PARAM_TEXT) && confirm_sesskey()) {
            $this->savecolontitles($quiz);
        }
        
        if (optional_param('resetcolontitles', '', PARAM_TEXT) && confirm_sesskey()) {
            //var_dump($redirecturl);
            $this->resetcolontitles($quiz);
            redirect($redirecturl);
        }
                
    }

    protected function savecolontitles($quiz) {
        global $DB;
        
        if ($formdata = $this->form->get_data()) {

        $colontitles = new stdClass();
        $colontitles->quizid = $quiz->id;
        $colontitles->cheader = $formdata->cheader['text'];
        $colontitles->cfooter = $formdata->cfooter['text'];
        $colontitles->cheaderformat = $formdata->cheader['format'];
        $colontitles->cfooterformat = $formdata->cfooter['format'];
        $colontitles->gradescaletype = $formdata->gradescaletype;
        
        $transaction = $DB->start_delegated_transaction();
        
        $saved_colontitles = $DB->get_record('quiz_exampaper_colontitles', array('quizid'=>$quiz->id));
//var_dump($saved_colontitles);        
        if ($saved_colontitles) {
            $colontitles->id = $saved_colontitles->id;
            $DB->update_record('quiz_exampaper_colontitles', $colontitles, false);
        } else {
            $DB->insert_record('quiz_exampaper_colontitles', $colontitles, false);
        }
        
        $transaction->allow_commit();
//die();        
}
    }
    
    protected function resetcolontitles($quiz) {
        global $DB;
        
        $transaction = $DB->start_delegated_transaction();
        $DB->delete_records('quiz_exampaper_colontitles', array('quizid'=>$quiz->id));
        $transaction->allow_commit();
    }
    
    /**
     * Add all the user-related columns to the $columns and $headers arrays.
     * @param table_sql $table the table being constructed.
     * @param array $columns the list of columns. Added to.
     * @param array $headers the columns headings. Added to.
     */
    protected function add_user_columns($table, &$columns, &$headers) {
        global $CFG;
        if (!$table->is_downloading() && $CFG->grade_report_showuserimage) {
            $columns[] = 'picture';
            $headers[] = '';
        }

        $columns[] = 'fullname';
        $headers[] = get_string('firstname').', '.get_string('lastname');

        if (!in_array('email', $columns)) {
            $columns[] = 'email';
            $headers[] = get_string('email');
        }
    }
	
    /**
     * Add all the time-related columns to the $columns and $headers arrays.
     * @param array $columns the list of columns. Added to.
     * @param array $headers the columns headings. Added to.
	 * TDMU override - only timefinish and duration columns
     */
    protected function add_time_columns(&$columns, &$headers) {
        $columns[] = 'timefinish';
        $headers[] = get_string('timecompleted', 'quiz');

        $columns[] = 'duration';
        $headers[] = get_string('attemptduration', 'quiz');
    }
    
    /**
     * Check necessary capabilities, and start the display of the regrade progress page.
     * @param object $quiz the quiz settings.
     * @param object $cm the cm object for the quiz.
     */
    protected function start_regrade($quiz, $cm) {
        require_capability('mod/quiz:regrade', $this->context);
        $this->print_header_and_tabs($cm, $this->course, $quiz, $this->mode);
    }

    /**
     * Finish displaying the regrade progress page.
     * @param moodle_url $nexturl where to send the user after the regrade.
     * @uses exit. This method never returns.
     */
    protected function finish_regrade($nexturl) {
        global $OUTPUT;
        \core\notification::success(get_string('regradecomplete', 'quiz_overview'));
        echo $OUTPUT->continue_button($nexturl);
        echo $OUTPUT->footer();
        die();
    }

    /**
     * Unlock the session and allow the regrading process to run in the background.
     */
    protected function unlock_session() {
        \core\session\manager::write_close();
        ignore_user_abort(true);
    }

    /**
     * Regrade a particular quiz attempt. Either for real ($dryrun = false), or
     * as a pretend regrade to see which fractions would change. The outcome is
     * stored in the quiz_exampaper_regrades table.
     *
     * Note, $attempt is not upgraded in the database. The caller needs to do that.
     * However, $attempt->sumgrades is updated, if this is not a dry run.
     *
     * @param object $attempt the quiz attempt to regrade.
     * @param bool $dryrun if true, do a pretend regrade, otherwise do it for real.
     * @param array $slots if null, regrade all questions, otherwise, just regrade
     *      the quetsions with those slots.
     */
    protected function regrade_attempt($attempt, $dryrun = false, $slots = null) {
        global $DB;
        // Need more time for a quiz with many questions.
        core_php_time_limit::raise(300);

        $transaction = $DB->start_delegated_transaction();

        $quba = question_engine::load_questions_usage_by_activity($attempt->uniqueid);

        if (is_null($slots)) {
            $slots = $quba->get_slots();
        }

        $finished = $attempt->state == quiz_attempt::FINISHED;
        foreach ($slots as $slot) {
            $qqr = new stdClass();
            $qqr->oldfraction = $quba->get_question_fraction($slot);

            $quba->regrade_question($slot, $finished);

            $qqr->newfraction = $quba->get_question_fraction($slot);

            if (abs($qqr->oldfraction - $qqr->newfraction) > 1e-7) {
                $qqr->questionusageid = $quba->get_id();
                $qqr->slot = $slot;
                $qqr->regraded = empty($dryrun);
                $qqr->timemodified = time();
                $DB->insert_record('quiz_exampaper_regrades', $qqr, false);
            }
        }

        if (!$dryrun) {
            question_engine::save_questions_usage_by_activity($quba);
        }

        $transaction->allow_commit();

        // Really, PHP should not need this hint, but without this, we just run out of memory.
        $quba = null;
        $transaction = null;
        gc_collect_cycles();
    }

    /**
     * Regrade attempts for this quiz, exactly which attempts are regraded is
     * controlled by the parameters.
     * @param object $quiz the quiz settings.
     * @param bool $dryrun if true, do a pretend regrade, otherwise do it for real.
     * @param \core\dml\sql_join|array $groupstudentsjoins empty for all attempts, otherwise regrade attempts
     * for these users.
     * @param array $attemptids blank for all attempts, otherwise only regrade
     * attempts whose id is in this list.
     */
    protected function regrade_attempts($quiz, $dryrun = false,
            \core\dml\sql_join$groupstudentsjoins = null, $attemptids = array()) {
        global $DB;
        $this->unlock_session();

        $sql = "SELECT quiza.*
                  FROM {quiz_attempts} quiza";
        $where = "quiz = :qid AND preview = 0";
        $params = array('qid' => $quiz->id);

        if ($this->hasgroupstudents && !empty($groupstudentsjoins->joins)) {
            $sql .= "\nJOIN {user} u ON u.id = quiza.userid
                    {$groupstudentsjoins->joins}";
            $where .= " AND {$groupstudentsjoins->wheres}";
            $params += $groupstudentsjoins->params;
        }

        if ($attemptids) {
            $aids = join(',', $attemptids);
            $where .= " AND quiza.id IN ({$aids})";
        }

        $sql .= "\nWHERE {$where}";
        $attempts = $DB->get_records_sql($sql, $params);
        if (!$attempts) {
            return;
        }

        $this->clear_regrade_table($quiz, $groupstudentsjoins);

        $progressbar = new progress_bar('quiz_exampaper_regrade', 500, true);
        $a = array(
            'count' => count($attempts),
            'done'  => 0,
        );
        foreach ($attempts as $attempt) {
            $this->regrade_attempt($attempt, $dryrun);
            $a['done']++;
            $progressbar->update($a['done'], $a['count'],
                    get_string('regradingattemptxofy', 'quiz_overview', $a));
        }

        if (!$dryrun) {
            $this->update_overall_grades($quiz);
        }
    }

    /**
     * Regrade those questions in those attempts that are marked as needing regrading
     * in the quiz_exampaper_regrades table.
     * @param object $quiz the quiz settings.
     * @param \core\dml\sql_join $groupstudentsjoins empty for all attempts, otherwise regrade attempts
     * for these users.
     */
    protected function regrade_attempts_needing_it($quiz, \core\dml\sql_join $groupstudentsjoins) {
        global $DB;
        $this->unlock_session();

        $join = '{quiz_exampaper_regrades} qqr ON qqr.questionusageid = quiza.uniqueid';
        $where = "quiza.quiz = :qid AND quiza.preview = 0 AND qqr.regraded = 0";
        $params = array('qid' => $quiz->id);

        // Fetch all attempts that need regrading.
        if ($this->hasgroupstudents && !empty($groupstudentsjoins->joins)) {
            $join .= "\nJOIN {user} u ON u.id = quiza.userid
                    {$groupstudentsjoins->joins}";
            $where .= " AND {$groupstudentsjoins->wheres}";
            $params += $groupstudentsjoins->params;
        }

        $toregrade = $DB->get_recordset_sql("
                SELECT quiza.uniqueid, qqr.slot
                  FROM {quiz_attempts} quiza
                  JOIN $join
                 WHERE $where", $params);

        $attemptquestions = array();
        foreach ($toregrade as $row) {
            $attemptquestions[$row->uniqueid][] = $row->slot;
        }
        $toregrade->close();

        if (!$attemptquestions) {
            return;
        }

        $attempts = $DB->get_records_list('quiz_attempts', 'uniqueid',
                array_keys($attemptquestions));

        $this->clear_regrade_table($quiz, $groupstudentsjoins);

        $progressbar = new progress_bar('quiz_exampaper_regrade', 500, true);
        $a = array(
            'count' => count($attempts),
            'done'  => 0,
        );
        foreach ($attempts as $attempt) {
            $this->regrade_attempt($attempt, false, $attemptquestions[$attempt->uniqueid]);
            $a['done']++;
            $progressbar->update($a['done'], $a['count'],
                    get_string('regradingattemptxofy', 'quiz_overview', $a));
        }

        $this->update_overall_grades($quiz);
    }

    /**
     * Count the number of attempts in need of a regrade.
     * @param object $quiz the quiz settings.
     * @param \core\dml\sql_join $groupstudentsjoins (joins, wheres, params) If this is given, only data relating
     * to these users is cleared.
     */
    protected function count_question_attempts_needing_regrade($quiz, \core\dml\sql_join $groupstudentsjoins) {
        global $DB;

        $userjoin = '';
        $usertest = '';
        $params = array();
        if ($this->hasgroupstudents) {
            $userjoin = "JOIN {user} u ON u.id = quiza.userid
                    {$groupstudentsjoins->joins}";
            $usertest = "{$groupstudentsjoins->wheres} AND u.id = quiza.userid AND ";
            $params = $groupstudentsjoins->params;
        }

        $params['cquiz'] = $quiz->id;
        $sql = "SELECT COUNT(DISTINCT quiza.id)
                  FROM {quiz_attempts} quiza
                  JOIN {quiz_exampaper_regrades} qqr ON quiza.uniqueid = qqr.questionusageid
                $userjoin
                 WHERE
                      $usertest
                      quiza.quiz = :cquiz AND
                      quiza.preview = 0 AND
                      qqr.regraded = 0";
        return $DB->count_records_sql($sql, $params);
    }

    /**
     * Are there any pending regrades in the table we are going to show?
     * @param string $from tables used by the main query.
     * @param string $where where clause used by the main query.
     * @param array $params required by the SQL.
     * @return bool whether there are pending regrades.
     */
    protected function has_regraded_questions($from, $where, $params) {
        global $DB;
        return $DB->record_exists_sql("
                SELECT 1
                  FROM {$from}
                  JOIN {quiz_exampaper_regrades} qor ON qor.questionusageid = quiza.uniqueid
                 WHERE {$where}", $params);
    }

    /**
     * Remove all information about pending/complete regrades from the database.
     * @param object $quiz the quiz settings.
     * @param \core\dml\sql_join $groupstudentsjoins (joins, wheres, params). If this is given, only data relating
     * to these users is cleared.
     */
    protected function clear_regrade_table($quiz, \core\dml\sql_join $groupstudentsjoins) {
        global $DB;

        // Fetch all attempts that need regrading.
        $select = "questionusageid IN (
                    SELECT uniqueid
                      FROM {quiz_attempts} quiza";
        $where = "WHERE quiza.quiz = :qid";
        $params = array('qid' => $quiz->id);
        if ($this->hasgroupstudents && !empty($groupstudentsjoins->joins)) {
            $select .= "\nJOIN {user} u ON u.id = quiza.userid
                    {$groupstudentsjoins->joins}";
            $where .= " AND {$groupstudentsjoins->wheres}";
            $params += $groupstudentsjoins->params;
        }
        $select .= "\n$where)";

        $DB->delete_records_select('quiz_exampaper_regrades', $select, $params);
    }

    /**
     * Update the final grades for all attempts. This method is used following
     * a regrade.
     * @param object $quiz the quiz settings.
     * @param array $userids only update scores for these userids.
     * @param array $attemptids attemptids only update scores for these attempt ids.
     */
    protected function update_overall_grades($quiz) {
        quiz_update_all_attempt_sumgrades($quiz);
        quiz_update_all_final_grades($quiz);
        quiz_update_grades($quiz);
    }

    /**
     * Create a filename for use when downloading data from a quiz report. It is
     * expected that this will be passed to flexible_table::is_downloading, which
     * cleans the filename of bad characters and adds the file extension.
     * @param string $report the type of report.
     * @param string $courseshortname the course shortname.
     * @param string $quizname the quiz name.
     * @return string the filename.
    */    
    protected function quiz_report_download_filename_extended($report, $courseshortname, $coursesectionname, $quizname, $groupid) {
        if ($groupid > 0) {
            $groupname = groups_get_group_name($groupid);
        } else {
            $groupname = 'all';
        }
        $filename = trim($courseshortname . '-' . $groupname . '-' . format_string($quizname, true) . '-' . $coursesectionname . '-' . $report);
        
        return $this->truncate($filename, 200);
}
    /**  
     * Truncate long text (especially - for usage with filenames)
    */
    private function truncate($string, $length, $dots = "_") {
        return (strlen($string) > $length) ? substr($string, 0, $length - strlen($dots)) . $dots : $string;
    }
	
    /**
     * Add all the grade and feedback columns, if applicable, to the $columns
     * and $headers arrays.
     * @param object $quiz the quiz settings.
     * @param bool $usercanseegrades whether the user is allowed to see grades for this quiz.
     * @param array $columns the list of columns. Added to.
     * @param array $headers the columns headings. Added to.
     * @param bool $includefeedback whether to include the feedbacktext columns
     */
    protected function add_grade_columns($quiz, $usercanseegrades, &$columns, &$headers, $includefeedback = true) {
        if ($usercanseegrades) {
            $columns[] = 'sumgrades';
            $headers[] = get_string('corectanswers', 'quiz_exampaper') . '/' .
                    quiz_format_grade($quiz, $quiz->grade);
			//tdmu special extra column (should be empty)
			$columns[] = 'extragrades';
			$headers[] = get_string('score', 'quiz_exampaper');
        }

        if ($includefeedback && quiz_has_feedback($quiz)) {
            $columns[] = 'feedbacktext';
            $headers[] = get_string('feedback', 'quiz');
        }
    }
}
