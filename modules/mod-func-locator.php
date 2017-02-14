<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-locator.php 57960 2016-03-17 20:01:11Z jonnybradley $

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}


/**
 * @return array
 */
function module_locator_info()
{
	return array(
		'name' => tra('Locator'),
		'description' => tra('Presents a map with the geolocated content within the page.'),
		'prefs' => array(),
		'params' => array(
		),
	);
}

/**
 * @param $mod_reference
 * @param $module_params
 */
function module_locator($mod_reference, $module_params)
{
	$headerlib = TikiLib::lib('header');

	$headerlib->add_map();

	// assign the default map centre from the prefs as a data attribute for the map-container div
	TikiLib::lib('smarty')->assign('center', TikiLib::lib('geo')->get_default_center());
}

