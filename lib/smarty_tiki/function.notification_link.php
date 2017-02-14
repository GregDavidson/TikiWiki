<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.notification_link.php 57964 2016-03-17 20:04:05Z jonnybradley $

function smarty_function_notification_link($params)
{
	global $user, $prefs;

	if ($prefs['monitor_enabled'] != 'y') {
		return;
	}

	if (! $user) {
		return '';
	}

	$servicelib = TikiLib::lib('service');

	$smarty = TikiLib::lib('smarty');
	$smarty->assign('monitor_link', $params);
	return $smarty->fetch('monitor/notification_link.tpl');
}

