<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-freetag.php 57960 2016-03-17 20:01:11Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_freetag_info()
{
	return array(
		'name' => tra('Tags Editor'),
		'description' => tra('Shows current tags and enables adding and removing some if permissions allow.'),
		'prefs' => array('feature_freetags'),
		'params' => array()
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_freetag($mod_reference, $module_params)
{
	global $sections, $section;
	$smarty = TikiLib::lib('smarty');
	$modlib = TikiLib::lib('mod');
	
	$globalperms = Perms::get();
	if ($globalperms->view_freetags && isset($sections[$section])) {
		$tagid = 0;
		$par = $sections[$section];
		if (isset($par['itemkey']) && !empty($_REQUEST["{$par['itemkey']}"])) {
			$tagid = $_REQUEST["{$par['itemkey']}"];
		} elseif (isset($par['key']) && !empty($_REQUEST["{$par['key']}"])) {
			$tagid = $_REQUEST["{$par['key']}"];
		}
		if ($tagid) {
			$smarty->assign('viewTags', 'y');
		}
	} else if ($modlib->is_admin_mode(true)) {
		$smarty->assign('viewTags', 'y');
	}
}
