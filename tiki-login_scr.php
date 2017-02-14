<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-login_scr.php 57957 2016-03-17 19:58:54Z jonnybradley $

$section_class = 'tiki_login';	// This will be body class instead of $section
include_once ("tiki-setup.php");

if ($prefs['login_autologin'] == 'y' && $prefs['login_autologin_redirectlogin'] == 'y' && !empty($prefs['login_autologin_redirectlogin_url'])) {
	$access->redirect($prefs['login_autologin_redirectlogin_url']);
}

if (isset($_REQUEST['clearmenucache'])) {
	TikiLib::lib('menu')->empty_menu_cache();
}
if (isset($_REQUEST['user'])) {
	if ($_REQUEST['user'] == 'admin' && (!isset($_SESSION["groups_are_emulated"]) || $_SESSION["groups_are_emulated"] != "y")) {
		$smarty->assign('showloginboxes', 'y');
		$smarty->assign('adminuser', $_REQUEST['user']);
	} else {
		$smarty->assign('loginuser', $_REQUEST['user']);
	}
}
if (($prefs['useGroupHome'] != 'y' || $prefs['limitedGoGroupHome'] == 'y') && !isset($_SESSION['loginfrom'])) {
	$_SESSION['loginfrom'] = (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $prefs['tikiIndex']);
}

$headerlib->add_js('$(document).ready( function() {$("#login-user").focus().select();} );');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('headtitle', tra('Log In'));
$smarty->assign('mid', 'tiki-login.tpl');

$smarty->display("tiki.tpl");
