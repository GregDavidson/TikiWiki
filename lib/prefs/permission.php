<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: permission.php 57966 2016-03-17 20:05:33Z jonnybradley $

function prefs_permission_list()
{
	return array(
		'permission_denied_url' => array(
			'name' => tra('Send to URL'),
            'description' => tra('URL to redirect to on "permission denied"'),
			'type' => 'text',
			'size' => '50',
			'default' => '',
			'tags' => array('basic'),
		),
		'permission_denied_login_box' => array(
			'name' => tra('On "Permission denied", display the log-in module (for anonymous users)'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('basic'),
		),
	);
}
