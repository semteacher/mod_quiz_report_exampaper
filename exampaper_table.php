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
 * This file defines the quiz grades table.
 *
 * @package   quiz_exampaper
 * @copyright 2008 Jamie Pratt
 * @copyright 2020 Andrii Semenets (semteacher@tdmu.edu.ua)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/report/attemptsreport_table.php');

/**
 * This is a table subclass for displaying the quiz grades report.
 *
 * @copyright 2008 Jamie Pratt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_exampaper_table extends quiz_attempts_report_table {

    protected $regradedqs = array();

    /**
     * Constructor
     * @param object $quiz
     * @param context $context
     * @param string $qmsubselect
     * @param quiz_exampaper_options $options
     * @param \core\dml\sql_join $groupstudentsjoins
     * @param \core\dml\sql_join $studentsjoins
     * @param array $questions
     * @param moodle_url $reporturl
     */
    public function __construct($quiz, $context, $qmsubselect,
                                quiz_exampaper_options $options, \core\dml\sql_join $groupstudentsjoins,
                                \core\dml\sql_join $studentsjoins, $questions, $reporturl) {
        parent::__construct('mod-quiz-report-exampaper-report', $quiz , $context,
                $qmsubselect, $options, $groupstudentsjoins, $studentsjoins, $questions, $reporturl);
    }

    public function build_table() {
        global $DB;

        if (!$this->rawdata) {
            return;
        }

        $this->strtimeformat = str_replace(',', ' ', get_string('strftimedatetime'));
        parent::build_table();

        // End of adding the data from attempts. Now add averages at bottom.
        //tdmu-remove separator
//        $this->add_separator();

        if (!empty($this->groupstudentsjoins->joins)) {
            $sql = "SELECT DISTINCT u.id
                      FROM {user} u
                    {$this->groupstudentsjoins->joins}
                     WHERE {$this->groupstudentsjoins->wheres}";
            $groupstudents = $DB->get_records_sql($sql, $this->groupstudentsjoins->params);
            if ($groupstudents) {
            //tdmu-remove average
//                $this->add_average_row(get_string('groupavg', 'grades'), $this->groupstudentsjoins);
            }
        }

        if (!empty($this->studentsjoins->joins)) {
            $sql = "SELECT DISTINCT u.id
                      FROM {user} u
                    {$this->studentsjoins->joins}
                     WHERE {$this->studentsjoins->wheres}";
            $students = $DB->get_records_sql($sql, $this->studentsjoins->params);
            if ($students) {
            //tdmu-remove average
//                $this->add_average_row(get_string('overallaverage', 'grades'), $this->studentsjoins);
            }
        }
        
        if ($this->is_downloading()) {
                //echo '<div>';
                //echo $this->options->cfootertext;
                //echo html_writer::input_hidden_params($displayurl);
                //echo '</div>';        
        }
    }


    /**
     * Add an average grade over the attempts of a set of users.
     * @param string $label the title ot use for this row.
     * @param \core\dml\sql_join $usersjoins (joins, wheres, params) for the users to average over.
     */
    protected function add_average_row($label, \core\dml\sql_join $usersjoins) {
        global $DB;

        list($fields, $from, $where, $params) = $this->base_sql($usersjoins);
        $record = $DB->get_record_sql("
                SELECT AVG(quiza.sumgrades) AS grade, COUNT(quiza.sumgrades) AS numaveraged
                  FROM $from
                 WHERE $where", $params);
        $record->grade = quiz_rescale_grade($record->grade, $this->quiz, false);

        if ($this->is_downloading()) {
            $namekey = 'lastname';
        } else {
            $namekey = 'fullname';
        }
        $averagerow = array(
            $namekey    => $label,
            'sumgrades' => $this->format_average($record),
            'feedbacktext'=> strip_tags(quiz_report_feedback_for_grade(
                                        $record->grade, $this->quiz->id, $this->context))
        );

        if ($this->options->slotmarks) {
            $dm = new question_engine_data_mapper();
            $qubaids = new qubaid_join($from, 'quiza.uniqueid', $where, $params);
            $avggradebyq = $dm->load_average_marks($qubaids, array_keys($this->questions));

            $averagerow += $this->format_average_grade_for_questions($avggradebyq);
        }

        $this->add_data_keyed($averagerow);
    }

    /**
     * Helper userd by {@link add_average_row()}.
     * @param array $gradeaverages the raw grades.
     * @return array the (partial) row of data.
     */
    protected function format_average_grade_for_questions($gradeaverages) {
        $row = array();

        if (!$gradeaverages) {
            $gradeaverages = array();
        }

        foreach ($this->questions as $question) {
            if (isset($gradeaverages[$question->slot]) && $question->maxmark > 0) {
                $record = $gradeaverages[$question->slot];
                $record->grade = quiz_rescale_grade(
                        $record->averagefraction * $question->maxmark, $this->quiz, false);

            } else {
                $record = new stdClass();
                $record->grade = null;
                $record->numaveraged = 0;
            }

            $row['qsgrade' . $question->slot] = $this->format_average($record, true);
        }

        return $row;
    }

    /**
     * Format an entry in an average row.
     * @param object $record with fields grade and numaveraged
     */
    protected function format_average($record, $question = false) {
        if (is_null($record->grade)) {
            $average = '-';
        } else if ($question) {
            $average = quiz_format_question_grade($this->quiz, $record->grade);
        } else {
            $average = quiz_format_grade($this->quiz, $record->grade);
        }

        if ($this->download) {
            return $average;
        } else if (is_null($record->numaveraged) || $record->numaveraged == 0) {
            return html_writer::tag('span', html_writer::tag('span',
                    $average, array('class' => 'average')), array('class' => 'avgcell'));
        } else {
            return html_writer::tag('span', html_writer::tag('span',
                    $average, array('class' => 'average')) . ' ' . html_writer::tag('span',
                    '(' . $record->numaveraged . ')', array('class' => 'count')),
                    array('class' => 'avgcell'));
        }
    }

    protected function submit_buttons() {
    //tdmu-disable regrade buttons at bottom of table - wrap_html_finish - too
//        if (has_capability('mod/quiz:regrade', $this->context)) {
//            echo '<input type="submit" class="btn btn-secondary m-r-1" name="regrade" value="' .
//                    get_string('regradeselected', 'quiz_overview') . '"/>';
//        }
//        parent::submit_buttons();
    }

    public function col_sumgrades($attempt) {
        if ($attempt->state != quiz_attempt::FINISHED) {
            return '-';
        }

        $grade = quiz_rescale_grade($attempt->sumgrades, $this->quiz);
        if ($this->is_downloading()) {
            return $grade;
        }

        if (isset($this->regradedqs[$attempt->usageid])) {
            $newsumgrade = 0;
            $oldsumgrade = 0;
            foreach ($this->questions as $question) {
                if (isset($this->regradedqs[$attempt->usageid][$question->slot])) {
                    $newsumgrade += $this->regradedqs[$attempt->usageid]
                            [$question->slot]->newfraction * $question->maxmark;
                    $oldsumgrade += $this->regradedqs[$attempt->usageid]
                            [$question->slot]->oldfraction * $question->maxmark;
                } else {
                    $newsumgrade += $this->lateststeps[$attempt->usageid]
                            [$question->slot]->fraction * $question->maxmark;
                    $oldsumgrade += $this->lateststeps[$attempt->usageid]
                            [$question->slot]->fraction * $question->maxmark;
                }
            }
            $newsumgrade = quiz_rescale_grade($newsumgrade, $this->quiz);
            $oldsumgrade = quiz_rescale_grade($oldsumgrade, $this->quiz);
            $grade = html_writer::tag('del', $oldsumgrade) . '/' .
                    html_writer::empty_tag('br') . $newsumgrade;
        }
        return html_writer::link(new moodle_url('/mod/quiz/review.php',
                array('attempt' => $attempt->attempt)), $grade,
                array('title' => get_string('reviewattempt', 'quiz')));
    }

    /**
     * @param string $colname the name of the column.
     * @param object $attempt the row of data - see the SQL in display() in
     * mod/quiz/report/overview/report.php to see what fields are present,
     * and what they are called.
     * @return string the contents of the cell.
     */
    public function other_cols($colname, $attempt) {
        if (!preg_match('/^qsgrade(\d+)$/', $colname, $matches)) {
            return null;
        }
        $slot = $matches[1];

        $question = $this->questions[$slot];
        if (!isset($this->lateststeps[$attempt->usageid][$slot])) {
            return '-';
        }

        $stepdata = $this->lateststeps[$attempt->usageid][$slot];
        $state = question_state::get($stepdata->state);

        if ($question->maxmark == 0) {
            $grade = '-';
        } else if (is_null($stepdata->fraction)) {
            if ($state == question_state::$needsgrading) {
                $grade = get_string('requiresgrading', 'question');
            } else {
                $grade = '-';
            }
        } else {
            $grade = quiz_rescale_grade(
                    $stepdata->fraction * $question->maxmark, $this->quiz, 'question');
        }

        if ($this->is_downloading()) {
            return $grade;
        }

        if (isset($this->regradedqs[$attempt->usageid][$slot])) {
            $gradefromdb = $grade;
            $newgrade = quiz_rescale_grade(
                    $this->regradedqs[$attempt->usageid][$slot]->newfraction * $question->maxmark,
                    $this->quiz, 'question');
            $oldgrade = quiz_rescale_grade(
                    $this->regradedqs[$attempt->usageid][$slot]->oldfraction * $question->maxmark,
                    $this->quiz, 'question');

            $grade = html_writer::tag('del', $oldgrade) . '/' .
                    html_writer::empty_tag('br') . $newgrade;
        }

        return $this->make_review_link($grade, $attempt, $slot);
    }

    public function col_regraded($attempt) {
        if ($attempt->regraded == '') {
            return '';
        } else if ($attempt->regraded == 0) {
            return get_string('needed', 'quiz_overview');
        } else if ($attempt->regraded == 1) {
            return get_string('done', 'quiz_overview');
        }
    }

    protected function requires_latest_steps_loaded() {
        return $this->options->slotmarks;
    }

    protected function is_latest_step_column($column) {
        if (preg_match('/^qsgrade([0-9]+)/', $column, $matches)) {
            return $matches[1];
        }
        return false;
    }

    protected function get_required_latest_state_fields($slot, $alias) {
        return "$alias.fraction * $alias.maxmark AS qsgrade$slot";
    }

    public function query_db($pagesize, $useinitialsbar = true) {
        parent::query_db($pagesize, $useinitialsbar);

        if ($this->options->slotmarks && has_capability('mod/quiz:regrade', $this->context)) {
            $this->regradedqs = $this->get_regraded_questions();
        }
    }

    /**
     * Get all the questions in all the attempts being displayed that need regrading.
     * @return array A two dimensional array $questionusageid => $slot => $regradeinfo.
     */
    protected function get_regraded_questions() {
        global $DB;

        $qubaids = $this->get_qubaids_condition();
        $regradedqs = $DB->get_records_select('quiz_exampaper_regrades',
                'questionusageid ' . $qubaids->usage_id_in(), $qubaids->usage_id_in_params());
        return quiz_report_index_by_keys($regradedqs, array('questionusageid', 'slot'));
    }
    
        //tdmu-override def
        public function wrap_html_start() {
        if ($this->is_downloading() || !$this->includecheckboxes) {
            return;
        }

        $url = $this->options->get_url();
        $url->param('sesskey', sesskey());

        echo '<div id="tablecontainer">';
        echo '<form id="attemptsform" method="post" action="' . $url->out_omit_querystring() . '">';

        echo html_writer::input_hidden_params($url);
        echo '<div>';
    }

    //tdmu-override def
    public function wrap_html_finish() {
        if ($this->is_downloading() || !$this->includecheckboxes) {
            return;
        }

        //tdmu-disable commands below table
//        echo '<div id="commands">';        
//        echo '<a href="javascript:select_all_in(\'DIV\', null, \'tablecontainer\');">' .
//                get_string('selectall', 'quiz') . '</a> / ';
//        echo '<a href="javascript:deselect_all_in(\'DIV\', null, \'tablecontainer\');">' .
//                get_string('selectnone', 'quiz') . '</a> ';
//        echo '&nbsp;&nbsp;';
//        $this->submit_buttons();
//        echo '</div>';

        // Close the form.
        echo '</div>';
        echo '</form></div>';
    }
    
        /**
     * Get the html for the download buttons
     *
     * Usually only use internally
     */
    public function download_buttons() {
        global $OUTPUT;

        if ($this->is_downloadable() && !$this->is_downloading()) {
            //tdmu - set default download option to 'html' there?
            //echo 'search: override!';
            //$select = $OUTPUT->download_dataformat_selector(get_string('downloadas', 'table'),
            //            $this->baseurl->out_omit_querystring(), 'download', $this->baseurl->params());
            $select = $OUTPUT->download_dataformat_selector(get_string('exampaperdownload', 'quiz_exampaper'),
                    $this->baseurl->out_omit_querystring(), 'download', $this->baseurl->params());
            $select = str_replace('option value="doc"', 'option value="doc" selected', $select);
            $select = str_replace('select name="download" id="downloadtype_download"', 'select name="download" id="downloadtype_download" hidden', $select);

            return $select;
        } else {
            return '';
        }
    }
    
    /**
     * This function is not part of the public api.
     * You don't normally need to call this. It is called automatically when
     * needed when you start adding data to the table.
     *
     */
    function start_output() {
        $this->started_output = true;
        if ($this->exportclass!==null) {
            $this->exportclass->start_table($this->sheettitle);
            //$this->exportclass->output_headers($this->headers); //old origin call

            if ($this->options->group > 0) {
                $groupname = groups_get_group_name($this->options->group);
            } else {
                $groupname = 'all students';
            }
            $this->options->cheadertext = str_replace("{group}", $groupname, $this->options->cheadertext);

            \dataformat_doc\writer::write_document_header($this->filename);
            \dataformat_doc\writer::write_div($this->options->cheadertext);
            \dataformat_doc\writer::write_table_header($this->headers);
        } else {
            $this->start_html();
            $this->print_headers();
            echo html_writer::start_tag('tbody');
        }
    }

    /**
     * You should call this to finish outputting the table data after adding
     * data to the table with add_data or add_data_keyed.
     *
     */
    function finish_output($closeexportclassdoc = true) {
        if ($this->exportclass!==null) {
            //$this->exportclass->finish_table(); //old origin call
            \dataformat_doc\writer::write_table_end();
            \dataformat_doc\writer::write_div($this->options->cfootertext);
            \dataformat_doc\writer::write_document_end();

            if ($closeexportclassdoc) {
                $this->exportclass->finish_document();
            }
        } else {
            $this->finish_html();
        }
    }

	protected function tdmu_extragrades($grade) {
		$grade_tdmu = $grade;
        if ($this->options->gradescaletype == quiz_exampaper_report::GRADESCALE_DIFCREDIT) {
            if ($grade <= 24) {$grade_tdmu = get_string('fail', 'quiz_exampaper');}
            elseif ($grade <= 41) {$grade_tdmu = $grade + 25;}
            elseif ($grade == 42) {$grade_tdmu = 68;}
            elseif ($grade == 43) {$grade_tdmu = 70;}
            elseif ($grade == 44) {$grade_tdmu = 72;}
            elseif ($grade == 45) {$grade_tdmu = 74;}
            elseif ($grade == 46) {$grade_tdmu = 76;}
            elseif ($grade == 47) {$grade_tdmu = 78;}
            else {$grade_tdmu = 80;}
        } elseif ($this->options->gradescaletype == quiz_exampaper_report::GRADESCALE_TESTEXAM) {
            if ($grade <= 24) {$grade_tdmu = get_string('fail', 'quiz_exampaper');}
            elseif ($grade <= 26) {$grade_tdmu = 38;}
            else {$grade_tdmu = $grade + 12;}
        } elseif ($this->options->gradescaletype == quiz_exampaper_report::GRADESCALE_ESSAYEXAM) {
            if ($grade <= 3) {$grade_tdmu = get_string('fail', 'quiz_exampaper');}
            elseif ($grade <= 7) {$grade_tdmu = $grade + 9;}
            elseif ($grade == 8) {$grade_tdmu = 18;}
            else {$grade_tdmu = 20;}
        }

		//$grade_tdmu = $grade;
		return $grade_tdmu;
	}
    /**
     * Generate the display of the checkbox column.
     * @param object $attempt the table row being output.
     * @return string HTML content to go inside the td.
     */
    public function col_extragrades($attempt) {
        //if ($attempt->attempt) {
        //    return '<input type="checkbox" name="attemptid[]" value="'.$attempt->attempt.'" />';
        //} else {
        //    return '';
        //}
		
		//return '';
        if ($attempt->state != quiz_attempt::FINISHED) {
            return '-';
        }

        $grade = quiz_rescale_grade($attempt->sumgrades, $this->quiz);
		$grade_tdmu = $this->tdmu_extragrades($grade);
        if ($this->is_downloading()) {
            return $grade_tdmu;
        }

        if (isset($this->regradedqs[$attempt->usageid])) {
            $newsumgrade = 0;
            $oldsumgrade = 0;
            foreach ($this->questions as $question) {
                if (isset($this->regradedqs[$attempt->usageid][$question->slot])) {
                    $newsumgrade += $this->regradedqs[$attempt->usageid]
                            [$question->slot]->newfraction * $question->maxmark;
                    $oldsumgrade += $this->regradedqs[$attempt->usageid]
                            [$question->slot]->oldfraction * $question->maxmark;
                } else {
                    $newsumgrade += $this->lateststeps[$attempt->usageid]
                            [$question->slot]->fraction * $question->maxmark;
                    $oldsumgrade += $this->lateststeps[$attempt->usageid]
                            [$question->slot]->fraction * $question->maxmark;
                }
            }
            $newsumgrade = quiz_rescale_grade($newsumgrade, $this->quiz);
			$newsumgrade_tdmu = $this->tdmu_extragrades($newsumgrade);
            $oldsumgrade = quiz_rescale_grade($oldsumgrade, $this->quiz);
			$oldsumgrade_tdmu = $this->tdmu_extragrades($oldsumgrade);
            $grade_tdmu = html_writer::tag('del', $oldsumgrade_tdmu) . '/' .
                    html_writer::empty_tag('br') . $newsumgrade_tdmu;
        }
        return html_writer::link(new moodle_url('/mod/quiz/review.php',
                array('attempt' => $attempt->attempt)), $grade_tdmu,
                array('title' => get_string('reviewattempt', 'quiz')));		
    }
}
