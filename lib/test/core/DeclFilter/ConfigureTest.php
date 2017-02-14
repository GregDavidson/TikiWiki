<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ConfigureTest.php 59642 2016-09-08 18:31:22Z jonnybradley $

/**
 * @group unit
 *
 */

class DeclFilter_ConfigureTest extends TikiTestCase
{
	function testSimple()
	{
		$configuration = array(
			array('staticKeyFilters' => array(
				'hello' => 'digits',
				'world' => 'alpha',
			)),
			array('staticKeyFiltersForArrays' => array(
				'foo' => 'digits',
			)),
			array('catchAllFilter' => new Zend\Filter\StringToUpper),
		);

		$filter = DeclFilter::fromConfiguration($configuration);

		$data = $filter->filter(
			array(
				'hello' => '123abc',
				'world' => '123abc',
				'foo' => array(
					'abc123',
					'def456',
				),
				'bar' => 'undeclared',
			)
		);

		$this->assertEquals($data['hello'], '123');
		$this->assertEquals($data['world'], 'abc');
		$this->assertContains('123', $data['foo']);
		$this->assertContains('456', $data['foo']);
		$this->assertEquals($data['bar'], 'UNDECLARED');
	}

	/**
	 * Triggered errors become exceptions...
	 * @expectedException PHPUnit_Framework_Error
	 */
	function testDisallowed()
	{
		$configuration = array(
			array('catchAllFilter' => new Zend\Filter\StringToUpper),
		);

		$filter = DeclFilter::fromConfiguration($configuration, array('catchAllFilter'));
	}

	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	function testMissingLevel()
	{
		$configuration = array(
			'catchAllUnset' => null,
		);

		$filter = DeclFilter::fromConfiguration($configuration);
	}

	function testUnsetSome()
	{
		$configuration = array(
			array('staticKeyUnset' => array('hello', 'world')),
			array('catchAllFilter' => new Zend\Filter\StringToUpper),
		);

		$filter = DeclFilter::fromConfiguration($configuration);

		$data = $filter->filter(
			array(
				'hello' => '123abc',
				'world' => '123abc',
				'bar' => 'undeclared',
			)
		);

		$this->assertFalse(isset($data['hello']));
		$this->assertFalse(isset($data['world']));
		$this->assertEquals($data['bar'], 'UNDECLARED');
	}

	function testUnsetOthers()
	{
		$configuration = array(
			array('staticKeyFilters' => array(
				'hello' => 'digits',
				'world' => 'alpha',
			)),
			array('catchAllUnset' => null),
		);

		$filter = DeclFilter::fromConfiguration($configuration);

		$data = $filter->filter(
			array(
				'hello' => '123abc',
				'world' => '123abc',
				'bar' => 'undeclared',
			)
		);

		$this->assertEquals($data['hello'], '123');
		$this->assertEquals($data['world'], 'abc');
		$this->assertFalse(isset($data['bar']));
	}

	function testFilterPattern()
	{
		$configuration = array(
			array('keyPatternFilters' => array(
				'/^hello/' => 'digits',
			)),
			array('keyPatternFiltersForArrays' => array(
				'/^fo+$/' => 'alpha',
			)),
		);

		$filter = DeclFilter::fromConfiguration($configuration);

		$data = $filter->filter(
			array(
				'hello123' => '123abc',
				'hello456' => '123abc',
				'world' => '123abc',
				'foo' => array(
					'abc123',
					'def456',
				),
			)
		);

		$this->assertEquals($data['hello123'], '123');
		$this->assertEquals($data['hello456'], '123');
		$this->assertEquals($data['world'], '123abc');
		$this->assertContains('abc', $data['foo']);
		$this->assertContains('def', $data['foo']);
	}

	function testUnsetPattern()
	{
		$configuration = array(
			array('keyPatternUnset' => array(
				'/^hello/',
			)),
		);

		$filter = DeclFilter::fromConfiguration($configuration);

		$data = $filter->filter(
			array(
				'hello123' => '123abc',
				'hello456' => '123abc',
				'world' => '123abc',
			)
		);

		$this->assertFalse(isset($data['hello123']));
		$this->assertFalse(isset($data['hello456']));
		$this->assertEquals($data['world'], '123abc');
	}
}
