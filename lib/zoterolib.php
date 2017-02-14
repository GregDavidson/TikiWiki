<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: zoterolib.php 57961 2016-03-17 20:01:56Z jonnybradley $

/**
 *
 */
class ZoteroLib extends TikiDb_Bridge
{
    /**
     * @return bool
     */
    function is_authorized()
	{
		$oauthlib = TikiLib::lib('oauth');
		return $oauthlib->is_authorized('zotero');
	}

    /**
     * @param $tag
     * @param int $limit
     * @return array|bool
     */
    function get_references($tag, $limit = 25)
	{
		global $prefs;

		$subset = null;
		if ($tag) {
			$subset = '/tags/' . rawurlencode($tag);
		}

		$arguments = array(
			'content' => 'bib',
			'limit' => $limit,
		);

		if (! empty($prefs['zotero_style'])) {
			$arguments['style'] = $prefs['zotero_style'];
		}

		$oauthlib = TikiLib::lib('oauth');
		$response = $oauthlib->do_request(
			'zotero',
			array(
				'url' => "https://api.zotero.org/groups/{$prefs['zotero_group_id']}$subset/items",
				'get' => $arguments,
			)
		);

		if ($response && $response->isSuccessful()) {
			$feed = Zend\Feed\Reader\Reader::importString($response->getBody());

			$data = array();
			foreach ($feed as $entry) {
				$data[] = array(
					'key' => basename($entry->getLink()),
					'url' => $entry->getLink(),
					'title' => $entry->getTitle(),
					'content' => $entry->getDescription(),
				);
			}

			return $data;
		}

		return false;
	}

    /**
     * @param $tag
     * @return bool|mixed
     */
    function get_first_entry($tag)
	{
		if ($references = $this->get_references($tag, 1)) {
			return reset($references);
		}

		return false;
	}

    /**
     * @param $itemId
     * @return array|bool
     */
    function get_entry($itemId)
	{
		global $prefs;

		$arguments = array(
			'content' => 'bib',
		);

		if (! empty($prefs['zotero_style'])) {
			$arguments['style'] = $prefs['zotero_style'];
		}

		$oauthlib = TikiLib::lib('oauth');
		$response = $oauthlib->do_request(
			'zotero',
			array(
				'url' => "https://api.zotero.org/groups/{$prefs['zotero_group_id']}/items/" . urlencode($itemId),
				'get' => $arguments,
			)
		);

		if ($response->isSuccessful()) {
			$entry = $response->getBody();
			$entry = str_replace('<entry ', '<feed xmlns="http://www.w3.org/2005/Atom"><entry ', $entry) . '</feed>';
			$feed = Zend\Feed\Reader\Reader::importString($entry);

			foreach ($feed as $entry) {
				return array(
					'key' => basename($entry->getLink()),
					'url' => $entry->getLink(),
					'title' => $entry->getTitle(),
					'content' => $entry->getDescription(),
				);
			}
		}

		return false;
	}

    /**
     * @param $tag
     * @return bool
     */
    function get_formatted_references($tag)
	{
		global $prefs;

		$subset = null;
		if ($tag) {
			$subset = '/tags/' . rawurlencode($tag);
		}

		$arguments = array(
			'content' => 'bib',
			'format' => 'bib',
			'limit' => 500,
		);

		if (! empty($prefs['zotero_style'])) {
			$arguments['style'] = $prefs['zotero_style'];
		}

		$oauthlib = TikiLib::lib('oauth');
		$response = $oauthlib->do_request(
			'zotero',
			array(
				'url' => "https://api.zotero.org/groups/{$prefs['zotero_group_id']}$subset/items",
				'get' => $arguments,
			)
		);

		if ($response->isSuccessful()) {
			$entry = $response->getBody();

			return $entry;
		}

		return false;
	}
}

