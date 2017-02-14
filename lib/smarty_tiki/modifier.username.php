<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: modifier.username.php 57964 2016-03-17 20:04:05Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_modifier_username($user, $login_fallback = true, $check_user_show_realnames = true, $html_encoding = true)
{
	global $prefs;
	$userlib = TikiLib::lib('user');

	$return = $userlib->clean_user($user, ! $check_user_show_realnames, $login_fallback);
	
	if ($html_encoding) $return = htmlspecialchars($return);
	return $return;
}
