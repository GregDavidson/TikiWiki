<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: perms.php 39469 2012-01-12 21:13:48Z changi67$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER['SCRIPT_NAME'], basename(__FILE__));

$groupList = null;
$is_token_access = false;
if ( $prefs['auth_token_access'] == 'y' && isset($_REQUEST['TOKEN']) ) {
	require_once 'lib/auth/tokens.php';
	$token = $_REQUEST['TOKEN'];

	unset($_GET['TOKEN']);
	unset($_POST['TOKEN']);
	unset($_REQUEST['TOKEN']);
	$tokenParams = $_GET;

 	/**
 	 * Shared 'Upload File' case
 	 */
	if ( isset($isUpload) && $isUpload && ! empty($_POST['galleryId']) && empty($_GET['galleryId']) ) {
		foreach ( (array) $_POST['galleryId'] as $v ) {
			if ( ! empty($tokenParams['galleryId']) ) {
				if ( $tokenParams['galleryId'] == $v ) {
					continue;
				} else {
					unset( $tokenParams['galleryId'] );
					break;
				}
			}
			$tokenParams['galleryId'] = $v;
		}
	}

	$tokenlib = AuthTokens::build($prefs);
	if ( $groups = $tokenlib->getGroups($token, $_SERVER['PHP_SELF'], $tokenParams) ) {
	 	$groupList = $groups;
	 	$detailtoken = $tokenlib->getToken($token);
	 	$is_token_access = true;

	 	/**
	 	 * Shared 'File download' case
	 	 */
	 	if (isset($_GET['fileId']) && $detailtoken['parameters'] == '{"fileId":"' . $_GET['fileId'] . '"}') {
	 		$_SESSION['allowed'][$_GET['fileId']] = true;
	 	}

		// If notification then alert
		if ($prefs['share_token_notification'] == 'y') {
			$nots = $tikilib->get_event_watches('auth_token_called', $detailtoken['tokenId']);
			$smarty->assign('prefix_url', $base_host);

			// Select in db the tokenId
			$notificationPage = '';
			$smarty->assign_by_ref('page_token', $notificationPage);

			if (is_array($nots)) {
				include_once ('lib/webmail/tikimaillib.php');
				$mail = new TikiMail();

				$mail->setSubject($detailtoken['email'] . ' ' . tra(' has accessed your temporary shared content'));

				foreach ($nots as $i=>$not) {
					$notificationPage = $not['url'];

				 	// Delete token from url
					$notificationPage = preg_replace('/[\?&]TOKEN=' . $detailtoken['token'] . '/', '', $notificationPage);

					// If file Gallery
					$smarty->assign('filegallery', 'n');
					if (preg_match("/\btiki-download_file.php\b/i", $notificationPage)) {
						$filegallib = TikiLib::lib('filegal');
						$smarty->assign('filegallery', 'y');
						$aParams = (array) json_decode($detailtoken['parameters']);
						$smarty->assign('fileId', $aParams['fileId']);

						$aFileInfos = $filegallib->get_file_info($aParams['fileId']);
						$smarty->assign('filegalleryId', $aFileInfos['galleryId']);
						$smarty->assign('filename', $aFileInfos['name']);
					}

					$smarty->assign('email_token', $detailtoken['email']);
					$txt = $smarty->fetch('mail/user_watch_token.tpl');
					$mail->setHTML($txt);
					$mailsent = $mail->send(array($not['email']));
				}
			}

		}

		if ( empty($notificationPage) ) {
				$notificationPage = preg_replace('/[\?&]TOKEN='.$token.'/','',$_SERVER['REQUEST_URI']);
		}
		// Log each token access
		$logslib->add_log('token', $detailtoken['email'] . ' ' . tra('has accessed the following shared content:') . ' ' . $notificationPage);
	} else {
		// Error Token expired
		$token_error = tra('Your access to this page has expired');
	}
}

$allperms = $userlib->get_enabled_permissions();

Perms_Context::setPermissionList($allperms);

$builder = new Perms_Builder;
$perms = $builder
	->withCategories($prefs['feature_categories'] == 'y')
	->withDefinitions($allperms)
	->build();

Perms::set($perms);

$_permissionContext = new Perms_Context($user, false);

if ($groupList) {
	$_permissionContext->overrideGroups($groupList);
}

$_permissionContext->activate(true);

unset($allperms);
unset($tokenParams);
