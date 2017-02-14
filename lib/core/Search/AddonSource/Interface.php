<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Interface.php 57971 2016-03-17 20:09:05Z jonnybradley $

interface Search_AddonSource_Interface
{
	/*
	 * Returns a boolean for whether or not the AddonSource should be indexing for this particular item
	 */
	function toIndex($objectType, $objectId, $data);

	function getData($objectType, $objectId, Search_Type_Factory_Interface $typeFactory, array $data = array());

	function getProvidedFields();
	
	function getGlobalFields();
}

