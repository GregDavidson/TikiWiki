<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: forums.php 57966 2016-03-17 20:05:33Z jonnybradley $

function prefs_forums_list()
{
	return array(
		'forums_ordering' => array(
			'name' => tra('Default Ordering'),
            'description' => tra(''),
			'type' => 'list',
			'options' => array(
				'created_asc' => tra('Creation date (asc)'),
				'created_desc' => tra('Creation Date (desc)'),
				'threads_desc' => tra('Topics (desc)'),
				'comments_desc' => tra('Threads (desc)'),
				'lastPost_desc' => tra('Latest post (desc)'),
				'hits_desc' => tra('Visits (desc)'),
				'name_desc' => tra('Name (desc)'),
				'name_asc' => tra('Name (asc)'),
			),
			'default' => 'created_desc',
		),
	);
}
