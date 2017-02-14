<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: include_fgal.php 58787 2016-06-05 13:59:28Z lindonb $

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}
$filegallib = TikiLib::lib('filegal');
if (isset($_REQUEST["filegalset"])) {
	simple_set_value("home_file_gallery");
}
if (isset($_REQUEST["filegalfeatures"])) {
	check_ticket('admin-inc-fgal');
	// Check for last character being a / or a \
	if (substr($_REQUEST["fgal_use_dir"], -1) != "\\" && substr($_REQUEST["fgal_use_dir"], -1) != "/" && $_REQUEST["fgal_use_dir"] != "") {
		$_REQUEST["fgal_use_dir"].= "/";
	}
	// Check for last character being a / or a \
	if (substr($_REQUEST["fgal_podcast_dir"], -1) != "\\" && substr($_REQUEST["fgal_podcast_dir"], -1) != "/" && $_REQUEST["fgal_podcast_dir"] != "") {
		$_REQUEST["fgal_podcast_dir"].= "/";
	}
	if (substr($_REQUEST["fgal_batch_dir"], -1) != "\\" && substr($_REQUEST["fgal_batch_dir"], -1) != "/" && $_REQUEST["fgal_batch_dir"] != "") {
		$_REQUEST["fgal_batch_dir"].= "/";
	}
	simple_set_value("fgal_use_dir");
	simple_set_value("fgal_podcast_dir");
	simple_set_value("fgal_batch_dir");
	if (!empty($_REQUEST['fgal_quota']) && !empty($_REQUEST['fgal_quota_default']) && $_REQUEST['fgal_quota_default'] > $_REQUEST['fgal_quota']) {
		$_REQUEST['fgal_quota_default'] = $_REQUEST['fgal_quota'];
	}
	simple_set_value('fgal_quota_default');
}
if (!empty($_REQUEST['updateMime'])) {
	$files = $filegallib->table('tiki_files');
	$rows = $files->fetchAll(array('fileId', 'filename', 'filetype'), array('archiveId' => 0, 'filetype' => 'application/octet-stream'));
	foreach ($rows as $row) {
		$t = $filegallib->fixMime($row);
		if ($t != 'application/octet-stream') {
			$files->update(array('filetype' => $t), array('fileId' => $row['fileId']));
		}
	}
}

if (!empty($_REQUEST['move'])) {
	if ($_REQUEST['move'] == 'to_fs') {
		if (empty($prefs['fgal_use_dir'])) {
			$errors[] = tra('You must specify a directory');
		} else {
			$feedbacks = array();
			$errors = $filegallib->moveFiles($_REQUEST['move'], $feedbacks);
		}
	} elseif ($_REQUEST['move'] == 'to_db') {
		$feedbacks = array();
		$errors = $filegallib->moveFiles($_REQUEST['move'], $feedbacks);
	}
	if (!empty($errors)) {
		Feedback::error(['mes' => $errors]);
	}
	if (!empty($feedbacks)) {
		Feedback::note(['mes' => $feedbacks]);
	}
}

if (isset($_REQUEST["filegallistprefs"])) {
	check_ticket('admin-inc-fgal');
	simple_set_value('fgal_list_id');
	simple_set_value('fgal_list_type');
	simple_set_value('fgal_list_name');
	simple_set_value('fgal_list_description');
	simple_set_value('fgal_list_size');
	simple_set_value('fgal_list_created');
	simple_set_value('fgal_list_lastModif');
	simple_set_value('fgal_list_creator');
	simple_set_value('fgal_list_author');
	simple_set_value('fgal_list_last_user');
	simple_set_value('fgal_list_comment');
	simple_set_value('fgal_list_files');
	simple_set_value('fgal_list_hits');
	simple_set_value('fgal_list_lastDownload');
	simple_set_value('fgal_list_deleteAfter');
	simple_set_value('fgal_show_checked');
	simple_set_value('fgal_list_share');
	simple_set_value('fgal_list_lockedby');
	$_REQUEST['fgal_sort_mode'] = (empty($_REQUEST['fgal_sortorder']) ? 'created' : $_REQUEST['fgal_sortorder']) . '_' . (empty($_REQUEST['fgal_sortdirection']) ? 'desc' : $_REQUEST['fgal_sortdirection']);
	$prefs['fgal_sort_mode'] = $_REQUEST['fgal_sort_mode'];
	simple_set_value('fgal_sort_mode');
	simple_set_toggle('fgal_show_explorer');
	simple_set_toggle('fgal_show_path');
	simple_set_toggle('fgal_show_slideshow');
	simple_set_toggle('fgal_list_ratio_hits');
	simple_set_value('fgal_default_view');
	simple_set_value('fgal_icon_fileId');
	simple_set_value('fgal_list_backlinks');
	simple_set_value('fgal_list_id_admin');
	simple_set_value('fgal_list_type_admin');
	simple_set_value('fgal_list_name_admin');
	simple_set_value('fgal_list_description_admin');
	simple_set_value('fgal_list_size_admin');
	simple_set_value('fgal_list_created_admin');
	simple_set_value('fgal_list_lastModif_admin');
	simple_set_value('fgal_list_creator_admin');
	simple_set_value('fgal_list_author_admin');
	simple_set_value('fgal_list_last_user_admin');
	simple_set_value('fgal_list_comment_admin');
	simple_set_value('fgal_list_files_admin');
	simple_set_value('fgal_list_hits_admin');
	simple_set_value('fgal_list_lastDownload_admin');
	simple_set_value('fgal_list_lockedby_admin');
	simple_set_value('fgal_list_backlinks_admin');
}

