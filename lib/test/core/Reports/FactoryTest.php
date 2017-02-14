<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: FactoryTest.php 57963 2016-03-17 20:03:23Z jonnybradley $

class Reports_FactoryTest extends TikiTestCase
{
	public function testBuild_shouldReturnInstances()
	{
		$classes = array('Reports_Users', 'Reports_Cache', 'Reports_Manager', 'Reports_Send');
		
		foreach ($classes as $className) {
			$this->assertInstanceOf($className, Reports_Factory::build($className));
		}
	}
	
	public function testBuild_shouldThrowExceptionForUnknownClass()
	{
		$this->setExpectedException('Exception', 'Unknown class Unknown_Class');
		Reports_Factory::build('Unknown_Class');
	}
}