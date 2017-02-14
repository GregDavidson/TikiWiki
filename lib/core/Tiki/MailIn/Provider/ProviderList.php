<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ProviderList.php 57969 2016-03-17 20:07:40Z jonnybradley $

namespace Tiki\MailIn\Provider;

class ProviderList
{
	private $list = [];

	function addProvider(ProviderInterface $provider)
	{
		$this->list[] = $provider;
	}

	function getList()
	{
		usort($this->list, function ($a, $b) {
			return strcmp($a->getLabel(), $b->getLabel());
		});
		return $this->list;
	}
}

