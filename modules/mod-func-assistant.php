<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mod-func-assistant.php 57961 2016-03-17 20:01:56Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


/**
 * @return array
 */
function module_assistant_info()
{
	return array(
		'name' => tra('Tiki Assistant'),
		'description' => tra('Display an assistant to guide new Tiki admins.'),
		'prefs' => array(),
		'params' => array()
	);
}
