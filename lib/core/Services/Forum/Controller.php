<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Controller.php 59038 2016-07-02 03:16:45Z lindonb $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Class Services_Forum_Controller
 */
class Services_Forum_Controller
{
	private $lib;
	private $access;

	function setUp()
	{
		Services_Exception_Disabled::check('feature_forums');
		$this->lib = TikiLib::lib('comments');
		$this->access = TikiLib::lib('access');
	}

	/**
	 * Admin forums "perform with checked" but with no action selected
	 *
	 * @param $input
	 * @throws Services_Exception
	 */
	public function action_no_action($input)
	{
		Services_Utilities::modalException(tra('No action was selected. Please select an action before clicking OK.'));
	}

	/**
	 * Moderator action that locks a forum topic
	 * @param $input
	 * @return array
	 */
	function action_lock_topic($input)
	{
		return $this->lockUnlock($input, 'lock');
	}

	/**
	 * Moderator action that unlocks a forum topic
	 * @param $input
	 * @return array
	 */
	function action_unlock_topic($input)
	{
		return $this->lockUnlock($input, 'unlock');
	}

	/**
	 * Moderator action to merge selected forum topics or posts with another topic
	 * @param $input
	 * @return array
	 * @throws Exception
	 */
	function action_merge_topic($input)
	{
		$forumId = $input->forumId->int();
		$this->checkPerms($forumId);
		$check = Services_Exception_BadRequest::checkAccess();
		if (!empty($check['ticket'])) {
			//check number of topics on first pass
			$selected = $input->asArray('forumtopic');
			if (count($selected) > 0) {
				$items = $this->getTopicTitles($selected);
				$toList = $this->lib->get_forum_topics($forumId, 0, -1);
				$toList = array_column($toList, 'title', 'threadId');
				$diff = array_diff_key($toList, $items);
				if (count($diff) > 0) {
					$object = count($items) > 1 ? 'topics' : 'topic';
					if (isset($input['comments_parentId'])) {
						unset($diff[$input['comments_parentId']]);
						$title = tr('Merge selected posts with another topic');
						$customMsg = count($items) === 1 ? tra('Merge this post:') : tra('Merge these posts:');
					} else {
						$title = tr('Merge selected topics with another topic');
						$customMsg = count($items) === 1 ? tra('Merge this topic:') : tra('Merge these topics:');
					}
					//provide redirect if js is not enabled
					$referer = Services_Utilities::noJsPath();
					return [
						'FORWARD' => [
							'controller' => 'access',
							'action' => 'confirm_select',
							'confirmAction' => $input->action->word(),
							'confirmController' => 'forum',
							'confirmButton' => tra('Merge'),
							'customMsg' => $customMsg,
							'toMsg' => tra('With this topic:'),
							'title' => $title,
							'items' => $items,
							'extra' => ['referer' => $referer],
							'ticket' => $check['ticket'],
							'toList' => $diff,
							'object' => $object,
							'modal' => '1',
						]
					];
				} else {
					Services_Utilities::modalException(tra('All topics or posts were selected, leaving none to merge with. Please make your selection again.'));
				}
			} else {
				Services_Utilities::modalException(tra('No topics were selected. Please select the topics you wish to merge before clicking the merge button.'));
			}
		//second pass - after popup modal form has been submitted
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			//perform merge
			$items = json_decode($input['items'], true);
			$toId = $input->toId->int();
			foreach ($items as $id => $topic) {
				if ($id !== $toId) {
					$this->lib->set_parent($id, $toId);
				}
			}
			$extra = json_decode($input['extra'], true);
			$toComment = $this->getTopicTitles([$toId]);
			//prepare feedback
			if (count($items) == 1) {
				$msg = tr('The following post has been merged with the %0 topic:', $toComment[$toId]);
			} else {
				$msg = tr('The following posts have been merged with the %0 topic:', $toComment[$toId]);
			}
			$feedback = [
				'tpl' => 'action',
				'mes' => $msg,
				'items' => $items,
			];
			Feedback::success($feedback, 'session');
			//return to page
			return Services_Utilities::refresh($extra['referer']);
 		}
	}

