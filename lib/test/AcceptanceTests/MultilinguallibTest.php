<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: MultilinguallibTest.php 58382 2016-04-20 08:59:56Z jonnybradley $

/**
 * @group gui
 */



class  AcceptanceTests_MultilinguallibTest extends TikiSeleniumTestCase
{
	protected function setUp()
	{
		$this->markTestSkipped("These tests are still too experimental, so skipping it.");    	
		$this->setBrowserUrl('http://localhost/');
		$this->current_test_db = "multilingualTestDump.sql";
		$this->restoreDBforThisTest();         
	}

	/**
	 * @group gui
	 */ 


}
