<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-forum_queue.php 60315 2016-11-18 14:59:00Z nkoth $

$section = 'forums';
require_once ('tiki-setup.php');
$access->check_feature('feature_forums');

// forumId must be received
if (!isset($_REQUEST["forumId"])) {
	$smarty->assign('msg', tra("No forum indicated"));
	$smarty->display("error.tpl");
	die;
}

$smarty->assign('forumId', $_REQUEST["forumId"]);
$commentslib = TikiLib::lib('comments');

// Approval from email
if (isset($_REQUEST["ahash"]) && isset($_REQUEST["qId"])) {
	$msg_info = $commentslib->queue_get($_REQUEST['qId']);
	$ahash = md5($_REQUEST['qId'] . $msg_info['title'] . $msg_info['data'] . $msg_info['user']);
	if ($_REQUEST["ahash"] == $ahash) {
		$commentslib->approve_queued($_REQUEST['qId']);
		echo ("You have now approved the post by " . htmlspecialchars(TikiLib::lib('user')->clean_user($msg_info['user'])));
	} else {
		echo ("Error: Unable to approve queued post.");
	}
	die;
}

$forum_info = $commentslib->get_forum($_REQUEST["forumId"]);

//Check individual permissions for this forum
$smarty->assign('individual', 'n');

$tikilib->get_perm_object($_REQUEST["forumId"], 'forum');

// Now if the user is the moderator then give hime forum admin privs
if ($user) {
	if ($forum_info["moderator"] == $user) {
		$tiki_p_admin_forum = 'y';
		$smarty->assign('tiki_p_admin_forum', 'y');
	} elseif (in_array($forum_info['moderator_group'], $userlib->get_user_groups($user))) {
		$tiki_p_admin_forum = 'y';
		$smarty->assign('tiki_p_admin_forum', 'y');
	}
}

$access->check_permission('tiki_p_admin_forum');
$smarty->assign_by_ref('forum_info', $forum_info);
include_once ('tiki-section_options.php');

if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'forum';
	$cat_objid = $_REQUEST["forumId"];
	include ('tiki-tc.php');
}

if (isset($_REQUEST['qId'])) {
	$msg_info = $commentslib->queue_get($_REQUEST['qId']);
	$smarty->assign_by_ref('msg_info', $msg_info);
}

$smarty->assign('form', 'y');

if (isset($_REQUEST['remove_attachment'])) {
	check_ticket('forum-queue');
	$commentslib->remove_thread_attachment($_REQUEST['remove_attachment']);
}

