<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: show.php 57966 2016-03-17 20:05:33Z jonnybradley $

function prefs_show_list()
{
	return array(
		'show_available_translations' => array(
			'name' => tra('Display available translations'),
			'description' => tra('Display list of available languages and offer to switch languages or translate. This appears on wiki pages and articles action buttons.'),
			'type' => 'flag',
			'default' =>'y',
		),
	);
}

