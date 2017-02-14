<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: sender.php 57966 2016-03-17 20:05:33Z jonnybradley $

function prefs_sender_list()
{
	return array(
		'sender_email' => array(
			'name' => tra('Sender email'),
			'description' => tra('Email address that will be used as the sender for outgoing emails.'),
			'type' => 'text',
			'size' => 40,
			'default' => '',
			'tags' => array('basic'),
		),
	);
}
