<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: AtLeastTest.php 57963 2016-03-17 20:03:23Z jonnybradley $

/**
 * @group unit
 *
 */

class Transition_AtLeastTest extends PHPUnit_Framework_TestCase
{
	function testOver()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(array('A', 'C', 'D', 'F'));
		$transition->addGuard('atLeast', 2, array('C', 'D', 'E', 'F', 'G'));

		$this->assertEquals(array(), $transition->explain());
	}

	function testRightOn()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(array('A', 'C', 'D', 'F'));
		$transition->addGuard('atLeast', 3, array('C', 'D', 'E', 'F', 'G'));

		$this->assertEquals(array(), $transition->explain());
	}

	function testUnder()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(array('A', 'C', 'D', 'F'));
		$transition->addGuard('atLeast', 4, array('C', 'D', 'E', 'F', 'G'));

		$this->assertEquals(
			array(array('class' => 'missing', 'count' => 1, 'set' => array('E', 'G')),),
			$transition->explain()
		);
	}

	function testImpossibleCondition()
	{
		$transition = new Tiki_Transition('A', 'B');
		$transition->setStates(array('A', 'C', 'D', 'F'));
		$transition->addGuard('atLeast', 4, array('C', 'D', 'E'));

		$this->assertEquals(
			array(array('class' => 'invalid', 'count' => 4, 'set' => array('C', 'D', 'E')),),
			$transition->explain()
		);
	}
}
