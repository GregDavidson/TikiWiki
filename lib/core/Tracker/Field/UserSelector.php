<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: UserSelector.php 60600 2016-12-12 11:15:40Z kroky6 $

/**
 * Handler class for UserSelector
 *
 * Letter key: ~u~
 *
 */
class Tracker_Field_UserSelector extends Tracker_Field_Abstract implements Tracker_Field_Synchronizable, Tracker_Field_Exportable
{
	public static function getTypes()
	{
		return array(
			'u' => array(
				'name' => tr('User Selector'),
				'description' => tr('Allows the selection of a user or users from a list.'),
				'help' => 'User selector',
				'prefs' => array('trackerfield_userselector'),
				'tags' => array('basic'),
				'default' => 'y',
				'params' => array(
					'autoassign' => array(
						'name' => tr('Auto-Assign'),
						'description' => tr('Assign the value based on the creator or modifier.'),
						'filter' => 'int',
						'default' => 0,
						'options' => array(
							0 => tr('None'),
							1 => tr('Creator'),
							2 => tr('Modifier'),
						),
						'legacy_index' => 0,
					),
					'notify' => array(
						'name' => tr('Email Notification'),
						'description' => tr('Send an email notification to the user(s) every time the item is modified.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
							2 => tr('Only when other users modify the item'),
						),
						'legacy_index' => 1,
					),
					'multiple' => array(
						'name' => tr('Multiple selection'),
						'description' => tr('Allow selection of multiple users from the list.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes (complete list)'),
							2 => tr('Yes (filterable by group)'),
						),
						'default' => 0,
					),
					'groupIds' => array(
						'name' => tr('Group IDs'),
						'description' => tr('Limit the list of users to members of specific groups.'),
						'separator' => '|',
						'filter' => 'int',
						'legacy_index' => 2,
					),
					'canChangeGroupIds' => array(
						'name' => tr('Groups that can modify autoassigned values'),
						'description' => tr('List of group IDs who can change this field, even without tracker_admin permission.'),
						'separator' => '|',
						'filter' => 'int',
					),
					'showRealname' => array(
						'name' => tr('Show real name if possible'),
						'description' => tr('Show real name if possible'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
						'default' => 0,
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		global $user, $prefs;

		$ins_id = $this->getInsertId();

		$data = array();

		$autoassign = (int) $this->getOption('autoassign');

		if ( isset($requestData[$ins_id])) {
			if ($autoassign == 0 || $this->canChangeValue()) {
				$ausers = $requestData[$ins_id];
				if( !is_array($ausers) ) {
					$ausers = TikiLib::lib('trk')->parse_user_field($ausers);
				}
				$userlib = TikiLib::lib('user');
				$users = array();
				foreach( $ausers as $auser ) {
					if ($userlib->user_exists($auser)) {
						$users[] = $auser;
					} elseif( $auser ) {
						$finaluser = null;
						if ($prefs['user_selector_realnames_tracker'] == 'y' && $this->getOption('showRealname')) {
							$finalusers = $userlib->find_best_user(array($auser), '', 'login');
							if (!empty($finalusers[0])) {
								$finaluser = $finalusers[0];
							}
						}
						if (empty($finaluser)) {
							Feedback::error(tr('User "%0" not found', $auser), 'session');
						} else {
							$users[] = $finaluser;
						}
					}
				}
				$data['value'] = TikiLib::lib('tiki')->str_putcsv($users);
			} else {
				if ($autoassign == 2) {
					if( $this->getOption('multiple') ) {
						$data['value'] = TikiLib::lib('trk')->parse_user_field($this->getValue());
						if( !in_array($user, $data['value']) ) {
							$data['value'][] = $user;
						}
						$data['value'] = TikiLib::lib('tiki')->str_putcsv($data['value']);
					} else {
						$data['value'] = $user;
					}
				} elseif ($autoassign == 1) {
					if (!$this->getItemId() || ($this->getTrackerDefinition()->getConfiguration('userCanTakeOwnership')  == 'y' && !$this->getValue())) {
						$data['value'] = $user; // the user appropiate the item
					} else {
						$data['value'] = $this->getValue();
						// unset($data['fieldId']); hmm?
					}
				} else {
					$data['value'] = '';
				}
			}
		} else {
			$data['value'] = $this->getValue(false);
		}

		return $data;
	}

	function renderInput($context = array())
	{
		global $user, $prefs;
		$smarty = TikiLib::lib('smarty');

		$value = $this->getConfiguration('value');
		if( $value ) {
			$value = TikiLib::lib('trk')->parse_user_field($value);
		} else {
			$value = array();
		}
		$autoassign = (int) $this->getOption('autoassign');
		if ((empty($value) && $autoassign == 1) || ($autoassign == 2 && !in_array($user, $value))) {	// always use $user for last mod autoassign
			$value[] = $user;
		}
		if ($autoassign == 0 || $this->canChangeValue()) {
			$groupIds = $this->getOption('groupIds', '');

			if ($prefs['user_selector_realnames_tracker'] === 'y' && $this->getOption('showRealname')) {
				$smarty->loadPlugin('smarty_modifier_username');
				$name = implode(', ', array_map('smarty_modifier_username', $value));
				$realnames = 'y';
			} else {
				$name = implode(', ', $value);
				$realnames = 'n';
			}

			if( $this->getOption('multiple') == 2 ) {
				$userlib = TikiLib::lib('user');
				if( !empty($groupIds) ) {
					$groupIds = explode('|', $groupIds);
				}
				$groups = $userlib->list_all_groups_with_permission();
				$groups = $userlib->get_group_info($groups);
				if( !empty($groupIds) ) {
					$groups = array_filter($groups, function($group) use ($groupIds) {
						return in_array($group['id'], $groupIds);
					});
				}
				$groups = array_map(function($group) {
					return $group['groupName'];
				}, $groups);
				$selected_groups = array();
				$users = $userlib->get_members($groups);
				foreach( $users as $group => &$usrs ) {
					if( array_intersect($value, $usrs) ) {
						$selected_groups[] = $group;
					}
					if( $this->getOption('showRealname') ) {
						$smarty->loadPlugin('smarty_modifier_username');
						$usrs = array_combine($usrs, array_map('smarty_modifier_username', $usrs));
					} else {
						$usrs = array_combine($usrs, $usrs);
					}
				}
				return $this->renderTemplate('trackerinput/userselector_grouped.tpl', $context, array(
					'groups' => $groups,
					'users' => $users,
					'selected_users' => $value,
					'selected_groups' => $selected_groups,
				));
			} else {
				$smarty->loadPlugin('smarty_function_user_selector');
				return smarty_function_user_selector(
					array(
						'user' => $name,
						'id'  => 'user_selector_' . $this->getConfiguration('fieldId'),
						'select' => $value,
						'name' => $this->getConfiguration('ins_id'),
						'multiple' => ( $this->getOption('multiple') ? 'true' : 'false' ),
						'editable' => 'y',
						'allowNone' => 'y',
						'groupIds' => $groupIds,
						'realnames' => $realnames,
					),
					$smarty
				);
			}
		} else {
			if ($this->getOption('showRealname')) {
				$smarty->loadPlugin('smarty_modifier_username');
				$out = implode(', ', array_map('smarty_modifier_username', $value));
			} else {
				$out = implode(', ', $value);
			}	
			return $out . '<input type="hidden" name="' . $this->getInsertId() . '" value="' . htmlspecialchars(TikiLib::lib('tiki')->str_putcsv($value)) . '">';
		}
	}

	function renderInnerOutput($context = array())
	{
		$value = $this->getConfiguration('value');
		if (empty($value)) {
			return '';
		} else {
			if ($this->getOption('showRealname')) {
				TikiLib::lib('smarty')->loadPlugin('smarty_modifier_username');
				return implode(', ', array_map('smarty_modifier_username', TikiLib::lib('trk')->parse_user_field($value)));
			} else {
				return implode(', ', TikiLib::lib('trk')->parse_user_field($value));
			}
		}
	}

	function importRemote($value)
	{
		return $value;
	}

	function exportRemote($value)
	{
		return $value;
	}

	function importRemoteField(array $info, array $syncInfo)
	{
		$groupIds = $this->getOption('groupIds', '');
		$groupIds = array_filter(explode('|', $groupIds));
		$groupIds = array_map('intval', $groupIds);

		$controller = new Services_RemoteController($syncInfo['provider'], 'user');
		$users = $controller->getResultLoader(
			'list_users',
			array(
				'groupIds' => $groupIds,
			)
		);

		$list = array();
		foreach ($users as $user) {
			$list[] = $user['login'];
		}

		if (count($list)) {
			$info['type'] = 'd';
			$info['options'] = implode(',', $list);
		} else {
			$info['type'] = 't';
			$info['options'] = '';
		}

		return $info;
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$baseKey = $this->getBaseKey();

		$value = $this->getValue();

		if ($this->getOption('showRealname')) {
			TikiLib::lib('smarty')->loadPlugin('smarty_modifier_username');
			$realName = implode(', ', array_map('smarty_modifier_username', TikiLib::lib('trk')->parse_user_field($value)));
		} else {
			$realName = implode(', ', TikiLib::lib('trk')->parse_user_field($value));	// add the _text option even if not using showRealname so we don't need to check
		}

		return array(
			$baseKey => $typeFactory->identifier($this->getValue()),
			"{$baseKey}_text" => $typeFactory->sortable($realName),
		);

	}

	/**
	 * tell the indexer about the real name _text field if using showRealname
	 *
	 * @return array
	 */
	function getGlobalFields()
	{
		$baseKey = $this->getBaseKey();

		$data = [$baseKey => true];

		if ($this->getOption('showRealname')) {
			$data["{$baseKey}_text"] = true;
		}

		return $data;
	}

	/**
	 * called from action_clone_item - sets to current user if autoassign == 1 or 2 (Creator or Modifier)
	 */
	function handleClone()
	{
		global $user;

		$value =  $this->getValue('');
		$autoassign = (int) $this->getOption('autoassign');

		if ($autoassign === 1 || $autoassign === 2) {
			if( $this->getOption('multiple') && $value ) {
				$value = TikiLib::lib('trk')->parse_user_field($value);
				if( !in_array($user, $value) ) {
					$value[] = $user;
				}
				$value = TikiLib::lib('tiki')->str_putcsv($value);
			} else {
				$value = $user;
			}
		}

		return array(
			'value' => $value,
		);

	}

	function getTabularSchema()
	{
		$permName = $this->getConfiguration('permName');
		$baseKey = $this->getBaseKey();
		$name = $this->getConfiguration('name');

		$schema = new Tracker\Tabular\Schema($this->getTrackerDefinition());

		$schema->addNew($permName, 'userlink')
			->setLabel($name)
			->setPlainReplacement('username')
			->setRenderTransform(function ($value) {
				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_modifier_userlink');

				if ($value) {
					return implode(', ', array_map('smarty_modifier_userlink', TikiLib::lib('trk')->parse_user_field($value)));
				}
			})
			;

		$schema->addNew($permName, 'realname')
			->setLabel($name)
			->setReadOnly(true)
			->setRenderTransform(function ($value) {
				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_modifier_username');

				if ($value) {
					$value = TikiLib::lib('trk')->parse_user_field($value);
					foreach( $value as &$v ) {
						$v = smarty_modifier_username($v, true, false, false);
					}
					return implode(', ', $value);
				}
			})
			;

		$schema->addNew($permName, 'username-itemlink')
			->setLabel($name)
			->setPlainReplacement('username')
			->addQuerySource('itemId', 'object_id')
			->setRenderTransform(function ($value, $extra) {
				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_function_object_link');

				if ($value) {
					$value = TikiLib::lib('trk')->parse_user_field($value);
					foreach( $value as &$v ) {
						$v = smarty_function_object_link([
							'type' => 'trackeritem',
							'id' => $extra['itemId'],
							'title' => $v,
						], $smarty);
					}
					return implode(', ', $value);
				}
			})
			;

		$schema->addNew($permName, 'realname-itemlink')
			->setLabel($name)
			->setPlainReplacement('realname')
			->addQuerySource('itemId', 'object_id')
			->setRenderTransform(function ($value, $extra) {
				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_function_object_link');
				$smarty->loadPlugin('smarty_modifier_username');

				if ($value) {
					$value = TikiLib::lib('trk')->parse_user_field($value);
					foreach( $value as &$v ) {
						$v = smarty_function_object_link([
							'type' => 'trackeritem',
							'id' => $extra['itemId'],
							'title' => smarty_modifier_username($v, true, false, false),
						], $smarty);
					}
					return implode(', ', $value);
				}
			})
			;

		$schema->addNew($permName, 'username')
			->setLabel($name)
			->setRenderTransform(function ($value) {
				return $value;
			})
			->setParseIntoTransform(function (& $info, $value) use ($permName) {
				$info['fields'][$permName] = $value;
			})
			;

		return $schema;
	}

	/** Checks if the current user can modify the value even if autoassigned usually
	 *
	 * @return boolean
	 */
	private function canChangeValue()
	{
		$groupsCanChangeValue = $this->getOption('canChangeGroupIds');
		if ($groupsCanChangeValue) {
			global $user;

			foreach ($groupsCanChangeValue as $groupId) {
				$groupName = TikiDb::get()->table('users_groups')->fetchOne('groupName', ['id' => $groupId]);
				if ($groupName && TikiLib::lib('user')->user_is_in_group($user, $groupName)) {
					return true;
				}
			}
		}
		$perms = Perms::get('tracker', $this->getConfiguration('trackerId'));

		return $perms->admin_trackers;
	}
}

