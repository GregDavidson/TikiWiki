#!/usr/bin/perl -w
# (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
# 
# All Rights Reserved. See copyright.txt for details and a complete list of authors.
# Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
# $Id: parse_changelog.pl 57973 2016-03-17 20:10:42Z jonnybradley $

use strict;

my $lastFile;

my %already;

my (@msgs, @new, @mod, @fix, @enh, @uncateg);

my %files;

our $TAG = 'REL-1-9-2';
our $CVSCMD = 'cvs log -N -r'.$TAG.'::';

#below is untested
if (!defined $ARGV[0]) {
    open(CVSCMD, $CVSCMD."|");
    *STDIN = *CVSCMD;
}

while (my $line = <>) {
    if ($line =~ m|^RCS file:|) {
	$line = <>;

	($lastFile) = $line =~ /Working file:\s*(\S+)/;
    }

    $lastFile or next;

    if ($line =~ /^description:/) {
	$line = <>;

	my $msg;

	while ($line && $line !~ /^=+$/) {
	    $msg = '';

	    my $revision = <>;
	    my $date = <>;

	    if ($revision !~ /^revision/ || $date !~ /^date:/) {
		die "bug!";
	    }

	    $line = <>;
	    while ($line !~ /^[-=]+$/) {
		$msg .= $line;
		$line = <>;
		if ($line =~ /\[(MOD|ADD|NEW|FIX|ENH)\]/) {
		    registerMsg($msg);
		    $msg = '';
		}
	    }
	    registerMsg($msg);
	}
    }
}

&printMsgs('New features', @new);
&printMsgs('Modifications', @mod);
&printMsgs('Enhancements', @enh);
&printMsgs('Bugfixes', @fix);
&printMsgs('Uncategorized', @uncateg);


sub registerMsg {
    my $msg = shift;
    chomp $msg;
    if ($msg) {
	if (!$already{$msg}) {
	    push @msgs, $msg;
	    if ($msg =~ /\[(ADD|NEW)\]/) {
		push @new, $msg;
	    } elsif ($msg =~ /\[MOD\]/) {
		push @mod, $msg;
	    } elsif ($msg =~ /\[FIX\]/) {
		push @fix, $msg;
	    } elsif ($msg =~ /\[ENH\]/) {
		push @enh, $msg;
	    } else {
		push @uncateg, $msg;
	    }
	    
	    $files{$msg} = [ $lastFile ];
	    
	    $already{$msg} = 1;
	} else {
	    push @{$files{$msg}}, $lastFile;
	}
    }
}

sub printMsgs {
    my $title = shift;
    my @list = @_;

    print '=' x 30, "\n";
    print $title, ":\n";

    foreach my $msg (@list) {
	print $msg, "\n";
	print '-' x 30, "\n";
    }
    print "\n";
}

sub help {
    print "usage: parse_changelog.pl cvslogfile.txt\n";
    print 'use "$CVSCMD > cvslogfile.txt" to obtain log', "\n";
    exit;
}

1;
