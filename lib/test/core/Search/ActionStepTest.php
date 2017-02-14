<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ActionStepTest.php 59643 2016-09-08 19:20:40Z jonnybradley $

class Search_ActionStepTest extends PHPUnit_Framework_TestCase
{
	function testMissingField()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(array('hello' => true)));

		$step = new Search_Action_ActionStep($action, array());
		$this->assertFalse($step->validate(array()));
		$this->assertEquals(array('hello'), $step->getFields());
	}

	function testMissingValueButNotRequired()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(array('hello' => false)));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(array('hello' => null))))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, array());
		$this->assertTrue($step->validate(array()));
		$this->assertEquals(array('hello'), $step->getFields());
	}

	function testValueProvidedStaticInDefinition()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(array('hello' => true)));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(array('hello' => 'world'))))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, array('hello' => 'world'));
		$this->assertTrue($step->validate(array()));
		$this->assertEquals(array(), $step->getFields());
	}

	function testValueProvidedInEntryDirectly()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(array('hello' => true)));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(array('hello' => 'world'))))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, array());
		$this->assertTrue($step->validate(array('hello' => 'world')));
		$this->assertEquals(array('hello'), $step->getFields());
	}

	function testDefinitionDefersToSingleField()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(array('hello' => true)));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(array('hello' => 'world'))))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, array('hello_field' => 'test'));
		$this->assertTrue($step->validate(array('test' => 'world')));
		$this->assertEquals(array('test'), $step->getFields());
	}

	function testDefinitionCoalesceField()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(array('hello' => true)));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(array('hello' => 'right'))))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, array('hello_field_coalesce' => 'foo,bar,test,baz,hello'));
		$this->assertTrue($step->validate(array('test' => 'right', 'baz' => 'wrong')));
		$this->assertEquals(array('foo', 'bar', 'test', 'baz', 'hello'), $step->getFields());
	}

	function testDefinitionCoalesceFieldNoMatch()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(array('hello' => true)));

		$step = new Search_Action_ActionStep($action, array('hello_field_coalesce' => 'foo,bar,test,baz,hello'));
		$this->assertFalse($step->validate(array()));
		$this->assertEquals(array('foo', 'bar', 'test', 'baz', 'hello'), $step->getFields());
	}

	function testRequiresValueAsArrayButMissing()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(array('hello+' => false)));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(array('hello' => array()))))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, array());
		$this->assertTrue($step->validate(array()));
		$this->assertEquals(array('hello'), $step->getFields());
	}

	function testRequiresValueAsArrayAndSingleValue()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(array('hello+' => false)));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(array('hello' => array('world')))))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, array('hello' => 'world'));
		$this->assertTrue($step->validate(array()));
		$this->assertEquals(array(), $step->getFields());
	}

	function testRequiresValueAsArrayAndMultipleValues()
	{
		$action = $this->createMock('Search_Action_Action');
		$action->expects($this->any())
			->method('getValues')
			->will($this->returnValue(array('hello+' => false)));
		$action->expects($this->once())
			->method('validate')
			->with($this->equalTo(new JitFilter(array('hello' => array('a', 'b')))))
			->will($this->returnValue(true));

		$step = new Search_Action_ActionStep($action, array('hello_field_multiple' => 'foo,bar,baz'));
		$this->assertTrue($step->validate(array('foo' => 'a', 'baz' => 'b')));
		$this->assertEquals(array('foo', 'bar', 'baz'), $step->getFields());
	}
}

