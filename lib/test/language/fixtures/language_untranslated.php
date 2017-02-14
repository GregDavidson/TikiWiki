<?php // -*- coding:utf-8 -*-
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: language_untranslated.php 57963 2016-03-17 20:03:23Z jonnybradley $

// Parameters:

// lang=xx    : only translates language 'xx',
//              if not given all languages are translated

// comments   : generate all comments (equal to close&module)

// close      : look for similar strings that are already translated and
//              generate a comment if a 'match' is made

// module     : generate comments that describe in which .php and/or .tpl
//              module(s) a certain string was found (useful for checking
//              translations in context)

// patch      : looks for the file 'language.patch' in the same directory
//              as the corresponding language.php and overrides any strings
//              in language.php - good if a user does not agree with
//              some translations or if only changes are sent to the maintainer

// spelling   : generates a file 'spellcheck_me.txt' that contains the
//              words used in the translation. It is then easy to check this
//              file for spelling errors (corrections must be done in
//              'language.php, however)

// groupwrite : Sets the generated files permissions to allow the generated
//              language.php also be group writable. This is good for
//              translators if they do not have root access to tiki but
//              are in the same group as the webserver. Please remember
//              to have write access removed when translation is finished
//              for security reasons. (Run script again without this
//              parameter)

// Examples:
// http://www.neonchart.com/get_strings.php?lang=sv
// Will translate language 'sv' and (almost) avoiding comment generation

// http://www.neonchart.com/get_strings.php?lang=sv&comments
// Will translate language 'sv' and generate all possible comments.
// This is the most usefull mode when working on a translation.

// http://www.neonchart.com/get_strings.php?lang=sv&nohelp&nosections
// These options will only provide the minimal amout of comments.
// Usefull mode when preparing a translation for distribution.

// http://www.neonchart.com/get_strings.php?nohelp&nosections
// Prepare all languages for release


// ### Note for translators about translation of text ending with punctuation
// ###
// ### The current list of concerned punctuation can be found in 'lib/init/tra.php'
// ### On 2009-03-02, it is: (':', '!', ';', '.', ',', '?')
// ### For clarity, we explain here only for colons: ':' but it is the same for the rest
// ###
// ### Short version: it is not a problem that string "Log In:" has no translation. Only "Log In" needs to be translated.
// ###
// ### Technical justification:
// ### If a string ending with colon needs translating (like "{tr}Login:{/tr}")
// ### then TikiWiki tries to translate 'Log In' and ':' separately.
// ### This allows to have only one translation for "{tr}Login{/tr}" and "{tr}Login:{/tr}"
// ### and it still allows to translate ":" as "&nbsp;:" for languages that
// ### need it (like french)

$lang=Array(
// ### Start of unused words
// ### Please remove manually!
// ### N.B. Legitimate strings may be marked// ### as unused!
// ### Please see http://tiki.org/tiki-index.php?page=UnusedWords for further info
"categorize" => "categorizar",
"Set prefs" => "Definir preferências",
"creation date" => "data de criação",
"last modification time" => "data da última modificação",
"Invalid old password or unknown user" => "Senha antiga inválida ou usuário desconhecido",
"Contributions by author" => "Contribuições por autor",
// ### end of unused words

// ### start of untranslated words
// ### uncomment value pairs as you translate
// "Kalture Video" => "Kalture Video",
"Communication error" => "Erro de comunicação",
// "Invalid response provided by the Kaltura server. Please retry" => "Invalid response provided by the Kaltura server. Please retry",
// "Delete comments" => "Delete comments",
"Approved Status" => "Aprovado",
// "Queued" => "Queued",
// "The file is already locked by %s" => "The file is already locked by %s",
// "Warning: The file is used in" => "Warning: The file is used in",
// "You do not have permission to edit this file" => "You do not have permission to edit this file",
// ### end of untranslated words
// ###

// ###
// ### start of possibly untranslated words
// ###
"###end###"=>"###end###");

