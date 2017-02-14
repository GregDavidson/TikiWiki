<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-user_preferences.php 60252 2016-11-13 19:07:24Z chealer $

$section = 'mytiki';
require_once ('tiki-setup.php');
$modlib = TikiLib::lib('mod');
$userprefslib = TikiLib::lib('userprefs');
// User preferences screen
if ($prefs['feature_userPreferences'] != 'y' && $prefs['change_password'] != 'y' && $tiki_p_admin_users != 'y') {
	$smarty->assign('msg', tra("This feature is disabled") . ": feature_userPreferences");
	$smarty->display("error.tpl");
	die;
}
$access->check_user($user);

$auto_query_args = array('userId', 'view_user');

$headerlib->add_map();

// Make sure user preferences uses https if set
if (!$https_mode && isset($https_login) && $https_login == 'required') {
	header('Location: ' . $base_url_https . 'tiki-user_preferences.php');
	die;
}
if (isset($_REQUEST['userId']) || isset($_REQUEST['view_user'])) {
	if (empty($_REQUEST['view_user'])) $userwatch = $tikilib->get_user_login($_REQUEST['userId']);
	else $userwatch = $_REQUEST['view_user'];
	if ($userwatch != $user) {
		if ($userwatch === false) {
			$smarty->assign('msg', tra("Unknown user"));
			$smarty->display("error.tpl");
			die;
		} else {
			$access->check_permission('tiki_p_admin_users');
		}
	}
} elseif (isset($_REQUEST["view_user"])) {
	if ($_REQUEST["view_user"] != $user) {
		$access->check_permission('tiki_p_admin_users');
		$userwatch = $_REQUEST["view_user"];
		if (!$userlib->user_exists($userwatch)) {
			$smarty->assign('msg', tra("Unknown user"));
			$smarty->display("error.tpl");
			die;
		}
	} else {
		$userwatch = $user;
	}
} else {
	$userwatch = $user;
}

