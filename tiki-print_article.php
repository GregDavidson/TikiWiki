<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-print_article.php 57956 2016-03-17 19:58:12Z jonnybradley $

$section = 'cms';
require_once ('tiki-setup.php');
$artlib = TikiLib::lib('art');
$access->check_feature('feature_cms_print');
if (!isset($_REQUEST["articleId"])) {
	$smarty->assign('msg', tra("No article indicated"));
	$smarty->display("error.tpl");
	die;
}

$parserlib = TikiLib::lib('parser');

$article_data = $artlib->get_article($_REQUEST["articleId"]);
$tikilib->get_perm_object($_REQUEST['articleId'], 'article');
if ($article_data === false) {
	if (!$user) $_SESSION['loginfrom'] = $_SERVER['REQUEST_URI'];
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra('Permission denied'));
	$smarty->display('error.tpl');
	die;
}
if (!$article_data) {
	$smarty->assign('msg', tra("Article not found"));
	$smarty->display("error.tpl");
	die;
}
if (($article_data['publishDate'] > $tikilib->now) && ($article_data['author'] != $user && $tiki_p_admin != 'y' && $tiki_p_admin_cms != 'y') && ($article_data['type'] != 'Event')) {
	$smarty->assign('msg', tra("Article is not published yet"));
	$smarty->display("error.tpl");
	die;
}

if ($article_data['ispublished'] == 'n' && $tiki_p_edit_article != 'y') {
	$smarty->assign('msg', tra("Article is not published yet"));
	$smarty->display("error.tpl");
	die;
}

if (isset($_REQUEST['switchlang']) && $_REQUEST['switchlang'] == 'y' && $prefs['feature_multilingual'] == 'y' && $prefs['feature_sync_language'] == 'y' && !empty($article_data["lang"]) && $prefs['language'] != $article_data["lang"]) {
	header('Location: tiki-switch_lang.php?language=' . $article_data['lang']);
	die;
}

$statslib = TikiLib::lib('stats');
if ($prefs['feature_categories'] == 'y') {
	$categlib = TikiLib::lib('categ');
}

$artlib->add_article_hit($_REQUEST["articleId"]);
$smarty->assign('articleId', $_REQUEST["articleId"]);
$smarty->assign('arttitle', $article_data["title"]);
$smarty->assign('topline', $article_data["topline"]);
$smarty->assign('show_topline', $article_data["show_topline"]);
$smarty->assign('subtitle', $article_data["subtitle"]);
$smarty->assign('show_subtitle', $article_data["show_subtitle"]);
$smarty->assign('linkto', $article_data["linkto"]);
$smarty->assign('show_linkto', $article_data["show_linkto"]);
$smarty->assign('image_caption', $article_data["image_caption"]);
$smarty->assign('show_image_caption', $article_data["show_image_caption"]);
$smarty->assign('lang', $article_data["lang"]);
$smarty->assign('authorName', $article_data["authorName"]);
$smarty->assign('author', $article_data["author"]);
$smarty->assign('show_author', $article_data["show_author"]);
$smarty->assign('topicId', $article_data["topicId"]);
$smarty->assign('type', $article_data["type"]);
$smarty->assign('rating', $article_data["rating"]);
$smarty->assign('entrating', $article_data["entrating"]);
$smarty->assign('useImage', $article_data["useImage"]);
$smarty->assign('isfloat', $article_data["isfloat"]);
$smarty->assign('image_name', $article_data["image_name"]);
$smarty->assign('image_type', $article_data["image_type"]);
$smarty->assign('image_size', $article_data["image_size"]);
$smarty->assign('image_x', $article_data["image_x"]);
$smarty->assign('list_image_x', $article_data["list_image_x"]);
$smarty->assign('image_y', $article_data["image_y"]);
$smarty->assign('image_data', urlencode($article_data["image_data"]));
$smarty->assign('reads', $article_data["nbreads"]);
$smarty->assign('show_reads', $article_data["show_reads"]);
$smarty->assign('size', $article_data["size"]);
$smarty->assign('show_size', $article_data["show_size"]);
$smarty->assign('use_ratings', $article_data["use_ratings"]);
$smarty->assign('comment_can_rate_article', $article_data["comment_can_rate_article"]);
$smarty->assign('ispublished', $article_data["ispublished"]);
if (strlen($article_data["image_data"]) > 0) {
	$smarty->assign('hasImage', 'y');
} else {
	$smarty->assign('hasImage', 'n');
}
if ($article_data['image_x'] > 0) {
	$smarty->assign('width', $article_data['image_x']);
} else {
	require_once('lib/images/images.php');
	$img = new Image($article_data['image_x'], false);
	$smarty->assign('width', $img->get_width()+2);
}
$smarty->assign('heading', $article_data["heading"]);
if ( $prefs['article_paginate'] == 'y' ) {
	if (!isset($_REQUEST['page'])) $_REQUEST['page'] = 1;
	// Get ~pp~, ~np~ and <pre> out of the way. --rlpowell, 24 May 2004
	$preparsed = array();
	$noparsed = array();

	$parserlib->plugins_remove($article_data["body"], $noparsed);
	$parserlib->parse_first($article_data["body"], $preparsed, $noparsed);
	$pages = $artlib->get_number_of_pages($article_data["body"]);
	$article_data["body"] = $artlib->get_page($article_data["body"], $_REQUEST['page']);
	$smarty->assign('pages', $pages);
	if ($pages > $_REQUEST['page']) {
		$smarty->assign('next_page', $_REQUEST['page'] + 1);
	} else {
		$smarty->assign('next_page', $_REQUEST['page']);
	}
	if ($_REQUEST['page'] > 1) {
		$smarty->assign('prev_page', $_REQUEST['page'] - 1);
	} else {
		$smarty->assign('prev_page', 1);
	}
	$smarty->assign('first_page', 1);
	$smarty->assign('last_page', $pages);
	$smarty->assign('pagenum', $_REQUEST['page']);
	// Put ~pp~, ~np~ and <pre> back. --rlpowell, 24 May 2004
	$parserlib = TikiLib::lib('parser');
	$parserlib->replace_preparse($article_data["body"], $preparsed, $noparsed);
}
if ($prefs["article_custom_attributes"] == 'y') {
	$t_article_attributes = $artlib->get_article_attributes($article_data["articleId"]);
	$type_attributes = $artlib->get_article_type_attributes($article_data["type"], 'relationId ASC');
	$article_attributes = array();
	foreach ($type_attributes as $attname => $att) {
		if (in_array($att["itemId"], array_keys($t_article_attributes))) {
			$article_attributes[$attname] = $t_article_attributes[$att["itemId"]];
		}
	}
	$smarty->assign('article_attributes', $article_attributes);
} else {
	$smarty->assign('article_attributes', array());
}
$smarty->assign('body', $article_data["body"]);
$smarty->assign('publishDate', $article_data["publishDate"]);
$smarty->assign('expireDate', $article_data["expireDate"]);
$smarty->assign('show_pubdate', $article_data["show_pubdate"]);
$smarty->assign('show_expdate', $article_data["show_expdate"]);
$smarty->assign('edit_data', 'y');
$body = $article_data["body"];
$heading = $article_data["heading"];

