<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: javascript.php 58872 2016-06-10 05:36:47Z nkoth $

function prefs_javascript_list()
{
	return array(
		'javascript_cdn' => array(
			'name' => tra('Use CDN for JavaScript'),
			'description' => tra('Obtain jQuery and jQuery UI libraries through a content delivery network (CDN).'),
			'type' => 'list',
			'options' => array(
				'none' => tra('None'),
				'google' => tra('Google (supports SSL via HTTPS)'),
				'jquery' => tra('jQuery'),
			),
			'default' => 'none',
			'tags' => array('basic'),
		),
		'javascript_assume_enabled' => array(
			'name' => tra('Assume JavaScript is enabled even if not supported'),
			'description' => tra('Assume JavaScript is enabled even if not supported, could be useful for load testing using tools like JMeter.'),
			'type' => 'flag',
			'default' => 'n',
			'tags' => array('advanced'),
		),
	);
}
