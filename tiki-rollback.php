<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-rollback.php 58505 2016-05-01 16:38:13Z jonnybradley $

require_once ('tiki-setup.php');
$histlib = TikiLib::lib('hist');
$wikilib = TikiLib::lib('wiki');

$access->check_feature('feature_wiki');

// Get the page from the request var or default it to HomePage
if (!isset($_REQUEST["page"])) {
	$smarty->assign('msg', tra("No page indicated"));
	$smarty->display("error.tpl");
	die;
} else {
	$page = $_REQUEST["page"];
	$smarty->assign_by_ref('page', $_REQUEST["page"]);
}
if (!isset($_REQUEST["version"])) {
	$smarty->assign('msg', tra("No version indicated"));
	$smarty->display("error.tpl");
	die;
} else {
	$version = $_REQUEST["version"];
	$smarty->assign_by_ref('version', $_REQUEST["version"]);
}
if (!($info = $tikilib->get_page_info($page))) {
	$smarty->assign('msg', tra('Page cannot be found'));
	$smarty->display('error.tpl');
	die;
}
if (!$histlib->version_exists($page, $version)) {
	$smarty->assign('msg', tra("Non-existent version"));
	$smarty->display("error.tpl");
	die;
}

$tikilib->get_perm_object($page, 'wiki page', $info);
$access->check_permission(array('tiki_p_rollback', 'tiki_p_edit'));

if (isset($_REQUEST["rollback"])) {

	$access->check_authenticity(tr('Are you sure you want to roll back "%0" to version #%1?', $page, $version));

	$histlib->use_version($page, $version, '');
	$tikilib->invalidate_cache($page);

	header("location: tiki-index.php?page=" . urlencode($page));
	die;
}
$version = $histlib->get_version($page, $version);
$version["data"] = $tikilib->parse_data($version["data"], array('preview_mode' => true, 'is_html' => $version['is_html']));
$smarty->assign_by_ref('preview', $version);
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-rollback.tpl');
$smarty->display("tiki.tpl");
