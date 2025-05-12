#!/usr/bin/perl
use strict;
use warnings;
use CGI;
use JSON;

# Process additional ticket-related operations
sub generate_ticket_report {
    my $tickets_file = '../data/tickets.txt';
    my %event_summary;
    
    # Read and process ticket data
    open(my $fh, '<', $tickets_file) or die "Could not open file '$tickets_file': $!";
    
    while (my $line = <$fh>) {
        chomp $line;
        my @ticket_data = split /\|/, $line;
        
        # Validate ticket data
        next unless scalar @ticket_data >= 6;
        
        my ($event, $date, $num_tickets, $name, $email, $seats, $timestamp) = @ticket_data;
        
        # Aggregate event sales
        $event_summary{$event}{total_tickets} += $num_tickets;
        $event_summary{$event}{total_revenue} += calculate_ticket_price($event, $num_tickets);
        push @{$event_summary{$event}{bookings}}, {
            name => $name,
            email => $email,
            date => $date,
            seats => $seats
        };
    }
    close $fh;
    
    return \%event_summary;
}

# Simple ticket pricing function
sub calculate_ticket_price {
    my ($event, $num_tickets) = @_;
    
    my %pricing = (
        'concert' => 50,
        'theater' => 75,
        'sports' => 100
    );
    
    my $base_price = $pricing{$event} || 50;
    return $base_price * $num_tickets;
}

# CGI processing
my $cgi = CGI->new();
print $cgi->header('application/json');

my $action = $cgi->param('action') || '';

if ($action eq 'report') {
    my $report = generate_ticket_report();
    print encode_json($report);
} else {
    print encode_json({
        error => 'Invalid action',
        supported_actions => ['report']
    });
}

exit 0;