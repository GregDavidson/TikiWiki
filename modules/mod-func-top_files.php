<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-top_files.php 57960 2016-03-17 20:01:11Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_top_files_info()
{
	return array(
		'name' => tra('Top Files'),
		'description' => tra('Displays the specified number of files with links to them, starting with the one with most hits.'),
		'prefs' => array('feature_file_galleries'),
		'params' => array(),
		'common_params' => array('nonums', 'rows')
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_top_files($mod_reference, $module_params)
{
	$smarty = TikiLib::lib('smarty');
	$filegallib = TikiLib::lib('filegal');
	$ranking = $filegallib->list_files(0, $mod_reference["rows"], 'hits_desc', '');
	
	$smarty->assign('modTopFiles', $ranking["data"]);
}
