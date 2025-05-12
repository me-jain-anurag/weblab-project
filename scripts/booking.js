document.addEventListener('DOMContentLoaded', function() {
    const seatMap = document.getElementById('seat-map');
    const form = document.getElementById('ticket-form');
    const bookingStatus = document.getElementById('booking-status');

    // Generate seat map (10x10 grid)
    function generateSeatMap() {
        for (let i = 1; i <= 100; i++) {
            const seat = document.createElement('div');
            seat.classList.add('seat');
            seat.textContent = i;
            seat.dataset.seatNumber = i;
            
            seat.addEventListener('click', function() {
                if (!seat.classList.contains('booked')) {
                    seat.classList.toggle('selected');
                }
            });

            seatMap.appendChild(seat);
        }
    }

    // Client-side form validation
    function validateForm() {
        const event = document.getElementById('event').value;
        const date = document.getElementById('date').value;
        const tickets = document.getElementById('tickets').value;
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const selectedSeats = document.querySelectorAll('.seat.selected');

        if (!event || !date || !tickets || !name || !email) {
            bookingStatus.textContent = 'Please fill out all fields.';
            bookingStatus.className = 'error';
            return false;
        }

        if (selectedSeats.length === 0) {
            bookingStatus.textContent = 'Please select seats.';
            bookingStatus.className = 'error';
            return false;
        }

        if (selectedSeats.length !== parseInt(tickets)) {
            bookingStatus.textContent = 'Number of selected seats must match ticket count.';
            bookingStatus.className = 'error';
            return false;
        }

        return true;
    }

    // Fetch seat availability
    async function checkSeatAvailability() {
        try {
            const response = await fetch('backend/check_availability.php');
            const bookedSeats = await response.json();
            
            bookedSeats.forEach(seatNumber => {
                const seat = document.querySelector(`.seat[data-seat-number="${seatNumber}"]`);
                if (seat) seat.classList.add('booked');
            });
        } catch (error) {
            console.error('Error checking seat availability:', error);
        }
    }

    // Form submission handler
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!validateForm()) return;

        const formData = new FormData(form);
        const selectedSeats = Array.from(document.querySelectorAll('.seat.selected'))
            .map(seat => seat.dataset.seatNumber);
        formData.append('selectedSeats', JSON.stringify(selectedSeats));

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                bookingStatus.textContent = result.message;
                bookingStatus.className = 'success';
                form.reset();
                document.querySelectorAll('.seat.selected').forEach(seat => {
                    seat.classList.remove('selected');
                });
                checkSeatAvailability();
            } else {
                bookingStatus.textContent = result.message;
                bookingStatus.className = 'error';
            }
        } catch (error) {
            bookingStatus.textContent = 'An error occurred while booking tickets.';
            bookingStatus.className = 'error';
            console.error('Booking error:', error);
        }
    });

    // Initialize
    generateSeatMap();
    checkSeatAvailability();
});