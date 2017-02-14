<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: BaseTest.php 60529 2016-12-06 11:56:36Z kroky6 $

/**
 * @group unit
 *
 */

class Perms_BaseTest extends TikiTestCase
{
	function testWithoutConfiguration()
	{
		Perms::set(new Perms);
		$accessor = Perms::get();

		$expect = new Perms_Accessor;

		$this->assertEquals($expect, $accessor);
	}

	function testGroupsPropagateToAccessor()
	{
		$perms = new Perms;
		$perms->setGroups(array('Registered', 'Administrator'));
		Perms::set($perms);

		$expect = new Perms_Accessor;
		$expect->setGroups(array('Registered', 'Administrator'));

		$this->assertEquals($expect, Perms::get());
	}

	function testContextPropagatesToAccessor()
	{
		$accessor = Perms::get(array('context'));

		$this->assertEquals(array('context'), $accessor->getContext());
	}

	function testWithoutArrayContext()
	{
		$expect = Perms::get(array('type' => 'wiki page', 'object' => 'HomePage'));
		$accessor = Perms::get('wiki page', 'HomePage');

		$this->assertEquals($expect, $accessor);
	}

	/**
	 * @dataProvider resolverMatches
	 */
	function testResolverFactoryChaining($context, $expectedResolver)
	{
		$perms = new Perms;

		$perms->setResolverFactories(
			array(
				new Perms_ResolverFactory_TestFactory(
					array('object'),
					array(
						'test:a' => $rA = new Perms_Resolver_Default(true),
						'test:b' => $rB = new Perms_Resolver_Default(true),
					)
				),
				new Perms_ResolverFactory_TestFactory(
					array('category'),
					array(
						'test:1' => $r1 = new Perms_Resolver_Default(true),
						'test:2' => $r2 = new Perms_Resolver_Default(true),
					)
				),
				new Perms_ResolverFactory_TestFactory(
					array(),
					array('test:' => $rG = new Perms_Resolver_Default(true),)
				),
			)
		);
		Perms::set($perms);

		$this->assertSame($$expectedResolver, Perms::get($context)->getResolver());
	}

	function resolverMatches()
	{
		return array(
			'testObjectA' => array(array('object' => 'a'), 'rA'),
			'testObjectB' => array(array('object' => 'b'), 'rB'),
			'testCategoryIgnoredWhenObjectMatches' => array(array('object' => 'b', 'category' => '1'), 'rB'),
			'testCategoryObtainOnObjectMiss' => array(array('object' => 'c', 'category' => '1'), 'r1'),
			'testCategoryOnly' => array(array('category' => '2'), 'r2'),
			'testObjectAndCategoryMiss' => array(array('object' => 'd', 'category' => '3'), 'rG'),
			'testNoContext' => array(array(), 'rG'),
		);
	}

	function testResolverNotCalledTwiceWhenFound()
	{
		$mock = $this->createMock('Perms_ResolverFactory');

		$mock->expects($this->exactly(2))
			->method('getHash')
			->will($this->returnValue('123'));

		$mock->expects($this->once())
			->method('getResolver')
			->will($this->returnValue(new Perms_Resolver_Default(true)));

		$perms = new Perms;
		$perms->setResolverFactories(array($mock,));
		Perms::set($perms);

		Perms::get();
		Perms::get();
	}

	function testResolverNotCalledTwiceWhenNotFound()
	{
		$mock = $this->createMock('Perms_ResolverFactory');

		$mock->expects($this->exactly(2))
			->method('getHash')
			->will($this->returnValue('123'));

		$mock->expects($this->once())
			->method('getResolver')
			->will($this->returnValue(false));

		$perms = new Perms;
		$perms->setResolverFactories(array($mock,));
		Perms::set($perms);

		Perms::get();
		Perms::get();
	}

