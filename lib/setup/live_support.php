<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: live_support.php 57965 2016-03-17 20:04:49Z jonnybradley $

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

$smarty->assign('user_is_operator', 'n');
if ( $user ) {
	include_once('lib/live_support/lsadminlib.php');
	if ( $lsadminlib->is_operator($user) ) {
		$smarty->assign('user_is_operator', 'y');
	}
}
