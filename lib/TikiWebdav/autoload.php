<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: autoload.php 57972 2016-03-17 20:09:51Z jonnybradley $

require_once 'lib/ezcomponents/Base/src/base.php';

/**
 * Autoload ezc classes 
 * 
 * @param string $className 
 */
function webdav_autoload($className)
{
    ezcBase::autoload($className);
}

spl_autoload_register('webdav_autoload');
