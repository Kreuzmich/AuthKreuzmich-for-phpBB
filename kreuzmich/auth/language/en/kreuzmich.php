<?php
/**
*
* @package phpBB Extension - Kreuzmich Auth
* @copyright (c) 2017 Christian Rubbert
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
	'LOGIN_ERROR_EXTERNAL_AUTH_KREUZMICH' => 'Kreuzmich Server is unreachable.',
	'LOGIN_ERROR_USERNAME_UMLAUT' => 'Please only use vocals instead of umlauts. <strong><span style="text-decoration: underline;">Don´t use replacements like ae, oe or ue</strong></span> (e.g. Gummibär => Gummibar, <strong><span style="text-decoration: underline;">not Gummibaer!</strong></span>). Please replace a ß only with a single s (e.g. Weißbrot => Weisbrot).',
	'LOGIN_ERROR_KREUZMICH' => 'Username or password incorrect.',
	'LOGIN_AUTH_KREUZMICH' => 'Sign on with Kreuzmich',
	'LOGIN_ERROR_KREUZMICH_EXPIRED' => 'Kreuzmich Account expired. Please visit help page on how to extend your account.',

  'KREUZMICH' => 'Kreuzmich',

  	'KREUZMICH_EXP_USER' => 'Authorize expired Kreuzmich users',
	'KREUZMICH_EXP_USER_EXPLAIN' => 'Decide whether activated, but expired Kreuzmich users should be able to login to the forum. (default NO)',
 
	'KREUZMICH_NEW_USER' => 'Accept new Kreuzmich users',
	'KREUZMICH_NEW_USER_EXPLAIN' => 'Decide whether active Kreuzmich users who don´t already have a forum account should be able to login. If YES, then a new forum account is generated on first login. If NO, only Kreuzmich users who already have a forum account can log in.',

	'KREUZMICH_USER' => 'HTTP Username',
	'KREUZMICH_USER_EXPLAIN' => '(optional) Kreuzmich HTTP Username for the extAuth URL',

	'KREUZMICH_PASSWORD' => 'HTTP Password',
	'KREUZMICH_PASSWORD_EXPLAIN' => '(optional) Kreuzmich HTTP Password for the extAuth URL',

	'KREUZMICH_URL' => 'URL',
	'KREUZMICH_URL_EXPLAIN' => 'The Kreuzmich extAuth URL, https:// will always be prefixed! (e.g. <samp>duesseldorf.kreuzmich.de/extAuth/json)</samp>'
));