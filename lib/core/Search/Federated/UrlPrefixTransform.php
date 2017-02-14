<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: UrlPrefixTransform.php 57971 2016-03-17 20:09:05Z jonnybradley $

namespace Search\Federated;

class UrlPrefixTransform
{
	private $prefix;

	function __construct($prefix)
	{
		$this->prefix = rtrim($prefix, '/');
	}

	function __invoke($entry)
	{
		if (isset($entry['url'])) {
			$entry['url'] = $this->prefix . '/' . ltrim($entry['url'], '/');
			$entry['_external'] = true;
		}

		return $entry;
	}
}

