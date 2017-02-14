<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: restrict.php 57966 2016-03-17 20:05:33Z jonnybradley $

function prefs_restrict_list()
{
	return array(
		'restrict_language' => array(
			'name' => tra('Restrict supported languages'),
			'description' => tra('Limit the languages made available on the site.'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('basic'),
		),
	);
}

