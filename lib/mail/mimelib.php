<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: mimelib.php 57967 2016-03-17 20:06:16Z jonnybradley $

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 *
 */
class mime
{
	function mime()
	{

	}

    /**
     * @param $input
     * @param string $default_ctype
     * @param string $crlf
     * @return array|bool
     */
    function decode($input,$default_ctype = 'text/plain', $crlf = "\r\n")
	{
		$back = array();

		$pos = strpos($input, $crlf.$crlf);
		if (!$pos) {
			$crlf = "\n";
			$pos = strpos($input, $crlf.$crlf);
			if (!$pos) {
				return false;
			}
		}

		$header = substr($input, 0, $pos);
		$body = substr($input, $pos + (2 * strlen($crlf)));

		$headparsed = preg_replace('/' . $crlf . "(\t| )/", ' ', $header);
		$heads = explode($crlf, trim($headparsed));

		if (substr($heads[0], 0, 5) == 'From ') {
			$heads[0] = str_replace('From ', 'x-From: ', $heads[0]);
		}

		foreach ($heads as $line) {
			$hdr_name = trim(substr($line, 0, strpos($line, ':')));
			$hdr_value = trim(substr($line, strpos($line, ':')+1));

			if (substr($hdr_value, 0, 1) == ' ')
				$hdr_value = substr($hdr_value, 1);

			$hdr_value = preg_replace('/(=\?[^?]+\?(Q|B|q|b)\?[^?]*\?=)( |' . "\t|" . $crlf . ')+=\?/', '\1=?', $hdr_value);

			while (preg_match('/(=\?([^?]+)\?(Q|B|q|b)\?([^?]*)\?=)/', $hdr_value, $matches)) {
				list(,$encoded,$charset,$encoding,$text) = $matches;

				switch ($encoding) {
					case 'B':
					case 'b':
						$text = base64_decode($text);
						break;

					case 'Q':
					case 'q':
						$text = str_replace('_', ' ', $text);
						preg_match_all('/=([A-F0-9]{2})/', $text, $matches);
						foreach ($matches[1] as $value) {
							$text = str_replace('=' . $value, chr(hexdec($value)), $text);
						}
						break;
				}

				if ($charset == 'iso-8859-1') {
					$text = utf8_encode($text);
				} elseif ($charset != 'utf-8' && function_exists('mb_convert_encoding')) {
					$text = mb_convert_encoding($text, 'utf-8', $charset);
				}

				$hdr_value = str_replace($encoded, $text, $hdr_value);
			}

			$lname = strtolower($hdr_name);

			if (isset($back['header'][$lname]) and !is_array($back['header'][$lname])) {
				$back['header'][$lname] = array($back['header'][$lname]);
				$back['header'][$lname][] = $hdr_value;
			} elseif (isset($back['header'][$lname])) {
				$back['header'][$lname][] = $hdr_value;
			} else {
				$back['header'][$lname] = $hdr_value;
			}

			$headers["$lname"] = $hdr_value;
		}

		while (list($key, $value) = each($headers)) {
			$input = $headers[$key];
			$it = array();
			if (($pos = strpos($input, ';')) !== false) {
				$it['value'] = trim(substr($input, 0, $pos));
				$input = trim(substr($input, $pos + 1));
				if (strlen($input) > 0) {
					preg_match_all('/(([[:alnum:]]+)="?([^"]*)"?\s?;?)+/i', $input, $matches);
					for ($i = 0, $icount_matches = count($matches[2]); $i < $icount_matches; $i++) {
						$it['other'][strtolower($matches[2][$i])] = $matches[3][$i];
					}
				}
			} else {
				$it['value'] = trim($input);
			}

			switch ($key) {
				case 'content-type':
					$content_type = $it;
					$back['type'] = $content_type['value'];
					if (isset($content_type['other'])) {
						while (list($p_name, $p_value) = each($content_type['other'])) {
							$back['ctype_parameters'][$p_name] = $p_value;
						}
					}
					break;

				case 'content-disposition':
					$content_disposition = $it;
					$back['disposition'] = $content_disposition['value'];

					if (isset($content_disposition['other'])) {
						while (list($p_name, $p_value) = each($content_disposition['other'])) {
							$back['d_parameters'][$p_name] = $p_value;
						}
					}
					break;

				case 'content-transfer-encoding':
					$content_transfer_encoding = $it;
					break;
			}
		}

		if (isset($content_type)) {
			$type = 'text';
			switch (strtolower($content_type['value'])) {
				case 'text/html':
					$type = 'html';

				case 'text/plain':
					if (!empty($content_disposition) && $content_disposition['value'] == 'attachment') {
						$back['attachments'][] = $back['d_parameters'];
					}
					$encoding = isset($content_transfer_encoding) ? $content_transfer_encoding['value'] : '7bit';
					$back['body'] = $this->decodeBody($body, $encoding);
					if ( array_key_exists('ctype_parameters', $back)
							and isset($back['ctype_parameters'])
							and $back['ctype_parameters']
							and (!isset($back['ctype_parameters']['charset']) or strtolower($back['ctype_parameters']['charset']) == 'iso-8858-1')
							and function_exists('utf8_encode')
					) {
						$back[$type][] = utf8_encode($back['body']);
					} elseif ( array_key_exists('ctype_parameters', $back)
										and isset($back['ctype_parameters'])
										and $back['ctype_parameters']
										and strtolower($back['ctype_parameters']['charset']) != 'utf-8'
										and function_exists('mb_convert_encoding')
					) {
					$back[$type][] = mb_convert_encoding($back['body'], 'utf-8', $back['ctype_parameters']['charset']);
					} else {
						$back[$type][] = $back['body'];
					}
					break;

				case 'multipart/signed':
				case 'multipart/digest':
				case 'multipart/alternative':
				case 'multipart/related':
				case 'multipart/mixed':
					$default_ctype = (strtolower($content_type['value']) === 'multipart/digest') ? 'message/rfc822' : 'text/plain';
					$tmp = explode('--' . $content_type['other']['boundary'], $body);
					for ($i = 1, $icount_tmp = count($tmp); $i < $icount_tmp - 1; $i++) {
						$parts[] = $tmp[$i];
					}

					for ($i = 0, $icount_parts = count($parts); $i < $icount_parts; $i++) {
						$back['parts'][] = $this->decode($parts[$i], $default_ctype);
					}
					break;

				case 'message/rfc822':
					$back['parts'][] = $this->decode($body);
					break;

				default:
					if (!isset($content_transfer_encoding['value'])) {
						$content_transfer_encoding['value'] = '7bit';
					}
					$back['body'] = $this->decodeBody($body, $content_transfer_encoding['value']);
					break;
			}
		} else {
			$back['body'] = $this->decodeBody($body);
		}
		$ctype = explode('/', $default_ctype);
		$back['ctype_primary'] = $ctype[0];
		$back['ctype_secondary'] = $ctype[1];

		return $back;
	}

