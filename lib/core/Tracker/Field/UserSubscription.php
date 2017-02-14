<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: UserSubscription.php 57968 2016-03-17 20:06:57Z jonnybradley $

/**
 * Handler class for User Subscription
 *
 * Letter key: ~U~
 *
 */
class Tracker_Field_UserSubscription extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'U' => array(
				'name' => tr('User Subscription'),
				'description' => tr('Allows registered users to subscribe themselves to a tracker item. Ideally, the item should only be editable by the creator or by administrators. Prepend the maximum amount of subscribers to the field value followed by # if such a limit is desired. For example, 50# means that 50 subscribers will be allowed for this item.'),
				'help' => 'Subscription Tracker Field',
				'prefs' => array('trackerfield_subscription'),
				'default' => 'n',
				'tags' => array('advanced'),
				'params' => array(
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		global $user, $jitPost;
		$userlib = TikiLib::lib('user');
		$smarty = TikiLib::lib('smarty');

		$ins_id = $this->getInsertId();

		if (isset($requestData[$this->getInsertId()])) {
			$value = $requestData[$this->getInsertId()];
			return array( 'value' => $value);
		} else {
			$value = $this->getValue();
			if (!$value) {
				$value = '0#';		// default to unlimited
			}
		}
		$current_field_ins = $this->parseUsers($value);

		if (isset($requestData['user_subscribe']) || $jitPost->user_subscribe->word()) { // check jitPost too which is used for trackerlist etc
			$found = false;
			if ($current_field_ins['maxsubscriptions']) {
				$nb = min($current_field_ins['maxsubscriptions'], intval($requestData['user_friends']));
			} else {
				$nb = isset($requestData['user_friends']) ? intval($requestData['user_friends']) : $jitPost->user_friends->int();
			}
			foreach ($current_field_ins['users_array'] as $i=>$U) {
				if ($U['login'] == $user) {
					$current_field_ins['users_array'][$i]['friends'] = $nb;
					$found = true;
					break;
				}
			}
			if (!$found) {
				$userlib = TikiLib::lib('user');
				$temp = $userlib->get_user_info($user);
				$current_field_ins['users_array'][] = array('id' => $temp['userId'], 'login' => $user, 'friends' => $nb);
				$current_field_ins['user_subscription'] = true;
			}
			$value = $this->encodeUsers($current_field_ins);
			$this->save($value);
			$current_field_ins = $this->parseUsers($value);
		}
		if (isset($requestData['user_unsubscribe']) || $jitPost->user_unsubscribe->word()) {
			foreach ($current_field_ins['users_array'] as $i=>$U) {
				if ($U['login'] == $user) {
					unset($current_field_ins['users_array'][$i]);
					$value = $this->encodeUsers($current_field_ins);
					$this->save($value);
					$current_field_ins = $this->parseUsers($value);
					break;
				}
			}
		}
		$current_field_ins['list'] = $this->parseShortcut($current_field_ins);
		$smarty->assign('current_field_ins', $current_field_ins);
		return $current_field_ins;
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/usersubscription.tpl', $context);
	}
	function renderOutput($context = array())
	{
		return $this->renderTemplate('trackeroutput/usersubscription.tpl', $context);
	}
	private function encodeUsers($current_field_ins)
	{
		$value =  $current_field_ins['maxsubscriptions'].'#';
		foreach ($current_field_ins['users_array'] as $i=>$U) {
			if (!empty($i))
				$value .= ',';
			$value .= $U['id'].'['.$U['friends'].']';
		}
		return $value;
	}
	private function parseUsers($value)
	{
		global $user;
		$userlib = TikiLib::lib('user');
		$current_field_ins['value'] = $value; // encoded value
		$temp = $userlib->get_user_info($user);
		$id_tiki_user = $temp['userId'];
		$pattern = "/(\d+)\[(\d+)\]/";
		preg_match_all($pattern, $value, $match);
		$users_array = array();
		$current_field_ins['user_subscription'] = false; // user is subscribed with firnd or not
		$current_field_ins['user_nb_users'] = 0; // total number of user attending
		$current_field_ins['user_nb_friends'] = 0; // total of friends for this user
		foreach ($match[1] as $j => $id_user) {
			$temp = $userlib->get_userId_info($id_user);
			array_push(
				$users_array,
				array(
					'id' => $id_user,
					'login' => $temp['login'],
					'friends' => $match[2][$j]
				)
			);
			$current_field_ins['user_nb_users'] += $match[2][$j] + 1;
			if ($id_user == $id_tiki_user) {
				$current_field_ins['user_subscription'] = true;
				$current_field_ins['user_nb_friends'] = $match[2][$j];
			}
		}
		$current_field_ins['users_array'] = $users_array; // list user and subscriptions
		$current_field_ins['maxsubscriptions'] = substr($value, 0, strpos($value, '#'));
		return $current_field_ins;
	}
	private function parseShortcut($current_field_ins)
	{
		$U_liste = NULL;
		$U_othersubscriptions = $current_field_ins['user_nb_friends'];
		if (!$current_field_ins['user_subscription']) {
			$U_othersubscriptions--;
		}
		if ($current_field_ins['maxsubscriptions']) {
			for ($j = 0; $j <= $current_field_ins['maxsubscriptions'] - $current_field_ins['user_nb_users'] + $U_othersubscriptions; $j++) {
				$U_liste[$j] = $j;
			}
		}
		return $U_liste;
	}
	private function save($value)
	{
		$query = 'update `tiki_tracker_item_fields` set `value`=? where `itemId`=? and `fieldId`=?';
		TikiLib::lib('trk')->query($query, array($value, $this->getItemId(), $this->getConfiguration('fieldId')));
	}
}

