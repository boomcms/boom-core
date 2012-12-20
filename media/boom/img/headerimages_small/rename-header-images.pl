#!/usr/bin/perl
	# Copyright 2009, Hoop Associates Ltd
	# Hoop Associates   www.thisishoop.com   mail@hoopassociates.co.uk

@data = `ls -1 .`;

foreach $line (@data) {
	$line =~ s/\r\n|\n//g;
	if ($line =~ m/jpg/g) {
		print "-" . $line . "-\n";
		@sp = split(/_/, $line);
		$sp[0] = int($sp[0]);
		`mv $line $sp[0].jpg`;
	}
}
