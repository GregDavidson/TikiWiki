<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: IncrementalUpdateTest.php 58072 2016-03-25 04:42:35Z dlucio $

class Search_Elastic_IncrementalUpdateTest extends Search_Index_IncrementalUpdateTest
{
	protected $index;

	function setUp()
	{
		$this->index = $this->getIndex();
		$this->index->destroy();

		$this->populate($this->index);
	}

	protected function getIndex()
	{
		$connection = new Search_Elastic_Connection('http://localhost:9200');

		$status = $connection->getStatus();
		if (! $status->ok) {
			$this->markTestSkipped('Elasticsearch needs to be available on localhost:9200 for the test to run.');
		}

		return new Search_Elastic_Index($connection, 'test_index');
	}

	function tearDown()
	{
		if ($this->index) {
			$this->index->destroy();
		}
	}
}

