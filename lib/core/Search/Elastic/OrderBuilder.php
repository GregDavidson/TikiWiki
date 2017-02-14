<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: OrderBuilder.php 60625 2016-12-13 12:41:33Z jonnybradley $

class Search_Elastic_OrderBuilder
{
	function build(Search_Query_Order $order)
	{
		$component = '_score';
		$field = $order->getField();

		if ($field !== Search_Query_Order::FIELD_SCORE) {
			if ($order->getMode() == Search_Query_Order::MODE_NUMERIC) {
				$component = array(
					"$field.nsort" => $order->getOrder(),
				);
			} else if ($order->getMode() == Search_Query_Order::MODE_DISTANCE) {
				$arguments = $order->getArguments();

				$component = [
					"_geo_distance" => [
						'geo_point' => [
							'lat' => $arguments['lat'],
							'lon' => $arguments['lon'],
						],
						'order' => $order->getOrder(),
						'unit' => $arguments['unit'],
						'distance_type' => $arguments['distance_type'],
					],
				];
			} else {
				$component = array(
					"$field.sort" => $order->getOrder(),
				);
			}
		}

		return array(
			"sort" => array(
				$component,
			),
		);
	}
}

