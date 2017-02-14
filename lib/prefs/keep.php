<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: keep.php 57966 2016-03-17 20:05:33Z jonnybradley $

function prefs_keep_list()
{
	return array(
		'keep_versions' => array(
			'name' => tra('Keep versions for'),
            'description' => tra('Never delete versions younger than (? days), default=1'),
			'type' => 'text',
			'size' => '5',
			'shorthint' => tra('days'),
			'default' => 1,
		),
	);	
}
