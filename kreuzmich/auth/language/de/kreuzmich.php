<?php
/**
*
* @package phpBB Extension - Kreuzmich Auth
* @copyright (c) 2017 Christian Rubbert, Raphael Menke
* @license http://opensource.org/licenses/BSD-2-Clause BSD 2-Clause License
*
*/
if(!defined('IN_PHPBB'))
{
	exit;
}
if(empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'LOGIN_ERROR_EXTERNAL_AUTH_KREUZMICH' => 'Fehler:<br>Der <i class="icon fa-kreuzmich fa-fw" aria-hidden="true"></i> Server konnte nicht erreicht werden. Ein technischer Fehler, wir arbeiten daran!',
	'LOGIN_ERROR_USERNAME_UMLAUT' => 'Fehler:<br>Dieser <i class="icon fa-kreuzmich fa-fw" aria-hidden="true"></i>-Benutzername enthält Umlaute.<br>Aus technischen Gründen müssen hier im Forum alle Umlaute in <i class="icon fa-kreuzmich fa-fw" aria-hidden="true"></i>-Benutzernamen ersetzt werden:<br>Bitte schreibe ein ä, ö, ü, ß als a, o, u bzw. s - <span style="text-decoration: underline;">nicht als ae, oe, ue oder ss!</span> (z.B. Gummibär => Gummibar, <span style="text-decoration: underline;">nicht Gummibaer!</span> oder Weißbrot => Weisbrot, <span style="text-decoration: underline;">nicht Weissbrot</span>).',
	'LOGIN_ERROR_KREUZMICH' => 'Fehler:<br>Kein <i class="icon fa-kreuzmich fa-fw" aria-hidden="true"></i> Konto gefunden.<br>Entweder hast du Benutzername/Passwort falsch eingegeben oder du bist noch nicht freigeschaltet.<br>Prüfe dies durch Login auf <i class="icon fa-kreuzmich fa-fw" aria-hidden="true"></i>. Siehe auch die Forumsseite  „Probleme beim Login?“ in der unteren grünen Leiste oder die <i class="icon fa-kreuzmich fa-fw" aria-hidden="true"></i> Hilfeseite.',
	'LOGIN_ERROR_KREUZMICH_EXPIRED' => 'Fehler:<br>Dein <i class="icon fa-kreuzmich fa-fw" aria-hidden="true"></i>-Konto ist <span style="text-decoration: underline;">abgelaufen</span>.<br>Konten sind vorerst nur für die Regelstudienzeit von 13 Semestern gültig.<br>Bitte logge dich auf <i class="icon fa-kreuzmich fa-fw" aria-hidden="true"></i> ein, um zu sehen, wie du deinen Studierendenstatus verlängern kannst.<br>Bitte wende dich bei Fragen <b><u>nicht an die Fachschaft bzw. das Referat Medien</b></u>, sondern direkt an <i class="icon fa-kreuzmich fa-fw" aria-hidden="true"></i>. Dort kann dir schneller geholfen werden. Für weitere Infos siehe <a href="https://duesseldorf.kreuzmich.de/help/helpDuesseldorf">die Hilfeseite bei <i class="icon fa-kreuzmich fa-fw" aria-hidden="true"></i></a>.',
	'LOGIN_ERROR_KREUZMICH_NO_NEW_USERS' => 'Fehler:<br>Dieses <i class="icon fa-kreuzmich fa-fw" aria-hidden="true"></i> Konto existiert nicht im Forum. Da der Zugang zum Forum für neue Benutzer gesperrt ist, kannst du dich nicht einloggen. Bitte wende dich an das Referat Medien unter „Kontakt“.',
	'LOGIN_ERROR_KREUZMICH_BLOCKED' => 'Fehler:<br>Der Zugang zum Forum ist für dein Konto gesperrt. Bitte wende dich an das Referat Medien unter „Kontakt“.',
	
	'LOGIN_AUTH_KREUZMICH' => 'Mit <i class="icon fa-kreuzmich fa-fw" aria-hidden="true"></i> anmelden',

	'KREUZMICH' => 'Kreuzmich',

	'KREUZMICH_EXP_USER' => 'Abgelaufene Kreuzmich Benutzer zulassen',
	'KREUZMICH_EXP_USER_EXPLAIN' => 'Sollen bei Kreuzmich existierende, aber abgelaufene Benutzer im Forum zugelassen werden? Betrifft Alumni und damit evtl. auch jetzige Dozenten. (Standard NEIN)',
  
	'KREUZMICH_NEW_USER' => 'Neue Kreuzmich Benutzer zulassen',
	'KREUZMICH_NEW_USER_EXPLAIN' => 'Sollen Kreuzmich Benutzer zugelassen werden, die bisher noch kein Benutzerkonto im Forum haben? Beim ersten Login wird dann ein neues Benutzerkonto für sie erstellt, mit dem sie sich einloggen können. Falls auf Nein gesetzt, werden keine neuen Benutzer ins Forum aufgenommen (Standard JA). Ob ein Benutzer das richtige Passwort eingegeben hat oder abgelaufen ist, wird vorher geprüft.',
	
	'KREUZMICH_USER' => 'HTTP Benutzername',
	'KREUZMICH_USER_EXPLAIN' => '(optional) Kreuzmich HTTP Benutzername für die extAuth URL',

	'KREUZMICH_PASSWORD' => 'HTTP Passwort',
	'KREUZMICH_PASSWORD_EXPLAIN' => '(optional) Kreuzmich HTTP Passwort für die extAuth URL',

	'KREUZMICH_URL' => 'URL',
	'KREUZMICH_URL_EXPLAIN' => 'Die Kreuzmich extAuth URL, https:// wird automatisch ergänzt. (z.B. <samp>duesseldorf.kreuzmich.de/extAuth/json)</samp>'

));