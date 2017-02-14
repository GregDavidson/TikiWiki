<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_agentinfo.php 57962 2016-03-17 20:02:39Z jonnybradley $

function wikiplugin_agentinfo_info()
{
	return array(
		'name' => tra('User Agent Info'),
		'documentation' => 'PluginAgentinfo',
		'description' => tra('Show user\'s browser and server information'),
		'prefs' => array('wikiplugin_agentinfo'),
		'introduced' => 1,
		'iconname' => 'computer',
		'params' => array(
			'info' => array(
				'required' => false,
				'name' => tra('Info'),
				'description' => tra('Display\'s the visitor\'s IP address (IP or default), browser information (BROWSER), or server software (SVRSW).'),
				'default' => 'IP',
				'filter' => 'alpha',
				'since' => '1',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('IP address'), 'value' => 'IP'), 
					array('text' => tra('Server software'), 'value' => 'SVRSW'), 
					array('text' => tra('Browser'), 'value' => 'BROWSER'), 
				),
				
			),
		),
	);
}

function wikiplugin_agentinfo($data, $params)
{
	global $tikilib;
	
	extract($params, EXTR_SKIP);

	$asetup = '';

	if (!isset($info)) {
		$info = 'IP';
	}

	if ($info == 'IP') {
		$asetup = $tikilib->get_ip_address();
	}

	if ($info == 'SVRSW' && isset($_SERVER['SERVER_SOFTWARE'])) {
		$asetup = $_SERVER["SERVER_SOFTWARE"];
	}
	
	if ($info == 'BROWSER' && isset($_SERVER['HTTP_USER_AGENT'])) {
		$asetup = $_SERVER["HTTP_USER_AGENT"];
	}

	return $asetup;
}
