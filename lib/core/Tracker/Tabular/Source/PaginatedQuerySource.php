<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: PaginatedQuerySource.php 57968 2016-03-17 20:06:57Z jonnybradley $

namespace Tracker\Tabular\Source;

class PaginatedQuerySource extends QuerySource
{
	private $resultset;

	function getEntries()
	{
		$result = $this->getResultSet();

		foreach ($result as $row) {
			yield new QuerySourceEntry($row);
		}
	}

	function getResultSet()
	{
		if (! $this->resultset) {
			$lib = \TikiLib::lib('unifiedsearch');

			$this->resultset = $this->query->search($lib->getIndex());
		}

		return $this->resultset;
	}
}

