<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-view_tracker.php 60606 2016-12-12 13:24:42Z jonnybradley $

$section = 'trackers';
require_once ('tiki-setup.php');

$access->check_feature('feature_trackers');

$trklib = TikiLib::lib('trk');
if ($prefs['feature_groupalert'] == 'y') {
	$groupalertlib = TikiLib::lib('groupalert');
}
$notificationlib = TikiLib::lib('notification');
if ($prefs['feature_categories'] == 'y') {
	$categlib = TikiLib::lib('categ');
}
$auto_query_args = array(
	'offset',
	'trackerId',
	'reloff',
	'itemId',
	'maxRecords',
	'status',
	'sort_mode',
	'initial',
	'filterfield',
	'filtervalue'
);
if (!empty($_REQUEST['itemId'])) $ratedItemId = $_REQUEST['itemId'];
$_REQUEST["itemId"] = 0;
$smarty->assign('itemId', $_REQUEST["itemId"]);
if (!isset($_REQUEST["trackerId"])) {
	$smarty->assign('msg', tra("No tracker indicated"));
	$smarty->display("error.tpl");
	die;
}
$trackerDefinition = Tracker_Definition::get($_REQUEST['trackerId']);
if (! $trackerDefinition) {
	$smarty->assign('msg', tra("No tracker indicated"));
	$smarty->display("error.tpl");
	die;
}

$tracker_info = $trackerDefinition->getInformation();
$fields['data'] = array();

$tikilib->get_perm_object($_REQUEST['trackerId'], 'tracker', $tracker_info);
if (!empty($_REQUEST['show']) && $_REQUEST['show'] == 'view') {
	$cookietab = '1';
} elseif (!empty($_REQUEST['show']) && $_REQUEST['show'] == 'mod') {
	$cookietab = '2';
} elseif (empty($_REQUEST['cookietab'])) {
	if ((isset($tracker_info['writerCanModify']) && $tracker_info['writerCanModify'] == 'y' && $user) or
		(isset($tracker_info['userCanSeeOwn']) && $tracker_info['userCanSeeOwn'] == 'y' && $user)) $cookietab = '1';
	elseif (!($tiki_p_view_trackers == 'y' || $tiki_p_admin == 'y' || $tiki_p_admin_trackers == 'y') && $tiki_p_create_tracker_items == 'y') $cookietab = "2";
	else if (!isset($cookietab)) {
		$cookietab = '1';
	}
} else {
	$cookietab = $_REQUEST['cookietab'];
}
$defaultvalues = array();
if (isset($_REQUEST['vals']) and is_array($_REQUEST['vals'])) {
	$defaultvalues = $_REQUEST['vals'];
	$cookietab = "2";
} elseif (isset($_REQUEST['new'])) {
	$cookietab = "2";
}
$smarty->assign('defaultvalues', $defaultvalues);
$my = '';
$ours = '';
if (isset($_REQUEST['my'])) {
	if ($tiki_p_admin_trackers == 'y') {
		$my = $_REQUEST['my'];
	} elseif ($user) {
		$my = $user;
	}
} elseif (isset($_REQUEST['ours'])) {
	if ($tiki_p_admin_trackers == 'y') {
		$ours = $_REQUEST['ours'];
	} elseif ($group) {
		$ours = $group;
	}
}
if ($tiki_p_create_tracker_items == 'y' && !empty($t['start'])) {
	if ($tikilib->now < $t['start']) {
		$tiki_p_create_tracker_items = 'n';
		$smarty->assign('tiki_p_create_tracker_items', 'n');
	}
}
if ($tiki_p_create_tracker_items == 'y' && !empty($t['end'])) {
	if ($tikilib->now > $t['end']) {
		$tiki_p_create_tracker_items = 'n';
		$smarty->assign('tiki_p_create_tracker_items', 'n');
	}
}

$access->check_permission_either(array('tiki_p_view_trackers', 'tiki_p_create_tracker_items'), tra('Create or view tracker'), 'tracker', $_REQUEST["trackerId"]);
$tikilib->get_perm_object($_REQUEST['trackerId'], 'tracker', $tracker_info);

if ($tracker_info['adminOnlyViewEditItem'] === 'y') {
	$access->check_permission('tiki_p_admin_trackers', tra('Admin this tracker'), 'tracker', $tracker_info['trackerId']);
}

