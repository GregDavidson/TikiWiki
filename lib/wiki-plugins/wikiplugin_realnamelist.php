<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_realnamelist.php 57961 2016-03-17 20:01:56Z jonnybradley $

function wikiplugin_realnamelist_info()
{
	return array(
		'name' => tra('User List with Real Names'),
		'documentation' => 'PluginRealNameList',
		'description' => tra('Show user real names for members of a group'),
		'prefs' => array( 'wikiplugin_realnamelist' ),
		'body' => tra('Group name - only users belonging to a group or groups with group names containing this text
			will be included in the list. If empty all site users will be included.'),
		'iconname' => 'user',
		'introduced' => 4,
		'params' => array(
			'sep' => array(
				'required' => false,
				'name' => tra('Separator'),
				'description' => tra('String to use between elements of the list if table layout is not used'),
				'since' => '4.0',
				'filter' => 'striptags',
				'default' => ', ',
			),
			'max' => array(
				'required' => false,
				'name' => tra('Maximum'),
				'description' => tra('Result limit'),
				'since' => '4.0',
				'filter' => 'int',
				'default' => -1,
			),
			'sort' => array(
				'required' => false,
				'name' => tra('Sort order'),
				'description' => tra('Set to sort in ascending or descending order (unsorted by default'),
				'since' => '4.0',
				'filter' => 'word',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Ascending'), 'value' => 'asc'), 
					array('text' => tra('Descending'), 'value' => 'desc')
				)
			),
			'layout' => array(
				'required' => false,
				'name' => tra('Layout'),
				'description' => tra('Set to table to show results in a table (not shown in a table by default)'),
				'since' => '4.0',
				'filter' => 'word',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Table'), 'value' => 'table')
				)
			),
			'link' => array(
				'required' => false,
				'name' => tra('Link'),
				'description' => tra('Make the listed names links to various types of user information'),
				'since' => '4.0',
				'filter' => 'word',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('User Information'), 'value' => 'userinfo'),
					array('text' => tra('User Page'), 'value' => 'userpage'),
					array('text' => tra('User Preferences'), 'value' => 'userpref')
				)
			),
			'exclude' => array(
				'required' => false,
				'name' => tra('Exclude'),
				'description' => tra('Exclude certain test or admin names from the list'),
				'since' => '4.0',
				'filter' => 'text',
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('admin'), 'value' => 'admin'),
					array('text' => tra('admin-test'), 'value' => 'admin-test'),
					array('text' => tra('test'), 'value' => 'test'),
					array('text' => tra('test-admin'), 'value' => 'test-admin')
				)
			)
		)
	);
}

function wikiplugin_realnamelist($data, $params)
{
	global $prefs, $tiki_p_admin, $tiki_p_admin_users;
	$userlib = TikiLib::lib('user');
	$tikilib = TikiLib::lib('tiki');

	extract($params, EXTR_SKIP);

	if (!isset($sep))
		$sep=', ';
	if (!isset($max)) {
		$numRows = -1;
	} else {
		$numRows = (int) $max;
	}

	if ($data) {
		$mid = 'g.`groupName` like ?';
		$groupjoin = ' LEFT JOIN `users_usergroups` g ON u.`userId` = g.`userId`';
		$findesc = '%' . $data . '%';
		$bindvars=array($findesc);
		$tableheader='users in group(s) containing ';
		$tableheader .= '*'.$data.'*:';
	} else {
		$mid = '1';
		$groupjoin = '';
		$bindvars=array();
		$tableheader='all users';
	}
	if (isset($sort)) {
		$sort=strtolower($sort);
		if (($sort=='asc') || ($sort=='desc')) {
			$mid .= ' ORDER BY `value`, `login` '.$sort;
		}
	}

	$exclude_clause='';
	if (isset($exclude)) {
		$exclude=strtolower($exclude);
		if (($exclude=='test') || ($exclude=='admin')) {
			$exclude_clause= ' u.`login` NOT LIKE \'%'.$exclude.'%\' AND ' ;
			//$exclude_clause= ' `users_users`.`login` NOT LIKE \'%'.$exclude.'%\' AND ' ;
		}
		if (($exclude=='test-admin') || ($exclude=='admin-test')) {
			$exclude_clause= ' u.`login` NOT LIKE \'%admin%\' AND u.`login` NOT LIKE \'%test%\' AND ';
			//$exclude_clause= ' `users_users`.`login` NOT LIKE \'%admin%\' AND `users_users`.`login` NOT LIKE \'%test%\' AND ';
		}
	}
	$pre=''; $post='';
	if (isset($layout)) {
		if ($layout=='table') {
			$pre='<table class=\'sortable\' id=\''.$tikilib->now.'\'><tr><th>'.tra($tableheader).'</th></tr><tr><td>';
			$sep = '</td></tr><tr><td>';
			$post='</td></tr></table>';
		}
	}

	$query = 'SELECT `login` , u.`userId` , `value` FROM `users_users` u'.$groupjoin.' LEFT JOIN `tiki_user_preferences` p ON p.`user` = u.`login` WHERE p.`prefName` = "realName" AND '.$exclude_clause.$mid; 

	$result = $tikilib->query($query, $bindvars, $numRows);
	$ret = array();

	while ($row = $result->fetchRow()) {
		$res = '';
		if (isset($link)) {
			if ($link == 'userpage') {
				if ($prefs['feature_wiki_userpage'] == 'y') {
					$wikilib = TikiLib::lib('wiki');
					$page = $prefs['feature_wiki_userpage_prefix'].$row['login'];
					if ($tikilib->page_exists($page)) {
						$res = '<a href="'.$wikilib->sefurl($page).'" title="'.tra('Page').'">';
					}
				}
			} elseif (isset($link) && $link == 'userpref') {
				if ($prefs['feature_userPreferences'] == 'y' && ($tiki_p_admin_users == 'y' || $tiki_p_admin == 'y')) {
					$res = '<a href="tiki-user_preferences.php?userId='.$row['userId'].'" title="'.tra('Preferences').'">';
				}
			} elseif (isset($link) && $link == 'userinfo') {
				if ($tiki_p_admin_users == 'y' || $tiki_p_admin == 'y') {
					$res = '<a href="tiki-user_information.php?userId='.$row['userId'].'" title="'.tra('User Information').'">';
				} else {
					$user_information = $tikilib->get_user_preference($row['login'], 'user_information', 'public');
					if (isset($user) && $user_information != 'private' && $row['login'] != $user) {
						$res = '<a href="tiki-user_information.php?userId='.$row['userId'].'" title="'.tra('User Information').'">';
					}
				}
			}
		}
		if ( $row['value'] != '' ) {
			$row['login'] = $row['value'];
		} else {
			$temp = $row['login'];
			$row['login']= '<i>'.$temp.'</i>';
		}
		$ret[] = $res.$row['login'].($res?'</a>':'');
	}
	return $pre.implode($sep, $ret).$post;
}
