<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Imap.php 57969 2016-03-17 20:07:40Z jonnybradley $

namespace Tiki\MailIn\Source;
use Tiki\MailIn\Exception\TransportException;
use Zend\Mail\Storage\Imap as ZendImap;
use Zend\Mail\Exception\ExceptionInterface as ZendMailException;

class Imap extends Pop3
{
	protected function connect()
	{
		try {
			$imap = new ZendImap([
				'host' => $this->host,
				'port' => $this->port,
				'user' => $this->username,
				'password' => $this->password,
				'ssl' => $this->port == 993,
			]);

			return $imap;
		} catch (ZendMailException $e) {
			throw new TransportException(tr("Login failed for IMAP account on %0:%1 for user %2", $this->host, $this->password, $this->username));
		}
	}
}
