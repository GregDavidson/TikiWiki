<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: load.php 57966 2016-03-17 20:05:33Z jonnybradley $

function prefs_load_list()
{
	return array (
		'load_threshold' => array(
			'name' => tra('Maximum average server load threshold in the last minute'),
			'description' => tra('Maximum average server load threshold in the last minute'),
			'type' => 'text',
			'filter' => 'digits',
			'size' => '3',
			'dependencies' => array(
				'use_load_threshold',
			),
			'default' => 3,
		),
	);
}
