<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_groupexpiry.php 57962 2016-03-17 20:02:39Z jonnybradley $

function wikiplugin_groupexpiry_info()
{
	return array(
		'name' => tra('Group Expiration'),
		'documentation' => 'PluginGroupExpiry',
		'description' => tra('Show the expiration date of a group the user belongs to'),
		'prefs' => array( 'wikiplugin_groupexpiry' ),
		'iconname' => 'group',
		'introduced' => 7,
		'params' => array(
			'group' => array(
				'required' => true,
				'name' => tra('Group Name'),
				'description' => tra('The name of an existing group on the site'),
				'since' => '7.0',
				'filter' => 'groupname',
			),
		),
	);
}

function wikiplugin_groupexpiry($data, $params)
{
	global $user;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');
	extract($params, EXTR_SKIP);	
	$groups = $userlib->get_user_groups($user);
	if (!in_array($group, $groups)) {
		return tra("not in group");
	}
	$userId = $tikilib->get_user_id($user);
	$date = $tikilib->getOne('SELECT `expire` FROM `users_usergroups` where `userId` = ? AND `groupName` = ?', array($userId, $group));
	if (!$date) {
		return tra("never expires");
	} else {
		return '~np~' . $tikilib->get_long_datetime($date) . '~/np~';
	}	
}
