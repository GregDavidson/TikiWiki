<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: middle.php 57966 2016-03-17 20:05:33Z jonnybradley $

function prefs_middle_list()
{
	return array(
		'middle_shadow_start' => array(
			'name' => tra('Middle shadow div start'),
            'description' => tra(''),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
		'middle_shadow_end' => array(
			'name' => tra('Middle shadow div end'),
            'description' => tra(''),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
	);	
}
