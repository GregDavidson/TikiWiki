#!/usr/bin/php
<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: console.php 60335 2016-11-20 19:23:21Z rjsmelo $

use Symfony\Component\Console\Input\ArgvInput;

define('TIKI_CONSOLE', 1);
declare(ticks = 1); // how often to check for signals

if (function_exists('pcntl_signal')) {
	$exit = function () {
		error_reporting(0); // Disable error reporting, misleading backtrace on kill
		exit;
	};

	pcntl_signal(SIGTERM, $exit);
	pcntl_signal(SIGHUP, $exit);
	pcntl_signal(SIGINT, $exit);
}


if (isset($_SERVER['REQUEST_METHOD'])) {
	die('Only available through command-line.');
}

require_once 'tiki-filter-base.php';
require_once 'lib/init/initlib.php';
include_once('lib/init/tra.php');
require_once('lib/setup/tikisetup.class.php');
require_once 'lib/setup/twversion.class.php';

$input = new ArgvInput;

if (false !== $site = $input->getParameterOption(array('--site'))) {
	$_SERVER['TIKI_VIRTUAL'] = $site;
}

$local_php = TikiInit::getCredentialsFile();

if (! is_readable($local_php)) {
	die("Credentials file local.php not found. See http://doc.tiki.org/Installation for more information.\n");
}

$console = new Tiki\Command\Application;

$console->add(new Tiki\Command\ConfigureCommand);
if (is_file($local_php) || TikiInit::getEnvironmentCredentials()) {
	require_once 'db/tiki-db.php';
	$console->add(new Tiki\Command\InstallCommand);
	$console->add(new Tiki\Command\UpdateCommand);
	$console->add(new Tiki\Command\MultiTikiListCommand);
	$console->add(new Tiki\Command\MultiTikiMoveCommand);
} else {
	$console->add(new Tiki\Command\UnavailableCommand('database:install'));
	$console->add(new Tiki\Command\UnavailableCommand('database:update'));
	$console->add(new Tiki\Command\UnavailableCommand('multitiki:list'));
	$console->add(new Tiki\Command\UnavailableCommand('multitiki:move'));
}

$installer = $installer = new Installer;
$isInstalled = $installer->isInstalled();

if ($isInstalled) {
	$bypass_siteclose_check = true;
	try {
		require_once 'tiki-setup.php';
	} catch (Exception $e) {
		$console->renderException($e, new \Symfony\Component\Console\Output\ConsoleOutput());
	}

	if (! $asUser = $input->getParameterOption(array('--as-user'))) {
		$asUser = 'admin';
	}

	if (TikiLib::lib('user')->user_exists($asUser)) {
		$permissionContext = new Perms_Context($asUser);
	}
}

