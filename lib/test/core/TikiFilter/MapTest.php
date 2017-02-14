<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: MapTest.php 59665 2016-09-10 17:14:40Z jonnybradley $

/** 
 * @group unit
 * 
 */

class TikiFilter_MapTest extends TikiTestCase
{
	function testDirect()
	{
		$this->assertTrue(TikiFilter::get('digits') instanceof Zend\Filter\Digits);
		$this->assertTrue(TikiFilter::get('alpha') instanceof TikiFilter_Alpha);
		$this->assertTrue(TikiFilter::get('alnum') instanceof TikiFilter_Alnum);
		$this->assertTrue(TikiFilter::get('striptags') instanceof Zend\Filter\StripTags);
		$this->assertTrue(TikiFilter::get('pagename') instanceof Zend\Filter\StripTags);
		$this->assertTrue(TikiFilter::get('username') instanceof Zend\Filter\StripTags);
		$this->assertTrue(TikiFilter::get('groupname') instanceof Zend\Filter\StripTags);
		$this->assertTrue(TikiFilter::get('topicname') instanceof Zend\Filter\StripTags);
		$this->assertTrue(TikiFilter::get('xss') instanceof TikiFilter_PreventXss);
		$this->assertTrue(TikiFilter::get('word') instanceof TikiFilter_Word);
		$this->assertTrue(TikiFilter::get('wikicontent') instanceof TikiFilter_WikiContent);
	}

	function testKnown()
	{
		$this->assertTrue(TikiFilter::get(new Zend\I18n\Filter\Alnum) instanceof Zend\I18n\Filter\Alnum);
	}

	/**
	 * Triggered errors become exceptions...
	 * @expectedException PHPUnit_Framework_Error
	 */
	function testUnknown()
	{
		$this->assertTrue(TikiFilter::get('does_not_exist') instanceof TikiFilter_PreventXss);
	}

	function testComposed()
	{
		$filter = new JitFilter(array('foo' => 'test123'));
		$filter->replaceFilter('foo', 'digits');

		$this->assertEquals('123', $filter['foo']);
	}

	function testDefault()
	{
		$filter = new JitFilter(array('foo' => 'test123'));
		$filter->setDefaultFilter('digits');

		$this->assertEquals('123', $filter['foo']);
	}

	function testRaw()
	{
		$filter = new TikiFilter_RawUnsafe;
		$this->assertEquals('alert', $filter->filter('alert'));
	}
}