	function testBulkLoading()
	{
		$mockObject = $this->createMock('Perms_ResolverFactory');
		$mockCategory = $this->createMock('Perms_ResolverFactory');
		$mockGlobal = $this->createMock('Perms_ResolverFactory');

		$perms = new Perms;
		$perms->setResolverFactories(array($mockObject, $mockCategory, $mockGlobal));
		Perms::set($perms);

		$mockObject->expects($this->once())
			->method('bulk')
			->with($this->equalTo(array('type' => 'wiki page')), $this->equalTo('object'), $this->equalTo(array('A', 'B', 'C', 'D', 'E')))
			->will($this->returnValue(array('A', 'C', 'E')));
		$mockCategory->expects($this->once())
			->method('bulk')
			->with($this->equalTo(array('type' => 'wiki page')), $this->equalTo('object'), $this->equalTo(array('A', 'C', 'E')))
			->will($this->returnValue(array('C')));
		$mockGlobal->expects($this->once())
			->method('bulk')
			->with($this->equalTo(array('type' => 'wiki page')), $this->equalTo('object'), $this->equalTo(array('C')))
			->will($this->returnArgument(0));

		$data = array(
			array('pageId' => 1, 'pageName' => 'A', 'content' => 'Hello World'),
			array('pageId' => 2, 'pageName' => 'B', 'content' => 'Hello World'),
			array('pageId' => 3, 'pageName' => 'C', 'content' => 'Hello World'),
			array('pageId' => 4, 'pageName' => 'D', 'content' => 'Hello World'),
			array('pageId' => 5, 'pageName' => 'E', 'content' => 'Hello World'),
		);

		Perms::bulk(array('type' => 'wiki page'), 'object', $data, 'pageName');
	}

	function customHash($context)
	{
		return serialize($context);
	}

	function testFiltering()
	{
		$perms = new Perms;
		$perms->setResolverFactories(
			array(
				new Perms_ResolverFactory_TestFactory(
					array('object'),
					array(
						'test:A' => new Perms_Resolver_Default(true),
						'test:B' => new Perms_Resolver_Default(true),
						'test:C' => new Perms_Resolver_Default(false),
						'test:D' => new Perms_Resolver_Default(false),
						'test:E' => new Perms_Resolver_Default(true),
					)
				),
			)
		);
		Perms::set($perms);

		$data = array(
			array('pageId' => 1, 'pageName' => 'A', 'content' => 'Hello World', 'creator' => 'admin'),
			array('pageId' => 2, 'pageName' => 'B', 'content' => 'Hello World', 'creator' => 'admin'),
			array('pageId' => 3, 'pageName' => 'C', 'content' => 'Hello World', 'creator' => 'admin'),
			array('pageId' => 4, 'pageName' => 'D', 'content' => 'Hello World', 'creator' => 'admin'),
			array('pageId' => 5, 'pageName' => 'E', 'content' => 'Hello World', 'creator' => 'admin'),
		);

		$out = Perms::filter(
			array('type' => 'wiki page'),
			'object',
			$data,
			array('object' => 'pageName', 'creator' => 'creator'),
			'view'
		);

		$expect = array(
			array('pageId' => 1, 'pageName' => 'A', 'content' => 'Hello World', 'creator' => 'admin'),
			array('pageId' => 2, 'pageName' => 'B', 'content' => 'Hello World', 'creator' => 'admin'),
			array('pageId' => 5, 'pageName' => 'E', 'content' => 'Hello World', 'creator' => 'admin'),
		);

		$this->assertEquals($expect, $out);
	}

	function testContextBuilding()
	{
		$perms = new Perms;
		$perms->setResolverFactories(
			array($mock = $this->createMock('Perms_ResolverFactory'))
		);
		Perms::set($perms);

		$mock->expects($this->once())
			->method('getHash')
			->with($this->equalTo(array('type' => 'wiki page', 'object' => 'Hello World', 'creator' => 'admin')))
			->will($this->returnValue(null));
		$mock->expects($this->once())
			->method('bulk');

		$data = array(
			array('pageId' => 1, 'pageName' => 'Hello World', 'content' => 'Hello World', 'creator' => 'admin'),
		);

		Perms::filter(array('type' => 'wiki page'), 'object', $data, array('object' => 'pageName', 'creator' => 'creator'), 'view');
	}

	function testSkipBulkOnEmptySet()
	{
		$perms = new Perms;
		$perms->setResolverFactories(
			array($mock = $this->createMock('Perms_ResolverFactory'))
		);
		Perms::set($perms);

		$mock->expects($this->never())
			->method('bulk');
	}
}

