<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-edit_submission.php 58749 2016-06-01 01:39:05Z lindonb $

$section = 'cms';
require_once ('tiki-setup.php');
$artlib = TikiLib::lib('art');

if ($prefs['feature_freetags'] == 'y') {
	$freetaglib = TikiLib::lib('freetag');
}

$access->check_feature('feature_submissions');
$access->check_permission('tiki_p_submit_article');
$errors = false;

$auto_query_args = array('subId');

if ($tiki_p_admin != 'y') {
	if ($tiki_p_use_HTML != 'y') {
		$_REQUEST['allowhtml'] = 'off';
	}
}

if (isset($_REQUEST['subId'])) {
	$subId = $_REQUEST['subId'];
} else {
	$subId = 0;
}

if (!empty($_REQUEST['topicId'])) {
	$topicId = $_REQUEST['topicId'];
} else {
	$topicId = '';
}

if (!empty($_REQUEST['type'])) {
	$type = $_REQUEST['type'];
} else {
	$type = '';
}

// We need separate numbering of previews, since we access preview images by this number
if (isset($_REQUEST['previewId'])) {
	$previewId = $_REQUEST['previewId'];
} else {
	$previewId = rand();
}

$smarty->assign('subId', $subId);
$smarty->assign('articleId', $subId);
$smarty->assign('previewId', $previewId);
$smarty->assign(
	'imageIsChanged',
	(isset($_REQUEST['imageIsChanged']) && $_REQUEST['imageIsChanged']=='y') ? 'y' : 'n'
);

$templateslib = TikiLib::lib('template');

if (isset($_REQUEST['templateId']) && $_REQUEST['templateId'] > 0) {
	$template_data = $templateslib->get_template($_REQUEST['templateId'], $prefs['language']);
	$_REQUEST['preview'] = 1;
	$_REQUEST['body'] = $template_data['content'];
	if ($templateslib->template_is_in_section($_REQUEST['templateId'], 'wiki_html')) {
		$_REQUEST['allowhtml'] = 'on';
	}
}

$smarty->assign('allowhtml', '');
$publishDate = $tikilib->now;
$expireDate = $tikilib->make_time(0, 0, 0, $tikilib->date_format("%m"), $tikilib->date_format("%d"), $tikilib->date_format("%Y") + 1);

//Use 12- or 24-hour clock for $publishDate time selector based on admin and user preferences
$userprefslib = TikiLib::lib('userprefs');
$smarty->assign('use_24hr_clock', $userprefslib->get_user_clock_pref($user));

$smarty->assign('arttitle', '');
$smarty->assign('topline', '');
$smarty->assign('subtitle', '');
$smarty->assign('linkto', '');
$smarty->assign('image_caption', '');
$smarty->assign('lang', $prefs['language']);
$authorName = $tikilib->get_user_preference($user, 'realName', $user);
$smarty->assign('authorName', $authorName);
$smarty->assign('topicId', $topicId);
$smarty->assign('type', $type);
$smarty->assign('useImage', 'n');
$smarty->assign('isfloat', 'n');
$hasImage = 'n';
$smarty->assign('hasImage', 'n');
$smarty->assign('image_name', '');
$smarty->assign('image_type', '');
$smarty->assign('image_size', '');
$smarty->assign('image_data', '');
$smarty->assign('image_x', $prefs['article_image_size_x']);
$smarty->assign('image_y', $prefs['article_image_size_y']);
$smarty->assign('heading', '');
$smarty->assign('body', '');
$smarty->assign('type', $type);
$smarty->assign('rating', 7);
$smarty->assign('edit_data', 'n');

if (isset($_REQUEST['templateId']) && $_REQUEST['templateId'] > 0) {
	$template_data = $templateslib->get_template($_REQUEST['templateId'], $prefs['language']);
	$_REQUEST['preview'] = 1;
	$_REQUEST['body'] = $template_data['content'];
}

