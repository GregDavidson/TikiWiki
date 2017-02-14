<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_tr.php 57961 2016-03-17 20:01:56Z jonnybradley $

function wikiplugin_tr_info()
{
	return array(
		'name' => tra('Translate'),
		'documentation' => 'PluginTR',
		'description' => tra('Translate text to the user language'),
		'prefs' => array( 'feature_multilingual', 'wikiplugin_tr' ),
		'body' => tra('string'),
		'iconname' => 'language',
		'introduced' => 2,
		'params' => array(
		),
	);
}

function wikiplugin_tr($data)
{
	return tra($data);
}
