<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: TestFactoryTest.php 60529 2016-12-06 11:56:36Z kroky6 $

/**
 * @group unit
 *
 */

class Perms_ResolverFactory_TestFactoryTest extends TikiTestCase
{
	/**
	 * @dataProvider hashes
	 */
	function testHashCorrect($known, $in, $out)
	{
		$factory = new Perms_ResolverFactory_TestFactory($known, array());

		$this->assertEquals($out, $factory->getHash($in));
	}

	function hashes()
	{
		return array(
			'empty' => array(array(), array(), 'test:'),
			'exact' => array(array('a'), array('a' => 1), 'test:1'),
			'miss' => array(array('b'), array('a' => 1), 'test:'),
			'multiple' => array(array('a', 'b'), array('a' => 1, 'b' => 2), 'test:1:2'),
			'extra' => array(array('a'), array('a' => 1, 'b' => 2), 'test:1'),
			'ordering' => array(array('a', 'b'), array('b' => 1, 'a' => 2), 'test:2:1'),
		);
	}

	function testFetchKnown()
	{
		$factory = new Perms_ResolverFactory_TestFactory(
			array('a'),
			array('test:1' => $a = new Perms_Resolver_Default(true))
		);

		$this->assertSame($a, $factory->getResolver(array('a' => 1)));
	}

	function testFetchUnknown()
	{
		$factory = new Perms_ResolverFactory_TestFactory(
			array('a'),
			array('test:1' => $a = new Perms_Resolver_Default(true))
		);

		$this->assertNull($factory->getResolver(array('a' => 2)));
	}
}