	/**
	 * Moderator action to move one or more topics
	 *
	 * @param $input
	 * @return array
	 * @throws Services_Exception
	 * @throws Services_Exception_BadRequest
	 * @throws Services_Exception_Denied
	 */
	function action_move_topic($input)
	{
		$forumId = $input->forumId->int();
		$this->checkPerms($forumId);
		$check = Services_Exception_BadRequest::checkAccess();
		if (!empty($check['ticket'])) {
			//check number of topics on first pass
			$selected = $input->asArray('forumtopic');
			if (count($selected) > 0) {
				$items = $this->getTopicTitles($selected);
				$all_forums = $this->lib->list_forums(0, -1, 'name_asc', '');
				foreach ($all_forums['data'] as $key => $forum) {
					if ($this->lib->admin_forum($forum['forumId'])) {
						$toList[$forum['forumId']] = $forum['name'];
					}
				}
				$fromName = $toList[$forumId];
				unset($toList[$forumId]);
				$customMsg = count($items) === 1 ? tra('Move this topic:') : tra('Move these topics:');
				$toMsg = tr('From the %0 forum to the below forum:', $fromName);
				//provide redirect if js is not enabled
				$referer = Services_Utilities::noJsPath();
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm_select',
						'title' => tra('Move selected topics to another forum'),
						'confirmAction' => $input->action->word(),
						'confirmController' => 'forum',
						'confirmButton' => tra('Move'),
						'customMsg' => $customMsg,
						'toMsg' => $toMsg,
						'toList' => $toList,
						'items' => $items,
						'ticket' => $check['ticket'],
						'extra' => [
							'id' => $forumId,
							'referer' => $referer
						],
						'modal' => '1',
					]
				];
			} else {
				Services_Utilities::modalException(tra('No topics were selected. Please select the topics you wish to move before clicking the move button.'));
			}
			//second pass - after popup modal form has been submitted
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			//perform topic move
			$extra = json_decode($input['extra'], true);
			$items = json_decode($input['items'], true);
			$toList = json_decode($input['toList'], true);
			$toId = $input->toId->int();
			foreach ($items as $id => $topic) {
				// To move a topic you just have to change the object
				$obj = 'forum:' . $toId;
				$this->lib->set_comment_object($id, $obj);
				// update the stats for the source and destination forums
				$this->lib->forum_prune($extra['forumId']);
				$this->lib->forum_prune($toId);
			}
			//prepare feedback
			$toName = $toList[$toId];
			if (count($items) == 1) {
				$msg = tr('The following topic has been moved to the %0 forum:', $toName);
			} else {
				$msg = tr('The following topics have been moved to the %0 forum:', $toName);
			}
			$feedback = [
				'tpl' => 'action',
				'mes' => $msg,
				'items' => $items,
			];
			Feedback::success($feedback, 'session');
			//return to page
			return Services_Utilities::refresh($extra['referer']);
		}
	}

	/**
	 * Moderator action to delete one or more topics
	 *
	 * @param $input
	 * @return array
	 * @throws Exception
	 */
	function action_delete_topic($input)
	{
		$forumId = $input->forumId->digits();
		$this->checkPerms($forumId);
		$check = Services_Exception_BadRequest::checkAccess();
		if (!empty($check['ticket'])) {
			//check number of topics on first pass
			$selected = $input->asArray('forumtopic');
			if (count($selected) > 0) {
				$items = $this->getTopicTitles($selected);
				if (isset($input['comments_parentId'])) {
					$object = count($items) > 1 ? 'posts' : 'post';
				} else {
					$object = count($items) > 1 ? 'topics' : 'topic';
				}
				//provide redirect if js is not enabled
				$referer = Services_Utilities::noJsPath();
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'title' => tra('Please confirm deletion'),
						'confirmAction' => $input->action->word(),
						'confirmController' => 'forum',
						'customVerb' => tra('delete'),
						'customObject' => tr('forum %0', $object),
						'items' => $items,
						'extra' => [
							'forumId' => $forumId,
							'referer' => $referer
						],
						'ticket' => $check['ticket'],
						'modal' => '1',
					]
				];
			} else {
				Services_Utilities::modalException(tra('No topics were selected. Please select the topics you wish to delete before clicking the delete button.'));
			}
		//second pass - after popup modal form has been submitted
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			//perform delete
			$items = json_decode($input['items'], true);
			foreach ($items as $id => $name) {
				if (is_numeric($id)) {
					$this->lib->remove_comment($id);
				}
			}
			$extra = json_decode($input['extra'], true);
			$this->lib->forum_prune((int) $extra['forumId']);
			//prepare feedback
			if (count($items) == 1) {
				$msg = tra('The following topic has been deleted:');
			} else {
				$msg = tra('The following topics have been deleted:');
			}
			$feedback = [
				'tpl' => 'action',
				'mes' => $msg,
				'items' => $items,
				'deleted_forumId' => $extra['forumId']
			];
			Feedback::success($feedback, 'session');
			//return to page
			return Services_Utilities::refresh($extra['referer']);
		}
	}

	/**
	 * Moderator action to delete a forum post attachment
	 *
	 * @param $input
	 * @return array
	 * @throws Exception
	 */
	function action_delete_attachment($input)
	{
		$forumId = $input->forumId->int();
		$this->checkPerms($forumId);
		$check = Services_Exception_BadRequest::checkAccess();
		if (!empty($check['ticket'])) {
			if (isset($input['remove_attachment'])) {
				$items[$input->remove_attachment->int()] = $input['filename'];
				//provide redirect if js is not enabled
				$referer = Services_Utilities::noJsPath();
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'title' => tra('Please confirm deletion'),
						'confirmAction' => $input->action->word(),
						'confirmController' => 'forum',
						'customVerb' => tra('delete'),
						'customObject' => tra('attachment'),
						'items' => $items,
						'extra' => ['referer' => $referer],
						'ticket' => $check['ticket'],
						'modal' => '1',
					]
				];
			} else {
				Services_Utilities::modalException(tra('No attachments were selected. Please select an attachment to delete.'));
			}
		//second pass - after popup modal form has been submitted
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			//perform attachment delete
			$items = json_decode($input['items'], true);
			foreach ($items as $id => $name) {
				if (is_numeric($id)) {
					$this->lib->remove_thread_attachment($id);
				}
			}
			//prepare feedback
			if (count($items) == 1) {
				$msg = tra('The following attachment has been deleted:');
			} else {
				$msg = tra('The following attachments have been deleted:');
			}
			$feedback = [
				'tpl' => 'action',
				'mes' => $msg,
				'items' => $items,
			];
			Feedback::success($feedback, 'session');
			//return to page
			return Services_Utilities::refresh($extra['referer']);
		}
	}

	/**
	 * Moderator action that archives a forum thread
	 * @param $input
	 * @return array
	 */
	function action_archive_topic($input)
	{
		return $this->archiveUnarchive($input, 'archive');
	}

	/**
	 * Moderator action that archives a forum thread
	 * @param $input
	 * @return array
	 */
	function action_unarchive_topic($input)
	{
		return $this->archiveUnarchive($input, 'unarchive');
	}

	/**
	 * Action to delete one or more forums
	 *
	 * @param $input
	 * @return array
	 * @throws Exception
	 */
	function action_delete_forum($input)
	{
		$selected = $input->asArray('checked');
		$perms = Perms::get('forum', $selected);
		if (!$perms->admin_forum) {
			throw new Services_Exception_Denied(tr('Reserved for forum administrators'));
		}
		$check = Services_Exception_BadRequest::checkAccess();
		if (!empty($check['ticket'])) {
			//check number of topics on first pass
			if (count($selected) > 0) {
				$items = $this->getForumNames($selected);
				$object = count($items) > 1 ? 'forums' : 'forum';
				//provide redirect if js is not enabled
				$referer = Services_Utilities::noJsPath();
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'title' => tra('Please confirm deletion'),
						'confirmAction' => $input->action->word(),
						'confirmController' => 'forum',
						'customVerb' => tra('delete'),
						'customObject' => tr($object),
						'items' => $items,
						'extra' => ['referer' => $referer],
						'ticket' => $check['ticket'],
						'modal' => '1',
					]
				];
			} else {
				Services_Utilities::modalException(tra('No forums were selected. Please select a forum to delete.'));
			}
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$items = json_decode($input['items'], true);
			foreach ($items as $id => $name) {
				if (is_numeric($id)) {
					$this->lib->remove_forum($id);
				}
			}
			//prepare feedback
			if (count($items) === 1) {
				$msg = tra('The following forum has been deleted:');
			} else {
				$msg = tra('The following forums have been deleted:');
			}
			$feedback = [
				'tpl' => 'action',
				'mes' => $msg,
				'items' => $items,
			];
			Feedback::success($feedback, 'session');
			//return to page
			return Services_Utilities::refresh($extra['referer']);
		}
	}

	private function checkPerms($forumId)
	{
		$perm = $this->lib->admin_forum($forumId);
		if (!$perm) {
			throw new Services_Exception_Denied(tr('Reserved for forum administrators and moderators'));
		}
	}

	/**
	 * Utility to get topic names
	 *
	 * @param $topicIds
	 * @return mixed
	 * @throws Exception
	 */
	private function getTopicTitles(array $topicIds)
	{
		foreach ($topicIds as $id) {
			$info = $this->lib->get_comment($id);
			if (!empty($info['title'])){
				$ret[(int) $id] = $info['title'];
			} else {
				$ret[(int) $id] = TikiLib::lib('tiki')->get_snippet($info['data'], "", false, "", 60);
			}
		}
		return $ret;
	}

	/**
	 * Utility to get forum names
	 *
	 * @param $forumIds
	 * @return mixed
	 * @throws Exception
	 */
	private function getForumNames(array $forumIds)
	{
		foreach ($forumIds as $id) {
			$info = $this->lib->get_forum($id);
			$ret[(int) $id] = $info['name'];
		}
		return $ret;
	}


	/**
	 * Utility used by action_lock_topic and action_unlock_topic since the code for both is similar
	 * @param $input
	 * @param $type
	 * @return array
	 * @throws Exception
	 */
	private function lockUnlock($input, $type)
	{
		$forumId = $input->forumId->int();
		$this->checkPerms($forumId);
		$check = Services_Exception_BadRequest::checkAccess();
		if (!empty($check['ticket'])) {
			//check number of topics on first pass
			$selected = $input->asArray('forumtopic');
			if (count($selected) > 0) {
				$items = $this->getTopicTitles($selected);
				$object = count($items) > 1 ? 'topics' : 'topic';
				$referer = Services_Utilities::noJsPath();
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'title' => tr('Please confirm %0', tra($type)),
						'confirmAction' => $type . '_topic',
						'confirmController' => 'forum',
						'customVerb' => tra($type),
						'customObject' => $object,
						'items' => $items,
						'extra' => ['referer' => $referer],
						'ticket' => $check['ticket'],
						'modal' => '1',
					]
				];
			} else {
				Services_Utilities::modalException(tr('No topics were selected. Please select the topics you wish to %0 before clicking the %0 button.', tra($type)));
			}
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			$items = json_decode($input['items'], true);
			$fn = $type . '_comment';
			//do the locking/unlocking
			foreach ($items as $id => $topic) {
				$this->lib->$fn($id);
			}
			$extra = json_decode($input['extra'], true);
			//prepare feedback
			$typedone = $type == 'lock' ? tra('locked') : tra('unlocked');
			if (count($items) == 1) {
				$msg = tr('The following topic has been %0:', $typedone);
			} else {
				$msg = tr('The following topics have been %0:', $typedone);
			}
			$feedback = [
				'tpl' => 'action',
				'mes' => $msg,
				'items' => $items,
			];
			Feedback::success($feedback, 'session');
			//return to page
			return Services_Utilities::refresh($extra['referer']);
		}
	}

	/**
	 * Utility used by action_archive_topic and action_unarchive_topic since the code for both is similar
	 * @param $input
	 * @param $type
	 * @return array
	 * @throws Exception
	 */
	private function archiveUnarchive($input, $type)
	{
		$forumId = $input->forumId->int();
		$this->checkPerms($forumId);
		$check = Services_Exception_BadRequest::checkAccess();
		if (!empty($check['ticket'])) {
			if ($input['comments_parentId']) {
				$topicId = $input->comments_parentId->int();
				$items = $this->getTopicTitles([$topicId]);
				//provide redirect if js is not enabled
				$referer = Services_Utilities::noJsPath();
				return [
					'FORWARD' => [
						'controller' => 'access',
						'action' => 'confirm',
						'title' => tr('Please confirm %0', tra($type)),
						'confirmAction' => $type . '_topic',
						'confirmController' => 'forum',
						'customVerb' => tra($type),
						'customObject' => tra('thread'),
						'items' => $items,
						'extra' => [
							'comments_parentId' => $topicId,
							'referer' => $referer
						],
						'ticket' => $check['ticket'],
						'modal' => '1',
					]
				];
			} else {
				Services_Utilities::modalException(tr('No threads were selected. Please select the threads you wish to %0.', tra($type)));
			}
		} elseif ($check === true && $_SERVER['REQUEST_METHOD'] === 'POST') {
			//perform archive/unarchive
			$items = json_decode($input['items'], true);
			$extra = json_decode($input['extra'], true);
			$fn = $type . '_thread';
			$this->lib->$fn($extra['comments_parentId']);
			//prepare feedback
			$typedone = $type == 'archive' ? tra('archived') : tra('unarchived');
			if (count($items) == 1) {
				$msg = tr('The following thread has been %0:', $typedone);
			} else {
				$msg = tr('The following thread have been %0:', $typedone);
			}
			$feedback = [
				'tpl' => 'action',
				'mes' => $msg,
				'items' => $items,
			];
			Feedback::success($feedback, 'session');
			//return to page
			return Services_Utilities::refresh($extra['referer']);
		}
	}
}

