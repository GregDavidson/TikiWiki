<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: ldaplib.php 57967 2016-03-17 20:06:16Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class LdapLib extends TikiLib
{
	/**
	 * Retrieve a specific field from a LDAP filter.
	 *
	 * @param str $dsn
	 * @param str $filter
	 * @param str $field
	 * @param bool $all : return all records if true
	 * @return str or array if $all = true
	 */
	function get_field($dsn, $filter, $field, $all = false)
	{
		// Force autoloading
		if (! class_exists('ADOConnection')) {
			return null;
		}

		// Try to connect
		$ldaplink = ADONewConnection($dsn);
		$return = null;

		if (!$ldaplink) {
			// Wrong DSN
			return $return;
		}

		$ldaplink->SetFetchMode(ADODB_FETCH_ASSOC);
		$rs = $ldaplink->Execute($filter);
		if ($rs) {
			while ($arr = $rs->FetchRow()) {
				if (isset($arr[$field])) {
					// Retrieve field
					$return[] = $arr[$field];
					if ( $all === false ) break;
				}
			}
		}

		// Disconnect
		$ldaplink->Close();

		return ($all ? $return : array_shift($return)) ;
	}
}

