<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-mustread.php 57960 2016-03-17 20:01:11Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_mustread_info()
{
	return array(
		'name' => tr('Must Read'),
		'description' => tr('Request the creation of a mustread item based on the current object.'),
		'prefs' => ['mustread_enabled'],
		'params' => array(
			'objectField' => array(
				'required' => true,
				'name' => tr('Object Field'),
				'description' => tr('Permanent name of the field containing the object reference'),
			),
		),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_mustread($mod_reference, $module_params)
{
	global $prefs;

	$smarty = TikiLib::lib('smarty');

	$object = current_object();

	$smarty->assign('mustread_module', [
		'object' => $object,
		'field' => $module_params['objectField'],
	]);
}
