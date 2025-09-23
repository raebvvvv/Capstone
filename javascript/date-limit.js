
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.querySelector('input[name="date_accomplished"]');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.max = today;
    }
});

