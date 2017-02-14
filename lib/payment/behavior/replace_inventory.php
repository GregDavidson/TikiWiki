<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: replace_inventory.php 57966 2016-03-17 20:05:33Z jonnybradley $

function payment_behavior_replace_inventory( $code, $quantity )
{
	$cartlib = TikiLib::lib('cart');
	$cartlib->change_inventory($code, $quantity);
	return true;
}