if ($tiki_p_view_trackers != 'y') {
	$userCreatorFieldId = $trackerDefinition->getWriterField();
	$groupCreatorFieldId = $trackerDefinition->getWriterGroupField();
	if ($user && !$my and ( (isset($tracker_info['writerCanModify']) and $tracker_info['writerCanModify'] == 'y') or
							(isset($tracker_info['userCanSeeOwn']) and $tracker_info['userCanSeeOwn'] == 'y'))
							 and !empty($userCreatorFieldId)) {
		$my = $user;
	} elseif ($user && !$ours and isset($tracker_info['writerGroupCanModify']) and $tracker_info['writerGroupCanModify'] == 'y' and !empty($groupCreatorFieldId)) {
		$ours = $group;
	}
}
$smarty->assign('my', $my);
$smarty->assign('ours', $ours);
if ($prefs['feature_groupalert'] == 'y') {
	$groupforalert = $groupalertlib->GetGroup('tracker', $_REQUEST['trackerId']);
	if ($groupforalert != '') {
		$showeachuser = $groupalertlib->GetShowEachUser('tracker', $_REQUEST["trackerId"], $groupforalert);
		$listusertoalert = $userlib->get_users(0, -1, 'login_asc', '', '', false, $groupforalert, '');
		$smarty->assign_by_ref('listusertoalert', $listusertoalert['data']);
	}
	$smarty->assign_by_ref('groupforalert', $groupforalert);
	$smarty->assign_by_ref('showeachuser', $showeachuser);
}
$status_types = array();
$status_raw = $trklib->status_types();
if (isset($_REQUEST['status'])) {
	$sts = preg_split('//', $_REQUEST['status'], -1, PREG_SPLIT_NO_EMPTY);
} elseif (isset($tracker_info["defaultStatus"])) {
	$sts = preg_split('//', $tracker_info["defaultStatus"], -1, PREG_SPLIT_NO_EMPTY);
	$_REQUEST['status'] = $tracker_info["defaultStatus"];
} else {
	$sts = array(
		'o'
	);
	$_REQUEST['status'] = 'o';
}
foreach ($status_raw as $let => $sta) {
	if ((isset(${$sta['perm']}) and ${$sta['perm']} == 'y') or ($my or $ours)) {
		if (in_array($let, $sts)) {
			$sta['class'] = 'statuson';
			$sta['statuslink'] = str_replace($let, '', implode('', $sts));
		} else {
			$sta['class'] = 'statusoff';
			$sta['statuslink'] = implode('', $sts) . $let;
		}
		$status_types["$let"] = $sta;
	}
}
$smarty->assign('status_types', $status_types);
if (count($status_types) == 0) {
	$tracker_info["showStatus"] = 'n';
}
$filterFields = array('isSearchable'=>'y', 'isTblVisible'=>'y', 'type'=>array('q','u','g','I','C','n','j','f'));
$sort_field = 0;
if (!isset($_REQUEST["sort_mode"])) {
	if (isset($tracker_info['defaultOrderKey'])) {
		if ($tracker_info['defaultOrderKey'] == - 1) $sort_mode = 'lastModif';
		elseif ($tracker_info['defaultOrderKey'] == - 2) $sort_mode = 'created';
		elseif ($tracker_info['defaultOrderKey'] == - 3) $sort_mode = 'itemId';
		else {
			$sort_field = $tracker_info['defaultOrderKey'];
			$sort_mode = 'f_' . $tracker_info['defaultOrderKey'];
			$filterFields['fieldId'] = $tracker_info['defaultOrderKey'];
		}
		if (isset($tracker_info['defaultOrderDir'])) {
			$sort_mode.= "_" . $tracker_info['defaultOrderDir'];
		} else {
			$sort_mode.= "_asc";
		}
	} else {
		$sort_mode = '';
	}
} else {
	$sort_mode = $_REQUEST["sort_mode"];
	if (preg_match('/f_([0-9]+)_/', $sort_mode, $matches)) {
		$sort_field = $matches[1];
		$filterFields['fieldId'] = $matches[1];
	}
}
$smarty->assign_by_ref('sort_mode', $sort_mode);
//get field settings (no values)
$xfields = array('data' => $trackerDefinition->getFields());

$popupFields = $trackerDefinition->getPopupFields();
$smarty->assign_by_ref('popupFields', $popupFields);

$smarty->assign('tracker_sync', $trackerDefinition->getSyncInformation());

