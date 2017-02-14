<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-user_information.php 57956 2016-03-17 19:58:12Z jonnybradley $

require_once ('tiki-setup.php');
$messulib = TikiLib::lib('message');

if ($prefs['feature_unified_user_details'] == 'y'){
	include "tiki-user_unified_details.php";
	die;
}

$registrationlib = TikiLib::lib('registration');
$trklib = TikiLib::lib('trk');
if (isset($_REQUEST['userId'])) {
	$userwatch = $tikilib->get_user_login($_REQUEST['userId']);
	if ($userwatch === false) {
		$smarty->assign('errortype', 'no_redirect_login');
		$smarty->assign('msg', tra("Unknown user"));
		$smarty->display("error.tpl");
		die;
	}
} elseif (isset($_REQUEST['view_user'])) {
	$userwatch = $_REQUEST['view_user'];
	if (!$userlib->user_exists($userwatch)) {
		$smarty->assign('errortype', 'no_redirect_login');
		$smarty->assign('msg', tra("Unknown user"));
		$smarty->display("error.tpl");
		die;
	}
} else {
	$access->check_user($user);
	$userwatch = $user;
}

$smarty->assign('userwatch', $userwatch);
// Custom fields
$customfields = array();
$customfields = $registrationlib->get_customfields($userwatch);
$smarty->assign_by_ref('customfields', $customfields);
$smarty->assign('infoPublic', 'y');
if ($tiki_p_admin != 'y') {
	$user_information = $tikilib->get_user_preference($userwatch, 'user_information', 'public');
	// If the user is trying to pull info on themselves, allow it.
	if ($user_information == 'private' && $userwatch != $user) {
		$smarty->assign('infoPublic', 'n');
	}
}
if ($user) {
	$smarty->assign('sent', 0);
	if (isset($_REQUEST['send'])) {
		check_ticket('user-information');
		$smarty->assign('sent', 1);
		$message = '';
		if (empty($_REQUEST['subject']) && empty($_REQUEST['body'])) {
			$smarty->assign('message', tra('ERROR: Either the subject or body must be non-empty'));
			$smarty->display("tiki.tpl");
			die;
		}
		$sent = $messulib->post_message($userwatch, $user, $_REQUEST['to'], '', $_REQUEST['subject'], $_REQUEST['body'], $_REQUEST['priority'], '', isset($_REQUEST['replytome']) ? 'y' : '', isset($_REQUEST['bccme']) ? 'y' : '');
		if ($sent) {
			$message = tra('Message sent to') . ':' . $userlib->clean_user($userwatch) . '<br />';
		} else {
			$message = tra('An error occurred, please check your mail settings and try again');
		}
		$smarty->assign('message', $message);
	}
}
if (isset($user) and $user != $userwatch) {
	TikiLib::events()->trigger('tiki.user.view',
		array(
			'type' => 'user',
			'object' => $userwatch,
			'user' => $user,
		)
	);
}
$smarty->assign('priority', 3);
if ($prefs['allowmsg_is_optional'] == 'y') {
	$allowMsgs = $tikilib->get_user_preference($userwatch, 'allowMsgs', 'y');
} else {
	$allowMsgs = 'y';
}
$smarty->assign('allowMsgs', $allowMsgs);
$smarty->assign_by_ref('user_prefs', $user_preferences[$userwatch]);
$user_style = $tikilib->get_user_preference($userwatch, 'theme', $prefs['site_style']);
$smarty->assign_by_ref('user_style', $user_style);
$user_language = $tikilib->get_language($userwatch);
$langLib = TikiLib::lib('language');
$user_language_text = $langLib->format_language_list(array($user_language));
$smarty->assign_by_ref('user_language', $user_language_text[0]['name']);
$realName = $tikilib->get_user_preference($userwatch, 'realName', '');
$gender = $tikilib->get_user_preference($userwatch, 'gender', '');
$country = $tikilib->get_user_preference($userwatch, 'country', 'Other');
$smarty->assign('country', $country);
$anonpref = $tikilib->get_preference('userbreadCrumb', 4);
$userbreadCrumb = $tikilib->get_user_preference($userwatch, 'userbreadCrumb', $anonpref);
$smarty->assign_by_ref('realName', $realName);
$smarty->assign_by_ref('gender', $gender);
$smarty->assign_by_ref('userbreadCrumb', $userbreadCrumb);
$homePage = $tikilib->get_user_preference($userwatch, 'homePage', '');
$smarty->assign_by_ref('homePage', $homePage);
$avatar = $tikilib->get_user_avatar($userwatch);
$smarty->assign('avatar', $avatar);
$user_information = $tikilib->get_user_preference($userwatch, 'user_information', 'public');
$smarty->assign('user_information', $user_information);
$userinfo = $userlib->get_user_info($userwatch);
$email_isPublic = $tikilib->get_user_preference($userwatch, 'email is public', 'n');
if ($email_isPublic != 'n') {
	$smarty->assign('scrambledEmail', TikiMail::scrambleEmail($userinfo['email'], $email_isPublic));
}
$userinfo['score'] = TikiLib::lib('score')->get_user_score($userwatch);
$smarty->assign_by_ref('userinfo', $userinfo);
$smarty->assign_by_ref('email_isPublic', $email_isPublic);
$userPage = $prefs['feature_wiki_userpage_prefix'] . $userinfo['login'];
$exist = $tikilib->page_exists($userPage);
$smarty->assign("userPage_exists", $exist);
if ($prefs['feature_display_my_to_others'] == 'y') {
	if ($prefs['feature_wiki'] == 'y') {
		$wikilib = TikiLib::lib('wiki');
		$user_pages = $wikilib->get_user_all_pages($userwatch, 'pageName_asc');
		$smarty->assign_by_ref('user_pages', $user_pages);
	}
	if ($prefs['feature_blogs'] == 'y') {
		$bloglib = TikiLib::lib('blog');
		$user_blogs = $bloglib->list_user_blogs($userwatch, false);
		$smarty->assign_by_ref('user_blogs', $user_blogs);
		$user_blog_posts = $bloglib->list_posts(0, -1, 'created_desc', '', -1, $userwatch);
		$smarty->assign_by_ref('user_blog_posts', $user_blog_posts['data']);
	}
	if ($prefs['feature_galleries'] == 'y') {
		$user_galleries = $tikilib->get_user_galleries($userwatch, -1);
		$smarty->assign_by_ref('user_galleries', $user_galleries);
	}
	if ($prefs['feature_trackers'] == 'y') {
		$trklib = TikiLib::lib('trk');
		$user_items = $trklib->get_user_items($userwatch);
		$smarty->assign_by_ref('user_items', $user_items);
	}
	if ($prefs['feature_articles'] == 'y') {
		$artlib = TikiLib::lib('art');
		$user_articles = $artlib->get_user_articles($userwatch, -1);
		$smarty->assign_by_ref('user_articles', $user_articles);
	}
	if ($prefs['feature_forums'] == 'y') {
		$commentslib = TikiLib::lib('comments');
		$user_forum_comments = $commentslib->get_user_forum_comments($userwatch, -1);
		$smarty->assign_by_ref('user_forum_comments', $user_forum_comments);
		$user_forum_topics = $commentslib->get_user_forum_comments($userwatch, -1, 'topics');
		$smarty->assign_by_ref('user_forum_topics', $user_forum_topics);
	}
	if ($prefs['user_who_viewed_my_stuff'] == 'y') {
		$mystuff = array();
		if (isset($user_pages)) {
			$stuffType = 'wiki page';
			foreach ($user_pages as $obj) {
				$mystuff[] = array( 'object' => $obj["pageName"], 'objectType' => $stuffType, 'comment' => '' );
			}
		}
		if (isset($user_blogs)) {
			$stuffType = 'blog';
			foreach ($user_blogs as $obj) {
				$mystuff[] = array( 'object' => $obj["blogId"], 'objectType' => $stuffType, 'comment' => '' );
			}
		}
		if (isset($user_articles)) {
			$stuffType = 'article';
			foreach ($user_articles as $obj) {
				$mystuff[] = array( 'object' => $obj["articleId"], 'objectType' => $stuffType, 'comment' => '' );
			}
		}
		if (isset($user_forum_topics)) {
			$stuffType = 'forum';
			foreach ($user_forum_topics as $obj) {
				$forum_comment = 'comments_parentId=' . $obj["threadId"];
				$mystuff[] = array( 'object' => $obj["object"], 'objectType' => $stuffType, 'comment' => $forum_comment );
			}
		}
		$logslib = TikiLib::lib('logs');
		$whoviewed = $logslib->get_who_viewed($mystuff, false);
		$smarty->assign('whoviewed', $whoviewed);
	}
}
if ($prefs['user_tracker_infos']) {
	// arg passed 11,56,58,68=trackerId,fieldId...
	$trackerinfo = explode(',', $prefs['user_tracker_infos']);
	$userTrackerId = array_shift($trackerinfo);
	if (!empty($trackerinfo)) {
		$filter = array('fieldId' => $trackerinfo);
	} else {
		$filter = array();
	}
	$fields = $trklib->list_tracker_fields($userTrackerId, 0, -1, 'position_asc', '', true, $filter);
	foreach ($fields['data'] as $field) {
		$lll[$field['fieldId']] = $field;
	}
	$definition = Tracker_Definition::get($userTrackerId);
	if ($definition) {
		$items = $trklib->list_items($userTrackerId, 0, 1, '', $lll, $definition->getUserField(), '', '', '', $userwatch);
		$smarty->assign_by_ref('userItem', $items['data'][0]);
	} else {
		$smarty->assign('userItem', array());
	}
}
ask_ticket('user-information');
// Get full user picture if it is set
if ($prefs["user_store_file_gallery_picture"] == 'y') {
	$userprefslib = TikiLib::lib('userprefs');
	if ($user_picture_id = $userprefslib->get_user_picture_id($userwatch)) {	
		$smarty->assign('user_picture_id', $user_picture_id);
	}	
}
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('mid', 'tiki-user_information.tpl');
$smarty->display("tiki.tpl");
