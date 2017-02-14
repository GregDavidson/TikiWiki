<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: BaseTest.php 59709 2016-09-15 11:07:20Z kroky6 $

class Search_Lucene_BaseTest extends Search_Index_BaseTest
{
	private $dir;

	function setUp()
	{
		$this->dir = dirname(__FILE__) . '/test_index';
		$this->tearDown();

		$index = new Search_Lucene_Index($this->dir);
		$this->populate($index);
		$this->index = $index;
	}

	function tearDown()
	{
		if ($this->index) {
			$this->index->destroy();
		}
	}

	protected function highlight($word)
	{
		return '<b style="color:black;background-color:#ff66ff">'.$word.'</b>';
	}
}

