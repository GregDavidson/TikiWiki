<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Abstract.php 57971 2016-03-17 20:09:05Z jonnybradley $

abstract class Reports_Send_EmailBuilder_Abstract
{
	abstract protected function getTitle();
	abstract public function getOutput(array $changes);
}
