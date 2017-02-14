<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: WriteFile.php 60593 2016-12-11 16:52:35Z chealer $

require_once('Language.php');

/**
 * @package   Tiki
 * @subpackage    Language
 * Class to update language.php file with new
 * collected strings.
 */
class Language_WriteFile
{
	/**
	 * Representation of the language file.
	 * @var Language_File
	 */
	protected $parseFile;
	
	/**
	 * Path to a language.php file
	 * @var string
	 */
	protected $filePath;
	
	/**
	 * Path to temporary language file.
	 * @var string
	 */
	protected $tmpFilePath;
	
	/**
	 * Current language translations.
	 * @var array
	 */
	protected $translations;
	
	public function __construct(Language_File $parseFile)
	{
		$this->parseFile = $parseFile;
		$this->filePath = $parseFile->filePath;
		$this->tmpFilePath = $this->filePath . '.tmp';
		
		if (!is_writable($this->filePath)) {
			throw new Language_Exception("Can't write to file $this->filePath.");
		}
	}
	
	/**
	 * Update language.php file with new strings.
	 * 
	 * @param array $strings English strings collected from source files
	 * @param bool $outputFiles whether file paths were string was found should be included or not in the output
	 * @return null
	 */
	public function writeStringsToFile(array $strings, $outputFiles = false)
	{
		if (empty($strings)) {
			return false;
		}
		
		// backup original language file
		copy($this->filePath, $this->filePath . '.old');
		
		$this->translations = $this->parseFile->getTranslations();

		$punctuations = array(':', '!', ';', '.', ',', '?'); // Should stay synchronized with tra_impl() (in lib/init/tra.php)
		$entries = array();
		foreach ($strings as $string) {
			if (isset($this->translations[$string['name']])) {
				$string['translation'] = $this->translations[$string['name']];
			} else {
				// Handle punctuations at the end of the string (cf. comments in lib/init/tra.php)
				// For example, if the string is 'Login:', we put 'Login' for translation instead
				// (except if we already have an explicit translation for 'Login:', in which case we don't reach this else)
				$stringLength = strlen($string['name']);
				$stringLastChar = $string['name'][$stringLength - 1];

				if (in_array($stringLastChar, $punctuations) ) {
					$trimmedString = substr($string['name'], 0, $stringLength - 1);
					$string['name'] = $trimmedString;
					if (isset($this->translations[$trimmedString])) {
						$string['translation'] = $this->translations[$trimmedString];
					}
				}
			}
			$entries[$string['name']] = $string;
		}


		$handle = fopen($this->tmpFilePath, 'w');
		
		if ($handle) {
			fwrite($handle, "<?php\n");
			fwrite($handle, $this->fileHeader());
			fwrite($handle, "\$lang = array(\n");
			
			foreach ($entries as $entry) {
				fwrite($handle, $this->formatString($entry, $outputFiles));
			}
			
			fwrite($handle, ");\n");
			fclose($handle);
		}
		
		rename($this->tmpFilePath, $this->filePath);	
	}

	/**
	 * Return the text used for language.php header
	 * @return string
	 */
	protected function fileHeader()
	{
		$header = <<<TXT
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Note for translators about translation of text ending with punctuation
// 
// The concerned punctuation signs can be found in tra_impl() (in 'lib/init/tra.php').
// As of 2016-12-04, these are: ':', '!', ';', '.', ',', '?'.
// For clarity, we explain here only for colons (':'), but it is the same for the rest.
// 
// Short version: it is not a problem that string "Login:" has no translation. Only "Login" needs to be translated.
// 
// Technical justification:
// If a string ending with colon needs translating (like "{tr}Login:{/tr}")
// then Tiki tries to translate 'Login' and ':' separately.
// This allows to have only one translation for "{tr}Login{/tr}" and "{tr}Login:{/tr}"
// and it still allows to translate ":" as " :" for languages that
// need it (like French)
// Note: the difference is invisible but " :" has an UTF-8 non-breaking-space, not a regular space, but the UTF-8 equivalent of the HTML &nbsp;.
// This allows correctly displaying emails and JavaScript messages, not only web pages as would happen with &nbsp;.


TXT;
		
		return $header;
	}

	/**
	 * Format a pair source and translation as
	 * a string to be written to a language.php file
	 * 
	 * @param array $entry an array with the English source string and the translation if any 
	 * @param bool $outputFiles whether file paths were string was found should be included or not in the output
	 * @return string
	 */
	protected function formatString(array $entry, $outputFiles = false)
	{
		// final formated string
		$string = '';
		
		if ($outputFiles && (isset($entry['files']) && !empty($entry['files']))) {
			$string .= '/* ' . join(', ', $entry['files']) . " */\n";
		}
		
		$source = Language::addPhpSlashes($entry['name']);
		
		if (isset($entry['translation'])) {
			$trans = Language::addPhpSlashes($entry['translation']);			
			$string .= "\"$source\" => \"$trans\",\n";
		} else {
			$string .= "// \"$source\" => \"$source\",\n";
		}
		
		return $string;
	}
}
