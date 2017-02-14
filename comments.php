<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: comments.php 58788 2016-06-05 15:05:06Z lindonb $

// $start_time = microtime(true);

// This file sets up the information needed to display
// the comments preferences, post-comment box and the
// list of comments. Finally it displays blog-comments.tpl
// using this information

// Setup URLS for the Comments next and prev buttons and use variables that
// cannot be aliased by normal tiki variables.
// Traverse each _REQUEST data adn put them in an array

//this script may only be included - so its better to err & die if called directly.
//smarty is not there - we need setup
require_once('tiki-setup.php');
$access = TikiLib::lib('access');
$tikilib = TikiLib::lib('tiki');
$headerlib = TikiLib::lib('header');

$access->check_script($_SERVER["SCRIPT_NAME"], basename(__FILE__));

/*
 * Determine the settings used to display the thread
 */

// user requests that could be used to change thread display settings
$handled_requests = array('comments_per_page', 'thread_style', 'thread_sort_mode');

// Then determine the final value for thread display settings
// If we are in a forum thread

if ( $prefs['forum_thread_user_settings'] == 'y' && $prefs['forum_thread_user_settings_keep'] == 'y' ) {
	// If 'forum_thread_user_settings' is enabled (allow user to change thread display settings)
	// and if the 'forum_thread_user_settings_keep' is enabled (keep user settings for all forums during his session)
	// ... we check session vars
	//  !! Session var is not used when there is an explicit user request !!

	foreach ($handled_requests as $request_name) {
		if ( isset($_SESSION['forums_'.$request_name]) && ! isset($_REQUEST[$request_name]) ) {
			$$request_name = $_SESSION['forums_'.$request_name];
		}
	}
}
foreach ($handled_requests as $request_name) {
	if ( empty($$request_name) && empty($_REQUEST[$request_name]) ) {
		$$request_name = $prefs['forum_'.$request_name];
	} elseif ( empty($$request_name) && !empty($_REQUEST[$request_name]) ) {
		$$request_name = $_REQUEST[$request_name];
	}
}

if ( $forum_info['is_flat'] == 'y' ) {
	// If we have a flat forum (i.e. we reply only to the first message / thread)
	// ... we then override $thread_style and force a 'plain' style
	$thread_style = 'commentStyle_plain';
}

// Assign final values to smarty vars in order
foreach ($handled_requests as $request_name) {
	$smarty->assign($request_name, $$request_name);
}

if ( ! isset($_REQUEST["comment_rating"]) ) {
	$_REQUEST["comment_rating"] = '';
}
$comments_aux = array();

// show/hide comments zone on request and store the status in a cookie (also works with javascript off)
if (isset($_REQUEST['comzone'])) {
	$comments_show = 'n';
	$comzone_state = $_REQUEST['comzone'];
	if ($comzone_state=='show'||$comzone_state=='o') {
		$comments_show = 'y';
		if (!isset($_COOKIE['comzone'])||$_COOKIE['comzone']=='c') {
			setcookie('comzone', 'o');
		}
	}
	if ($comzone_state=='hide'||$comzone_state=='c') {
		if (!isset($_COOKIE['comzone'])||$_COOKIE['comzone']=='o') {
			setcookie('comzone', 'c');
		}
	}
} else {
	$comments_show = 'n';
}
$comments_t_query = '';
$comments_first = 1;

foreach ($comments_vars as $c_name) {
	$comments_avar["name"] = $c_name;

	if (isset($_REQUEST[$c_name])) {
		$comments_avar["value"] = $_REQUEST[$c_name];
		$comments_aux[] = $comments_avar;
	}

	if (isset($_REQUEST[$c_name])) {
		if ($comments_first) {
			$comments_first = 0;
			$comments_t_query .= "?$c_name=" . urlencode($_REQUEST["$c_name"]);
		} else {
			$comments_t_query .= "&amp;$c_name=" . urlencode($_REQUEST["$c_name"]);
		}
	}
}

$smarty->assign('comments_request_data', $comments_aux);

if (!isset($_REQUEST['comments_threshold'])) {
	$_REQUEST['comments_threshold'] = 0;
} else {
	$smarty->assign('comments_threshold_param', '&amp;comments_threshold='.$_REQUEST['comments_threshold']);
}

$smarty->assign('comments_threshold', $_REQUEST['comments_threshold']);
// This sets up comments father as the father
$comments_parsed = parse_url($tikilib->httpPrefix().$_SERVER["REQUEST_URI"]);

if (!isset($comments_parsed["query"])) {
	$comments_parsed["query"] = '';
}

TikiLib::parse_str($comments_parsed["query"], $comments_query);
$comments_father = $comments_parsed["path"];