$orderkey = false;
$listfields = array();
$usecategs = false;
$textarea_options = false;
$all_descends = false;

$fieldFactory = $trackerDefinition->getFieldFactory();

$itemObject = Tracker_Item::newItem($_REQUEST['trackerId']);

$ins_fields = array('data' => array());

foreach ($xfields['data'] as $i => $current_field) {
	$current_field_ins = null;

	$fid = $current_field["fieldId"];
	$ins_id = 'ins_' . $fid;
	$current_field["ins_id"] = $ins_id;
	$current_field["id"] = $fid;
	$filter_id = 'filter_' . $fid;
	$current_field["filter_id"] = $filter_id;
	if (!empty($sort_field) and $sort_field == $fid) {
		$orderkey = true;
	}

	$fieldIsVisible = $itemObject->canViewField($fid);
	$fieldIsEditable = $itemObject->canModifyField($fid);

	if ($fieldIsVisible || $fieldIsEditable) {
		$handler = $fieldFactory->getHandler($current_field);

		if ($handler) {
			$field_values = $insert_values = $handler->getFieldData($_REQUEST);
			$current_field_ins = array_merge($current_field, $insert_values);
		}
	}

	//exclude fields that should not be listed
	if ($fieldIsVisible && ($current_field_ins['isTblVisible'] == 'y' or in_array($fid, $popupFields))) {
		$listfields[$fid] = $current_field_ins;
		if ($fieldIsEditable) {
			$listfields[$fid]['editable'] = true;
		} else {
			$listfields[$fid]['editable'] = false;
		}
	}

	if (! empty($current_field_ins)) {
		if ($fieldIsEditable) {
			$ins_fields['data'][$i] = $current_field_ins;
		}
		if ($fieldIsVisible) {
			$fields['data'][$i] = $current_field_ins;
		}
	}
}

// Collect information from the provided fields
$newItemRateField = null;
$newItemRate = null;
if (!empty($ins_fields['data'])) {
	foreach ($ins_fields['data'] as $current_field) {
		if ($current_field['type'] == 's' && $current_field['name'] == 'Rating') {
			$newItemRateField = $current_field;
			$newItemRate = $current_field['request_rate'];
		}
	}
}

if (!$orderkey && $sort_mode == '') {
	$sort_mode = 'lastModif_asc';
}
if (!empty($_REQUEST['remove'])) {
	$item_info = $trklib->get_item_info($_REQUEST['remove']);
	$actionObject = Tracker_Item::fromInfo($item_info);
	if ($actionObject->canRemove()) {
		//bypass the question to confirm delete or not
		if (empty($_REQUEST['force'])) {
			$access->check_authenticity();
		$trklib->remove_tracker_item($_REQUEST['remove']);
		}
	}
} elseif (isset($_REQUEST["batchaction"]) and $_REQUEST["batchaction"] == 'delete') {
	check_ticket('view-trackers');
	$access->check_authenticity(tr('Are you sure you want to delete the selected items?'));
	$transaction = $tikilib->begin();

	foreach ($_REQUEST['action'] as $batchid) {
		$item_info = $trklib->get_item_info($batchid);
		$actionObject = Tracker_Item::fromInfo($item_info);
		if ($actionObject->canRemove()) {
			$trklib->remove_tracker_item($batchid);
		}
	}

	$transaction->commit();
	
} elseif (isset($_REQUEST['batchaction']) and ($_REQUEST['batchaction'] == 'o' || $_REQUEST['batchaction'] == 'p' || $_REQUEST['batchaction'] == 'c')) {
	check_ticket('view-trackers');
	$transaction = $tikilib->begin();

	foreach ($_REQUEST['action'] as $batchid) {
		$item_info = $trklib->get_item_info($batchid);
		$actionObject = Tracker_Item::fromInfo($item_info);
		if ($actionObject->canModify()) {
			$trklib->replace_item($_REQUEST['trackerId'], $batchid, array('data' => ''), $_REQUEST['batchaction']);
		}
	}

	$transaction->commit();
}
$smarty->assign('mail_msg', '');
$smarty->assign('email_mon', '');
if ($prefs['feature_user_watches'] == 'y' and $tiki_p_watch_trackers == 'y') {
	if ($user and isset($_REQUEST['watch'])) {
		check_ticket('view-trackers');
		if ($_REQUEST['watch'] == 'add') {
			$tikilib->add_user_watch($user, 'tracker_modified', $_REQUEST["trackerId"], 'tracker', $tracker_info['name'], "tiki-view_tracker.php?trackerId=" . $_REQUEST["trackerId"]);
		} else {
			$tikilib->remove_user_watch($user, 'tracker_modified', $_REQUEST["trackerId"], 'tracker');
		}
	}
	$smarty->assign('user_watching_tracker', 'n');
	$it = $tikilib->user_watches($user, 'tracker_modified', $_REQUEST['trackerId'], 'tracker');
	if ($user and $tikilib->user_watches($user, 'tracker_modified', $_REQUEST['trackerId'], 'tracker')) {
		$smarty->assign('user_watching_tracker', 'y');
	}
	// Check, if the user is watching this tracker by a category.
	if ($prefs['feature_categories'] == 'y') {
		$watching_categories_temp = $categlib->get_watching_categories($_REQUEST["trackerId"], 'tracker', $user);
		$smarty->assign('category_watched', 'n');
		if (count($watching_categories_temp) > 0) {
			$smarty->assign('category_watched', 'y');
			$watching_categories = array();
			foreach ($watching_categories_temp as $wct) {
				$watching_categories[] = array(
					"categId" => $wct,
					"name" => $categlib->get_category_name($wct)
				);
			}
			$smarty->assign('watching_categories', $watching_categories);
		}
	}
}

