<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: nextprev.php 57966 2016-03-17 20:05:33Z jonnybradley $

function prefs_nextprev_list()
{
	return array(
		'nextprev_pagination' => array(
			'name' => tra('Use relative (next / previous) pagination links'),
            'description' => tra(''),
			'type' => 'flag',
			'default' => 'y',
		),
	);
}
