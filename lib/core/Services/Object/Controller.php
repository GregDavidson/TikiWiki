<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

class Services_Object_Controller
{
	public static function supported()
	{
		global $prefs;
		$supported = array();

		if ($prefs['feature_trackers'] == 'y') {
			$supported[] = 'trackeritem';
		}

		if ($prefs['activity_basic_events'] == 'y' || $prefs['activity_custom_events'] == 'y' || $prefs['monitor_enabled']) {
			$supported[] = 'activity';
		}

		return $supported;
	}

	function action_infobox($input)
	{
		$type = $input->type->none();
		if (! in_array($type, self::supported())) {
			throw new Services_Exception_NotAvailable(tr('No box available for %0', $type));
		}

		return array(
			'type' => $type,
			'object' => $input->object->none(),
			'content' => $this->{'infobox_' . $type}($input),
			'plain' => $input->plain->int(),
			'format' => $input->format->word(),
		);
	}

	private function infobox_trackeritem($input)
	{
		$itemId = $input->object->int();
		$trklib = TikiLib::lib('trk');

		if (! $item = $trklib->get_tracker_item($itemId)) {
			throw new Services_Exception_NotFound;
		}

		if (! $definition = Tracker_Definition::get($item['trackerId'])) {
			throw new Services_Exception_NotFound;
		}

		$itemObject = Tracker_Item::fromInfo($item);

		if (! $itemObject->canView()) {
			throw new Services_Exception('Permission denied', 403);
		}

		$fields = array();
		foreach ($definition->getPopupFields() as $fieldId) {
			if ($itemObject->canViewField($fieldId) && $field = $definition->getField($fieldId)) {
				$fields[] = $field;
			}
		}

		$smarty = TikiLib::lib('smarty');
		$smarty->assign('fields', $fields);
		$smarty->assign('item', $item);
		$smarty->assign('can_modify', $itemObject->canModify());
		$smarty->assign('can_remove', $itemObject->canRemove());
		$smarty->assign('mode', $input->mode->text() ? $input->mode->text() : '');	// default divs mode
		return $smarty->fetch('object/infobox/trackeritem.tpl');
	}

	private function infobox_activity($input)
	{
		$itemId = $input->object->int();
		$lib = TikiLib::lib('activity');
		$info = $lib->getActivity($itemId);

		if (! $info) {
			throw new Services_Exception_NotFound;
		}

		$smarty = TikiLib::lib('smarty');
		$smarty->assign('activity', $itemId);
		$smarty->assign('format', $input->format->word());
		return $smarty->fetch('object/infobox/activity.tpl');
	}


	function action_lock($input)
	{
		$attributelib = TikiLib::lib('attribute');

		$type = $input->type->text();
		$object = $input->object->text();
		$value = $input->value->text();

		list($perm, $adminperm, $attribute, $permtype) = $this->setup_locking($type);

		$perms = Perms::get($permtype, $object);
		$lockedby = $attributelib->get_attribute($type, $object, $attribute);


		if (empty($lockedby) || $perms->$adminperm) {

			Services_Exception_Denied::checkObject($perm, $permtype, $object);

			if (! empty($object)) {
				$return = TikiLib::lib('attribute')->set_attribute($type, $object, $attribute, $value);

				if (!$return) {
					Feedback::error(tr('Invalid attribute name "%0"', $attribute), 'session');
				}
			}

			return ['locked' => true];
		}

		return [];
	}

	function action_unlock($input)
	{
		global $user;
		$attributelib = TikiLib::lib('attribute');

		$type = $input->type->text();
		$object = $input->object->text();

		list($perm, $adminperm, $attribute, $permtype) = $this->setup_locking($type);

		$perms = Perms::get($permtype, $object);
		$lockedby = $attributelib->get_attribute($type, $object, $attribute);

		if ($lockedby) {	// it's locked

			if ($perms->$adminperm || ($user === $lockedby && $perms->$perm)) {

				if (! empty($object)) {
					$res = $attributelib->set_attribute($type, $object, $attribute, '');

					if (!$res) {
						Feedback::error(tr('Invalid attribute name "%0"', $attribute), 'session');
					}
				}

				return ['locked' => false];

			} else {
				Services_Exception_Denied::checkObject($adminperm, $permtype, $object);
			}
		}
		return [];
	}

	/**
	 * Generic function to allow consistently formatted errors from javascript using Feedback
	 *
	 * @param $input JitFilter filtered input object
	 */
	function action_report_error($input)
	{
		Feedback::error($input->message->text(), 'session');
		Feedback::send_headers();
	}

	/**
	 * @param $type
	 * @return array string
	 * @throws Exception
	 * @throws Services_Exception_Disabled
	 */
	private function setup_locking($type)
	{
		$perm = 'lock';    // default (for wiki page, so not used here yet)
		$adminperm ='admin';
		$attribute = 'tiki.object.lock';
		$permtype = $type;

		switch ($type) {
			case 'template':
				Services_Exception_Disabled::check('lock_content_templates');
				$perm = 'lock_content_templates';
				$adminperm = 'admin_content_templates';
				break;
			case 'wiki structure':
				Services_Exception_Disabled::check('lock_wiki_structures');
				$perm = 'lock_structures';
				$adminperm = 'admin_structures';
				$permtype = 'wiki page';		// perms for structures are actually from the top wiki page (don't ask)
				break;
			default:
				Feedback::error(tr('Cannot lock "%0"', $type), 'session');
		}

		return array($perm, $adminperm, $attribute, $permtype);
	}
}

