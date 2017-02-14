<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-view_articles.php 57956 2016-03-17 19:58:12Z jonnybradley $

$section = 'cms';
//get_strings tra('Articles Home');
require_once ('tiki-setup.php');
$artlib = TikiLib::lib('art');

if ($prefs['article_use_new_list_articles'] == 'y') {
	include "lists/articles.php";
	die;
}

if ($prefs['feature_freetags'] == 'y') {
	$freetaglib = TikiLib::lib('freetag');
}
if ($prefs['feature_categories'] == 'y') {
	$categlib = TikiLib::lib('categ');
}

$access->check_feature('feature_articles');

if (isset($_REQUEST["remove"])) {
	$access->check_permission('tiki_p_remove_article');
	$access->check_authenticity();
	$artlib->remove_article($_REQUEST["remove"]);
}
// This script can receive the threshold
// for the information as the number of
// days to get in the log 1,3,4,etc
// it will default to 1 recovering information for today
if (empty($_REQUEST["sort_mode"])) {
	$sort_mode = $prefs['art_sort_mode'];
} else {
	$sort_mode = $_REQUEST["sort_mode"];
}
$smarty->assign_by_ref('sort_mode', $sort_mode);
// If offset is set use it if not then use offset =0
// use the maxRecords php variable to set the limit
// if sortMode is not set then use lastModif_desc
if (!isset($_REQUEST["offset"])) {
	$offset = 0;
} else {
	$offset = $_REQUEST["offset"];
}
$smarty->assign_by_ref('offset', $offset);
if (isset($_REQUEST['date_min']) || isset($_REQUEST['date_max'])) {
	$date_min = isset($_REQUEST['date_min']) ? $_REQUEST['date_min'] : 0;
	$date_max = isset($_REQUEST['date_max']) ? $_REQUEST['date_max'] : $tikilib->now;
} elseif (isset($_SESSION["thedate"])) {
	$date_min = 0;
	if ($_SESSION["thedate"] < $tikilib->now) {
		$date_max = $_SESSION["thedate"];
	} else {
		if ($tiki_p_admin == 'y' || $tiki_p_admin_cms == 'y') {
			$date_max = $_SESSION["thedate"];
		} else {
			$date_max = $tikilib->now;
		}
	}
} else {
	$date_min = 0;
	$date_max = $tikilib->now;
}
//Keep track of month of last viewed article for article months_links module foldable display
$_SESSION['cms_last_viewed_month'] = TikiLib::date_format("%Y-%m", $date_max);
$min_rating = isset($_REQUEST['min_rating']) ? $_REQUEST['min_rating'] : '';
$max_rating = isset($_REQUEST['max_rating']) ? $_REQUEST['max_rating'] : '';
if (isset($_REQUEST["find"])) {
	$find = $_REQUEST["find"];
} else {
	$find = '';
}
$smarty->assign_by_ref('find', $find);
if (isset($_REQUEST["type"])) {
	$type = $_REQUEST["type"];
} else {
	$type = '';
}
if (isset($_REQUEST["topic"])) {
	$topic = $_REQUEST["topic"];
} else {
	$topic = '';
}
if (isset($_REQUEST['topicName'])) {
	$topicName = $_REQUEST['topicName'];
} else {
	$topicName = '';
}
if (isset($_REQUEST["categId"])) {
	$categId = $_REQUEST["categId"];
} else {
	$categId = '';
}
$smarty->assign_by_ref('categId', $categId);
if (!isset($_REQUEST['lang'])) {
	$_REQUEST['lang'] = '';
}
// Get a list of last changes to the Wiki database
$listpages = $artlib->list_articles($offset, $prefs['maxArticles'], $sort_mode, $find, $date_min, $date_max, $user, $type, $topic, 'y', $topicName, $categId, '', '', $_REQUEST['lang'], $min_rating, $max_rating, false, 'y');
if ($prefs['feature_multilingual'] == 'y') {
	$multilinguallib = TikiLib::lib('multilingual');
	$listpages['data'] = $multilinguallib->selectLangList('article', $listpages['data']);
	foreach ($listpages['data'] as &$article) {
		$article['translations'] = $multilinguallib->getTranslations('article', $article['articleId'], $article["title"], $article['lang']);
	}
}
$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);
$temp_max = count($listpages["data"]);
for ($i = 0; $i < $temp_max; $i++) {
	$listpages["data"][$i]["parsed_heading"] = $tikilib->parse_data(
		$listpages["data"][$i]["heading"],
		array(
			'min_one_paragraph' => true,
			'is_html' => $artlib->is_html($listpages["data"][$i], true),
		)
	);
	$comments_prefix_var = 'article:';
	$comments_object_var = $listpages["data"][$i]["articleId"];
	$comments_objectId = $comments_prefix_var . $comments_object_var;
	$listpages["data"][$i]["comments_cant"] = TikiLib::lib('comments')->count_comments($comments_objectId);
	if ($prefs['feature_freetags'] == 'y') { // And get the Tags for the posts
		$listpages["data"][$i]["freetags"] = $freetaglib->get_tags_on_object($listpages["data"][$i]["articleId"], "article");
	}
}
if (!empty($topicName) && !strstr($topicName, '!') && !strstr($topicName, '+')) {
	$smarty->assign_by_ref('topic', $topicName);
} elseif (!empty($topic) && is_numeric($topic)) {
	if (!empty($listpages['data'][0]['topicName'])) $smarty->assign_by_ref('topic', $listpages['data'][0]['topicName']);
	else {
		$topic_info = $artlib->get_topic($topic);
		if (isset($topic_info['name'])) $smarty->assign_by_ref('topic', $topic_info['name']);
	}
}
if (!empty($type) && !strstr($type, '!') && !strstr($type, '+')) {
	$smarty->assign_by_ref('type', $type);
}
$smarty->assign('maxArticles', $prefs['maxArticles']);
// If there're more records then assign next_offset
$smarty->assign_by_ref('listpages', $listpages["data"]);
$smarty->assign_by_ref('cant', $listpages["cant"]);
if ($prefs['feature_user_watches'] == 'y') {
	if ($user && isset($_REQUEST['watch_action'])) {
		$access->check_authenticity();
		if ($_REQUEST['watch_action'] == 'add') {
			$tikilib->add_user_watch($user, 'article_*', '*');
		} else {
			$tikilib->remove_user_watch($user, 'article_*', '*', null);
		}
	}
	$smarty->assign('user_watching_articles', ($user && $tikilib->user_watches($user, 'article_*', '*')) ? 'y' : 'n');
}
$headerLinks = 'y';
$smarty->assign('headerLinks', $headerLinks);
include_once ('tiki-section_options.php');
ask_ticket('view_article');
// Display the template
$smarty->assign('mid', 'tiki-view_articles.tpl');
$smarty->display("tiki.tpl");