// Custom fields
$registrationlib = TikiLib::lib('registration');
$customfields = $registrationlib->get_customfields();
foreach ($customfields as $i => $c) {
	$customfields[$i]['value'] = $tikilib->get_user_preference($userwatch, $c['prefName']);
}
$smarty->assign_by_ref('customfields', $customfields);
$smarty->assign('userwatch', $userwatch);
$foo = parse_url($_SERVER["REQUEST_URI"]);
$foo1 = str_replace("tiki-user_preferences", "tiki-editpage", $foo["path"]);
$foo2 = str_replace("tiki-user_preferences", "tiki-index", $foo["path"]);
$smarty->assign('url_edit', $tikilib->httpPrefix() . $foo1);
$smarty->assign('url_visit', $tikilib->httpPrefix() . $foo2);
$smarty->assign('show_mouseover_user_info', isset($prefs['show_mouseover_user_info']) ? $prefs['show_mouseover_user_info'] : $prefs['feature_community_mouseover']);
if ($prefs['feature_userPreferences'] == 'y' && isset($_REQUEST["new_prefs"])) {
	check_ticket('user-prefs');
	// setting preferences
	if ($prefs['change_theme'] == 'y' && empty($group_theme)) {
		if (isset($_REQUEST['mytheme'])) {
			$themeandoption = $themelib->extract_theme_and_option($_REQUEST['mytheme']);
			$theme = $themeandoption[0];
			$themeOption = $themeandoption[1];
			$tikilib->set_user_preference($userwatch, 'theme', $theme);
			if (isset($themeOption)) {
				$tikilib->set_user_preference($userwatch, 'theme_option', empty($themeOption) ? '' : $themeOption);
			}
			//Something is needed for the theme change to be displayed without additional manual page refresh. Problem: when modifying another user's settings (not my user's) using any of the below ways the refreshed screen will show my user's preference screen instead of staying on the edited user's preference screen
			//header("location: tiki-user_preferences.php?view_user=$userwatch");
			//$access->redirect($_SERVER['REQUEST_URI'], '', 200); 
		}
	}
	if (isset($_REQUEST["userbreadCrumb"])) $tikilib->set_user_preference($userwatch, 'userbreadCrumb', $_REQUEST["userbreadCrumb"]);
	$langLib = TikiLib::lib('language');
	if (isset($_REQUEST["language"]) && $langLib->is_valid_language($_REQUEST['language'])) {
		if ($tiki_p_admin || $prefs['change_language'] == 'y') {
			$tikilib->set_user_preference($userwatch, 'language', $_REQUEST["language"]);
		}
		if ($userwatch == $user) {
			include ('lang/' . $_REQUEST["language"] . '/language.php');
		}
	} else {
		$tikilib->set_user_preference($userwatch, 'language', '');
	}
	if (isset($_REQUEST['read_language'])) {
		$list = array();
		$tok = strtok($_REQUEST['read_language'], ' ');
		while (false !== $tok) {
			$list[] = $tok;
			$tok = strtok(' ');
		}
		$list = array_unique($list);
		$langLib = TikiLib::lib('language');
		$list = array_filter($list, array($langLib, 'is_valid_language'));
		$list = implode(' ', $list);
		$tikilib->set_user_preference($userwatch, 'read_language', $list);
	}
	if (isset($_REQUEST['display_timezone'])) {
		$tikilib->set_user_preference($userwatch, 'display_timezone', $_REQUEST['display_timezone']);
	}
	$tikilib->set_user_preference($userwatch, 'user_information', $_REQUEST['user_information']);
	if (isset($_REQUEST['display_12hr_clock']) && $_REQUEST['display_12hr_clock'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'display_12hr_clock', 'y');
		$smarty->assign('display_12hr_clock', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'display_12hr_clock', 'n');
		$smarty->assign('display_12hr_clock', 'n');
	}
	if (isset($_REQUEST['diff_versions']) && $_REQUEST['diff_versions'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'diff_versions', 'y');
		$smarty->assign('diff_versions', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'diff_versions', 'n');
		$smarty->assign('diff_versions', 'n');
	}
	if ($prefs['feature_community_mouseover'] == 'y') {
		if (isset($_REQUEST['show_mouseover_user_info']) && $_REQUEST['show_mouseover_user_info'] == 'on') {
			$tikilib->set_user_preference($userwatch, 'show_mouseover_user_info', 'y');
			$smarty->assign('show_mouseover_user_info', 'y');
		} else {
			$tikilib->set_user_preference($userwatch, 'show_mouseover_user_info', 'n');
			$smarty->assign('show_mouseover_user_info', 'n');
		}
	}
	$email_isPublic = isset($_REQUEST['email_isPublic']) ? $_REQUEST['email_isPublic'] : 'n';
	$tikilib->set_user_preference($userwatch, 'email is public', $email_isPublic);
	$tikilib->set_user_preference($userwatch, 'mailCharset', $_REQUEST['mailCharset']);
	// Custom fields
	foreach ($customfields as $custpref => $prefvalue) {
		if (isset($_REQUEST[$customfields[$custpref]['prefName']])) $tikilib->set_user_preference($userwatch, $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
	}
	if (isset($_REQUEST["realName"]) && ($prefs['auth_ldap_nameattr'] == '' || $prefs['auth_method'] != 'ldap')) {
		$tikilib->set_user_preference($userwatch, 'realName', $_REQUEST["realName"]);
		if ( $prefs['user_show_realnames'] == 'y' ) {
			$cachelib = TikiLib::lib('cache');
			$cachelib->invalidate('userlink.'.$user.'0');
		}
	}
	if ($prefs['feature_community_gender'] == 'y') {
		if (isset($_REQUEST["gender"])) $tikilib->set_user_preference($userwatch, 'gender', $_REQUEST["gender"]);
	}
	if (isset($_REQUEST["homePage"])) $tikilib->set_user_preference($userwatch, 'homePage', $_REQUEST["homePage"]);

	if (isset($_REQUEST['location'])) {
		if ($coords = TikiLib::lib('geo')->parse_coordinates($_REQUEST['location'])) {
			$tikilib->set_user_preference($userwatch, 'lat', $coords['lat']);
			$tikilib->set_user_preference($userwatch, 'lon', $coords['lon']);
			if (isset($coords['zoom'])) {
				$tikilib->set_user_preference($userwatch, 'zoom', $coords['zoom']);
			}
		}
	}

	// Custom fields
	foreach ($customfields as $custpref => $prefvalue) {
		// print $customfields[$custpref]['prefName'];
		// print $_REQUEST[$customfields[$custpref]['prefName']];
		$tikilib->set_user_preference($userwatch, $customfields[$custpref]['prefName'], $_REQUEST[$customfields[$custpref]['prefName']]);
	}
	$tikilib->set_user_preference($userwatch, 'country', $_REQUEST["country"]);
	if (isset($_REQUEST['mess_maxRecords'])) $tikilib->set_user_preference($userwatch, 'mess_maxRecords', $_REQUEST['mess_maxRecords']);
	if (isset($_REQUEST['mess_archiveAfter'])) $tikilib->set_user_preference($userwatch, 'mess_archiveAfter', $_REQUEST['mess_archiveAfter']);
	if (isset($_REQUEST['mess_sendReadStatus']) && $_REQUEST['mess_sendReadStatus'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mess_sendReadStatus', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mess_sendReadStatus', 'n');
	}
	if (isset($_REQUEST['minPrio'])) $tikilib->set_user_preference($userwatch, 'minPrio', $_REQUEST['minPrio']);
	if ($prefs['allowmsg_is_optional'] == 'y') {
		if (isset($_REQUEST['allowMsgs']) && $_REQUEST['allowMsgs'] == 'on') {
			$tikilib->set_user_preference($userwatch, 'allowMsgs', 'y');
		} else {
			$tikilib->set_user_preference($userwatch, 'allowMsgs', 'n');
		}
	}
	if (isset($_REQUEST['mytiki_pages']) && $_REQUEST['mytiki_pages'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_pages', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_pages', 'n');
	}
	if (isset($_REQUEST['mytiki_blogs']) && $_REQUEST['mytiki_blogs'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_blogs', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_blogs', 'n');
	}
	if (isset($_REQUEST['mytiki_gals']) && $_REQUEST['mytiki_gals'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_gals', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_gals', 'n');
	}
	if (isset($_REQUEST['mytiki_msgs']) && $_REQUEST['mytiki_msgs'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_msgs', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_msgs', 'n');
	}
	if (isset($_REQUEST['mytiki_tasks']) && $_REQUEST['mytiki_tasks'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_tasks', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_tasks', 'n');
	}
	if (isset($_REQUEST['mytiki_forum_topics']) && $_REQUEST['mytiki_forum_topics'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_forum_topics', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_forum_topics', 'n');
	}
	if (isset($_REQUEST['mytiki_forum_replies']) && $_REQUEST['mytiki_forum_replies'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_forum_replies', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_forum_replies', 'n');
	}
	if (isset($_REQUEST['mytiki_items']) && $_REQUEST['mytiki_items'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_items', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_items', 'n');
	}
	if (isset($_REQUEST['mytiki_articles']) && $_REQUEST['mytiki_articles'] == 'on') {
		$tikilib->set_user_preference($userwatch, 'mytiki_articles', 'y');
	} else {
		$tikilib->set_user_preference($userwatch, 'mytiki_articles', 'n');
	}
	if (isset($_REQUEST['tasks_maxRecords'])) $tikilib->set_user_preference($userwatch, 'tasks_maxRecords', $_REQUEST['tasks_maxRecords']);
	if ($prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster']) && $prefs['feature_intertiki_import_preferences'] == 'y') { //send to the master
		$userlib->interSendUserInfo($prefs['interlist'][$prefs['feature_intertiki_mymaster']], $userwatch);
	}

	TikiLib::events()->trigger(
		'tiki.user.update', array(
			'type' => 'user',
			'object' => $userwatch,
			'user' => $GLOBALS['user'],
		)
	);
}
if ($prefs['auth_method'] == 'ldap' && $user == 'admin' && $prefs['ldap_skip_admin'] == 'y') {
	$change_password = 'y';
	$smarty->assign('change_password', $change_password);
}
if (isset($_REQUEST['chgadmin'])) {
	check_ticket('user-prefs');
	if (isset($_REQUEST['pass'])) {
		$pass = $_REQUEST['pass'];
	} else {
		$pass = '';
	}
	// check user's password, admin doesn't need it to change other user's info
	if ($tiki_p_admin != 'y' || $user == $userwatch) {
		if ($prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster'])) {
			if ($ok = $userlib->intervalidate($prefs['interlist'][$prefs['feature_intertiki_mymaster']], $userwatch, $pass)) if ($ok->faultCode()) $ok = false;
		} else {
			list($ok, $userwatch, $error) = $userlib->validate_user($userwatch, $pass);
		}
		if (!$ok) {
			$smarty->assign('msg', tra("Invalid password. Your current password is required to change administrative information"));
			$smarty->display("error.tpl");
			die;
		}
	}
	if (!empty($_REQUEST['email']) && ($prefs['login_is_email'] != 'y' || $user == 'admin') && $_REQUEST['email'] != $userlib->get_user_email($userwatch)) {
		$userlib->change_user_email($userwatch, $_REQUEST['email'], $pass);
		Feedback::success(sprintf(tra('Email is set to %s'), $_REQUEST['email']));
		if ($prefs['feature_intertiki'] == 'y' && !empty($prefs['feature_intertiki_mymaster']) && $prefs['feature_intertiki_import_preferences'] == 'y') { //send to the master
			$userlib->interSendUserInfo($prefs['interlist'][$prefs['feature_intertiki_mymaster']], $userwatch);
		}
	}
	// If user has provided new password, let's try to change
	if (!empty($_REQUEST["pass1"])) {
		if ($_REQUEST["pass1"] != $_REQUEST["pass2"]) {
			$smarty->assign('msg', tra("The passwords did not match"));
			$smarty->display("error.tpl");
			die;
		}
		$polerr = $userlib->check_password_policy($_REQUEST["pass1"]);
		if (strlen($polerr) > 0) {
			$smarty->assign('msg', $polerr);
			$smarty->display("error.tpl");
			die;
		}
		$userlib->change_user_password($userwatch, $_REQUEST["pass1"]);
		if ($prefs['feature_user_encryption'] === 'y') {
			// Notify CryptLib about the login
			$cryptlib = TikiLib::lib('crypt');
			$cryptlib->onChangeUserPassword($_REQUEST["pass"], $_REQUEST["pass1"]);
		}
		Feedback::success(sprintf(tra('Password has been changed')));
	}
}
if (isset($_REQUEST['deleteaccount']) && $tiki_p_delete_account == 'y') {
   check_ticket('user-prefs');
   if (!isset($_REQUEST['deleteaccountconfirm']) || $_REQUEST['deleteaccountconfirm'] != '1') {
      $smarty->assign('msg', tra("If you really want to delete your account, you must check the checkbox"));
      $smarty->display("error.tpl");
      die;
   }
   $userlib->remove_user($userwatch);
   if ($user == $userwatch) {
	   header('Location: tiki-logout.php');
   } elseif ($tiki_p_admin_users == 'y') {
	   header('Location: tiki-adminusers.php');
   } else {
	   header("Location: $base_url");
   }
   die();
}

$location = array(
	'lat' => (float) $tikilib->get_user_preference($userwatch, 'lat', ''),
	'lon' => (float) $tikilib->get_user_preference($userwatch, 'lon', ''),
	'zoom' => (int) $tikilib->get_user_preference($userwatch, 'zoom', ''),
);

$location = TikiLib::lib('geo')->build_location_string($location);

$smarty->assign('location', $location);

$tikilib->get_user_preference($userwatch, 'mytiki_pages', 'y');
$tikilib->get_user_preference($userwatch, 'mytiki_blogs', 'y');
$tikilib->get_user_preference($userwatch, 'mytiki_gals', 'y');
$tikilib->get_user_preference($userwatch, 'mytiki_items', 'y');
$tikilib->get_user_preference($userwatch, 'mytiki_msgs', 'y');
$tikilib->get_user_preference($userwatch, 'mytiki_tasks', 'y');
$tikilib->get_user_preference($userwatch, 'mylevel', '1');
$tikilib->get_user_preference($userwatch, 'tasks_maxRecords');
$tikilib->get_user_preference($userwatch, 'mess_maxRecords', 20);
$tikilib->get_user_preference($userwatch, 'mess_archiveAfter', 0);
$tikilib->get_user_preference($userwatch, 'mess_sendReadStatus', 0);
$tikilib->get_user_preference($userwatch, 'allowMsgs', 'y');
$tikilib->get_user_preference($userwatch, 'minPrio', 3);
$tikilib->get_user_preference($userwatch, 'theme', '');
$tikilib->get_user_preference($userwatch, 'language', $prefs['language']);
$tikilib->get_user_preference($userwatch, 'realName', '');
if ($prefs['feature_community_gender'] == 'y') {
	$tikilib->get_user_preference($userwatch, 'gender', 'Hidden');
}
$tikilib->get_user_preference($userwatch, 'country', 'Other');
$tikilib->get_user_preference($userwatch, 'userbreadCrumb', $prefs['site_userbreadCrumb']);
$tikilib->get_user_preference($userwatch, 'homePage', '');
$tikilib->get_user_preference($userwatch, 'email is public', 'n');
if (isset($user_preferences[$userwatch]['email is public'])) {
	$user_preferences[$userwatch]['email_isPublic'] = $user_preferences[$userwatch]['email is public'];
}
$tikilib->get_user_preference($userwatch, 'mailCharset', $prefs['default_mail_charset']);
$tikilib->get_user_preference($userwatch, 'display_12hr_clock', 'n');
$userinfo = $userlib->get_user_info($userwatch);
$smarty->assign_by_ref('userinfo', $userinfo);
//user theme
$themelib = TikiLib::lib('theme');
$available_themesandoptions = $themelib->get_available_themesandoptions();
$smarty->assign_by_ref('available_themesandoptions', $available_themesandoptions);
$userwatch_theme = $tikilib->get_user_preference($userwatch, 'theme', null);
$userwatch_themeOption = $tikilib->get_user_preference($userwatch, 'theme_option', null);
$smarty->assign_by_ref('userwatch_theme', $userwatch_theme);
$smarty->assign_by_ref('userwatch_themeOption', $userwatch_themeOption);
//user language
$languages = array();
$langLib = TikiLib::lib('language');
$languages = $langLib->list_languages();
$smarty->assign_by_ref('languages', $languages);

$user_pages = $tikilib->get_user_pages($userwatch, -1);
$smarty->assign_by_ref('user_pages', $user_pages);
$bloglib = TikiLib::lib('blog');
$user_blogs = $bloglib->list_user_blogs($userwatch, false);
$smarty->assign_by_ref('user_blogs', $user_blogs);
$user_galleries = $tikilib->get_user_galleries($userwatch, -1);
$smarty->assign_by_ref('user_galleries', $user_galleries);
$user_items = TikiLib::lib('trk')->get_user_items($userwatch);
$smarty->assign_by_ref('user_items', $user_items);
$flags = $tikilib->get_flags('','','', true);
$smarty->assign_by_ref('flags', $flags);
$scramblingMethods = array("n", "strtr", "unicode", "x", 'y'); // email_isPublic utilizes 'n'
$smarty->assign_by_ref('scramblingMethods', $scramblingMethods);
$scramblingEmails = array(
		tra("no"),
		TikiMail::scrambleEmail($userinfo['email'], 'strtr'),
		TikiMail::scrambleEmail($userinfo['email'], 'unicode') . "-" . tra("unicode"),
		TikiMail::scrambleEmail($userinfo['email'], 'x'), $userinfo['email'],
	);
$smarty->assign_by_ref('scramblingEmails', $scramblingEmails);
$avatar = $tikilib->get_user_avatar($userwatch);
$smarty->assign_by_ref('avatar', $avatar);
$mailCharsets = array('utf-8', 'iso-8859-1');
$smarty->assign_by_ref('mailCharsets', $mailCharsets);
$smarty->assign_by_ref('user_prefs', $user_preferences[$userwatch]);
$tikilib->get_user_preference($userwatch, 'user_information', 'public');
$tikilib->get_user_preference($userwatch, 'diff_versions', 'n');
$usertrackerId = false;
$useritemId = false;
if ($prefs['userTracker'] == 'y') {
	$re = $userlib->get_usertracker($userinfo["userId"]);
	if (isset($re['usersTrackerId']) and $re['usersTrackerId']) {
		$trklib = TikiLib::lib('trk');
		$info = $trklib->get_item_id($re['usersTrackerId'], $re['usersFieldId'], $userwatch);
		$usertrackerId = $re['usersTrackerId'];
		$useritemId = $info;
	}
}
$smarty->assign('usertrackerId', $usertrackerId);
$smarty->assign('useritemId', $useritemId);
// Custom fields
foreach ($customfields as $custpref => $prefvalue) {
	$customfields[$custpref]['value'] = $tikilib->get_user_preference($userwatch, $customfields[$custpref]['prefName'], $customfields[$custpref]['value']);
	$smarty->assign($customfields[$custpref]['prefName'], $customfields[$custpref]['value']);
}
if ($prefs['feature_messages'] == 'y' && $tiki_p_messages == 'y') {
	$unread = $tikilib->user_unread_messages($userwatch);
	$smarty->assign('unread', $unread);
}
$smarty->assign('timezones', TikiDate::getTimeZoneList());

$tikilib->set_display_timezone($user);

if (isset($prefs['display_timezone'])) {
	$smarty->assign('display_timezone', $prefs['display_timezone']);
}

if ($prefs['users_prefs_display_timezone'] == 'Site') {
	$smarty->assign('warning_site_timezone_set', 'y');
} else {
	$smarty->assign('warning_site_timezone_set', 'n');
}

$smarty->assign('userPageExists', 'n');
if ($prefs['feature_wiki'] == 'y' and $prefs['feature_wiki_userpage'] == 'y') {
	if ($tikilib->page_exists($prefs['feature_wiki_userpage_prefix'] . $user)) $smarty->assign('userPageExists', 'y');
}
include_once ('tiki-section_options.php');
ask_ticket('user-prefs');
$smarty->assign('mid', 'tiki-user_preferences.tpl');
$smarty->display("tiki.tpl");
