<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: cart_gift_certificate_refund.php 57966 2016-03-17 20:05:33Z jonnybradley $

function payment_behavior_cart_gift_certificate_refund(
		$giftcertId = 0,
		$giftcertMode = '',
		$giftcertAmount = 0,
		$giftcertDiscount = 0
		)
{
	$cartlib = TikiLib::lib('cart');
	global $prefs;

	if ($giftcertMode == "Percentage" || $giftcertMode == "Coupon Percentage") {
		$cartlib->set_tracker_value_custom(
			$prefs['payment_cart_giftcert_tracker_name'],
			"Current Balance or Percentage",
			$giftcertId,
			$giftcertAmount
		);
	} else {
		$currentBalance = $cartlib->get_tracker_value_custom(
			$prefs['payment_cart_giftcert_tracker_name'],
			"Current Balance or Percentage",
			$giftcertId
		);
		$cartlib->set_tracker_value_custom(
			$prefs['payment_cart_giftcert_tracker_name'],
			"Current Balance or Percentage",
			$giftcertId,
			$currentBalance + $giftcertDiscount
		);
	}
	return true;
}