// If the submissionId is passed then get the submission data
if (isset($_REQUEST['subId'])) {
	$article_data = $artlib->get_submission($_REQUEST['subId']);

	$publishDate = $article_data['publishDate'];
	$expireDate = $article_data['expireDate'];
	$smarty->assign('arttitle', $article_data['title']);
	$smarty->assign('topline', $article_data['topline']);
	$smarty->assign('subtitle', $article_data['subtitle']);
	$smarty->assign('linkto', $article_data['linkto']);
	$smarty->assign('image_caption', $article_data['image_caption']);
	$smarty->assign('lang', $article_data['lang']);
	$smarty->assign('authorName', $article_data['authorName']);
	$smarty->assign('topicId', $article_data['topicId']);
	$smarty->assign('useImage', $article_data['useImage']);
	$smarty->assign('isfloat', $article_data['isfloat']);
	$smarty->assign('image_name', $article_data['image_name']);
	$smarty->assign('image_type', $article_data['image_type']);
	$smarty->assign('image_size', $article_data['image_size']);
	$smarty->assign('image_data', urlencode($article_data['image_data']));
	$smarty->assign('reads', $article_data['nbreads']);
	$smarty->assign('image_x', $article_data['image_x']);
	$smarty->assign('image_y', $article_data['image_y']);
	$smarty->assign('type', $article_data['type']);
	$smarty->assign('rating', $article_data['rating']);

	if (strlen($article_data['image_data']) > 0) {
		$smarty->assign('hasImage', 'y');

		$hasImage = 'y';
	}

	$smarty->assign('heading', $article_data['heading']);
	$smarty->assign('body', $article_data['body']);
	$smarty->assign('edit_data', 'y');

	$data = $article_data['image_data'];
	$imgname = $article_data['image_name'];

	$body = $article_data['body'];
	$heading = $article_data['heading'];
	$smarty->assign('parsed_body', $tikilib->parse_data($body, array('is_html' => 'y')));
	$smarty->assign('parsed_heading', $tikilib->parse_data($heading), array('is_html' => 'y'));
}
if (!empty($_REQUEST['translationOf'])) {
	$translationOf = $_REQUEST['translationOf'];
	$smarty->assign('translationOf', $translationOf);
}

if (isset($_REQUEST['subId'])) {
	if ($_REQUEST['subId'] > 0) {
		if (($tiki_p_edit_submission != 'y' and $article_data['author'] != $user) or $user == '') {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg', tra('You do not have permission to edit submissions'));
			$smarty->display('error.tpl');
			die;
		}
	}
}

if (isset($_REQUEST['allowhtml'])) {
	if ($_REQUEST['allowhtml'] == 'on') {
		$smarty->assign('allowhtml', 'y');
	} else {
		$smarty->assign('allowhtml', 'n');
	}
} else if ($_SESSION['wysiwyg'] === 'y' && $prefs['wysiwyg_htmltowiki'] !== 'y') {
	$smarty->assign('allowhtml', 'y');
}

if ((isset($_REQUEST["save"]) || isset($_REQUEST["submitarticle"]))
			&& empty($user)
			&& $prefs['feature_antibot'] == 'y'
			&& !$captchalib->validate()
) {
	Feedback::error(['mes' => $captchalib->getErrors()]);
	$errors = true;
}

$topics = $artlib->list_topics();
$smarty->assign_by_ref('topics', $topics);

$smarty->assign('preview', 0);

