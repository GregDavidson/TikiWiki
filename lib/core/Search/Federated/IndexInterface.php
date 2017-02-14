<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: IndexInterface.php 57971 2016-03-17 20:09:05Z jonnybradley $

namespace Search\Federated;

interface IndexInterface
{
	function getTransformations();

	function applyContentConditions(\Search_Query $query, $content);

	function applySimilarConditions(\Search_Query $query, $type, $object);
}

