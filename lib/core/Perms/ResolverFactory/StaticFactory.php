<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: StaticFactory.php 57971 2016-03-17 20:09:05Z jonnybradley $

/**
 * Simple factory always providing the same resolver. Used to provide
 * a grant-all resolver to admin users.
 */
class Perms_ResolverFactory_StaticFactory implements Perms_ResolverFactory
{
	private $key;
	private $resolver;

	function __construct( $key, $resolver )
	{
		$this->key = $key;
		$this->resolver = $resolver;
	}

	function bulk( array $baseContext, $bulkKey, array $values )
	{
		return array();
	}

	function getHash( array $context )
	{
		return $this->key;
	}

	function getResolver( array $context )
	{
		return $this->resolver;
	}
}
