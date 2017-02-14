<?php
// ===================================================================================================
//                           _  __     _ _
//                          | |/ /__ _| | |_ _  _ _ _ __ _
//                          | ' </ _` | |  _| || | '_/ _` |
//                          |_|\_\__,_|_|\__|\_,_|_| \__,_|
//
// This file is part of the Kaltura Collaborative Media Suite which allows users
// to do with audio, video, and animation what Wiki platfroms allow them to do with
// text.
//
// Copyright (C) 2006-2016  Kaltura Inc.
//
// This file has been included in the Tiki distribution with special permission 
// from Kaltura Inc. for the convenience of Tiki users. It is not LGPL licensed. 
// Please obtain your own copy from http://kaltura.org if you need it for any other purpose.
//
// @ignore
// ===================================================================================================

/**
 * @package Kaltura
 * @subpackage Client
 */
require_once(dirname(__FILE__) . "/../KalturaClientBase.php");
require_once(dirname(__FILE__) . "/../KalturaEnums.php");
require_once(dirname(__FILE__) . "/../KalturaTypes.php");
require_once(dirname(__FILE__) . "/KalturaScheduledTaskClientPlugin.php");
require_once(dirname(__FILE__) . "/KalturaMetadataClientPlugin.php");

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaExecuteMetadataXsltObjectTask extends KalturaObjectTask
{
	/**
	 * Metadata profile id to lookup the metadata object
	 * 	 
	 *
	 * @var int
	 */
	public $metadataProfileId = null;

	/**
	 * Metadata object type to lookup the metadata object
	 * 	 
	 *
	 * @var KalturaMetadataObjectType
	 */
	public $metadataObjectType = null;

	/**
	 * The XSLT to execute
	 * 	 
	 *
	 * @var string
	 */
	public $xslt = null;


}

/**
 * @package Kaltura
 * @subpackage Client
 */
class KalturaScheduledTaskMetadataClientPlugin extends KalturaClientPlugin
{
	protected function __construct(KalturaClient $client)
	{
		parent::__construct($client);
	}

	/**
	 * @return KalturaScheduledTaskMetadataClientPlugin
	 */
	public static function get(KalturaClient $client)
	{
		return new KalturaScheduledTaskMetadataClientPlugin($client);
	}

	/**
	 * @return array<KalturaServiceBase>
	 */
	public function getServices()
	{
		$services = array(
		);
		return $services;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return 'scheduledTaskMetadata';
	}
}

