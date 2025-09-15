// Booking functionality
function bookAppointment(expertName) {
    const modal = document.getElementById('bookingModal');
    const expertNameElement = document.getElementById('expertName');
    
    expertNameElement.textContent = `with ${expertName}`;
    modal.style.display = 'block';
}

function closeBookingModal() {
    const modal = document.getElementById('bookingModal');
    modal.style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('bookingModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

// Form submission
document.addEventListener('DOMContentLoaded', function() {
    const bookingForm = document.querySelector('.booking-form');
    
    bookingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = new FormData(bookingForm);
        const expertName = document.getElementById('expertName').textContent;
        
        // Show success message
        alert(`Appointment booked successfully ${expertName}! You will receive a confirmation email shortly.`);
        
        // Close modal and reset form
        closeBookingModal();
        bookingForm.reset();
    });
});