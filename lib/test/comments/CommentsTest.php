<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: CommentsTest.php 59950 2016-10-11 09:18:39Z kroky6 $

class CommentsTest extends TikiTestCase
{

	private $lib;

	function setUp()
	{
		$this->lib = TikiLib::lib('comments');
	}

	function testGetHref()
	{
		$this->assertEquals('tiki-index.php?page=HomePage&amp;threadId=9&amp;comzone=show#threadId9', $this->lib->getHref('wiki page', 'HomePage', 9));
		$this->assertEquals('tiki-view_blog_post.php?postId=1&amp;threadId=10&amp;comzone=show#threadId10', $this->lib->getHref('blog post', 1, 10));
	}

	function testGetRootPath()
	{
		$comments = $this->lib->table('tiki_comments');
		$parentId = $comments->insert(array(
			'objectType' => 'trackeritem',
			'object' => 1,
			'parentId' => 0
		));
		$childId = $comments->insert(array(
			'objectType' => 'trackeritem',
			'object' => 1,
			'parentId' => $parentId
		));
		$this->assertEquals(array(), $this->lib->get_root_path($parentId));
		$this->assertEquals(array($parentId), $this->lib->get_root_path($childId));
		$comments->delete(array('threadId' => $childId));
		$comments->delete(array('threadId' => $parentId));
	}
}

