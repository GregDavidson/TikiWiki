<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-admin_surveys.php 57957 2016-03-17 19:58:54Z jonnybradley $

$section = 'surveys';
require_once ('tiki-setup.php');
include_once ('lib/surveys/surveylib.php');
$access->check_feature('feature_surveys');

$auto_query_args = array(
	'surveyId',
	'offset',
	'sort_mode',
	'find'
);

if (!isset($_REQUEST["surveyId"])) {
	$_REQUEST["surveyId"] = 0;
}
$smarty->assign('surveyId', $_REQUEST["surveyId"]);
$tikilib->get_perm_object($_REQUEST['surveyId'], 'survey');
$access->check_permission('tiki_p_admin_surveys');
if (isset($_REQUEST["save"])) {
	check_ticket('admin-surveys');
	$sid = $srvlib->replace_survey($_REQUEST["surveyId"], $_REQUEST["name"], $_REQUEST["description"], $_REQUEST["status"]);
	$cat_type = 'survey';
	$cat_objid = $sid;
	$cat_desc = substr($_REQUEST["description"], 0, 200);
	$cat_name = $_REQUEST["name"];
	$cat_href = "tiki-take_survey.php?surveyId=" . $cat_objid;
	include_once ("categorize.php");
	$cookietab = 1;
	$_REQUEST["surveyId"] = 0;
}
if (!empty($_REQUEST["surveyId"])) {
	$info = $srvlib->get_survey($_REQUEST["surveyId"]);
	$cookietab = 2;
} else {
	$info = array();
	$info["name"] = '';
	$info["description"] = '';
	$info["status"] = 'o'; //check to see if survey is open
	$cookietab = 1;
}
$smarty->assign('info', $info);
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$srvlib->remove_survey($_REQUEST["remove"]);
}
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign('find', $find);
$smarty->assign_by_ref('sort_mode', $sort_mode);
$channels = $srvlib->list_surveys($offset, $maxRecords, $sort_mode, $find);
$temp_max = count($channels["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	if ($userlib->object_has_one_permission($channels["data"][$i]["surveyId"], 'survey')) {
		$channels["data"][$i]["individual"] = 'y';
		if ($userlib->object_has_permission($user, $channels["data"][$i]["surveyId"], 'survey', 'tiki_p_take_survey')) {
			$channels["data"][$i]["individual_tiki_p_take_survey"] = 'y';
		} else {
			$channels["data"][$i]["individual_tiki_p_take_survey"] = 'n';
		}
		if ($userlib->object_has_permission($user, $channels["data"][$i]["surveyId"], 'survey', 'tiki_p_view_survey_stats')) {
			$channels["data"][$i]["individual_tiki_p_view_survey_stats"] = 'y';
		} else {
			$channels["data"][$i]["individual_tiki_p_view_survey_stats"] = 'n';
		}
		if ($tiki_p_admin == 'y' || $userlib->object_has_permission($user, $channels["data"][$i]["surveyId"], 'survey', 'tiki_p_admin_surveys')) {
			$channels["data"][$i]["individual_tiki_p_take_survey"] = 'y';
			$channels["data"][$i]["individual_tiki_p_view_survey_stats"] = 'y';
			$channels["data"][$i]["individual_tiki_p_admin_surveys"] = 'y';
		}
	} else {
		$channels["data"][$i]["individual"] = 'n';
	}
}
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
// Fill array with possible number of questions per page (qpp)
$qpp = array(
	1,
	2,
	3,
	4
);
for ($i = 5; $i < 50; $i+= 5) $qpp[] = $i;
$hrs = array();
for ($i = 0; $i < 10; $i++) $hrs[] = $i;
$mins = array();
for ($i = 1; $i < 120; $i++) $mins[] = $i;
$smarty->assign('qpp', $qpp);
$smarty->assign('hrs', $hrs);
$smarty->assign('mins', $mins);
$cat_type = 'survey';
$cat_objid = $_REQUEST["surveyId"];
include_once ("categorize_list.php");
$section = 'surveys';
include_once ('tiki-section_options.php');
ask_ticket('admin-surveys');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_surveys.tpl');
$smarty->display("tiki.tpl");
