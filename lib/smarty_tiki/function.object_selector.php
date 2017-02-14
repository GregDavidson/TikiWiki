<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: function.object_selector.php 57964 2016-03-17 20:04:05Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 * Variable arguments to be sent as filters for the object list. Filters match the unified search
 * field filters.
 *
 * Reserved parameters:
 *  - _id for the field ID
 *  - _class for the field classes
 *  - _name for the field name
 *  - _value for the current value (type:objectId)
 *  - _filter is the same as all other arguements, expecting an array
 *
 * The component will build a drop list for the object selector if the results fit in a reasonable amount
 * of space or will use autocomplete on the object title otherwise.
 */
function smarty_function_object_selector( $params, $smarty )
{
	global $prefs;
	static $uniqid = 0;

	$arguments = [
		'simpleid' => null,
		'simplename' => null,
		'simplevalue' => null,
		'simpleclass' => 'hidden',
		'name' => null,
		'class' => null,
		'id' => null,
		'value' => null,
		'filter' => [],
		'title' => null,
		'threshold' => null,
		'parent' => null,
		'parentkey' => null,
		'format' => null,
		'placeholder' => tr('Title'),
	];

	// Handle reserved parameters
	foreach (array_keys($arguments) as $var) {
		if (isset($params["_$var"])) {
			$arguments[$var] = $params["_$var"];
		}
		unset($params["_$var"]);
	}

	if ($prefs['feature_search'] !== 'y') {
		if ($arguments['simplename'] && isset($arguments['simplevalue'])) {
			if ($params['type'] === 'trackerfield' && $arguments['separator'] === ',') {
				$help = tra('Comma-separated list of field IDs');
			} else {
				$help = tr('%0 list separated with "%1"', ucfirst($params['type']), $arguments['separator']);
			}
			return "<input type='text' name='{$arguments['simplename']}' value='{$arguments['simplevalue']}' size='50'>" .
					"<div class='help-block'>" . $help . "</div>";
		} else {
			return tra('Object selector requires Unified Index to be enabled.');
		}
	}

	if (empty($arguments['id'])) {
		$arguments['id'] = 'object_selector_' . ++$uniqid;
	}
	if (empty($arguments['simpleid'])) {
		$arguments['simpleid'] = 'object_selector_' . ++$uniqid;
	}

	if ($arguments['filter']) {
		$arguments['filter'] = array_merge($arguments['filter'], $params);
	} else {
		$arguments['filter'] = $params;
	}

	if ($arguments['simplevalue'] && ! $arguments['value'] && isset($arguments['filter']['type'])) {
		$arguments['value'] = "{$arguments['filter']['type']}:{$arguments['simplevalue']}";
		$arguments['simpleclass'] = null;
		$arguments['class'] .= ' hidden';
	}

	$selector = TikiLib::lib('objectselector');
	$arguments['current_selection'] = $selector->read($arguments['value']);

	$arguments['filter'] = json_encode($arguments['filter']);

	$smarty->assign(
		'object_selector',
		$arguments
	);

	return $smarty->fetch('object_selector.tpl');
}

