<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: block.filter.php 57965 2016-03-17 20:04:49Z jonnybradley $

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 *
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_block_filter($params, $content, $smarty, &$repeat)
{
	global $prefs;

	if ( $repeat ) return;

	$tikilib = TikiLib::lib('tiki');
	$unifiedsearchlib = TikiLib::lib('unifiedsearch');

	if (! isset($params['action'])) {
		$params['action'] = '';
	}

	$types = $unifiedsearchlib->getSupportedTypes();

	$filter = isset($_REQUEST['filter']) ? $_REQUEST['filter'] : array();
	if (isset($params['filter'])) {
		$filter = array_merge($filter, $params['filter']);
	}

	// General
	$smarty->assign('filter_action', $params['action']);

	$smarty->assign('filter_content', isset($filter['content']) ? $filter['content'] : '');
	$smarty->assign('filter_type', isset($filter['type']) ? $filter['type'] : $prefs['search_default_where']);
	$smarty->assign('filter_types', $types);

	$sort_mode = isset($_REQUEST['sort_mode']) ? $_REQUEST['sort_mode'] : 'score_ndesc';
	$sort_modes = array(
		'score_ndesc' => tra('Relevance'),
		'object_type_asc' => tra('Type'),
		'title_asc' => tra('Title'),
		'modification_date_ndesc' => tra('Modified date'),
		'visits_ndesc' => tra('Visits'),
	);
	$smarty->assign('sort_mode', $sort_mode);
	$smarty->assign('sort_modes', $sort_modes);

	// Categories
	if ($prefs['feature_categories'] == 'y' && $prefs['search_show_category_filter'] == 'y') {
		$smarty->assign('filter_deep', isset($filter['deep']));
		$smarty->assign('filter_categories', isset($filter['categories']) ? $filter['categories'] : '');
		$smarty->assign('filter_categmap', json_encode(TikiDb::get()->fetchMap('SELECT categId, name FROM tiki_categories')));

		// Generate the category tree {{{
		$categlib = TikiLib::lib('categ');
		require_once 'lib/tree/BrowseTreeMaker.php';
		$ctall = $categlib->getCategories();

		if ($prefs['unified_excluded_categories'] === 'y') {		// remove those excluded categs
			$ctall = array_diff_key($ctall, array_flip($prefs['unified_excluded_categories']));
		}

		$tree_nodes = array();
		foreach ($ctall as $c) {
			$name = htmlentities($c['name'], ENT_QUOTES, 'UTF-8');

			$body = <<<BODY
<label>
	<input type="checkbox" value="{$c['categId']}"/>
	{$name}
</label>
BODY;

			$tree_nodes[] = array(
				'id' => $c['categId'],
				'parent' => $c['parentId'],
				'data' => $body,
			);
		}

		$tm = new BrowseTreeMaker('categ');
		$res = $tm->make_tree(0, $tree_nodes);
		$smarty->assign('filter_category_picker', $res);
		// }}}
	}

	if ($prefs['feature_freetags'] == 'y') {
		$freetaglib = TikiLib::lib('freetag');

		$smarty->assign('filter_tags', isset($filter['tags']) ? $filter['tags'] : '');
		$smarty->assign('filter_tagmap', json_encode(TikiDb::get()->fetchMap('SELECT tagId, tag FROM tiki_freetags')));
		$smarty->assign('filter_tags_picker', (string) $freetaglib->get_cloud());
	}

	// Language
	if ($prefs['feature_multilingual'] == 'y') {
		$langLib = TikiLib::lib('language');
		$languages = $langLib->list_languages();
		$smarty->assign('filter_languages', $languages);
		$smarty->assign('filter_language_unspecified', isset($filter['language_unspecified']));
		$smarty->assign('filter_language', isset($filter['language']) ? $filter['language'] : '');
	}

	return $smarty->fetch('filter.tpl');
}