// If we are in preview mode then preview it!
if (isset($_REQUEST['preview']) || !empty($errors)) {
	check_ticket('edit-submission');
# convert from the displayed 'site' time to 'server' time

	//Convert 12-hour clock hours to 24-hour scale to compute time
	if (!empty($_REQUEST['publish_Meridian'])) {
		$_REQUEST['publish_Hour'] = date('H', strtotime($_REQUEST['publish_Hour'] . ':00 ' . $_REQUEST['publish_Meridian']));
	}
	if (!empty($_REQUEST['expire_Meridian'])) {
		$_REQUEST['expire_Hour'] = date('H', strtotime($_REQUEST['expire_Hour'] . ':00 ' . $_REQUEST['expire_Meridian']));
	}

	$publishDate = $tikilib->make_time(
		$_REQUEST['publish_Hour'],
		$_REQUEST['publish_Minute'],
		0,
		$_REQUEST['publish_Month'],
		$_REQUEST['publish_Day'],
		$_REQUEST['publish_Year']
	);

	$expireDate = $tikilib->make_time(
		$_REQUEST['expire_Hour'],
		$_REQUEST['expire_Minute'],
		0,
		$_REQUEST['expire_Month'],
		$_REQUEST['expire_Day'],
		$_REQUEST['expire_Year']
	);

	$smarty->assign('reads', '0');
	if (isset($_REQUEST['preview'])) $smarty->assign('preview', 1);
	$smarty->assign('edit_data', 'y');
	$smarty->assign('arttitle', $_REQUEST['title']);
	$smarty->assign('authorName', $_REQUEST['authorName']);
	$smarty->assign('topicId', $_REQUEST['topicId']);
	$smarty->assign('topicName', $topics[$_REQUEST['topicId']]['name']);

	if (isset($_REQUEST['useImage']) && $_REQUEST['useImage'] == 'on') {
		$useImage = 'y';
	} else {
		$useImage = 'n';
	}

	if (isset($_REQUEST['isfloat']) && $_REQUEST['isfloat'] == 'on') {
		$isfloat = 'y';
	} else {
		$isfloat = 'n';
	}

	$smarty->assign('image_data', $_REQUEST['image_data']);

	if (strlen($_REQUEST['image_data']) > 0) {
		$smarty->assign('hasImage', 'y');

		$hasImage = 'y';
	}


	$type = $artlib->get_type($_REQUEST['type']);

	$smarty->assign('show_topline', $type["show_topline"]);
	$smarty->assign('show_subtitle', $type["show_subtitle"]);
	$smarty->assign('show_image_caption', $type["show_image_caption"]);
	$smarty->assign('show_author', $type["show_author"]);
	$smarty->assign('show_reads', $type["show_reads"]);
	$smarty->assign('show_pubdate', $type["show_pubdate"]);
	$smarty->assign('show_expdate', $type["show_expdate"]);
	$smarty->assign('show_linkto', $type["show_linkto"]);
	$smarty->assign('use_ratings', $type["use_ratings"]);

	if (!isset($_REQUEST['topline'])) $_REQUEST['topline'] = '';
	if (!isset($_REQUEST['subtitle'])) $_REQUEST['subtitle'] = '';
	if (!isset($_REQUEST['linkto'])) $_REQUEST['linkto'] = '';
	if (!isset($_REQUEST['image_caption'])) $_REQUEST['image_caption'] = '';
	if (!isset($_REQUEST['lang'])) $_REQUEST['lang'] = '';

	$smarty->assign('topline', $_REQUEST['topline']);
	$smarty->assign('subtitle', $_REQUEST['subtitle']);
	$smarty->assign('linkto', $_REQUEST['linkto']);
	$smarty->assign('image_caption', $_REQUEST['image_caption']);
	$smarty->assign('lang', $_REQUEST['lang']);
	$smarty->assign('image_name', $_REQUEST['image_name']);
	$smarty->assign('image_type', $_REQUEST['image_type']);
	$smarty->assign('image_size', $_REQUEST['image_size']);
	$smarty->assign('image_x', $_REQUEST['image_x']);
	$smarty->assign('image_y', $_REQUEST['image_y']);
	$smarty->assign('useImage', $useImage);
	$smarty->assign('isfloat', $isfloat);
	$smarty->assign('type', $_REQUEST['type']);
	$smarty->assign('rating', $_REQUEST['rating']);
	$smarty->assign('entrating', floor($_REQUEST['rating']));
	$imgname = $_REQUEST['image_name'];
	$data = urldecode($_REQUEST['image_data']);

	// Parse the information of an uploaded file and use it for the preview
	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {

		$file_name = $_FILES['userfile1']['name'];
		// Simple check if it's an image file
		if (preg_match('/\.(gif|png|jpe?g)$/i', $file_name)) {
			$fp = fopen($_FILES['userfile1']['tmp_name'], "rb");
			$data = fread($fp, filesize($_FILES['userfile1']['tmp_name']));
			fclose($fp);

			$imgtype = $_FILES['userfile1']['type'];
			$imgsize = $_FILES['userfile1']['size'];
			$imgname = $_FILES['userfile1']['name'];
			$smarty->assign('image_data', urlencode($data));
			$smarty->assign('image_name', $imgname);
			$smarty->assign('image_type', $imgtype);
			$smarty->assign('image_size', $imgsize);
			$hasImage = 'y';
			$smarty->assign('hasImage', 'y');

			// Create preview cache image, for display afterwards
			$cachefile = $prefs['tmpDir'];

			if ($tikidomain) {
				$cachefile.= "/$tikidomain";
			}

			$cachefile.= '/article_preview.' . $previewId;

			if (move_uploaded_file($_FILES['userfile1']['tmp_name'], $cachefile)) {
				$smarty->assign('imageIsChanged', 'y');
			}
		}
	}


	$smarty->assign('heading', $_REQUEST['heading']);
	$smarty->assign('edit_data', 'y');

	if (isset($_REQUEST['allowhtml']) && $_REQUEST['allowhtml'] == 'on') {
		$body = $_REQUEST['body'];

		$heading = $_REQUEST['heading'];
	} else {
		$body = strip_tags($_REQUEST['body'], '<a><pre><p><img><hr><b><i>');

		$heading = strip_tags($_REQUEST['heading'], '<a><pre><p><img><hr><b><i>');
	}

	$smarty->assign('size', strlen($body));

	$parsed_body = $tikilib->parse_data($body, array('is_html' => 'y'));
	$parsed_heading = $tikilib->parse_data($heading, array('is_html' => 'y'));

	$smarty->assign('parsed_body', $parsed_body);
	$smarty->assign('parsed_heading', $parsed_heading);

	$smarty->assign('body', $body);
	$smarty->assign('heading', $heading);
}

