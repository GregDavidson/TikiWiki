<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: BasicTest.php 57963 2016-03-17 20:03:23Z jonnybradley $

/**
 * @group unit
 *
 */

class Transition_BasicTest extends PHPUnit_Framework_TestCase
{
	function testSimpleTransition()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(array('A'));

		$this->assertTrue($transition->isReady());
	}

	function testAlreadyInTarget()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(array('B'));

		$this->assertFalse($transition->isReady());
	}

	function testInBoth()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(array('A', 'B'));

		$this->assertFalse($transition->isReady());
	}

	function testExplainWhenReady()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(array('A'));

		$this->assertEquals(array(), $transition->explain());
	}

	function testExplainWhenOriginNotMet()
	{
		$transition = new Tiki_Transition('A', 'B');

		$this->assertEquals(
			array(array('class' => 'missing', 'count' => 1, 'set' => array('A')),),
			$transition->explain()
		);
	}

	function testExplainWhenInTarget()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(array('A', 'B'));

		$this->assertEquals(
			array(array('class' => 'extra', 'count' => 1, 'set' => array('B')),),
			$transition->explain()
		);
	}

	function testAddUnknownGuardType()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(array('A'));
		$transition->addGuard('foobar', 5, array('D', 'E', 'F'));

		$this->assertEquals(
			array(array('class' => 'unknown', 'count' => 1, 'set' => array('foobar')),),
			$transition->explain()
		);
	}

	function testAddPassingCustomGuard()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(array('A', 'C', 'F'));
		$transition->addGuard('exactly', 2, array('C', 'D', 'E', 'F'));

		$this->assertTrue($transition->isReady());
	}

	function testAddFailingCustomGuard()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(array('A', 'C', 'F'));
		$transition->addGuard('exactly', 4, array('C', 'D', 'E', 'F', 'G'));

		$this->assertEquals(
			array(array('class' => 'missing', 'count' => 2, 'set' => array('D', 'E', 'G')),),
			$transition->explain()
		);
	}

	function testImpossibleCondition()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(array('A', 'C', 'D', 'F'));
		$transition->addGuard('exactly', 4, array('C', 'D', 'E'));

		$this->assertEquals(
			array(array('class' => 'invalid', 'count' => 4, 'set' => array('C', 'D', 'E')),),
			$transition->explain()
		);
	}
}
