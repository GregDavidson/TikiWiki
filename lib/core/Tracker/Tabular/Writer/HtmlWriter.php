<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: HtmlWriter.php 57967 2016-03-17 20:06:16Z jonnybradley $

namespace Tracker\Tabular\Writer;

class HtmlWriter
{
	function __construct()
	{
	}

	function getData(\Tracker\Tabular\Source\SourceInterface $source)
	{
		$schema = $source->getSchema();
		$schema = $schema->getHtmlOutputSchema();

		$columns = $schema->getColumns();

		foreach ($source->getEntries() as $entry) {
			yield array_map(function ($column) use ($entry) {
				return $entry->render($column);
			}, $columns);
		}
	}
}