if (isset($_REQUEST["save"])) {
	if ($itemObject->canModify()) {
		$captchalib = TikiLib::lib('captcha');
		if (empty($user) && $prefs['feature_antibot'] == 'y' && !$captchalib->validate()) {
			$smarty->assign('msg', $captchalib->getErrors());
			$smarty->assign('errortype', 'no_redirect_login');
			$smarty->display("error.tpl");
			die;
		}
		// Check field values for each type and presence of mandatory ones
		$mandatory_missing = array();
		$err_fields = array();
		$categorized_fields = $trackerDefinition->getCategorizedFields();
		$field_errors = $trklib->check_field_values($ins_fields, $categorized_fields, $_REQUEST['trackerId'], empty($_REQUEST['itemId'])?'':$_REQUEST['itemId']);
		$smarty->assign('err_mandatory', $field_errors['err_mandatory']);
		$smarty->assign('err_value', $field_errors['err_value']);
		// values are OK, then lets add a new item
		if (count($field_errors['err_mandatory']) == 0 && count($field_errors['err_value']) == 0) {
			$smarty->assign('input_err', '0'); // no warning to display
			check_ticket('view-trackers');
			if (!isset($_REQUEST["status"]) or ($tracker_info["showStatus"] != 'y' and $tiki_p_admin_trackers != 'y')) {
				$_REQUEST["status"] = '';
			}
			if (empty($_REQUEST["itemId"]) && $tracker_info['oneUserItem'] == 'y') { // test if one item per user
				$_REQUEST['itemId'] = $trklib->get_user_item($_REQUEST['trackerId'], $tracker_info);
			}
			$itemid = $trklib->replace_item($_REQUEST["trackerId"], $_REQUEST["itemId"], $ins_fields, $_REQUEST['status']);
			if (isset($_REQUEST['listtoalert']) && $prefs['feature_groupalert'] == 'y') {
				$groupalertlib->Notify($_REQUEST['listtoalert'], "tiki-view_tracker_item.php?itemId=$itemid");
			}
			$cookietab = "1";
			$smarty->assign('itemId', '');
			if (isset($newItemRate)) {
				$trackerId = $_REQUEST["trackerId"];
				$trklib->replace_rating($trackerId, $itemid, $newItemRateField, $user, $newItemRate);
			}
			if (isset($_REQUEST["viewitem"]) && $_REQUEST["viewitem"] == 'view') {
				header('location: ' . preg_replace('#[\r\n]+#', '', "tiki-view_tracker_item.php?trackerId=" . $_REQUEST["trackerId"] . "&itemId=" . $itemid));
				die;
			} elseif (isset($_REQUEST["viewitem"]) && $_REQUEST["viewitem"] == 'new') {
				header('location: ' . preg_replace('#[\r\n]+#', '', "tiki-view_tracker.php?trackerId=" . $_REQUEST["trackerId"] . "&cookietab=2"));
				die;
			}
			if (isset($tracker_info["defaultStatus"])) {
				$_REQUEST['status'] = $tracker_info["defaultStatus"];
			}
		} else {
			$cookietab = "2";
			$smarty->assign('input_err', '1'); // warning to display
			
		}
		if (isset($newItemRate)) {
			$trackerId = $_REQUEST["trackerId"];
			$trklib->replace_rating($trackerId, $itemid, $newItemRateField, $user, $newItemRate);
		}
	}
}
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (!empty($_REQUEST["maxRecords"])) {
	$maxRecords = $_REQUEST['maxRecords'];
}
if (isset($_REQUEST["initial"])) {
	$initial = $_REQUEST["initial"];
} else {
	$initial = '';
}
$smarty->assign('initial', $initial);
$writerfield = $trackerDefinition->getWriterField();
$writergroupfield = $trackerDefinition->getWriterGroupField();

