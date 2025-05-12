<?php
header('Content-Type: application/json');

function saveTicket($event, $date, $tickets, $name, $email, $selectedSeats) {
    $ticketData = implode('|', [
        $event,
        $date,
        $tickets,
        $name,
        $email,
        implode(',', $selectedSeats),
        date('Y-m-d H:i:s')
    ]);

    $result = file_put_contents('../data/tickets.txt', $ticketData . PHP_EOL, FILE_APPEND);
    return $result !== false;
}

function isSeatsAvailable($selectedSeats) {
    $bookedSeats = file_exists('../data/tickets.txt') 
        ? file('../data/tickets.txt', FILE_IGNORE_NEW_LINES) 
        : [];

    // Extract all booked seats from existing tickets
    $allBookedSeats = [];
    foreach ($bookedSeats as $ticket) {
        $ticketParts = explode('|', $ticket);
        if (isset($ticketParts[5])) {
            $allBookedSeats = array_merge(
                $allBookedSeats, 
                explode(',', $ticketParts[5])
            );
        }
    }

    // Check if any selected seats are already booked
    foreach ($selectedSeats as $seat) {
        if (in_array($seat, $allBookedSeats)) {
            return false;
        }
    }

    return true;
}

// Handle booking request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => 'Booking failed'];

    // Validate input
    $requiredFields = ['event', 'date', 'tickets', 'name', 'email', 'selectedSeats'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $response['message'] = "Missing required field: $field";
            echo json_encode($response);
            exit;
        }
    }

    $event = filter_input(INPUT_POST, 'event', FILTER_SANITIZE_STRING);
    $date = filter_input(INPUT_POST, 'date', FILTER_SANITIZE_STRING);
    $tickets = filter_input(INPUT_POST, 'tickets', FILTER_VALIDATE_INT);
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $selectedSeats = json_decode($_POST['selectedSeats'], true);

    if (!$email) {
        $response['message'] = 'Invalid email address';
        echo json_encode($response);
        exit;
    }

    // Additional validation
    if (count($selectedSeats) !== $tickets) {
        $response['message'] = 'Seat count mismatch';
        echo json_encode($response);
        exit;
    }

    // Check seat availability
    if (!isSeatsAvailable($selectedSeats)) {
        $response['message'] = 'Some selected seats are already booked';
        echo json_encode($response);
        exit;
    }

    // Save ticket
    if (saveTicket($event, $date, $tickets, $name, $email, $selectedSeats)) {
        $response['success'] = true;
        $response['message'] = 'Tickets booked successfully!';
    }

    echo json_encode($response);
    exit;
}
?>