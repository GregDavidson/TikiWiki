<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: PaginationTest.php 57963 2016-03-17 20:03:23Z jonnybradley $

class Search_MySql_PaginationTest extends Search_Index_PaginationTest
{
	function setUp()
	{
		$this->index = new Search_MySql_Index(TikiDb::get(), 'test_index');
		$this->index->destroy();
	}

	function tearDown()
	{
		if ($this->index) {
			$this->index->destroy();
		}
	}
}

