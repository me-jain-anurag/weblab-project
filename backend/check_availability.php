<?php
// Set response header to JSON
header('Content-Type: application/json');

// Function to retrieve booked seats
function getBookedSeats() {
    // Initialize empty booked seats array
    $bookedSeats = [];
    
    // Check if tickets file exists
    if (file_exists('../data/tickets.txt')) {
        // Read all lines from tickets file
        $tickets = file('../data/tickets.txt', FILE_IGNORE_NEW_LINES);
        
        // Process each ticket line
        foreach ($tickets as $ticket) {
            // Split ticket data by pipe delimiter
            $ticketParts = explode('|', $ticket);
            
            // Ensure ticket has seat information (6th element)
            if (count($ticketParts) >= 6) {
                // Extract seat numbers
                $seatNumbers = explode(',', $ticketParts[5]);
                
                // Merge with existing booked seats
                $bookedSeats = array_merge($bookedSeats, $seatNumbers);
            }
        }
    }
    
    // Return unique booked seats
    return array_unique($bookedSeats);
}

// Additional optional filter for specific events or dates
function filterBookedSeats($event = null, $date = null) {
    $allBookedSeats = [];
    
    if (file_exists('../data/tickets.txt')) {
        $tickets = file('../data/tickets.txt', FILE_IGNORE_NEW_LINES);
        
        foreach ($tickets as $ticket) {
            $ticketParts = explode('|', $ticket);
            
            // Detailed ticket parts
            // 0: event, 1: date, 5: seats
            if (count($ticketParts) >= 6) {
                // Optional event filtering
                if ($event && $ticketParts[0] !== $event) {
                    continue;
                }
                
                // Optional date filtering
                if ($date && $ticketParts[1] !== $date) {
                    continue;
                }
                
                $seatNumbers = explode(',', $ticketParts[5]);
                $allBookedSeats[] = [
                    'event' => $ticketParts[0],
                    'date' => $ticketParts[1],
                    'seats' => $seatNumbers
                ];
            }
        }
    }
    
    return $allBookedSeats;
}

// Handle different request types
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Check if specific event or date is requested
    $event = isset($_GET['event']) ? $_GET['event'] : null;
    $date = isset($_GET['date']) ? $_GET['date'] : null;
    
    if ($event || $date) {
        // Return filtered booked seats
        echo json_encode(filterBookedSeats($event, $date));
    } else {
        // Return all booked seats
        echo json_encode(getBookedSeats());
    }
    exit;
}
?>