    /**
     * @param $input
     * @param string $encoding
     * @return mixed|string
     */
    function decodeBody($input, $encoding = '7bit')
	{
		switch ($encoding) {
			case '7bit':
				return $input;
							break;

			case 'quoted-printable':
				$input = preg_replace('/=\r?\n/', '', $input);
				if (preg_match_all('/=[A-Z0-9]{2}/', $input, $matches)) {
					$matches = array_unique($matches[0]);
					foreach ($matches as $value) {
						$input = str_replace($value, chr(hexdec(substr($value, 1))), $input);
					}
				}
				return $input;

							break;

			case 'base64':
				return base64_decode($input);
							break;

			default:
				return $input;
		}
	}



	/**
	 * @param $decodedMail array	output from \mime::decode
	 * @param $type string			text or html
	 * @return string
	 */
	function getPartBody($decodedMail, $type)
	{
		$body = '';

		if (!empty($decodedMail['parts'])) {
			foreach ($decodedMail['parts'] as $part) {
				if (isset($part['parts'])) {
					return $this->getPartBody($part, $type);
				}
				if (empty($body) && isset($part[$type])) {
					$body = $part[$type];
					break;
				}
			}
		} else if (isset($decodedMail[$type])) {
			$body = $decodedMail[$type];
		} else if ($type === 'text' && isset($decodedMail['body'])) {
			$body = $decodedMail['body'];
		}
		if (is_array($body)) {
			$body = reset($body);
		}
		return $body;
	}


	/** replace MS "smart quotes" with dumb ones
	 * @param $body string
	 * @return string
	 */
	public function cleanQuotes($body)
	{
		$quotes = array(        // thanks to http://stackoverflow.com/a/1262210/2459703
			"\xC2\xAB" => '"', // « (U+00AB) in UTF-8
			"\xC2\xBB" => '"', // » (U+00BB) in UTF-8
			"\xE2\x80\x98" => "'", // ‘ (U+2018) in UTF-8
			"\xE2\x80\x99" => "'", // ’ (U+2019) in UTF-8
			"\xE2\x80\x9A" => "'", // ‚ (U+201A) in UTF-8
			"\xE2\x80\x9B" => "'", // ‛ (U+201B) in UTF-8
			"\xE2\x80\x9C" => '"', // “ (U+201C) in UTF-8
			"\xE2\x80\x9D" => '"', // ” (U+201D) in UTF-8
			"\xE2\x80\x9E" => '"', // „ (U+201E) in UTF-8
			"\xE2\x80\x9F" => '"', // ‟ (U+201F) in UTF-8
			"\xE2\x80\xB9" => "'", // ‹ (U+2039) in UTF-8
			"\xE2\x80\xBA" => "'", // › (U+203A) in UTF-8
		);
		$body = strtr($body, $quotes);
		return $body;
	}

    /**
     * @param $output
     * @return array
     */
    function get_bodies($output)
	{
			$bodies = array();	/* BUG: only one body for the moment */
			if (isset($output['text'][0]))
				$body = $output['text'][0];
			elseif (isset($output['parts'][0]) && isset($output['parts'][0]['text'][0]))
				$body = $output['parts'][0]['text'][0];
			elseif (isset($output['parts'][0]) && isset($output['parts'][0]['parts'][0]) && isset($output['parts'][0]['parts'][0]['text'][0]))
				$body = $output['parts'][0]['parts'][0]['text'][0];
			else
				$body = '';
			$bodies[] = $body;
			return $bodies;
	}

    /**
     * @param $output
     * @return array
     */
    function get_attachments($output)
	{
		$cnt = 0;
		$attachments = array();

		if (!isset($output['parts'])) {
			return $attachments;

		}

		$att = array();

		for ($it = 0, $itcount_output = count($output['parts']); $it < $itcount_output; $it++) {
			if (isset($output['parts'][$it]['d_parameters']['filename'])) {
				$attachmentPart = $output['parts'][$it];
				$att['part'] = $it;
				$att['name'] = $attachmentPart['d_parameters']['filename'];
				if (isset($attachmentPart['ctype_primary']))
					$att['type'] = $attachmentPart['ctype_primary'] .'/'. $attachmentPart['ctype_secondary'];
				else
					$att['type'] = '';
				$att['data'] = $attachmentPart['body'];
				$att['size'] = strlen($att['data']);
				$attachments[] = $att;
			}
		}

		return $attachments;
	}

}
