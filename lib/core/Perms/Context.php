<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Context.php 57971 2016-03-17 20:09:05Z jonnybradley $

class Perms_Context
{
	private static $permissionList = array();

	private $previousUser;
	private $previousGroupList;

	private $user;
	private $groupList = array();

	public static function setPermissionList($allperms)
	{
		$permissionList = array_keys($allperms);

		$shortPermList = array_map(function ($name) {
			return substr($name, 7);
		}, $permissionList);

		self::$permissionList = $shortPermList;
	}

	function __construct($user, $activate = true)
	{
		$tikilib = TikiLib::lib('tiki');
		$this->user = $user;
		$this->groupList = $tikilib->get_user_groups($user);

		if ($activate) {
			$this->activate();
		}
		// var_log(isset($tiki_p_edit), 'isset(tiki_p_edit)');
	}

	function overrideGroups(array $groupList)
	{
		$this->groupList = $groupList;
	}

	function activate($globalize = false)
	{
		global $user, $globalperms;
		// global $tiki_p_edit; // NGender: remove with var_log calls !!
		$perms = Perms::getInstance();
		$this->previousUser = $user;
		$this->previousGroupList = $perms->getGroups();
		$smarty = TikiLib::lib('smarty');
		$user = $this->user;
		$perms->setGroups($this->groupList);

		$globalperms = Perms::get();
		// var_log(isset($tiki_p_edit), 'isset(tiki_p_edit)');
		// var_log(self::$permissionList, 'self::permissionList');
		$globalperms->globalize(self::$permissionList, $smarty, false);
		// var_log(isset($tiki_p_edit), 'isset(tiki_p_edit)');
		if (is_object($smarty)) {
			$smarty->assign('globalperms', $globalperms);
			// var_log(isset($tiki_p_edit), 'isset(tiki_p_edit)');
		}
		// var_log(isset($tiki_p_edit), 'isset(tiki_p_edit)');
	}

	function __destruct()
	{
		global $user, $globalperms;
		$user = $this->previousUser;

		$perms = Perms::getInstance();
		$perms->setGroups($this->previousGroupList);
		$globalperms = Perms::get();
	}
}