if (isset($_REQUEST['qId'])) {
	if (isset($_REQUEST['save']) || isset($_REQUEST['saveapp'])) {
		check_ticket('forum-queue');
		$smarty->assign('form', 'n');

		if (!isset($_REQUEST['summary']))
			$_REQUEST['summary'] = $msg_info['summary'];

		if (!isset($_REQUEST['topic_smiley']))
			$_REQUEST['topic_smiley'] = $msg_info['topic_smiley'];

		if (!isset($_REQUEST['type']))
			$_REQUEST['type'] = $msg_info['type'];

		if (!isset($_REQUEST['topic_title']))
			$_REQUEST['topic_title'] = $msg_info['topic_title'];

		if (!isset($_REQUEST['in_reply_to']))
			$_REQUEST['in_reply_to'] = $msg_info['in_reply_to'];

		if (!isset($_REQUEST['parentId']))
			$_REQUEST['parentId'] = $msg_info['parentId'];

		if ($_REQUEST['parentId'] > 0) {
			$p_info = $commentslib->get_comment($_REQUEST['parentId']);

			$_REQUEST['topic_title'] = $p_info['title'];
		}

		$commentslib->replace_queue(
			$_REQUEST['qId'],
			$_REQUEST['forumId'],
			'forum:' . $_REQUEST['forumId'],
			$_REQUEST['parentId'],
			$msg_info['user'],
			$_REQUEST['title'],
			$_REQUEST['data'],
			$_REQUEST['type'],
			$_REQUEST['topic_smiley'],
			$_REQUEST['summary'],
			$_REQUEST['topic_title'],
			$_REQUEST['in_reply_to']
		);
		if ( isset($_REQUEST['saveapp']) )
			$commentslib->approve_queued($_REQUEST['qId']);
		unset ($_REQUEST['qId']);
	}

	if (isset($_REQUEST['remove'])) {
		$access->check_authenticity();
		$smarty->assign('form', 'n');
		$commentslib->remove_queued($_REQUEST['qId']);
	}

	if (isset($_REQUEST['topicize'])) {
	check_ticket('forum-queue');
		$smarty->assign('form', 'n');

		// Convert to a topic
		if (!isset($_REQUEST['summary']))
			$_REQUEST['summary'] = '';

		if (!isset($_REQUEST['type']))
			$_REQUEST['type'] = '';

		if (!isset($_REQUEST['topic_smiley']))
			$_REQUEST['topic_smiley'] = '';

		if (!isset($_REQUEST['topic_title']))
			$_REQUEST['topic_title'] = '';

		if (!isset($_REQUEST['in_reply_to']))
			$_REQUEST['in_reply_to'] = '';

		$_REQUEST['parentId'] = 0;
		$_REQUEST['type'] = 'n';
		$commentslib->replace_queue(
			$_REQUEST['qId'],
			$_REQUEST['forumId'],
			'forum:' . $_REQUEST['forumId'],
			$_REQUEST['parentId'],
			$msg_info['user'],
			$_REQUEST['title'],
			$_REQUEST['data'],
			$_REQUEST['type'],
			$_REQUEST['topic_smiley'],
			$_REQUEST['summary'],
			$_REQUEST['topic_title'],
			$_REQUEST['in_reply_to']
		);
		unset ($_REQUEST['qId']);
	}
}

if (isset($_REQUEST['rej']) && isset($_REQUEST['msg'])) {
	check_ticket('forum-queue');
	foreach (array_keys($_REQUEST['msg'])as $msg) {
		$commentslib->remove_queued($msg);
	}
}

if (isset($_REQUEST['app']) && isset($_REQUEST['msg'])) {
	check_ticket('forum-queue');
	foreach (array_keys($_REQUEST['msg'])as $msg) {
		$commentslib->approve_queued($msg);
	}
}

// Quickjumpt to other forums
if ($tiki_p_admin_forum == 'y' || $prefs['feature_forum_quickjump'] == 'y') {
	$all_forums = $commentslib->list_forums(0, -1, 'name_asc', '');
	Perms::bulk(array( 'type' => 'forum' ), 'object', $all_forums['data'], 'forumId');

	$temp_max = count($all_forums["data"]);
	for ($i = 0; $i < $temp_max; $i++) {
		$forumperms = Perms::get(array( 'type' => 'forum', 'object' => $all_forums['data'][$i]['forumId'] ));
		$all_forums["data"][$i]["can_read"] = $forumperms->forum_read ? 'y' : 'n';
	}

	$smarty->assign('all_forums', $all_forums['data']);
}

// Number of queued messages
if ($tiki_p_admin_forum == 'y') {
	$smarty->assign('queued', $commentslib->get_num_queued('forum' . $_REQUEST['forumId']));
}

// Items will contain messages
if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'timestamp_asc';
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
$items = $commentslib->list_forum_queue('forum:' . $_REQUEST['forumId'], $offset, $maxRecords, $sort_mode, $find);
$smarty->assign('cant', $items['cant']);
$smarty->assign_by_ref('cant_pages', $items['cant']);

$smarty->assign_by_ref('items', $items["data"]);

$topics = $commentslib->get_forum_topics($_REQUEST['forumId']);
$smarty->assign_by_ref('topics', $topics);
ask_ticket('forum-queue');

// Display the template
$smarty->assign('mid', 'tiki-forum_queue.tpl');
$smarty->display("tiki.tpl");
