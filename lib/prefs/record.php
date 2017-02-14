<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: record.php 57966 2016-03-17 20:05:33Z jonnybradley $

function prefs_record_list()
{
	return array(
		'record_untranslated' => array(
			'name' => tra('Record untranslated strings'),
			'description' => tra('Keep track of the unsuccessful attemps to translate strings.'),
			'type' => 'flag',
			'default' => 'n',
		),
	);
}
