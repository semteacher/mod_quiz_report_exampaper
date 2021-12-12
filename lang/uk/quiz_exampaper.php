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
 * Strings for component 'quiz_exampaper', language 'uk', branch 'MOODLE_20_STABLE'
 *
 * @package   quiz_exampaper
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @copyright 2020 Andrii Semenets (semteacher@tdmu.edu.ua)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'Відомість оцінок';
$string['exampaper'] = 'Відомість оцінок';
$string['exampaperdownload'] = 'Завантажити відомість: ';
$string['exampaperfilename'] = 'examgradespaper';
$string['exampaperreport'] = 'звіт Відомість оцінок';
$string['exampapercolontitlesdisplayoptions'] = 'ТНМУ: Шкала оцінювання і колонтитули відомості';
$string['exampapercheader'] = 'Колонтитули - заголовок відомості';
$string['exampapercfooter'] = 'Колонтитули - підвал відомості';
$string['exampapersavecolontitles'] = 'Зберегти статичний заголовок і шкалу оцінювання';
$string['exampaperesetcolontitles'] = 'Відновити заголовок і шкалу оцінювання по-замовчуванню';
$string['exampaperesetcolontitlescofirmation'] = 'Ви впевнені? Всі Ваші зміни колонтитулів будуть втрачені!';
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
<td style="text-align:center;width:100pt;border:none;border-bottom:solid windowtext 1.0pt;">2021/2022</td>
<td style="border:none;text-align:right;padding:0.05cm 0.1cm 0.05cm 0.1cm;">Курс</td>
<td style="text-align:center;width:50pt;border:none;border-bottom:solid windowtext 1.0pt;"></td>
<td style="border:none;text-align:right;padding:0.05cm 0.1cm 0.05cm 0.1cm;">Група</td>
<td style="text-align:center;width:50pt;border:none;border-bottom:solid windowtext 1.0pt;" class="groupname">{group}</td>
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
$string['corectanswers'] = 'Вірно';
$string['score'] = 'Оцінка';
$string['fail'] = 'Не склав';

$string['exampapersavegradescaletype'] = 'Застосувати вибрану шкалу оцінювання';
$string['gradescaletypefrom'] = 'Вибертіть тип іспиту (шкалу оцінювання)';
$string['difcreditlabel'] = 'Диференційований залік (50-80)';
$string['testexamlabel'] = 'Іспит - тестова частина (38-60)';
$string['essayexamlabel'] = 'Іспит - усна частина (ессе, 4-9)';