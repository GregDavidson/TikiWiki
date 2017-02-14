<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: SubjectPrefixFactory.php 57969 2016-03-17 20:07:40Z jonnybradley $

namespace Tiki\MailIn\Action;
use Tiki\MailIn\Account;
use Tiki\MailIn\Source\Message;
use Tiki\MailIn\Exception\MailInException;

class SubjectPrefixFactory implements FactoryInterface
{
	private $config;

	function __construct($config)
	{
		$this->config = $config;
	}

	function createAction(Account $account, Message $message)
	{
		$subject = $message->getSubject();

		foreach ($this->config as $prefix => $factory) {
			if (empty($prefix) || strpos($subject, $prefix) === 0) {
				$subject = trim(substr($subject, strlen($prefix)));
				$message->setSubject($subject);

				return $factory->createAction($account, $message);
			}
		}

		throw new MailInException(tr("Unable to find suitable action."));
	}
}

