<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: 20110727_tracker_multilingual_convert_tiki.php 57973 2016-03-17 20:10:42Z jonnybradley $

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * @param $installer
 */
function pre_20110727_tracker_multilingual_convert_tiki($installer)
{
	global $multilingual_tracker_content;
	global $multilingual_tracker_content_logs;
	$fields = $installer->table('tiki_tracker_fields');

	$multilingualFields = $fields->fetchColumn(
		'fieldId',
		array(
			'isMultilingual' => 'y',
			'type' => $fields->in(array('t', 'a')),
		)
	);

	$unilingualFields = $fields->fetchColumn(
		'fieldId',
		array(
			'isMultilingual' => $fields->not('y'),
			'type' => $fields->in(array('t', 'a')),
		)
	);

	$table = $installer->table('tiki_tracker_item_fields');

	// Clean up data that does not match the field definition
	$table->deleteMultiple(
		array(
			'fieldId' => $table->in($multilingualFields),
			'lang' => '',
		)
	);

	$table->deleteMultiple(
		array(
			'fieldId' => $table->in($unilingualFields),
			'lang' => $table->not(''),
		)
	);

	// Collect the data stored in the multilingual fields
	$result = $table->fetchAll(
		$table->all(),
		array(
			'lang' => $table->not(''),
			'fieldId' => $table->in($multilingualFields),
		)
	);

	$multilingual_tracker_content = array();
	foreach ($result as $row) {
		$itemId = $row['itemId'];
		$fieldId = $row['fieldId'];
		$lang = $row['lang'];
		$value = $row['value'];

		$multilingual_tracker_content[$itemId][$fieldId][$lang] = $value;
	}

	// Remove all affected data
	foreach ($multilingual_tracker_content as $itemId => $fields) {
		foreach ($fields as $fieldId => $data) {
			$table->deleteMultiple(
				array(
					'itemId' => $itemId,
					'fieldId' => $fieldId,
				)
			);
		}
	}

	// Similar treatment on logs, although less corruption is expected
	$table = $installer->table('tiki_tracker_item_field_logs');
	$result = $table->fetchAll($table->all(), array('lang' => $table->not(''),));
	$multilingual_tracker_content_logs = array();

	foreach ($result as $row) {
		$version = $row['version'];
		$itemId = $row['itemId'];
		$fieldId = $row['fieldId'];
		$lang = $row['lang'];
		$value = $row['value'];

		$multilingual_tracker_content_logs[$itemId][$version][$fieldId][$lang] = $value;
	}

	$table->deleteMultiple(array('lang' => $table->not(''),));
}

/**
 * @param $installer
 */
function post_20110727_tracker_multilingual_convert_tiki($installer)
{
	global $multilingual_tracker_content;
	global $multilingual_tracker_content_logs;

	// Insert back the data in a different format
	$table = $installer->table('tiki_tracker_item_fields');
	foreach ($multilingual_tracker_content as $itemId => $fields) {
		foreach ($fields as $fieldId => $data) {
			$table->insert(
				array(
					'itemId' => $itemId,
					'fieldId' => $fieldId,
					'value' => json_encode($data),
				)
			);
		}
	}

	$table = $installer->table('tiki_tracker_item_field_logs');
	foreach ($multilingual_tracker_content_logs as $itemId => $versions) {
		foreach ($versions as $version => $fields) {
			foreach ($fields as $fieldId => $data) {
				$table->insert(
					array(
						'version' => $version,
						'itemId' => $itemId,
						'fieldId' => $fieldId,
						'value' => json_encode($data),
					)
				);
			}
		}
	}

	$multilingual_tracker_content = null;
	$multilingual_tracker_content_logs = null;
}

