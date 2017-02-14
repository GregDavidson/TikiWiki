<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: 20100623_semantic_to_relation_tiki.php 57973 2016-03-17 20:10:42Z jonnybradley $

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * @param $installer
 */
function pre_20100623_semantic_to_relation_tiki($installer)
{
	$results = $installer->fetchAll('SELECT * FROM tiki_links WHERE reltype IS NOT NULL');

	foreach ($results as $row) {
		$reltypes = array_filter(explode(',', $row['reltype']));

		foreach ($reltypes as $type) {
			$installer->query(
				'INSERT INTO tiki_object_relations (relation, source_type, source_itemId, target_type, target_itemId) VALUES(?, ?, ?, ?, ?)',
				array(
					'tiki.link.' . $type,
					'wiki page',
					$row['fromPage'],
					'wiki page',
					$row['toPage'],
				)
			);
		}
	}
}

