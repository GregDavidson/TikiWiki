<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-freetags_morelikethis.php 57960 2016-03-17 20:01:11Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_freetags_morelikethis_info()
{
	return array(
		'name' => tra('Similar-Tag Items'),
		'description' => tra('Shows content with multiple tags in common.'),
		'prefs' => array('feature_freetags'),
		'params' => array(
			'type' => array(
				'required' => false,
				'name' => tra('Type'),
				'description' => tra('Type of objects to extract.'),
				'filter' => 'text',
			),
		),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_freetags_morelikethis($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	$freetaglib = TikiLib::lib('freetag');

	$out = null;
	if (isset($module_params['type'])) {
		$out = $module_params['type'];
	}
	
	if ($object = current_object()) {
		$morelikethis = $freetaglib->get_similar($object['type'], $object['object'], $mod_reference["rows"], $out);
		$smarty->assign('modMoreLikeThis', $morelikethis);
		$smarty->assign('module_rows', $mod_reference["rows"]);
	}

	$smarty->assign('tpl_module_title', tra("Similar pages"));
}
