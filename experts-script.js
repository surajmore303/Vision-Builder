// Booking functionality
function bookAppointment(expertName) {
    const modal = document.getElementById('bookingModal');
    const expertNameElement = document.getElementById('expertName');
    const expertNameInput = document.getElementById('expertNameInput');
    
    expertNameElement.textContent = `with ${expertName}`;
    expertNameInput.value = expertName;
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
    const bookingForm = document.getElementById('bookingForm');
    
    bookingForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(bookingForm);
        formData.append('action', 'book_appointment');
        
        fetch('booking_handler.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                closeBookingModal();
                bookingForm.reset();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            alert('Booking failed. Please try again.');
        });
    });
});