if ($my and $writerfield) {
	$filterfield = $writerfield;
} elseif ($ours and $writergroupfield) {
	$filterfield = $writergroupfield;
} else {
	if (isset($_REQUEST["filterfield"])) {
		$filterfield = $_REQUEST["filterfield"];
	} else {
		$filterfield = '';
	}
}
$smarty->assign('filterfield', $filterfield);
if ($my and $writerfield) {
	$exactvalue = $my;
	$filtervalue = '';
	$_REQUEST['status'] = 'opc';
} elseif ($ours and $writergroupfield) {
	$exactvalue = $userlib->get_user_groups($user);
	$filtervalue = '';
	$_REQUEST['status'] = 'opc';
} else {
	if (isset($_REQUEST["filtervalue"]) and is_array($_REQUEST["filtervalue"]) and isset($_REQUEST["filtervalue"]["$filterfield"])) {
		$filtervalue = $_REQUEST["filtervalue"]["$filterfield"];
	} else if (isset($_REQUEST["filtervalue"])) {
		$filtervalue = $_REQUEST["filtervalue"];
	} else {
		$filtervalue = '';
	}
	if (!empty($_REQUEST['filtervalue_other'])) {
		$filtervalue = $_REQUEST['filtervalue_other'];
	}
	$field = $trackerDefinition->getField($filterfield);
	if( $field && in_array($field['type'], array('d', 'D', 'R')) )
		$exactvalue = $filtervalue;
	else
		$exactvalue = '';
}
$smarty->assign('filtervalue', $filtervalue);
if (is_array($filtervalue)) {
	foreach ($filtervalue as $fil) {
		$filtervalueencoded = "&amp;filtervalue[" . rawurlencode($filterfield) . "][]=" . rawurlencode($fil);
	}
	$smarty->assign('filtervalueencoded', $filtervalueencoded);
}
$smarty->assign('status', $_REQUEST["status"]);
if (isset($_REQUEST["trackerId"])) {
	$trackerId = $_REQUEST["trackerId"];
}
if (isset($tracker_info['useRatings']) and $tracker_info['useRatings'] == 'y' and $user and $tiki_p_tracker_vote_ratings == 'y' and !empty($_REQUEST['trackerId']) and !empty($ratedItemId) and isset($newItemRate) and ($newItemRate == 'NULL' || in_array($newItemRate, explode(',', $tracker_info['ratingOptions'])))) {
	$trklib->replace_rating($_REQUEST['trackerId'], $ratedItemId, $newItemRateField, $user, $newItemRate);
}
$items = $trklib->list_items($_REQUEST["trackerId"], $offset, $maxRecords, $sort_mode, $listfields, $filterfield, $filtervalue, $_REQUEST["status"], $initial, $exactvalue, '', $xfields);
$urlquery['status'] = $_REQUEST['status'];
$urlquery['initial'] = $initial;
$urlquery['trackerId'] = $_REQUEST["trackerId"];
$urlquery['sort_mode'] = $sort_mode;
$urlquery['exactvalue'] = $exactvalue;
$urlquery['filterfield'] = $filterfield;
if (is_array($filtervalue)) {
	foreach ($filtervalue as $fil) {
		$urlquery["filtervalue[" . $filterfield . "][]"] = $fil;
	}
} else {
	$urlquery["filtervalue[" . $filterfield . "]"] = $filtervalue;
}
$smarty->assign_by_ref('urlquery', $urlquery);
if ($tracker_info['useComments'] == 'y' && ($tracker_info['showComments'] == 'y' || isset($tracker_info['showLastComment']) && $tracker_info['showLastComment'] == 'y')) {
	foreach ($items['data'] as $itkey => $oneitem) {
		if ($tracker_info['showComments'] == 'y') {
			$items['data'][$itkey]['comments'] = $trklib->get_item_nb_comments($items['data'][$itkey]['itemId']);
		}
		if (isset($tracker_info['showLastComment']) && $tracker_info['showLastComment'] == 'y') {
			$l = $trklib->list_last_comments($items['data'][$itkey]['trackerId'], $items['data'][$itkey]['itemId'], 0, 1);
			$items['data'][$itkey]['lastComment'] = !empty($l['cant']) ? $l['data'][0] : '';
		}
	}
}
if ($tracker_info['useAttachments'] == 'y' && $tracker_info['showAttachments'] == 'y') {
	foreach ($items["data"] as $itkey => $oneitem) {
		$res = $trklib->get_item_nb_attachments($items["data"][$itkey]['itemId']);
		$items["data"][$itkey]['attachments'] = $res['attachments'];
		$items["data"][$itkey]['hits'] = $res['hits'];
	}
}
foreach ($fields['data'] as $fd) {	// add field info for searchable fields not shown in the list
	$fid = $fd["fieldId"];
	if ($fd['isSearchable'] == 'y' and !isset($listfields[$fid]) and $itemObject->canViewField($fid)) {
		$listfields[$fid] = $fd;
	}
}

