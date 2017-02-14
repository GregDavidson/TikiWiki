<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Controller.php 59599 2016-09-05 17:16:49Z amnabilal $

class Services_AutoSave_Controller
{
	function setUp()
	{
		Services_Exception_Disabled::check('feature_ajax');
		Services_Exception_Disabled::check('ajax_autosave');
		Services_Exception_Disabled::check('feature_warn_on_edit');
	}

	/**
	 * Get contents of autosave
	 *
	 * @param $input JitFilter    editor_id, referer
	 * @return array data         string: markup contents
	 */

	function action_get($input)
	{
		$referer = $input->referer->text();
		$res = '';

		if ($this->checkReferrer($referer)) {
			$res = TikiLib::lib('autosave')->get_autosave($input->editor_id->text(), $referer);
		}

		return array(
			'data' => $res,
		);
	}

	/**
	 * Save something to a autosave
	 *
	 * @param $input JitFilter    editor_id, referer
	 * @return array              int: chars saved
	 */

	function action_save($input)
	{
		$referer = $input->referer->text();
		$res = '';

		if ($this->checkReferrer($referer)) {
			$data = $input->data->none();
			$res = TikiLib::lib('autosave')->auto_save($input->editor_id->text(), $data, $referer);
		}

		return array(
			'data' => $res,
		);
	}

	/**
	 * Remove autosave (cache file)
	 *
	 * @param $input JitFilter	editor_id, referer
	 * @return array
	 */

	function action_delete($input)
	{
		$referer = $input->referer->text();

		if ($this->checkReferrer($referer)) {
			TikiLib::lib('autosave')->remove_save($input->editor_id->text(), $referer);
		}

		return array();
	}

	/**
	 * Check if user can and is editing that object
	 *
	 * @param $referer string  user:section:object id
	 * @return bool
	 */

	private function checkReferrer($referer)
	{
		global $page, $user;

		$referer = explode(':', $referer);	// user, section, object id
		$isok = false;

		if ($referer && count($referer) === 3 && $referer[1] === 'wiki_page') {
			$page = rawurldecode($referer[2]);	// plugins use global $page for approval

			$isok = Perms::get('wiki page', $page)->edit && $user === TikiLib::lib('tiki')->get_semaphore_user($page);
		}

		return $isok;
	}
	//function added to hold current state of fancy table / sorted table for pdf and print version. So when user generates pdf he gets his sorted data not default data in table.
	function action_storeTable($input){
	   //write content to file
	    $tableName=$input->tableName->text();
	    //$tableHTML=$input->tableHTML->text();
	   $tableFile=fopen("temp/".$tableName.'_'.session_id().".txt","w");
	   //fwrite($tableFile,$input->tableHTML->text());
	   fwrite($tableFile,$input->tableHTML->html());
	   //create session array to hold temp tables for printing, table original name and file name
	   chmod($tableFile,0755);
		
	}
}

