<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Interface.php 57968 2016-03-17 20:06:57Z jonnybradley $

interface Tracker_Field_Interface
{
	public static function getTypes();

	/**
	 * Optional method for implementations supporting multiple implementations or needing custom construction.
	 *
	 * public static function build($type, $trackerDefinition, $fieldInfo, $itemData);
	 */

	/**
	 * return the values of a field (not necessarily the html that will be displayed) for input or output
	 * The values come from either the requestData if defined, the database if defined or the default
	 * @param array something like $_REQUEST
	 * @return 
	 */
	function getFieldData(array $requestData = array());

	/**
	 * return the html of the input form for a field
	 *  either call renderTemplate if using a tpl or use php code
	 * @param
	 * @return html
	*/
	function renderInput($context = array());

	/**
	 * return the html for the output of a field
	 *  with the link, prepend, append....
	 *  Use renderInnerOutput
	 * @param
	 * @return html
	*/
	function renderOutput($context = array());

	/**
	 * Generate the plain text comparison to include in the watch email.
	 */
	function watchCompare($old, $new);

	//function handleSave($value, $oldValue);

	//function isValid($ins_fields_data);
}