$smarty->assign('trackerId', $_REQUEST["trackerId"]);
$smarty->assign('tracker_info', $tracker_info);
$smarty->assign('fields', $fields['data']);
$smarty->assign('ins_fields', $ins_fields['data']);
$smarty->assign_by_ref('items', $items["data"]);
$smarty->assign_by_ref('item_count', $items['cant']);
$smarty->assign_by_ref('listfields', $listfields);
$users = $userlib->list_all_users();
$smarty->assign_by_ref('users', $users);
if ($tiki_p_export_tracker == 'y') {
	$trackers = $trklib->list_trackers();
	$smarty->assign_by_ref('trackers', $trackers['data']);
	include_once ('lib/wiki-plugins/wikiplugin_trackerfilter.php');
	$formats = '';
	$filters = wikiplugin_trackerFilter_get_filters($_REQUEST['trackerId'], array(), $formats);
	$smarty->assign_by_ref('filters', $filters);
	if (!empty($_REQUEST['displayedFields'])) {
		if (is_string($_REQUEST['displayedFields'])) {
			$smarty->assign('displayedFields', preg_split('/[:,]/', $_REQUEST['displayedFields']));
		} else {
			$smarty->assign_by_ref('displayedFields', $_REQUEST['displayedFields']);
		}
	}
	$smarty->assign('recordsMax', $items['cant']);
	$smarty->assign('recordsOffset', 1);
}
include_once ('tiki-section_options.php');
$smarty->assign('uses_tabs', 'y');
$smarty->assign('show_filters', 'n');
if (count($fields['data']) > 0) {
	foreach ($fields['data'] as $it) {
		if ($it['isSearchable'] == 'y') {
			$smarty->assign('show_filters', 'y');
			break;
		}
	}
}
if (isset($tracker_info['useRatings']) && $tracker_info['useRatings'] == 'y' && $items['data']) {
	foreach ($items['data'] as $f => $v) {
		$items['data'][$f]['my_rate'] = $tikilib->get_user_vote("tracker." . $_REQUEST["trackerId"] . '.' . $items['data'][$f]['itemId'], $user);
	}
}
setcookie('tab', $cookietab);
$smarty->assign('cookietab', $cookietab);
ask_ticket('view-trackers');

// Generate validation js
if ($prefs['feature_jquery'] == 'y' && $prefs['feature_jquery_validation'] == 'y') {
	$validatorslib = TikiLib::lib('validators');
	$validationjs = $validatorslib->generateTrackerValidateJS($fields['data']);
	$smarty->assign('validationjs', $validationjs);
}
//Use 12- or 24-hour clock for $publishDate time selector based on admin and user preferences
$userprefslib = TikiLib::lib('userprefs');
$smarty->assign('use_24hr_clock', $userprefslib->get_user_clock_pref($user));

// Display the template
$smarty->assign('mid', 'tiki-view_tracker.tpl');
$smarty->display("tiki.tpl");
