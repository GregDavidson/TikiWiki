<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_sf.php 57961 2016-03-17 20:01:56Z jonnybradley $

define('SF_CACHE', 48); # in hours

function wikiplugin_sf_info()
{
	return array(
		'name' => tra('SourceForge'),
		'documentation' => 'PluginSF',
		'description' => tra('Creates a link to SourceForge tracker items'),
		'prefs' => array( 'wikiplugin_sf' ),
		'body' => tra('text'),
		'iconname' => 'link',
		'introduced' => 1,
		'params' => array(
			'groupid' => array(
				'required' => true,
				'name' => tra('Group ID'),
				'description' => tra('SourceForge project ID (shows as group_id in the URL of a tracker item'),
				'since' => '1',
				'filter' => 'digits',
				'default' => '',
			),
			'trackerid' => array(
				'required' => true,
				'name' => tra('Tracker ID'),
				'description' => tra('SourceForge tracker ID (shows as atid in the URL of a tracker item'),
				'since' => '1',
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'tracker',
			),
			'itemid' => array(
				'required' => true,
				'name' => tra('Item ID'),
				'description' => tra('SourceForge item ID (shows as aid in the URL of a tracker item'),
				'since' => '1',
				'filter' => 'digits',
				'default' => '',
				'profile_reference' => 'tracker_item',
			),
			'title' => array(
				'required' => false,
				'name' => tra('Link title'),
				'description' => tra('First part of link tooltip identifying the type of tracker item (bug, feature
					request, patch or support request).'),
				'filter' => 'alpha',
				'default' => 'Item',
				'since' => 7.0,
			),
			),
	);
}

function get_artifact_label($gid, $atid, $aid, $reload=false)
{
	$agent = $_SERVER['HTTP_USER_AGENT'];
	$cachefile = "temp/sftrackers.cache.$gid.$atid.$aid";
	$cachelimit = time() - 60*60*SF_CACHE;
	$url = 'http://sourceforge.net/tracker/index.php?func=detail&aid=' . $aid . '&group_id=' . $gid . '&atid=' . $atid;
	if (!is_file($cachefile)) $reload = true;
	$back = false;
	if ($reload or (filemtime($cachefile) < $cachelimit)) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt($ch, CURLOPT_REFERER, $url);
		$buffer = curl_exec($ch);
		curl_close($ch);
		if (preg_match("/<title>[^-]*-([^<]*)<\/title>/i", $buffer, $match)) {
			$fp = fopen($cachefile, "wb");
			fputs($fp, $match[1]);
			fclose($fp);
		} elseif (is_file($cachefile)) {
			$fp = fopen($cachefile, "rb");
			$back = fgets($fp);
			fclose($fp);
		}
	} else {
		$fp = fopen($cachefile, "rb");
		$back = fgets($fp, 4096);
		fclose($fp);
	}
	return $back;
}

function wikiplugin_sf($data, $params)
{	
	if (function_exists('curl_init')) {
		if (empty($params['itemid']) || empty($params['groupid']) || empty($params['trackerid'])) {
			return 'Plugin SF failed. One or more of the following parameters are missing: groupid, trackerid or itemid.';
		}
		$title = empty($params['title']) ? 'Item' : $params['title'];	
		$label = get_artifact_label($params['groupid'], $params['trackerid'], $params['itemid']);
		$back = '<a href="http://sourceforge.net/tracker/index.php?func=detail&aid=' . $params['itemid'] 
					. '&group_id=' . $params['groupid'] . '&atid=' . $params['trackerid'] 
					. '" target="_blank" title="' . $title . ' ' . $params['itemid'] 
					. '" class="wiki external" rel="external nofollow">' . $label . '</a>';
	} else {
		$back = 'Plugin SF failed: The php-curl module must be loaded to use this plugin.';
	}
	return $back;
}
