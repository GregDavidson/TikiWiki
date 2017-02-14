<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Factory.php 57967 2016-03-17 20:06:16Z jonnybradley $

require_once('lib/language/WriteFile.php');
require_once('lib/language/File.php');

/**
 * Create WriteFile objects
 */
class Language_WriteFile_Factory
{
	/**
	 * Create a WriteFile object
	 * 
	 * @param string $filePath path to language.php file
	 * @return Language_WriteFile
	 */
	public function factory($filePath)
	{
		return new Language_WriteFile(new Language_File($filePath));
	}
}