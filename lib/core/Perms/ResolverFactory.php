<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ResolverFactory.php 57971 2016-03-17 20:09:05Z jonnybradley $

interface Perms_ResolverFactory
{
	function getHash( array $context );
	function getResolver( array $context );
	function bulk( array $baseContext, $bulkKey, array $values );
}

