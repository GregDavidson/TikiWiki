#!/bin/bash
# (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id: findstyledef.sh 57973 2016-03-17 20:10:42Z jonnybradley $

# finds all the style and class definitions in tpl and php files
#
# param needed for execution: rootdir of tiki
# 
# ohertel@tw.o

perl ./findstyles.pl $1 | sort | uniq > result.txt
