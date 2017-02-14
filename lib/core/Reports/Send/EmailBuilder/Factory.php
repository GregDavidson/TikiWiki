<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Factory.php 57971 2016-03-17 20:09:05Z jonnybradley $

class Reports_Send_EmailBuilder_Factory
{
	/**
	 * Build childs of Reports_Send_EmailBuilder_Abstract
	 * based on the $eventName.
	 * @param string $eventName
	 * @return mixed
	 */
	public function build($eventName)
	{
		$className = 'Reports_Send_EmailBuilder_' .
								str_replace(' ', '', ucwords(str_replace('_', ' ', $eventName)));
		
		if (class_exists($className)) {
			return new $className;
		} else {
			throw new Exception("There is no class to handle the event $eventName.");
		}
	}
}