// Pro
if ((isset($_REQUEST['save']) || isset($_REQUEST['submitarticle'])) && empty($errors)) {
	check_ticket('edit-submission');
	$imagegallib = TikiLib::lib('imagegal');

	# convert from the displayed 'site' time to UTC time
	//Convert 12-hour clock hours to 24-hour scale to compute time
	if (!empty($_REQUEST['publish_Meridian'])) {
		$_REQUEST['publish_Hour'] = date('H', strtotime($_REQUEST['publish_Hour'] . ':00 ' . $_REQUEST['publish_Meridian']));
	}

	if (!empty($_REQUEST['expire_Meridian'])) {
		$_REQUEST['expire_Hour'] = date('H', strtotime($_REQUEST['expire_Hour'] . ':00 ' . $_REQUEST['expire_Meridian']));
	}

	$publishDate = $tikilib->make_time(
		$_REQUEST['publish_Hour'],
		$_REQUEST['publish_Minute'],
		0,
		$_REQUEST['publish_Month'],
		$_REQUEST['publish_Day'],
		$_REQUEST['publish_Year']
	);

	$expireDate = $tikilib->make_time(
		$_REQUEST['expire_Hour'],
		$_REQUEST['expire_Minute'],
		0,
		$_REQUEST['expire_Month'],
		$_REQUEST['expire_Day'],
		$_REQUEST['expire_Year']
	);

	if (isset($_REQUEST['allowhtml']) && $_REQUEST['allowhtml'] == 'on' || $_SESSION['wysiwyg'] == 'y') {
		$body = $_REQUEST['body'];

		$heading = $_REQUEST['heading'];
	} else {
		$body = strip_tags($_REQUEST['body'], '<a><pre><p><img><hr><b><i>');

		$heading = strip_tags($_REQUEST['heading'], '<a><pre><p><img><hr><b><i>');
	}

	if (isset($_REQUEST['useImage']) && $_REQUEST['useImage'] == 'on') {
		$useImage = 'y';
	} else {
		$useImage = 'n';
	}

	if (isset($_REQUEST['isfloat']) && $_REQUEST['isfloat'] == 'on') {
		$isfloat = 'y';
	} else {
		$isfloat = 'n';
	}

	$imgdata = urldecode($_REQUEST['image_data']);

	if (strlen($imgdata) > 0) {
		$hasImage = 'y';
	}

	$imgname = $_REQUEST['image_name'];
	$imgtype = $_REQUEST['image_type'];
	$imgsize = $_REQUEST['image_size'];

	if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
		$fp = fopen($_FILES['userfile1']['tmp_name'], 'rb');

		$imgdata = fread($fp, filesize($_FILES['userfile1']['tmp_name']));
		fclose($fp);
		$imgtype = $_FILES['userfile1']['type'];
		$imgsize = $_FILES['userfile1']['size'];
		$imgname = $_FILES['userfile1']['name'];
	}

	// Parse $edit and eliminate image references to external URIs (make them internal)
	$body = $imagegallib->capture_images($body);
	$heading = $imagegallib->capture_images($heading);

	// If page exists
	if (!isset($_REQUEST['topicId'])) {
		$smarty->assign('msg', tra('You have to create a topic first'));

		$smarty->display('error.tpl');
		die;
	}
	if (!isset($_REQUEST['topline'])) $_REQUEST['topline'] = '';
	if (!isset($_REQUEST['subtitle'])) $_REQUEST['subtitle'] = '';
	if (!isset($_REQUEST['linkto'])) $_REQUEST['linkto'] = '';
	if (!isset($_REQUEST['image_caption'])) $_REQUEST['image_caption'] = '';
	if (!isset($_REQUEST['lang'])) $_REQUEST['lang'] = '';

	$subid = $artlib->replace_submission(
		strip_tags($_REQUEST['title'], '<a><pre><p><img><hr><b><i>'),
		$_REQUEST['authorName'],
		$_REQUEST['topicId'],
		$useImage,
		$imgname,
		$imgsize,
		$imgtype,
		$imgdata,
		$heading,
		$body,
		$publishDate,
		$expireDate,
		$user,
		$subId,
		$_REQUEST['image_x'],
		$_REQUEST['image_y'],
		$_REQUEST['type'],
		$_REQUEST['topline'],
		$_REQUEST['subtitle'],
		$_REQUEST['linkto'],
		$_REQUEST['image_caption'],
		$_REQUEST['lang'],
		$_REQUEST['rating'],
		$isfloat
	);

	$cat_type = 'submission';
	$cat_objid = $subid;
	$cat_desc = substr($_REQUEST['heading'], 0, 200);
	$cat_name = $_REQUEST['title'];
	$cat_href = 'tiki-edit_submission.php?subId=' . $cat_objid;

	include_once ('categorize.php');
	include_once ('freetag_apply.php');

	// Add attributes
	if ($prefs['article_custom_attributes'] == 'y') {
		$valid_att = $artlib->get_article_type_attributes($_REQUEST['type']);
		$attributeArray = array();
		foreach ($valid_att as $att) {
			// need to convert . to _ for matching
			$toMatch = str_replace('.', '_', $att['itemId']);
			if (isset($_REQUEST[$toMatch])) {
				$attributeArray[$att['itemId']] = $_REQUEST[$toMatch];
			}
		}
		$artlib->set_article_attributes($subid, $attributeArray, true);
	}
	// Remove image cache because image may have changed, and we
	// don't want to show the old image
	@$artlib->delete_image_cache('submission', $subId);
	// Remove preview cache because it won't be used any more
	@$artlib->delete_image_cache('preview', $previewId);

	if ( isset($_REQUEST['save']) && $tiki_p_autoapprove_submission == 'y' ) {
		$artlib->approve_submission($subid);
		header('location: tiki-view_articles.php');
		die;
	}

	header('location: tiki-list_submissions.php');
	die;
}

