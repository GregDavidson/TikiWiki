<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Step.php 60502 2016-12-01 11:50:43Z kroky6 $

interface Search_Action_Step
{
	function getFields();

	function validate(array $entry);

	function execute(array $entry);

  function requiresInput();
}

