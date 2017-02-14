Tikiwiki DevTools 
-----------------
$Id: README.txt 20346 2009-07-26 14:39:02Z kissaki $

The content of this directory is intended to be used for 
Tikiwiki development. It's included in the package for use
of developers whose task is to release new versions of tikiwiki.

Main tools
==========

* release.sh
  create tarballs for release/testing

* tikiwiki.spec
  made by dheltzel
  script to build a rpm package from tarball release.

* tikirelease.sh
  made by mose
  script for lazy release manager. edit it before use !!

* ggg-trace.php
  made by George G. Geller
  For tracing and debugging php code -- output a text file.


Helper tools
============

* findstyledef.sh, findstyledef.pl
  made by ohertel
  Provide a report on CSS classes referenced in tpl and php files

* csscheck.sh, stripbraces.pl, stripcomments.pl
  made by mdavey
  Provide a report on CSS classes used in a stylesheet
  See http://tikiwiki.org/RecipeRestoreCss for details

