<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-list_posts.php 57957 2016-03-17 19:58:54Z jonnybradley $

require_once ('tiki-setup.php');
$bloglib = TikiLib::lib('blog');
$access->check_feature('feature_blogs');
$access->check_permission('tiki_p_blog_admin');

if (isset($_REQUEST["remove"])) {
	$access->check_authenticity();
	$bloglib->remove_post($_REQUEST["remove"]);
}
if (isset($_REQUEST['checked'])) {
	check_ticket('list_posts');
	$checked = is_array($_REQUEST['checked']) ? $_REQUEST['checked'] : array($_REQUEST['checked']);
	// Delete post(s)
	if (isset($_REQUEST['remove']) || isset($_REQUEST['remove_x'])) {
		$access->check_authenticity(tra('Delete posts'));
		foreach ($checked as $id) {
			$bloglib->remove_post($id);
		}
	}
}

if (!isset($_REQUEST["sort_mode"])) {
	$sort_mode = 'created_desc';
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);

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
if (isset($_REQUEST['blogId'])) {
	$blogId = $_REQUEST['blogId'];
	$blog = $bloglib->get_blog($blogId);
	$smarty->assign('blogTitle', $blog['title']);
	$smarty->assign('blogId', $blogId);
} else {
	$blogId = -1;
}

$posts = $bloglib->list_posts($offset, $maxRecords, $sort_mode, $find, $blogId);
$smarty->assign_by_ref('cant', $posts["cant"]);
$smarty->assign_by_ref('posts', $posts["data"]);

ask_ticket('list-posts');
// Display the template
$smarty->assign('mid', 'tiki-list_posts.tpl');
$smarty->display("tiki.tpl");
