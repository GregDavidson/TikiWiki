<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Synchronizable.php 57968 2016-03-17 20:06:57Z jonnybradley $

interface Tracker_Field_Synchronizable extends Tracker_Field_Interface
{
	function importRemote($value);

	function exportRemote($value);

	function importRemoteField(array $info, array $syncInfo);
}