$comments_complete_father = $comments_father . $comments_t_query;

if (strstr($comments_complete_father, "?")) {
	$comments_complete_father .= '&amp;';
} else {
	$comments_complete_father .= '?';
}

$smarty->assign('comments_father', $comments_father);
$smarty->assign('comments_complete_father', $comments_complete_father);

if (!isset($_REQUEST["comments_threadId"])) {
	$_REQUEST["comments_threadId"] = 0;
}
$smarty->assign("comments_threadId", $_REQUEST["comments_threadId"]);

// The same for replies to comments threads

if (!isset($_REQUEST["comments_reply_threadId"])) {
	$_REQUEST["comments_reply_threadId"] = 0;
}
$smarty->assign("comments_reply_threadId", $_REQUEST["comments_reply_threadId"]);

// Include the library for comments (if not included)
$commentslib = TikiLib::lib('comments');

if (!isset($comments_prefix_var)) {
	$comments_prefix_var = '';
}

if ( ! isset( $comments_objectId ) ) {
	if (!isset($_REQUEST[$comments_object_var])) {
		die ("The comments_object_var variable cannot be found as a REQUEST variable");
	}
	$comments_objectId = $comments_prefix_var . $_REQUEST["$comments_object_var"];
}

$feedbacks = array();
$errors = array();
if ( isset($_REQUEST['comments_objectId']) && $_REQUEST['comments_objectId'] == $comments_objectId
	&& (isset($_REQUEST['comments_postComment']) || isset($_REQUEST['comments_postComment_anonymous']) )) {
	$forum_info = $commentslib->get_forum($_REQUEST['forumId']);
	$threadId = $commentslib->post_in_forum($forum_info, $_REQUEST, $feedbacks, $errors);
	if (!empty($threadId) && empty($errors)) {
		$url = "tiki-view_forum_thread.php?forumId=" . $_REQUEST['forumId'] . "&comments_parentId=" . $_REQUEST['comments_parentId'] . "&threadId=" . $threadId;
		if (!empty($_REQUEST['comments_threshold'])) {
			$url .= "&comments_threshold=".$_REQUEST['comments_threshold'];
		}
		if (!empty($_REQUEST['comments_offset'])) {
			$url .= "&comments_offset=".$_REQUEST['comments_offset'];
		}
		if (!empty($_REQUEST['comments_per_page'])) {
			$url .= "&comments_per_page=".$_REQUEST['comments_per_page'];
		}
		if (!empty($_REQUEST['thread_style'])) {
			$url .= "&thread_style=".$_REQUEST['thread_style'];
		}
		if (!empty($_REQUEST['thread_sort_mode'])) {
			$url .= "&thread_sort_mode=".$_REQUEST['thread_sort_mode'];
		}
		$url .= "#threadId=".$threadId; //place anchor on newly created message

		//Watches
		if ( $prefs['feature_user_watches'] == 'y') {
			if ( isset($_REQUEST['watch']) && $_REQUEST['watch'] == 'y') {
				$tikilib->add_user_watch($user, 'forum_post_thread', $_REQUEST['comments_parentId'], 'forum topic', $forum_info['name'] . ':' . $thread_info['title'], "tiki-view_forum_thread.php?forumId=" . $_REQUEST['forumId'] . "&amp;comments_parentId=" . $_REQUEST['comments_parentId']);
			} else {
				$tikilib->remove_user_watch($user, 'forum_post_thread', $_REQUEST['comments_parentId'], 'forum topic');
			}
		}

		header('location: ' . $url);
		die;
	}
	if (!empty($errors)) {
		Feedback::warning(['mes' => $errors]);
	}
	if (!empty($feedbacks)) {
		Feedback::note(['mes' => $feedbacks]);
	}
	if ( isset( $pageCache ) ) {
		$pageCache->invalidate();
	}
}

global $tiki_p_admin_comments;

if ($tiki_p_admin_forum == 'y') {
	if (isset($_REQUEST["comments_remove"]) && isset($_REQUEST["comments_threadId"])) {
		$access->check_authenticity(tra('Are you sure you want to remove that post?'));
		$comments_show = 'y';
		$commentslib->remove_comment($_REQUEST["comments_threadId"]);
		$_REQUEST["comments_threadId"] = 0;
		$smarty->assign('comments_threadId', 0);
	}
}

