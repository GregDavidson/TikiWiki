<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Lazy.php 57968 2016-03-17 20:06:57Z jonnybradley $

class Tiki_Render_Lazy
{
	private $callback;
	private $data;

	function __construct($callback)
	{
		$this->callback = $callback;
	}

	function __toString()
	{
		if ($this->callback) {
			try {
				$this->data = call_user_func($this->callback);
			} catch (Exception $e) {
				$this->data = $e->getMessage();
			}
			$this->callback = null;
		}

		return (string) $this->data;
	}
}

