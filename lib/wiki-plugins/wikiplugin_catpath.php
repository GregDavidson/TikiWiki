<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_catpath.php 57962 2016-03-17 20:02:39Z jonnybradley $

function wikiplugin_catpath_info()
{
	return array(
		'name' => tra('Category Path'),
		'documentation' => 'PluginCatPath',
		'description' => tra('Show the full category path for a wiki page'),
		'prefs' => array( 'feature_categories', 'wikiplugin_catpath' ),
		'iconname' => 'structure',
		'introduced' => 1,
		'params' => array(
			'divider' => array(
				'required' => false,
				'name' => tra('Separator'),
				'description' => tr('String used to separate the categories in the path. Default character is %0.',
					'<code>></code>'),
				'since' => '1',
				'default' => '>',
			),
			'top' => array(
				'required' => false,
				'name' => tra('Display Top Category'),
				'description' => tra('Show the top category as part of the path name (not shown by default)'),
				'since' => '1',
				'filter' => 'alpha',
				'default' => 'no',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 'y'), 
					array('text' => tra('No'), 'value' => 'n')
				),
			),
		),
	);
}

function wikiplugin_catpath($data, $params)
{
	global $prefs;

	$smarty = TikiLib::lib('smarty');
	$tikilib = TikiLib::lib('tiki');
	$categlib = TikiLib::lib('categ');

	if ($prefs['feature_categories'] != 'y') {
		return "<span class='warn'>" . tra("Categories are disabled"). "</span>";
	}

	extract($params, EXTR_SKIP);

	// default divider is '>'
	if (!(isset($divider))) {
		$divider = '>';
	}

	// default setting for top is 'no'
	if (!(isset($top))) {
		$top = 'no';
	} elseif ($top != 'y' and $top != 'yes' and $top != 'n' and $top != 'no') {
		$top = 'no';
	}

	$objId = urldecode($_REQUEST['page']);

	$cats = $categlib->get_object_categories('wiki page', $objId);

	$catpath = '';

	foreach ($cats as $categId) {
		$catpath .= '<span class="categpath">';

		// Display TOP on each line if wanted
		if ($top == 'yes' or $top == 'y') {
			$catpath .= '<a class="categpath" href="tiki-browse_categories.php?parentId=0">TOP</a> ' . $divider . ' ';
		}

		$path = '';
		$info = $categlib->get_category($categId);
		$path
			= '<a class="categpath" href="tiki-browse_categories.php?parentId=' . $info["categId"] . '">' . htmlspecialchars($info["name"]) . '</a>';

		while ($info["parentId"] != 0) {
			$info = $categlib->get_category($info["parentId"]);

			$path = '<a class="categpath" href="tiki-browse_categories.php?parentId=' . $info["categId"] . '">' . htmlspecialchars($info["name"]) . '</a> ' . htmlspecialchars($divider) . ' ' . $path;
		}

		$catpath .= $path . '</span><br />';
	}

	return $catpath;
}
