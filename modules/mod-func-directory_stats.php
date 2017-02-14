<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-directory_stats.php 57960 2016-03-17 20:01:11Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_directory_stats_info()
{
	return array(
		'name' => tra('Directory Statistics'),
		'description' => tra('Displays statistics about the directory, including the number of sites validated and to validate, the number of categories, of searches and of visited links.'),
		'prefs' => array('feature_directory'),
		'documentation' => 'Module directory_stats',
		'params' => array()
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_directory_stats($mod_reference, $module_params)
{
	global $prefs;
	$smarty = TikiLib::lib('smarty');
	$tikilib = TikiLib::lib('tiki');
	$ranking = $tikilib->dir_stats();
	$smarty->assign('modDirStats', $ranking);
}
