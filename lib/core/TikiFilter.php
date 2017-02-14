<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: TikiFilter.php 60313 2016-11-18 02:29:40Z drsassafras $

class TikiFilter
{
	/**
	 * Provides a filter instance based on the input. Either a filter
	 * can be passed or a name.
	 * 
	 * @param mixed
	 * @return \Zend\Filter\FilterInterface
	 */
	public static function get( $filter )
	{
		if ( $filter instanceof \Zend\Filter\FilterInterface ) {
			return $filter;
		}

		switch( $filter )
		{
		case 'alpha':
			return new TikiFilter_Alpha;    // Removes all but alphabetic characters.
        case 'word':
            return new TikiFilter_Word;     // A single word of alphabetic characters (im pretty sure) ?I18n?
		case 'alnum':
			return new TikiFilter_Alnum;    // Only alphabetic characters and digits. All other characters are suppressed. I18n support
		case 'digits':
			return new Zend\Filter\Digits;  // Removes everything except for digits eg. '12345 to 67890' returns 1234567890
		case 'int':
			return new Zend\Filter\ToInt;   // Allows you to transform a sclar value which contains into an integer. eg. '-4 is less than 0' returns -4
		case 'isodate':
			return new TikiFilter_IsoDate;
		case 'isodatetime':
			return new TikiFilter_IsoDate('Y-m-d H:i:s');
		case 'username':
		case 'groupname':
		case 'pagename':
		case 'topicname':
		case 'themename':
		case 'email':
		case 'url':
		case 'text':
		case 'date':
		case 'time':
		case 'datetime':
			// Use striptags
		case 'striptags':
			return new Zend\Filter\StripTags;   // Strips XML and HTML tags
		case 'xss':
			return new TikiFilter_PreventXss;   // Leave everything except for potentially malicious HTML
		case 'purifier':
			return new TikiFilter_HtmlPurifier('temp/cache');  // Strips non-valid HTML and potentially malicious HTML.
		case 'wikicontent':
			return new TikiFilter_WikiContent;
		case 'rawhtml_unsafe':
		case 'none':
			return new TikiFilter_RawUnsafe;
		case 'lang':
			return new Zend\Filter\PregReplace('/^.*([a-z]{2})(\-[a-z]{2}).*$/', '$1$2');
		case 'imgsize':
			return new Zend\Filter\PregReplace('/^.*(\d+)\s*(%?).*$/', '$1$2');
		case 'attribute_type':
			return new TikiFilter_AttributeType;
		default:
			trigger_error('Filter not found: ' . $filter, E_USER_WARNING);
			return new TikiFilter_PreventXss;
		}
	}
}
