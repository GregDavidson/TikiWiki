<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugininline.php 57965 2016-03-17 20:04:49Z jonnybradley $

function prefs_wikiplugininline_list($partial = false) 
{
	global $tikilib;
	$parserlib = TikiLib::lib('parser');
	
	$defaultInline = array(
		'file' => 'y',
		'getaccesstoken' => 'y',
		'googleanalytics' => 'y',
		'group' => 'y',
		'grouplist' => 'y',
		'mail' => 'y',
		'perm' => 'y',
		'smarty' => 'y',
		'trackeritemfield' => 'y',
		'transclude' => 'y',
		'zotero' => 'y',
	);

	if ($partial) {
		$out = array();
		$list = array();
		$alias = array();
		foreach ( glob('lib/wiki-plugins/wikiplugin_*.php') as $file ) {
			$base = basename($file);
			$plugin = substr($base, 11, -4);

			$list[] = $plugin;
		}

		global $prefs;
		if ( isset($prefs['pluginaliaslist']) ) {
			$alias = @unserialize($prefs['pluginaliaslist']);
			$alias = array_filter($alias);
		}
		$list = array_filter(array_merge($list, $alias));
		sort($list);

		foreach ( $list as $plugin ) {
			$preference = 'wikiplugininline_' . $plugin;
			$out[$preference] = array(
				'default' => isset($defaultInline[$plugin]) ? 'y' : 'n',
			);
		}

		return $out;
	}

	$prefs = array();

	foreach ( $parserlib->plugin_get_list() as $plugin ) {
		$info = $parserlib->plugin_info($plugin);

		$prefs['wikiplugininline_' . $plugin] = array(
			'name' => tr('Inline plugin %0', $info['name']),
			'description' => '',
			'type' => 'flag',
			'default' => isset($defaultInline[$plugin]) ? 'y' : 'n',
		);

		if (isset($info['tags'])) {
			$prefs['wikiplugininline_' . $plugin]['tags'] = (array) $info['tags'];
		}
	}

	return $prefs;
}
