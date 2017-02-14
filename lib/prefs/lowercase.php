<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: lowercase.php 57966 2016-03-17 20:05:33Z jonnybradley $

function prefs_lowercase_list()
{
	return array(
		'lowercase_username' => array(
			'name' => tra('Force lowercase'),
            'description' => tra(''),
			'type' => 'flag',
			'help' => 'Login+Config#Case_Sensitivity',
			'default' => 'n',
		),
	);	
}
