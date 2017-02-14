<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-freetags_most_popular.php 57960 2016-03-17 20:01:11Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_freetags_most_popular_info()
{
	return array(
		'name' => tra('Most Popular Tags'),
		'description' => tra('Shows the most popular tags. More popularity is indicated by a larger font.'),
		'prefs' => array('feature_freetags'),
		'params' => array(
			'type' => array(
				'name' => tra('Display type'),
				'description' => tra('If set to "cloud", links are displayed as a cloud.') . " " . tr('Default: "list".'),
				'filter' => 'word'
			),
			'max' => array(
				'name' => tra('Maximum elements'),
				'description' => tra('If set to a number, limits the number of tags displayed.') . " " . tr('Default: 10.'),
				'filter' => 'int'
			),
			'where' => array(
				'required' => false,
				'name' => tra('Object type'),
				'description' => tra('Type of objects to extract. Set to All to find all types.'),
				'filter' => 'text',
				'default' => null,
				'options' => array (
					array('text' => tra('Same'), 'value' => 'all'),
					array('text' => tra('All'), 'value' => 'all'),
					array('text' => tra('Wiki Pages'), 'value' => 'wiki page'),
					array('text' => tra('Blog Posts'), 'value' => 'blog post'),
					array('text' => tra('Article'), 'value' => 'article'),
					array('text' => tra('Directory'), 'value' => 'directory'),
					array('text' => tra('Faqs'), 'value' => 'faq'),
					array('text' => tra('File Galleries'), 'value' => 'file gallery'),
					array('text' => tra('Files'), 'value' => 'file'),
					array('text' => tra('Polls'), 'value' => 'poll'),
					array('text' => tra('Quizzes'), 'value' => 'quiz'),
					array('text' => tra('Surveys'), 'value' => 'survey'),
					array('text' => tra('Trackers'), 'value' => 'tracker'),
				),
			),
			'objectId' => array(
				'required' => false,
				'name' => tra('BlogId'),
				'description' => tra('Blog Id if only blog posts selected. More than one blog can be provided, separated by colon. Example: 1:5'),
				'default' => null,
				'profile_reference' => 'blog',
			),
		),
		'common_params' => array('rows') // This is not clean. We should use just max instead of max and rows as fallback,
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_freetags_most_popular($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	$globalperms = Perms::get();
	if ($globalperms->view_freetags) {
		$freetaglib = TikiLib::lib('freetag');
		$most_popular_tags = $freetaglib->get_most_popular_tags('', 0, empty($module_params['max']) ? $mod_reference["rows"] : $module_params['max'], empty($module_params['where'])?'': $module_params['where'], empty($module_params['objectId'])?'': $module_params['objectId']);
		$smarty->assign_by_ref('most_popular_tags', $most_popular_tags);
		$smarty->assign('type', (isset($module_params['type']) && $module_params['type'] == 'cloud') ? 'cloud' : 'list');
	}
}
