<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: user_dummy3.php 57961 2016-03-17 20:01:56Z jonnybradley $

require_once('lib/wizard/wizard.php');

/**
 * Set up the wysiwyg editor, including inline editing
 */
class UserWizardDummy3 extends Wizard 
{
	function isEditable ()
	{
		return false;
	}

	function onSetupPage ($homepageUrl) 
	{
		$smarty = TikiLib::lib('smarty');

		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		return true;		
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/user_dummy3.tpl';
		return $wizardTemplate;
	}

	function onContinue ($homepageUrl) 
	{
		// Run the parent first
		parent::onContinue($homepageUrl);
	}
}
