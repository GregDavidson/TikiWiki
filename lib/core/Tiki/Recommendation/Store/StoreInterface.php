<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: StoreInterface.php 57968 2016-03-17 20:06:57Z jonnybradley $

namespace Tiki\Recommendation\Store;

use Tiki\Recommendation\Recommendation;
use Tiki\Recommendation\RecommendationSet;

interface StoreInterface
{
	function isReceived($input, Recommendation $recommendation);

	function store($input, RecommendationSet $recommendation);

	function getInputs();

	function terminate();
}
