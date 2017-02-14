<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: footer.php 57966 2016-03-17 20:05:33Z jonnybradley $

function prefs_footer_list()
{
	return array(
		'footer_shadow_start' => array(
			'name' => tra('Footer shadow div start'),
            'description' => tra(''),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
		'footer_shadow_end' => array(
			'name' => tra('Footer shadow div end'),
            'description' => tra(''),
			'type' => 'textarea',
			'size' => '2',
			'default' => '',
		),
	);	
}
