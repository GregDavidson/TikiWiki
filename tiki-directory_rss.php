<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-directory_rss.php 57957 2016-03-17 19:58:54Z jonnybradley $

require_once ('tiki-setup.php');
require_once ('lib/directory/dirlib.php');
$rsslib = TikiLib::lib('rss');
if ($prefs['feed_directories'] != 'y') {
	$errmsg = tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}
if ($prefs['feature_directory'] != 'y') {
	$errmsg = tr("This feature is disabled: %0", 'feature_directory');
	require_once ('tiki-rss_error.php');
}
if ($tiki_p_view_directory != 'y') {
	$smarty->assign('errortype', 401);
	$errmsg = tra("Permission denied");
	require_once ('tiki-rss_error.php');
}
if (!isset($_REQUEST["parent"])) {
	$errmsg = tra("No parent specified");
	require_once ('tiki-rss_error.php');
}
$feed = "directory";
$uniqueid = $feed . "?parent=" . $_REQUEST["parent"];
$output = $rsslib->get_from_cache($uniqueid);
if ($output["data"] == "EMPTY") {
	$title = tra("Tiki RSS feed for directory sites");
	$rc = $dirlib->dir_get_category($_REQUEST["parent"]);
	$desc = tr("Latest sites of directory %0.", $rc["name"]);
	$id = "siteId";
	$titleId = "name";
	$descId = "description";
	$dateId = "created";
	$readrepl = "tiki-directory_redirect.php?$id=%s";
	$tmp = $prefs['feed_' . $feed . '_title'];
	if ($tmp <> '') {
		$title = $tmp;
	}
	$tmp = $prefs['feed_' . $feed . '_desc'];
	if ($desc <> '') {
		$desc = $tmp;
	}
	$changes = $dirlib->dir_list_sites($_REQUEST["parent"], 0, $prefs['feed_directories_max'], $dateId . '_desc', '', 'y');
	$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, '', $id, $title, $titleId, $desc, $descId, $dateId, '');
}
header("Content-type: " . $output["content-type"]);
print $output["data"];