// Set date to today before it's too late
$_SESSION['thedate'] = $tikilib->now;

// get list of valid types
$types = $artlib->list_types_byname();

if (empty($article_data) && empty($_REQUEST['type'])) {
	// Select the first type as default selection
	if (empty($types)) {
		$type = '';
	} else {
		$type = key($types);
	}
	$smarty->assign('type', $type);
}

if ($prefs['article_custom_attributes'] == 'y') {
	$article_attributes = $artlib->get_article_attributes($subId, true);
	$smarty->assign('article_attributes', $article_attributes);
	$all_attributes = array();
	$js_string = '';

	foreach ($types as &$t) {
		// javascript needs htmlid to show/hide to be properties of basic array
		$type_attributes = $artlib->get_article_type_attributes($t['type'], 'relationId ASC');
		$all_attributes = array_merge($all_attributes, $type_attributes);
		foreach ($type_attributes as $att) {
			$htmlid = str_replace('.', '_', $att['itemId']);
			$t[$htmlid] = 'y';
			$js_string .= "'$htmlid', 'y', ";
		}
	}
	$smarty->assign('all_attributes', $all_attributes);
	$headerlib->add_js("articleCustomAttributes = new Array(); articleCustomAttributes = [$js_string];");
}
$smarty->assign_by_ref('types', $types);

if ($prefs['feature_cms_templates'] == 'y') {
	$templates = $templateslib->list_templates('cms', 0, -1, 'name_asc', '');
}

$smarty->assign_by_ref('templates', $templates['data']);

if ($prefs['feature_multilingual'] == 'y') {
	$languages = array();
	$langLib = TikiLib::lib('language');
	$languages = $langLib->list_languages();
	$smarty->assign_by_ref('languages', $languages);
}

$cat_type = 'submission';
$cat_objid = $subId;
include_once ('categorize_list.php');

if ($prefs['feature_freetags'] == 'y') {
	include_once ('freetag_list.php');
	if (isset($_REQUEST['preview'])) {
		$smarty->assign('taglist', $_REQUEST['freetag_string']);
	}
}

$smarty->assign('publishDate', $publishDate);
$smarty->assign('expireDate', $expireDate);
$smarty->assign('siteTimeZone', $prefs['display_timezone']);

$wikilib = TikiLib::lib('wiki');
$plugins = $wikilib->list_plugins(true, 'body');
$smarty->assign_by_ref('plugins', $plugins);

$smarty->assign('showtags', 'n');
$smarty->assign('qtcycle', '');
ask_ticket('edit-submission');

$smarty->assign('section', $section);
include_once ('tiki-section_options.php');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the Index Template
$smarty->assign('mid', 'tiki-edit_submission.tpl');
$smarty->display('tiki.tpl');
