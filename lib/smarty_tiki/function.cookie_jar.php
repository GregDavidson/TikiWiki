<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.cookie_jar.php 57965 2016-03-17 20:04:49Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * smarty_function_cookie_jar: Get a cookie value from the Tiki Cookie Jar
 *
 * params:
 *	- name: Name of the cookie
 */
function smarty_function_cookie_jar($params, $smarty)
{
	if ( empty($params['name']) ) return;
	return getCookie($params['name']);
}