// We need to figure out in which theme we are before the page parsing
// in case the page contains pluginModule in which cas the parser triggers tiki-modules.php
// which needs $tc_theme for deciding on the visible modules everywhere in the page
include_once ('tiki-section_options.php');
if ($prefs['feature_theme_control'] == 'y') {
	$cat_type = 'article';
	$cat_objid = $_REQUEST["articleId"];
	include ('tiki-tc.php');
}

$smarty->assign('parsed_body', $tikilib->parse_data($body, array('is_html' => $artlib->is_html($article_data))));
$smarty->assign(
	'parsed_heading',
	$tikilib->parse_data(
		$heading,
		array(
			'min_one_paragraph' => true,
			'is_html' => $artlib->is_html($article_data, true),
		)
	)
);
if ($prefs['article_related_articles'] == 'y') {
	$article_data['related_articles'] = $artlib->get_related_articles($article_data['articleId']);
	if (isset($article_data['related_articles']) && !empty($article_data['related_articles'])) {
		$smarty->assign('related_articles', $article_data['related_articles']);
	}
}

$topics = $artlib->list_topics();
if (isset($topics[$article_data['topicId']])) {
	$smarty->assign('topicName', $topics[$article_data['topicId']]['name']);
}
$smarty->assign_by_ref('topics', $topics);

$objId = $_REQUEST['articleId'];
if ($prefs['feature_categories'] == 'y') {
	$is_categorized = $categlib->is_categorized('article', $objId);
}
// Display category path or not (like {catpath()})
if (isset($is_categorized) && $is_categorized) {
	$smarty->assign('is_categorized', 'y');
	if ($prefs['feature_categories'] == 'y' && $prefs['feature_categorypath'] == 'y') {
		$cats = $categlib->get_object_categories('article', $objId);
		$display_catpath = $categlib->get_categorypath($cats);
		$smarty->assign('display_catpath', $display_catpath);
	}
	// Display current category objects or not (like {category()})
	if (isset($prefs['feature_categoryobjects']) and $prefs['feature_categories'] == 'y') {
		if ($prefs['feature_categoryobjects'] == 'y') {
			$catids = $categlib->get_object_categories('article', $objId);
			$display_catobjects = $categlib->get_categoryobjects($catids);
			$smarty->assign('display_catobjects', $display_catobjects);
		}
	}
	if ($prefs['feature_categories'] == 'y' && $prefs['category_morelikethis_algorithm'] != '') {
		$freetaglib = TikiLib::lib('freetag');
		$category_related_objects = $freetaglib->get_similar('article', $_REQUEST['articleId'], empty($prefs['category_morelikethis_mincommon_max'])? $prefs['maxRecords']: $prefs['category_morelikethis_mincommon_max'], null, 'category');
		$smarty->assign_by_ref('category_related_objects', $category_related_objects);
	}
} else {
	$smarty->assign('is_categorized', 'n');
}

if ($prefs['feature_multilingual'] == 'y' && $article_data['lang']) {
	$multilinguallib = TikiLib::lib('multilingual');
	$trads = $multilinguallib->getTranslations('article', $article_data['articleId'], $article_data["title"], $article_data['lang']);
	$smarty->assign('trads', $trads);
}
//Keep track of month of last viewed article for article months_links module foldable display
$_SESSION['cms_last_viewed_month'] = TikiLib::date_format("%Y-%m", $article_data["publishDate"]);
ask_ticket('print-article');
//add a hit
$statslib->stats_hit($article_data["title"], "article", $article_data['articleId']);
if ($prefs['feature_actionlog'] == 'y') {
	$logslib->add_action('Viewed', $_REQUEST['articleId'], 'article');
}
// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->assign('print_page', 'y');
$smarty->display("tiki-print_article.tpl");
