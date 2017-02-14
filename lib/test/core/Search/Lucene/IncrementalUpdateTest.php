<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: IncrementalUpdateTest.php 57963 2016-03-17 20:03:23Z jonnybradley $

/**
 * @group unit
 */
abstract class Search_Lucene_IncrementalUpdateTest extends Search_Index_IncrementalUpdateTest
{
	private $dir;

	function setUp()
	{
		$this->dir = dirname(__FILE__) . '/test_index';
		$this->tearDown();

		$index = $this->getIndex();
		$this->populate($index);
	}

	function tearDown()
	{
		$this->getIndex()->destroy();
	}

	protected function getIndex()
	{
		return new Search_Lucene_Index($this->dir);
	}
}
