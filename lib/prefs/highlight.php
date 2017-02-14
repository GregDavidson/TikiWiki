<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: highlight.php 57966 2016-03-17 20:05:33Z jonnybradley $


function prefs_highlight_list($partial = false)
{
	return array(
		'highlight_group' => array(
			'name' => tra('Highlight group'),
            'description' => tra(''),
			'help' => 'Groups',
			'type' => 'list',
			'options' => highlight_group_values($partial),
			'default' => '0',
		),
	);
}

/**
 * highlight_group_values
 * 
 * @access public
 * @return array: group list with '0' => None as added value
 */
function highlight_group_values($partial)
{
	$userlib = TikiLib::lib('user');

	if ($partial) {
		return false;
	}

	$listgroups = $userlib->get_groups(0, -1, 'groupName_desc', '', '', 'n');

	$dropdown_listgroups = array();
	$dropdown_listgroups['0'] = tra('None');

	foreach ($listgroups['data'] as $onegroup) {
		$dropdown_listgroups[$onegroup['groupName']] =substr($onegroup['groupName'], 0, 50);
	}

	return $dropdown_listgroups;
}
