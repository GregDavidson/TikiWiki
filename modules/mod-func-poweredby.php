<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-poweredby.php 57960 2016-03-17 20:01:11Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * @return array
 */
function module_poweredby_info()
{
	return array(
		'name' => tra('Powered By'),
		'description' => tra('Powered by Tiki, and others'),
		'params' => array(
			'tiki' => array(
				'name' => tra('Tiki'),
				'description' => tra('Shows "Powered by Tiki Wiki CMS Groupware" message') . ' (y/n)',
				'filter' => 'alpha',
			),
			'version' => array(
				'name' => tra('Version'),
				'description' => tra('Tiki version info') . ' (y/n)',
				'filter' => 'alpha',
			),
			'credits' => array(
				'name' => tra('Credits'),
				'description' => tra('Shows theme credits (contents of credits.tpl)') . ' (y/n)',
				'filter' => 'alpha',
						),
			'icons' => array(
				'name' => tra('Icons'),
				'description' => tra('Shows various "powered by" icons') . ' (y/n)',
				'filter' => 'alpha',
			),
		),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_poweredby($mod_reference, $module_params)
{

}