if ($isInstalled) {
	$console->add(new Tiki\Command\CacheClearCommand);
	$console->add(new Tiki\Command\LessCompileCommand);
	$console->add(new Tiki\Command\BackupDBCommand);
	$console->add(new Tiki\Command\BackupFilesCommand);
	$console->add(new Tiki\Command\ProfileBaselineCommand);
} else {
	$console->add(new Tiki\Command\UnavailableCommand('cache:clear'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('less:compile'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('database:backup'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('backup:files'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('profile:baseline'))->ignoreValidationErrors();
}

if ($isInstalled && ! $installer->requiresUpdate()) {
	$console->add(new Tiki\Command\AddonInstallCommand);
	$console->add(new Tiki\Command\AddonRemoveCommand);
	$console->add(new Tiki\Command\AddonUpgradeCommand);
	$console->add(new Tiki\Command\DailyReportSendCommand);
	$console->add(new Tiki\Command\GoalCheckCommand);
	$console->add(new Tiki\Command\FilesBatchuploadCommand);
	$console->add(new Tiki\Command\FilesDeleteoldCommand);
	$console->add(new Tiki\Command\IndexRebuildCommand);
	$console->add(new Tiki\Command\IndexOptimizeCommand);
	$console->add(new Tiki\Command\IndexCatchUpCommand);
	$console->add(new Tiki\Command\ListExecuteCommand);
	$console->add(new Tiki\Command\MailInPollCommand);
	$console->add(new Tiki\Command\MailQueueSendCommand);
	$console->add(new Tiki\Command\NotificationDigestCommand);
	$console->add(new Tiki\Command\PreferencesGetCommand);
	$console->add(new Tiki\Command\PreferencesSetCommand);
	$console->add(new Tiki\Command\PreferencesDeleteCommand);
	$console->add(new Tiki\Command\ProfileForgetCommand);
	$console->add(new Tiki\Command\ProfileInstallCommand);
	$console->add(new Tiki\Command\ProfileExport\Init);
	$console->add(new Tiki\Command\RecommendationBatchCommand);
	$console->add(new Tiki\Command\RefreshRssCommand);
	$console->add(new Tiki\Command\RssClearCacheCommand);
	$console->add(new Tiki\Command\TrackerImportCommand);
	$console->add(new Tiki\Command\TrackerClearCommand);
	$console->add(new Tiki\Command\AdminIndexRebuildCommand);
	$console->add(new Tiki\Command\UsersListCommand);
	$console->add(new Tiki\Command\UsersPasswordCommand);
} else {
	$console->add(new Tiki\Command\UnavailableCommand('addon:install'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('addon:remove'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('addon:upgrade'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('daily-report:send'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('goal:check'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('files:batchupload'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('files:deleteold'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('index:rebuild'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('index:optimize'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('index:catch-up'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('list:execute'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('mail-in:poll'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('mail-queue:send'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('notification:digest'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('preferences:get'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('preferences:set'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('preferences:delete'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('profile:forget'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('profile:apply'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('profile:export:init'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('recommendation:batch'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('rss:refresh'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('rss:clear'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('tracker:import'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('tracker:clear'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('preferences:rebuild-index'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('users:password'))->ignoreValidationErrors();
	$console->add(new Tiki\Command\UnavailableCommand('users:list'))->ignoreValidationErrors();
}

if (file_exists('profiles/info.ini')) {
	$console->add(new Tiki\Command\ProfileExport\ActivityRuleSet);
	$console->add(new Tiki\Command\ProfileExport\ActivityStreamRule);
	$console->add(new Tiki\Command\ProfileExport\Article);
	$console->add(new Tiki\Command\ProfileExport\ArticleTopic);
	$console->add(new Tiki\Command\ProfileExport\ArticleType);
	$console->add(new Tiki\Command\ProfileExport\AllModules);
	$console->add(new Tiki\Command\ProfileExport\Category);
	$console->add(new Tiki\Command\ProfileExport\FileGallery);
	$console->add(new Tiki\Command\ProfileExport\Forum);
	$console->add(new Tiki\Command\ProfileExport\Goal);
	$console->add(new Tiki\Command\ProfileExport\GoalSet);
	$console->add(new Tiki\Command\ProfileExport\Group);
	$console->add(new Tiki\Command\ProfileExport\IncludeProfile);
	$console->add(new Tiki\Command\ProfileExport\Menu);
	$console->add(new Tiki\Command\ProfileExport\Module);
	$console->add(new Tiki\Command\ProfileExport\Preference);
	$console->add(new Tiki\Command\ProfileExport\RatingConfig);
	$console->add(new Tiki\Command\ProfileExport\RatingConfigSet);
	$console->add(new Tiki\Command\ProfileExport\RecentChanges);
	$console->add(new Tiki\Command\ProfileExport\Rss);
	$console->add(new Tiki\Command\ProfileExport\Tracker);
	$console->add(new Tiki\Command\ProfileExport\TrackerField);
	$console->add(new Tiki\Command\ProfileExport\WikiPage);

	$console->add(new Tiki\Command\ProfileExport\Finalize);
}

if (is_file('db/redact/local.php') && ($site == 'redact') ) {
	$console->add(new Tiki\Command\RedactDBCommand);
} else {
	$console->add(new Tiki\Command\UnavailableCommand('database:redact'))->ignoreValidationErrors();
}

$console->run();
