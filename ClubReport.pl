#!/usr/bin/perl
use DBI;
use MIME::Lite;
use MIME::Base64;
#use Net::SMTP;
use strict;
use Text::CSV;
use warnings;

my($mday, $mon, $year) = (localtime)[3,4,5];
my $mydate = sprintf "%.4d-%2d-%2d", $year+1900, $mon+1, $mday;

# set name of database to connect to
#my $database='TestBadge';
my $database='BadgeDB';
my $report = '';
my $eval_rtn = 99;

sub SafeFile {
	use utf8;
	use Unicode::Normalize;

	my ($input) = @_;
	my $result = NFD($input); # Unicode normalization Form D (NFD), canonical decomposition.
	$result =~ s/[^[:ascii:]]//g; # Remove all non-ascii.
	$result =~ s/ - /_/g; # Replace all " - " with "_"
	$result =~ s/[^A-Za-z0-9]/_/g; # Replace all non-alphanumericals with _
	return $result;
}

# connection to the database
my $dbh = DBI->connect("dbi:mysql:$database", "badge", "badgeuserpassword")
or die "Can't make database connect: $DBI::errstr\n";

my $query = "SELECT * FROM clubs WHERE status = 0 AND poc_email <> '' ORDER BY club_name";  # LIMIT 1";
my $rec = $dbh->prepare($query);
my $numrows = $rec->execute();

print "Found " . $numrows . " clubs\n";

#$csv->print($fh, $rec->{NAME});

while ( (my @club) = $rec->fetchrow_array() ) {

print "Processing: " . $club[2];
	my $shortname=$club[3];
	my $poc=$club[4];

	my $msg = MIME::Lite->new(
		From    => 'AGC Range <no-reply@associatedgunclubs.org>',
		'Reply-To' => 'president@associatedgunclubs.org',
		To      => $poc,
	    Cc      => 'president@associatedgunclubs.org',
		Bcc		=> 'sharkbit@hotmail.com',
		Subject => $shortname."'s active members of AGC",
		Type    => 'multipart/mixed',
	);

	## Full Table List *, Note Some fields must be quoted due to use od reserved words.
	## id,badge_number,prefix,first_name,last_name,suffix,address,city,state,zip,gender,yob,email,email_op,phone,phone_op,ice_contact,ice_phone,club_name,club_id,mem_type,badge_type,primary,incep,expires,qrcode,wt_date,wt_instru,remarks,status,badge_subscription_id,work_credits,soft_delete,created_at,updated_at,sticker
	my $query2 = "SELECT badge_number,`prefix`,first_name,last_name,suffix,address,city,`state`,zip,gender,yob,email,email_op,phone,phone_op,ice_contact,ice_phone,incep,expires,wt_date,wt_instru,status ".
		" FROM badges WHERE status = 'approved' AND  expires > now() ".
			" AND badge_number in (SELECT badge_number FROM badge_to_club WHERE club_id=".$club[1].")";
 #print "\n".$query2."\n";
 #exit;

	my $rec2 = $dbh->prepare($query2);
	my $memrows = $rec2->execute();
	
	if ($memrows > 0) { ## Found records, Process data.
		print " - ".$memrows." active members\n";
		$msg->attach(
			Type     => 'TEXT',
			Data     => " Here is your club roster as of today.\n\n AGC Range",
		);

		my $csv = Text::CSV->new({ binary => 1, eol => "\r\n", quote_char => "'" })
		  or die "Cannot use CSV: " . Text::CSV->error_diag();
		my $clubfile=SafeFile($club[2]).'-'.$mydate.".csv";
		open my $fh, ">:raw", $clubfile; 

		my @cols = @{$rec2->{NAME}};
		$csv->print($fh, \@cols);
		
		while ( (my @member) = $rec2->fetchrow_array() ) {
			$csv->print($fh, \@member);
		}
		close $fh or die "Failed to write CSV: $!"; 

		$report = $report.$shortname." has ".$memrows." current members.  Report sent to ".$poc.",";

		$msg->attach(
			Type     => 'text/csv',
			Path     => $clubfile,
			Filename => $clubfile,
		);

		$eval_rtn = eval { $msg->send };

		unlink $clubfile;
	} else { ## No Records Found
		print " - 0 active members\n";
		$report = $report.$shortname." has no active members.  Report sent to ".$poc.",";
		$msg->attach(
			Type     => 'TEXT',
			Data     => "Your club has no active members at AGC as of today.\n\n ",
		);

		$eval_rtn = eval { $msg->send };
	}

	if ( $eval_rtn == 1 ) {
		$report = $report." Mail Sent!\n";
	} else {
		$report = $report." Error:".$eval_rtn." Mail Failed!\n";
	}

}

print "\n\n".$report;