if ($_REQUEST["comments_threadId"] > 0) {
	$comment_info = $commentslib->get_comment($_REQUEST["comments_threadId"]);

	$smarty->assign('comment_title', $comment_info["title"]);
	$smarty->assign('comment_rating', $comment_info["comment_rating"]);
	$smarty->assign('comment_data', $comment_info["data"]);
} elseif ($_REQUEST["comments_reply_threadId"] > 0) {
	// Replies to comments.

	$comment_info = $commentslib->get_comment($_REQUEST["comments_reply_threadId"]);

	global $prefs;

	if ( $prefs['feature_forum_allow_flat_forum_quotes'] != 'y' && $comment_info['parentId'] > 0 && $forum_info['is_flat'] == 'y' ) {
		$smarty->assign('msg', tra("This forum is flat and doesn't allow replies to other replies"));
		$smarty->display("error.tpl");
		die;
	}

	if ( $comment_info["data"] != ''  ) {
		if ( ($prefs['feature_forum_parse'] == 'y' || $prefs['section_comments_parse'] == 'y') && $prefs['feature_use_quoteplugin'] == 'y' ) {
			if ($prefs['forum_quote_prevent_nesting'] == 'y') {
				$comment_info["data"] = trim(preg_replace("/{QUOTE\(.*?\)}(.|\n)*?{QUOTE}/", "", $comment_info["data"])); //strip quotes to prevent nesting
			}
			$comment_info["data"] = "\n{QUOTE(thread_id=>" . $_REQUEST["comments_reply_threadId"] . ")}" . $comment_info["data"] . '{QUOTE}';
		} else {
			$comment_info["data"] = preg_replace('/\n/', "\n> ", $comment_info["data"]);
			$comment_info["data"] = "\n> " . $comment_info["data"];
		}
	}
	$smarty->assign('comment_data', $comment_info["data"]);

	if ( ! array_key_exists("title", $comment_info) ) {
		if ( array_key_exists("comments_title", $_REQUEST) ) {
			$comment_info["title"] = $_REQUEST["comments_title"];
		} else {
			$comment_info["title"] = "";
			$_REQUEST["comments_title"] = "";
		}
	}

	if ( $prefs['forum_reply_forcetitle'] == 'y' ) {
		$comment_title = '';
	} elseif ( $prefs['forum_comments_no_title_prefix'] != 'y' ) {
		$comment_title = tra('Re:').' '.$comment_info["title"];
	} else {
		$comment_title = $comment_info["title"];
	}
	$smarty->assign('comment_title', $comment_title);
	$smarty->assign('comments_reply_threadId', $_REQUEST["comments_reply_threadId"]);
} else {
	$smarty->assign('comment_title', '');
	$smarty->assign('comment_rating', '');
	$smarty->assign('comment_data', '');
}

$smarty->assign('comment_preview', 'n');

if (isset($_REQUEST["comments_previewComment"]) || isset($_REQUEST["comments_postComment"])) {
	$comment_preview = array();

	$comment_preview['title'] = $_REQUEST["comments_title"];
	$comment_preview['userName'] = $user;

	$comment_preview['parsed'] = $commentslib->parse_comment_data(strip_tags($_REQUEST["comments_data"]));
	$comment_preview['rating'] = $_REQUEST["comment_rating"];
	$comment_preview['commentDate'] = $tikilib->now;

	if (isset($_REQUEST["anonymous_name"])) {
		$comment_preview['anonymous_name'] = $_REQUEST["anonymous_name"];
	}
	if (isset($_REQUEST["anonymous_email"])) {
		$comment_preview['email'] = $_REQUEST["anonymous_email"];
	}
	if (isset($_REQUEST["anonymous_website"])) {
		$comment_preview['website'] = $_REQUEST["anonymous_website"];
	}

	$smarty->assign('comment_preview_data', $comment_preview);

	if (isset($_REQUEST["comments_previewComment"])) {
		$smarty->assign('comment_preview', 'y');
	}
	$smarty->assign('comment_data', $_REQUEST["comments_data"]);
	$smarty->assign('comment_title', $comment_preview["title"]);
	$comments_show = 'y';
}

// Always show comments when a display setting has been explicitely specified
if ( isset($_REQUEST['comments_per_page']) || isset($_REQUEST['thread_style']) || isset($_REQUEST['thread_sort_mode']) ) {
	$comments_show = 'y';
}

if (!isset($_REQUEST["comments_commentFind"])) {
	$_REQUEST["comments_commentFind"] = '';
} else {
	$comments_show = 'y';
}

// Offset setting for the list of comments
if (!isset($_REQUEST["comments_offset"])) {
	$comments_offset = 0;
} else {
	$comments_offset = $_REQUEST["comments_offset"];
}

$smarty->assign('comments_offset', $comments_offset);

// Now check if we are displaying top-level comments or a specific comment
if (!isset($_REQUEST["comments_parentId"])) {
	$_REQUEST["comments_parentId"] = 0;
}

$smarty->assign('comments_parentId', $_REQUEST["comments_parentId"]);

