<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-tracker_rss.php 57956 2016-03-17 19:58:12Z jonnybradley $

require_once ('tiki-setup.php');
$trklib = TikiLib::lib('trk');
$rsslib = TikiLib::lib('rss');
$smarty->loadPlugin('smarty_modifier_sefurl');

if ($prefs['feed_tracker'] != 'y') {
	$errmsg = tra("rss feed disabled");
	require_once ('tiki-rss_error.php');
}
if ($prefs['feature_trackers'] != 'y') {
	$errmsg = tra("This feature is disabled") . ": feature_trackers";
	require_once ('tiki-rss_error.php');
}
if (!isset($_REQUEST["trackerId"])) {
	$errmsg = tra("No trackerId specified");
	require_once ('tiki-rss_error.php');
}
$perms = Perms::get(array('type' => 'tracker', 'object' => $_REQUEST['trackerId']));
if ($tiki_p_admin_trackers != 'y' && (!$perms->view_trackers && !$perms->view_trackers_pending && !$perms->view_trackers_closed)) {
	$smarty->assign('errortype', 401);
	$errmsg = tra("You do not have permission to view this section");
	require_once ('tiki-rss_error.php');
}
$feed = "tracker";
$id = "trackerId";
$uniqueid = "$feed.id=" . $_REQUEST["trackerId"];
if (isset($_REQUEST['sort_mode'])) {
	$sort_mode = $_REQUEST['sort_mode'];
	$uniqueid.= $sort_mode;
} else {
	$sort_mode = 'created_desc';
}
$output = $rsslib->get_from_cache($uniqueid);
if ($output["data"] == "EMPTY") {
	$tmp = $trklib->get_tracker($_REQUEST["$id"]);
	if (empty($tmp)) {
		$errmsg = tra("Incorrect param");
		require_once ('tiki-rss_error.php');
	}
	$title = $prefs['feed_tracker_title'] . $tmp["name"];
	$desc = $prefs['feed_tracker_desc'] . $tmp["description"];
	$tmp = null;
	$tmp = $prefs['feed_' . $feed . '_title'];
	if ($tmp <> '') {
		$title = $tmp;
	}
	$tmp = $prefs['feed_' . $feed . '_desc'];
	if ($desc <> '') {
		$desc = $tmp;
	}
	$titleId = "rss_subject";
	$descId = "rss_description";
	$authorId = ""; // "user";
	$dateId = "created";
	$urlparam = "itemId";
	$readrepl = "tiki-view_tracker_item.php?$id=%s&$urlparam=%s";
	$listfields = $trklib->list_tracker_fields($_REQUEST[$id]);
	$fields = array();
	foreach ($listfields['data'] as $f) {
		if ($f['isHidden'] == 'y' || $f['isHidden'] == 'c' || $f['isHidden'] == 'r') {
			continue;
		}
		$fields[$f['fieldId']] = $f;
	}
	if (isset($_REQUEST['filterfield'])) {
		$filterfield = explode(':', $_REQUEST['filterfield']);
		if (isset($_REQUEST['exactvalue'])) {
			$exactvalue = explode(':', $_REQUEST['exactvalue']);
		}
		if (isset($_REQUEST['filtertvalue'])) {
			$exactvalue = explode(':', $_REQUEST['filtervalue']);
		}
	} else {
		$filterfield = null;
		$exactvalue = null;
		$filtervalue = null;
	}
	$doNotShowEmptyField = $trklib->get_trackers_options($_REQUEST[$id], 'doNotShowEmptyField');
	$doNotShowEmptyField = !empty($doNotShowEmptyField[0]['value']) && $doNotShowEmptyField[0]['value'] === 'y';

	if (isset($_REQUEST['status'])) {
		if (!$trklib->valid_status($_REQUEST['status'])) {
			$errmsg = tra("Incorrect parameter");
			require_once ('tiki-rss_error.php');
		}
		$status = $_REQUEST['status'];
	} else {
		$status = 'opc';
	}
	if (!$perms->view_trackers) {
		$status = str_replace('o', '', $status);
	}
	if (!$perms->view_trackers_pending) {
		$status = str_replace('p', '', $status);
	}
	if (!$perms->view_trackers_closed) {
		$status = str_replace('c', '', $status);
	}
	if (empty($status)) {
		$smarty->assign('errortype', 401);
		$errmsg = tra("You do not have permission to view this section");
		require_once ('tiki-rss_error.php');
	}
	// try to deal with two mutually exclusive params added pre-tiki 11 to hide the "Tracker item: #123456" title
	// showitemId and noId (showitemId will take precedence)
	$showItemId = false;									// default to looking nice

	if (isset($_REQUEST['showitemId'])) {
		$showItemId = $_REQUEST['showitemId'] === 'y';
	} else if (isset($_REQUEST['noId'])) {
		$showItemId = $_REQUEST['noId'] === 'n';
	}

	$tmp = $trklib->list_items($_REQUEST[$id], 0, $prefs['feed_tracker_max'], $sort_mode, $fields, $filterfield, $filtervalue, $status, null, $exactvalue);
	foreach ($tmp["data"] as $data) {
		$data[$titleId] = $showItemId ? tra('Tracker item:') . ' #' . $data[$urlparam] : '';
		$data[$descId] = '';
		$first_text_field = null;
		$aux_subject = null;
		foreach ($data["field_values"] as $data2) {
			$showEvenIfEmpty = array('s', 'STARS', 'h', 'l', 'W');	// this duplicates the logic in tiki-view_tracker_item.tpl
			if (isset($data2["name"]) && !empty($data2['value']) || !$doNotShowEmptyField || in_array($data2['type'], $showEvenIfEmpty)) {
				if (!isset($data[$data2['fieldId']])) {
					$data[$data2['fieldId']] = $data2['value'];
				}
				$data2['value'] = $trklib->field_render_value(
					array(
						'field' => $data2,
						'item' => $data,
						'process' => 'y',
					)
				);
				if (empty($data2['value'])) {
					$data2['value'] = '(' . tra('empty') . ')';
				} else {
					$data2['value'] = htmlspecialchars_decode($data2['value']);
				}
				if ($prefs['feed_tracker_labels'] === 'y') {
					$data[$descId] .= $data2["name"] . ": ";
				} else if (preg_match_all('/(<img[^>]*>)/', $data2['value'], $m)) {
					$data2['value'] = implode('', $m[1]);
				}
				$data[$descId] .= $data2["value"] . "<br />";
				$field_name_check = strtolower($data2["name"]);
				if ($field_name_check == "subject") {
					$aux_subject = " - " . $data2["value"];
				} elseif (!isset($aux_subject)) {
					// alternative names for subject field:
					if (($field_name_check == "summary") || ($field_name_check == "name") || ($field_name_check == "title") || ($field_name_check == "topic")) {
						$aux_subject = $data2["value"];
					} elseif ($data2["type"] == 't' && !isset($first_text_field)) {
						$first_text_field = $data2["name"] . ": " . $data2["value"];
					}
				}
			}
		}
		if (!$showItemId) {
			$data[$titleId] = empty($aux_subject) ? $first_text_field : $aux_subject;
		} elseif (!isset($aux_subject) && isset($first_text_field)) {
			$data[$titleId] .= (empty($data[$titleId])?'': ' - ') . $first_text_field;
		} elseif (isset($aux_subject)) {
			$data[$titleId] .= (empty($data[$titleId])?'': ' - ') . $aux_subject;
		}
		$data[$titleId] = strip_tags($data[$titleId]);
		$data["id"] = $_REQUEST["$id"];
		$data["field_values"] = null;
		$data['sefurl'] = smarty_modifier_sefurl($data['itemId'], 'trackeritem');
		$changes["data"][] = $data;
		$data = null;
	}
	$tmp = null;
	if (isset($changes['data'])) {
		$output = $rsslib->generate_feed($feed, $uniqueid, '', $changes, $readrepl, $urlparam, $id, $title, $titleId, $desc, $descId, $dateId, $authorId);
	}
	$changes = null;
}
header("Content-type: " . $output["content-type"]);
print $output["data"];
