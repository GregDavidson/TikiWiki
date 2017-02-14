<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: upgrade_new_in_13.php 57961 2016-03-17 20:01:56Z jonnybradley $

require_once('lib/wizard/wizard.php');

/**
 * The Wizard's language handler 
 */
class UpgradeWizardNewIn13 extends Wizard
{
    function pageTitle ()
    {
        return tra('New in Tiki 13');
    }

	function isEditable ()
	{
		return true;
	}
	
	function onSetupPage ($homepageUrl) 
	{
		global $prefs;
		// Run the parent first
		parent::onSetupPage($homepageUrl);
		
		$showPage = true;
        // Show if any more specification is needed

        return $showPage;
	}

	function getTemplate()
	{
		$wizardTemplate = 'wizard/upgrade_new_in_13.tpl';
		return $wizardTemplate;
	}

	function onContinue ($homepageUrl) 
	{
        global $tikilib;

		// Run the parent first
		parent::onContinue($homepageUrl);

    }
}