$forum_check = explode(':', $comments_objectId);
if ($forum_check[0] == 'forum' ) {
	$smarty->assign('forumId', $forum_check[1]);
}

$smarty->assign(
	'comments_grandParentId',
	isset($_REQUEST['comments_grandParentId'])
	? $_REQUEST['comments_grandParentId'] : ''
);

if (isset($_REQUEST["post_reply"]) && isset($_REQUEST["comments_reply_threadId"])) {
	$threadId_if_reply = $_REQUEST["comments_reply_threadId"];
} else {
	$threadId_if_reply = 0;
}

if (empty($thread_sort_mode)) {
	if ( !empty($_REQUEST['thread_sort_mode'])) {
		$thread_sort_mode = $_REQUEST['thread_sort_mode'];
	} else {
		$thread_sort_mode = 'commentDate_asc';
	}
}

$comments_coms = $commentslib->get_comments(
	$comments_objectId,
	$_REQUEST["comments_parentId"],
	$comments_offset, $comments_per_page, $thread_sort_mode, $_REQUEST["comments_commentFind"],
	$_REQUEST['comments_threshold'], $thread_style, $threadId_if_reply
);

if ($comments_prefix_var == 'forum:') {
	$comments_cant = $commentslib->count_comments('topic:'. $_REQUEST['comments_parentId']); // comments in the topic not in the forum
} else {
	$comments_cant = $commentslib->count_comments($comments_objectId);
}
$comments_cant_page = $comments_coms['cant'];

$smarty->assign('comments_below', $comments_coms["below"]);
$smarty->assign('comments_cant', $comments_cant);

// Offset management
$comments_maxRecords = $comments_per_page;

if ( $comments_maxRecords != 0 ) {
	$comments_cant_pages = ceil($comments_cant_page / $comments_maxRecords);
	$smarty->assign('comments_actual_page', 1 + ($comments_offset / $comments_maxRecords));
} else {
	$comments_cant_pages = 1;
	$smarty->assign('comments_actual_page', 1);
}
$smarty->assign('comments_cant_pages', $comments_cant_pages);

if ($comments_cant_page > ($comments_offset + $comments_maxRecords)) {
	$smarty->assign('comments_next_offset', $comments_offset + $comments_maxRecords);
} else {
	$smarty->assign('comments_next_offset', -1);
}

// If offset is > 0 then prev_offset
if ($comments_offset > 0) {
	$smarty->assign('comments_prev_offset', $comments_offset - $comments_maxRecords);
} else {
	$smarty->assign('comments_prev_offset', -1);
}

$smarty->assign('comments_coms', $comments_coms["data"]);

// Grab the parent comment to show.  -rlpowell
$parent_com = '';
if (isset($_REQUEST["comments_parentId"])
		&& $_REQUEST["comments_parentId"] > 0
		&& $tiki_p_forum_post == 'y'
		&& (isset($_REQUEST['comments_previewComment']) || isset($_REQUEST['post_reply']))) {
	$parent_com = $commentslib->get_comment($_REQUEST['comments_parentId']);
}
$smarty->assign_by_ref('parent_com', $parent_com);

// Get comments / forum lock status
$thread_is_locked = ( ! empty($comments_objectId) && $commentslib->is_object_locked($comments_objectId) ) ? 'y' : 'n';
$forum_is_locked = $thread_is_locked;
$thread_is_locked = $comment_info['locked'];
$smarty->assign('forum_is_locked', $forum_is_locked);
$smarty->assign('thread_is_locked', $thread_is_locked);

$smarty->assign('post_reply', !empty($_REQUEST['post_reply']) ? $_REQUEST['post_reply'] : '');
$smarty->assign('edit_reply', !empty($_REQUEST['edit_reply']) ? $_REQUEST['edit_reply'] : '');

if ($prefs['feature_contribution'] == 'y') {
	$contributionItemId = $_REQUEST["comments_threadId"];
	include_once('contribution.php');
}
// see if comments are allowed on this specific wiki page
global $section;
if ($section == 'wiki page') {
	if ($prefs['wiki_comments_allow_per_page'] != 'n') {
		global $info;
		if (!empty($info['comments_enabled'])) {
			$smarty->assign('comments_allowed_on_page', $info['comments_enabled']);
		} else {
			if ($prefs['wiki_comments_allow_per_page'] == 'y') {
				$smarty->assign('comments_allowed_on_page', 'y');
			} else {
				$smarty->assign('comments_allowed_on_page', 'n');
			}
		}
	} else {
		$smarty->assign('comments_allowed_on_page', 'y');
	}
}

$headerlib->add_jsfile('lib/comments/commentslib.js');
$smarty->assign('comments_objectId', $comments_objectId);
$smarty->assign('comments_show', $comments_show);
