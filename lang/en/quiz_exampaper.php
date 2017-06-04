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
 * Strings for component 'quiz_exampaper', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   quiz_exampaper
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['exampaper'] = 'Exam Grades Paper';
$string['exampaperdownload'] = 'Download report paper: ';
$string['exampaperfilename'] = 'examgradespaper';
$string['exampaperreport'] = 'Exam grades paper report';
$string['exampapercolontitlesdisplayoptions'] = 'Report colontitles';
$string['exampapercheader'] = 'Colontitles - report header';
$string['exampapercfooter'] = 'Colontitles - report footer';
$string['exampapersavecolontitles'] = 'Save static colontitles';
$string['exampaperesetcolontitles'] = 'Reset colontitles to default';
$string['exampaperesetcolontitlescofirmation'] = 'Are you absolutely sure? All your changes of the colontites will be completely deleted !';
$string['exampapercheaderdefault'] = '
<p style="text-align: center;">МІНІСТЕРСТВО ОХОРОНИ ЗДОРОВ\'Я УКРАЇНИ</p><p style="text-align: center;"><b>Тернопільський державний медичний університет імені І.Я.Горбачевського</b></p><p>
</p>
<p style="text-align: left;">Факультет: {$a->facultyname}<br>
Спеціальність: {$a->specialityname}<br>
Група: {$a->groupname}</span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span>20____/20____ н.р.</span>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <span>Курс ____</span></p>
<h4 style="text-align: center;">ЕКЗАМЕНАЦІЙНА ВІДОМІСТЬ № _________</h4>
<p>з {$a->subjectname}<br>
за ______ семестр, ______________ _____________</p>
';
$string['exampapercfooterdefault'] = '
<p>Голова комісії ________________________________________________________<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;(вчені звання, прізвище та ініціали) &nbsp; &nbsp;(підпис)</p>
<p>Члени комісії&nbsp;________________________________________________________<br>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;(вчені звання, прізвище та ініціали) &nbsp; &nbsp;(підпис)</p>
<p>_________________________________________________________________________</p>
<p>_________________________________________________________________________</p>
<p>1. Навпроти прізвища студента, який не з\'явився на підсумковий контроль, екзаменатор вказує - "не з\'явився"<br>2. Відомість подається в деканат не пізніше наступного дня після проведення підсумкового контролю.</p>
';