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
$string['pluginname'] = 'Exam Grades Paper';
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
<p style="margin:3pt 0pt 3pt 0pt;text-align:center;">МІНІСТЕРСТВО ОХОРОНИ ЗДОРОВ\'Я УКРАЇНИ</p>
<p style="margin:3pt 0pt 3pt 0pt;text-align:center;"><b>Тернопільський національний медичний університет імені І.Я.Горбачевського</b></p>
<table border=0 cellspacing=0 cellpadding=0 style="border-collapse:collapse">
<tbody>
<tr>
<td style="width:100pt;border:none;padding:0.05cm 0.1cm 0.05cm 0.1cm;">Факультет</td>
<td colspan="4" style="border:none;border-bottom:solid windowtext 1.0pt;"></td>
<td style="border:none;"></td>
</tr>
<tr>
<td style="width:100pt;border:none;padding:0.05cm 0.1cm 0.05cm 0.1cm;">Спеціальність</td>
<td colspan="4" style="border:none;border-bottom:solid windowtext 1.0pt;"></td>
<td style="border:none;"></td>
</tr>
<tr>
<td style="width:100pt;border:none;padding:0.05cm 0.1cm 0.05cm 0.1cm;">Навчальний рік</td>
<td style="text-align:center;width:100pt;border:none;border-bottom:solid windowtext 1.0pt;">2017/2018</td>
<td style="border:none;text-align:right;padding:0.05cm 0.1cm 0.05cm 0.1cm;">Курс</td>
<td style="text-align:center;width:50pt;border:none;border-bottom:solid windowtext 1.0pt;"></td>
<td style="border:none;text-align:right;padding:0.05cm 0.1cm 0.05cm 0.1cm;">Група</td>
<td style="text-align:center;width:50pt;border:none;border-bottom:solid windowtext 1.0pt;"></td>
</tr>
</tbody>
</table>
<h4 style="margin:6pt 0pt 6pt 0pt;text-align:center;">ЕКЗАМЕНАЦІЙНА ВІДОМІСТЬ № _________</h4>
<table border=0 cellspacing=0 cellpadding=0 style="border-collapse:collapse">
<tbody>
<tr>
<td style="width:110pt;border:none;padding:0.05cm 0.1cm 0.05cm 0.1cm;">З (назва дисципліни)</td>
<td colspan="5" style="border:none;border-bottom:solid windowtext 1.0pt;"></td>
</tr>
<tr>
<td style="border:none;padding:0.05cm 0.1cm 0.05cm 0.1cm;">Семестр</td>
<td style="text-align:center;width:50pt;border:none;border-bottom:solid windowtext 1.0pt;"></td>
<td style="border:none;text-align:right;width:100pt;padding:0.05cm 0.1cm 0.05cm 0.1cm;">Форма контролю</td>
<td style="text-align:center;width:100pt;border:none;border-bottom:solid windowtext 1.0pt;"></td>
<td style="border:none;text-align:right;padding:0.05cm 0.1cm 0.05cm 0.1cm;">Дата</td>
<td style="text-align:center;width:90pt;border:none;border-bottom:solid windowtext 1.0pt;"></td>
</tr>
</tbody>
</table>
<br>
';
$string['exampapercfooterdefault'] = '
<table border=0 cellspacing=0 cellpadding=0 style="border-collapse:collapse">
<tbody>
<tr>
<td style="width:100pt;border:none;padding:0.05cm 0.1cm 0.05cm 0.1cm;">Голова комісії</td>
<td style="border:none;border-bottom:solid windowtext 1.0pt;"></td>
</tr>
<tr>
<td style="width:100pt;border:none;padding:0.05cm 0.1cm 0.05cm 0.1cm;"></td>
<td style="text-align:center;border:none;">(вчені звання, прізвище та ініціали)&nbsp; &nbsp;(підпис)</td>
</tr>
<tr>
<td style="width:100pt;border:none;padding:0.05cm 0.1cm 0.05cm 0.1cm;">Члени комісії</td>
<td style="border:none;border-bottom:solid windowtext 1.0pt;"></td>
</tr>
<tr>
<td style="width:100pt;border:none;padding:0.05cm 0.1cm 0.05cm 0.1cm;"></td>
<td style="text-align:center;border:none;">(вчені звання, прізвище та ініціали)&nbsp; &nbsp;(підпис)</td>
</tr>
<tr>
<td colspan="2" style="border:none;border-bottom:solid windowtext 1.0pt;padding:0.1cm 0.1cm 0.1cm 0.1cm;">&nbsp;</td>
</tr>
<tr>
<td colspan="2" style="border:none;border-bottom:solid windowtext 1.0pt;padding:0.1cm 0.1cm 0.1cm 0.1cm;">&nbsp;</td>
</tr>
</tbody>
</table>
<p style="margin:3pt 0pt 3pt 0pt;">1. Навпроти прізвища студента, який не з\'явився на підсумковий контроль, екзаменатор вказує - "не з\'явився"<br>2. Відомість подається в деканат не пізніше наступного дня після проведення підсумкового контролю.</p>
';
$string['corectanswers'] = 'Correct';
$string['score'] = 'Grade';
$string['fail'] = 'Не склав';

$string['exampapergradescaletypedisplayoptions'] = 'TNMU: Exam type (grade scale)';
$string['exampapersavegradescaletype'] = 'Save exam type and apply grade scale';
$string['gradescaletypefrom'] = 'Choose exam type (set grade scale)';
$string['difcreditlabel'] = 'Differential credit (50-80)';
$string['testexamlabel'] = 'General Exam (38-60)';
$string['essayexamlabel'] = 'Oral exam (essay, 4-9)';