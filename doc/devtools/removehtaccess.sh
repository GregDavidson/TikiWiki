#!/bin/bash
# (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id: removehtaccess.sh 57973 2016-03-17 20:10:42Z jonnybradley $

# Script to remove _htaccess which can be browsed unless hidden
# these files give an attacker useful information

if [ ! -d 'db' ]; then
        echo "You must launch this script from your (multi)tiki root dir."
        exit 0
fi
		

find . -name _htaccess -type f -exec rm -f {} \;

echo "Done."
