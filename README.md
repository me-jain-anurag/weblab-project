# Ticket Booking System

## Project Overview
This is a comprehensive ticket booking system using XHTML, CSS, JavaScript, PHP, and Perl technologies.

## Technologies Used
- XHTML 1.0 Strict (Frontend Structure)
- CSS (Styling)
- JavaScript (Client-side Interactions)
- PHP (Backend Processing)
- Perl (Additional Processing)

## Features
- Dynamic seat selection
- Real-time seat availability checking
- Client-side and server-side validation
- Simple ticket booking and storage

## Project Structure
- `index.xhtml`: Main booking interface
- `styles/main.css`: Styling for the booking page
- `scripts/booking.js`: Client-side interactions
- `backend/book_ticket.php`: Ticket booking logic
- `backend/check_availability.php`: Seat availability checking
- `cgi-bin/process_booking.pl`: Additional ticket processing

## Setup Requirements
- Web Server (Apache recommended)
- PHP 7.4+
- Perl 5.x
- CGI module for Perl

## Installation Steps
1. Clone the repository
2. Configure web server to support XHTML, PHP, and Perl CGI
3. Ensure `data/tickets.txt` is writable
4. Set appropriate file permissions

## Security Considerations
- Implement additional server-side security measures
- Use prepared statements for database interactions
- Validate and sanitize all user inputs

## Limitations
- Uses file-based storage (replace with database for production)
- Basic error handling
- No advanced ticket management features

## Future Improvements
- Implement database backend
- Add user authentication
- Create admin panel for ticket management
- Implement more robust error handling