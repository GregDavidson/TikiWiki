<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-admin_mailin.php 58748 2016-05-31 23:05:28Z lindonb $

require_once ('tiki-setup.php');
//check if feature is on
$access->check_feature('feature_mailin');
$access->check_permission(array('tiki_p_admin_mailin'));

$mailinlib = TikiLib::lib('mailin');

// List
$accounts = $mailinlib->list_mailin_accounts(0, -1, 'account_asc', '');
$smarty->assign('accounts', $accounts['data']);

if (isset($_REQUEST['mailin_autocheck'])) {
	if ($_REQUEST['mailin_autocheck'] == 'y' && !(preg_match('/[0-9]+/', $_REQUEST['mailin_autocheckFreq'])
			&& $_REQUEST['mailin_autocheckFreq'] > 0))
	{
		Feedback::warning(tra('Frequency should be a positive integer!'));
	} else {
		$tikilib->set_preference('mailin_autocheck', $_REQUEST['mailin_autocheck']);
		$tikilib->set_preference('mailin_autocheckFreq', $_REQUEST['mailin_autocheckFreq']);
		if ($prefs['mailin_autocheck'] == 'y') {
			Feedback::warning(sprintf(tra('Mail-in accounts set to be checked every %s minutes'),
				$prefs['mailin_autocheckFreq']));
		} else {
			Feedback::warning(sprintf(tra('Automatic Mail-in accounts checking disabled')));
		}
	}
}

$artlib = TikiLib::lib('art');

$smarty->assign('mailin_types', $mailinlib->list_available_types());
ask_ticket('admin-mailin');

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');
$smarty->display('tiki-admin_mailin.tpl');
