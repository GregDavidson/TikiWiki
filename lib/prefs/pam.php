<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: pam.php 57966 2016-03-17 20:05:33Z jonnybradley $

function prefs_pam_list()
{
	return array(
		'pam_create_user_tiki' => array(
			'name' => tra('Create user if not already a registered user'),
            'description' => tra(''),
			'type' => 'flag',
			'default' => 'n',
		),
		'pam_skip_admin' => array(
			'name' => tra('Use Tiki authentication for Admin login'),
            'description' => tra(''),
			'type' => 'flag',
			'default' => 'n',
		),
		'pam_service' => array(
			'name' => tra('PAM service'),
            'description' => tra(''),
			'type' => 'text',
			'size' => 20,
			'hint' => tra('Currently unused'),
			'default' => '',
		),
	);	
}
