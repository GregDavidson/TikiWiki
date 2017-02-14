<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Object.php 57971 2016-03-17 20:09:05Z jonnybradley $

class Perms_Reflection_Object implements Perms_Reflection_Container
{
	protected $factory;
	protected $type;
	protected $object;

	function __construct( $factory, $type, $object )
	{
		$this->factory = $factory;
		$this->type = $type;
		$this->object = $object;
	}

	function add( $group, $permission )
	{
		$userlib = TikiLib::lib('user');
		$userlib->assign_object_permission($group, $this->object, $this->type, $permission);
	}

	function remove( $group, $permission )
	{
		$userlib = TikiLib::lib('user');
		$userlib->remove_object_permission($group, $this->object, $this->type, $permission);
	}

	function getDirectPermissions()
	{
		$userlib = TikiLib::lib('user');
		$set = new Perms_Reflection_PermissionSet;

		$permissions = $userlib->get_object_permissions($this->object, $this->type);
		foreach ( $permissions as $row ) {
			$set->add($row['groupName'], $row['permName']);
		}

		return $set;
	}

	function getParentPermissions()
	{
		if ( $permissions = $this->getCategoryPermissions() ) {
			return $permissions;
		} else {
			return $this->factory->get('global', null)->getDirectPermissions();
		}
	}

	private function getCategoryPermissions()
	{
		$categories = $this->getCategories();

		$set = new Perms_Reflection_PermissionSet;
		$count = 0;
		foreach ( $categories as $category ) {
			$category = $this->factory->get('category', $category);
			foreach ( $category->getDirectPermissions()->getPermissionArray() as $group => $perms ) {
				foreach ($perms as $perm) {
					$set->add($group, $perm);
					++$count;
				}
			}
		}

		if ( $count != 0 ) {
			return $set;
		}
	}

	private function getCategories()
	{
		$categlib = TikiLib::lib('categ');

		return $categlib->get_object_categories($this->type, $this->object);
	}
}
