<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-admin_external_wikis.php 57958 2016-03-17 19:59:37Z jonnybradley $

require_once ('tiki-setup.php');
$adminlib = TikiLib::lib('admin');

$access->check_permission('tiki_p_admin');

if (!isset($_REQUEST["extwikiId"])) {
	$_REQUEST["extwikiId"] = 0;
}
$smarty->assign('extwikiId', $_REQUEST["extwikiId"]);
if ($_REQUEST["extwikiId"]) {
	$info = $adminlib->get_extwiki($_REQUEST["extwikiId"]);
} else {
	$info = array(
		'name' => '',
		'extwiki' => '',
		'indexname' => '',
		'groups' => [],
	);
}
$smarty->assign('info', $info);
if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$adminlib->remove_extwiki($_REQUEST["remove"]);
}
if (isset($_REQUEST["save"])) {
	check_ticket('admin-external-wikis');
	$selector = TikiLib::lib('objectselector');
	$items = $selector->readMultipleSimple('group', $jitRequest->groups->text(), ';');
	$items = array_map(function ($i) { return $i['id']; }, $items);

	$adminlib->replace_extwiki($_REQUEST["extwikiId"], $_REQUEST["extwiki"], $_REQUEST['name'], $jitRequest->indexname->word(), $items);
	$info = array(
		'name' => '',
		'extwiki' => '',
		'indexname' => '',
		'groups' => [],
	);
	$smarty->assign('info', $info);
	$smarty->assign('name', '');
}
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'extwikiId_desc';
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
$channels = $adminlib->list_extwiki($offset, $maxRecords, $sort_mode, $find);
$smarty->assign_by_ref('cant_pages', $channels["cant"]);
$smarty->assign_by_ref('channels', $channels["data"]);
ask_ticket('admin-external-wikis');
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
// Display the template
$smarty->assign('mid', 'tiki-admin_external_wikis.tpl');
$smarty->display("tiki.tpl");
