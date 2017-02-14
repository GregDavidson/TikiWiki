<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-admin_calendars.php 57958 2016-03-17 19:59:37Z jonnybradley $

$section = 'calendar';
require_once ('tiki-setup.php');
$categlib = TikiLib::lib('categ');
$calendarlib = TikiLib::lib('calendar');
if ($prefs['feature_groupalert'] == 'y') {
	$groupalertlib = TikiLib::lib('groupalert');
}
$auto_query_args = array('calendarId', 'sort_mode', 'find', 'offset');
if (!isset($_REQUEST["calendarId"])) {
	$access->check_permission(array('tiki_p_admin_calendar'));
	$_REQUEST['calendarId'] = 0;
} else {
	$info = $calendarlib->get_calendar($_REQUEST['calendarId']);
	if (empty($info)) {
		$smarty->assign('msg', tra('Incorrect param'));
		$smarty->display('error.tpl');
		die;
	}
	$objectperms = Perms::get('calendar', $_REQUEST['calendarId']);
	if (!$objectperms->admin_calendar) {
		$access->display_error('', tra('Permission denied').": ". 'tiki_p_admin_calendar', '403');
	}
}
if (isset($_REQUEST["drop"])) {
	$access->check_authenticity();
	$calendarlib->drop_calendar($_REQUEST['calendarId']);
	$_REQUEST["calendarId"] = 0;
}
if (isset($_REQUEST["save"])) {
	check_ticket('admin-calendars');
	$customflags["customlanguages"] = $_REQUEST["customlanguages"];
	$customflags["customlocations"] = $_REQUEST["customlocations"];
	$customflags["customparticipants"] = $_REQUEST["customparticipants"];
	$customflags["customcategories"] = $_REQUEST["customcategories"];
	$customflags["custompriorities"] = $_REQUEST["custompriorities"];
	$customflags["customsubscription"] = isset($_REQUEST["customsubscription"]) ? $_REQUEST["customsubscription"] : 'n';
	$customflags["personal"] = $_REQUEST["personal"];
	$customflags['customstatus'] = isset($_REQUEST['customstatus']) ? $_REQUEST['customstatus'] : 'y';
	$options = $_REQUEST['options'];
	if (array_key_exists('customcolors', $options) && strPos($options['customcolors'], '-') > 0) {
		$customColors = explode('-', $options['customcolors']);
		if (!preg_match('/^[0-9a-fA-F]{3,6}$/', $customColors[0])) $options['customfgcolor'] = '000000';
		else $options['customfgcolor'] = $customColors[0];
		if (!preg_match('/^[0-9a-fA-F]{3,6}$/', $customColors[1])) $options['custombgcolor'] = 'ffffff';
		else $options['custombgcolor'] = $customColors[1];
	}
	if (!preg_match('/^[0-9a-fA-F]{3,6}$/', $options['customfgcolor'])) $options['customfgcolor'] = '';
	if (!preg_match('/^[0-9a-fA-F]{3,6}$/', $options['custombgcolor'])) $options['custombgcolor'] = '';
	//Convert 12-hour clock hours to 24-hour scale to compute time
	if (!empty($_REQUEST['startday_Meridian'])) {
		$_REQUEST['startday_Hour'] = date('H', strtotime($_REQUEST['startday_Hour'] . ':00 ' . $_REQUEST['startday_Meridian']));
	}
	if (!empty($_REQUEST['endday_Meridian'])) {
		$_REQUEST['endday_Hour'] = date('H', strtotime($_REQUEST['endday_Hour'] . ':00 ' . $_REQUEST['endday_Meridian']));
	}
	$options['startday'] = $_REQUEST['startday_Hour'] * 60 * 60;
	$options['endday'] = $_REQUEST['endday_Hour'] == 0 ? (24 * 60 * 60) - 1 : ($_REQUEST['endday_Hour'] * 60 * 60);
	$extra = array(
		'calname',
		'description',
		'location',
		'description',
		'language',
		'category',
		'participants',
		'url',
		'status',
		'status_calview'
	);
	foreach ($extra as $ex) {
		if (isset($_REQUEST['show'][$ex]) and $_REQUEST['show'][$ex] == 'on') {
			$options["show_$ex"] = 'y';
		} else {
			$options["show_$ex"] = 'n';
		}
	}
	if (isset($_REQUEST['viewdays'])) $options['viewdays'] = $_REQUEST['viewdays'];
	$options['allday'] = isset($_REQUEST['allday'])? 'y':'n';
	$options['nameoneachday'] = isset($_REQUEST['nameoneachday'])? 'y': 'n';
	$_REQUEST["calendarId"] = $calendarlib->set_calendar($_REQUEST["calendarId"], $user, $_REQUEST["name"], $_REQUEST["description"], $customflags, $options);
	$info = $calendarlib->get_calendar($_REQUEST['calendarId']);
	if ($prefs['feature_groupalert'] == 'y') {
		$groupalertlib->AddGroup('calendar', $_REQUEST["calendarId"], $_REQUEST['groupforAlert'], !empty($_REQUEST['showeachuser']) ? $_REQUEST['showeachuser'] : 'n');
	}
	if ($_REQUEST['personal'] == 'y') {
		$userlib->assign_object_permission("Registered", $_REQUEST["calendarId"], "calendar", "tiki_p_view_calendar");
		$userlib->assign_object_permission("Registered", $_REQUEST["calendarId"], "calendar", "tiki_p_view_events");
		$userlib->assign_object_permission("Registered", $_REQUEST["calendarId"], "calendar", "tiki_p_add_events");
		$userlib->assign_object_permission("Registered", $_REQUEST["calendarId"], "calendar", "tiki_p_change_events");
	}
	if ($prefs['feature_categories'] == 'y') {
		$cat_type = 'calendar';
		$cat_objid = $_REQUEST["calendarId"];
		$cat_desc = $_REQUEST["description"];
		$cat_name = $_REQUEST["name"];
		$cat_href = "tiki-calendar.php?calIds[]=" . $_REQUEST["calendarId"];
		include_once("categorize.php");
	}
	$cookietab=1;
	$_REQUEST['calendarId'] = 0;
}
if (isset($_REQUEST['clean']) && isset($_REQUEST['days'])) {
	check_ticket('admin-calendars');
	$calendarlib->cleanEvents($_REQUEST['calendarId'], $_REQUEST['days']);
}
if ($prefs['feature_categories'] == 'y') {
	$cat_type = 'calendar';
	$cat_objid = $_REQUEST["calendarId"];
	include_once ("categorize_list.php");
	$cs = $categlib->get_object_categories('calendar', $cat_objid);
	if (!empty($cs)) {
		for ($i = count($categories) - 1; $i >= 0; --$i) {
			if (in_array($categories[$i]['categId'], $cs)) {
				$categories[$i]['incat'] = 'y';
			}
		}
	}
}
if ($_REQUEST['calendarId'] != 0) {
	$cookietab = 2;
} else {
	$info = array();
	$info["name"] = '';
	$info["description"] = '';
	$info["customlanguages"] = 'n';
	$info["customlocations"] = 'n';
	$info["customparticipants"] = 'n';
	$info["customcategories"] = 'n';
	$info["custompriorities"] = 'n';
	$info["customsubscription"] = 'n';
	$info['customstatus'] = 'n';
	$info["customurl"] = 'n';
	$info["customfgcolor"] = '';
	$info["custombgcolor"] = '';
	$info["show_calname"] = 'y';
	$info["show_description"] = 'y';
	$info["show_category"] = 'n';
	$info["show_location"] = 'n';
	$info["show_language"] = 'n';
	$info["show_participants"] = 'n';
	$info["show_url"] = 'n';
	$info['show_status'] = 'n';
	$info['show_status_calview'] = '';
	$info["user"] = "$user";
	$info["personal"] = 'n';
	$info["startday"] = '25200';
	$info["endday"] = '72000';
	$info["allday"] = '';
	$info["nameoneachday"] = '';
	$info["defaulteventstatus"] = 1;
	$info['viedays'] = $prefs['calendar_view_days'];
	if (!empty($_REQUEST['show']) && $_REQUEST['show'] == 'mod') {
		$cookietab = 2;
	} else {
		if (!isset($cookietab)) {
			$cookietab = 1;
		}
	}
}
if ($prefs['feature_groupalert'] == 'y') {
	$info["groupforAlertList"] = array();
	$info["groupforAlert"] = $groupalertlib->GetGroup('calendar', $_REQUEST["calendarId"]);
	$all_groups = $userlib->list_all_groups();
	if (is_array($all_groups)) {
		foreach ($all_groups as $g) {
			$groupforAlertList[$g] = ($g == $info['groupforAlert']) ? 'selected' : '';
		}
	}
	$showeachuser = $groupalertlib->GetShowEachUser('calendar', $_REQUEST['calendarId'], $info['groupforAlert']);
	$smarty->assign('groupforAlert', $info['groupforAlert']);
	$smarty->assign_by_ref('groupforAlertList', $groupforAlertList);
	$smarty->assign_by_ref('showeachuser', $showeachuser);
}
setcookie('tab', $cookietab);
$smarty->assign_by_ref('cookietab', $cookietab);
$smarty->assign('name', $info["name"]);
$smarty->assign('description', $info["description"]);
$smarty->assign('owner', $info["user"]);
$smarty->assign('customlanguages', $info["customlanguages"]);
$smarty->assign('customlocations', $info["customlocations"]);
$smarty->assign('customparticipants', $info["customparticipants"]);
$smarty->assign('customcategories', $info["customcategories"]);
$smarty->assign('custompriorities', $info["custompriorities"]);
$smarty->assign('customsubscription', $info["customsubscription"]);
$smarty->assign('customurl', $info["customurl"]);
$smarty->assign('customfgcolor', $info["customfgcolor"]);
$smarty->assign('custombgcolor', $info["custombgcolor"]);
$smarty->assign('customColors', $info["customfgcolor"] . "-" . $info["custombgcolor"]);
$smarty->assign('show_calname', $info["show_calname"]);
$smarty->assign('show_description', $info["show_description"]);
$smarty->assign('show_category', $info["show_category"]);
$smarty->assign('show_location', $info["show_location"]);
$smarty->assign('show_language', $info["show_language"]);
$smarty->assign('show_participants', $info["show_participants"]);
$smarty->assign('show_url', $info["show_url"]);
$smarty->assign('calendarId', $_REQUEST["calendarId"]);
$smarty->assign('personal', $info["personal"]);
$smarty->assign('startday', $info["startday"] < 0 ? 0 : $info['startday']);
$smarty->assign('endday', $info["endday"] < 0 ? 0 : $info['endday']);
//Use 12- or 24-hour clock for $publishDate time selector based on admin and user preferences
$userprefslib = TikiLib::lib('userprefs');
$smarty->assign('use_24hr_clock', $userprefslib->get_user_clock_pref($user));

$smarty->assign('defaulteventstatus', $info['defaulteventstatus']);
$smarty->assign(
	'eventstatus',
	array(
		0 => tra('Tentative') ,
		1 => tra('Confirmed') ,
		2 => tra('Cancelled')
	)
);
$smarty->assign_by_ref('info', $info);
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'name_asc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
$calendars = $calendarlib->list_calendars($offset, $maxRecords, $sort_mode, $find);
foreach (array_keys($calendars["data"]) as $i) {
	$calendars["data"][$i]["individual"] = $userlib->object_has_one_permission($i, 'calendar');
}
$smarty->assign_by_ref('cant', $calendars['cant']);
$smarty->assign_by_ref('calendars', $calendars["data"]);
$days_names = array(
	tra("Sunday"),
	tra("Monday"),
	tra("Tuesday"),
	tra("Wednesday"),
	tra("Thursday"),
	tra("Friday"),
	tra("Saturday")
);
$smarty->assign('days_names', $days_names);
include_once ('tiki-section_options.php');
ask_ticket('admin-calendars');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('uses_tabs', 'y');
$smarty->assign('mid', 'tiki-admin_calendars.tpl');
$smarty->display("tiki.tpl");