$usedSize = $filegallib->getUsedSize();
$smarty->assign_by_ref('usedSize', $usedSize);
if (isset($_REQUEST["filegalhandlers"])) {
	check_ticket('admin-inc-fgal');
	if (!empty($_REQUEST['mimes'])) {
		$mimes = $_REQUEST['mimes'];
		foreach ($mimes as $mime => $cmd) {
			$mime = trim($mime);
			if (empty($cmd)) {
				$filegallib->delete_file_handler($mime);
			} else {
				$filegallib->change_file_handler($mime, $cmd);
			}
		}
	}
	if (!empty($_REQUEST['newMime']) && !empty($_REQUEST['newCmd'])) {
		$filegallib->change_file_handler($_REQUEST['newMime'], $_REQUEST['newCmd']);
	}
}
if (isset($_REQUEST["filegalredosearch"])) {
	$filegallib->reindex_all_files_for_search_text();
}

if (isset($_REQUEST["filegalfixvndmsfiles"])) {
	$filegallib->fix_vnd_ms_files();
}

if ($prefs['fgal_viewerjs_feature'] === 'y') {
	$viewerjs_err = '';
	if (empty($prefs['fgal_viewerjs_uri'])) {

		$viewerjs_err = tra('ViewerJS URI not set');

	} else if (strpos($prefs['fgal_viewerjs_uri'], '://') === false) {	// local install

		if (! is_readable($prefs['fgal_viewerjs_uri'])) {
			$viewerjs_err = tr('ViewerJS URI not found (local file not readable)');
		}

	} else {												// remote (will take a while)

		$file_headers = get_headers(\ZendOpenId\OpenId::absoluteUrl($prefs['fgal_viewerjs_uri']));
		if (strpos($file_headers[0], '200') === false) {
			$viewerjs_err = tr('ViewerJS URI not found (%0)', $file_headers[0]);
		}
	}

	$smarty->assign('viewerjs_err', $viewerjs_err);
}

if (!empty($prefs['fgal_sort_mode']) && preg_match('/(.*)_(asc|desc)/', $prefs['fgal_sort_mode'], $matches)) {
	$smarty->assign('fgal_sortorder', $matches[1]);
	$smarty->assign('fgal_sortdirection', $matches[2]);
} else {
	$smarty->assign('fgal_sortorder', 'created');
	$smarty->assign('fgal_sortdirection', 'desc');
}
$options_sortorder = array(
	tra('Creation Date') => 'created',
	tra('Name') => 'name',
	tra('Last modification date') => 'lastModif',
	tra('Hits') => 'hits',
	tra('Owner') => 'user',
	tra('Description') => 'description',
	tra('ID') => 'id'
);
$smarty->assign_by_ref('options_sortorder', $options_sortorder);
$handlers = $filegallib->get_file_handlers();
ksort($handlers);
$smarty->assign("fgal_handlers", $handlers);
$usedTypes = $filegallib->getFiletype();
$missingHandlers = array();
$vnd_ms_files_exist = false;

foreach ($usedTypes as $type) {
	if (! $filegallib->get_parse_app($type, true)) {
		$missingHandlers[] = $type;
		if (strpos($type, '/vnd.ms-') !== false) {
			$vnd_ms_files_exist = true;
		}
	}
}

$smarty->assign_by_ref('missingHandlers', $missingHandlers);
$smarty->assign('vnd_ms_files_exist', $vnd_ms_files_exist);
include_once ('fgal_listing_conf.php');
ask_ticket('admin-inc-fgal');
