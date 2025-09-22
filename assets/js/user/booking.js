document.addEventListener('DOMContentLoaded', function() {
    const eventSelect = document.getElementById('theme');
    const packagesSelect = document.getElementById('packages');
    const packagesField = document.getElementById('packages-field');

    // Initial state: hide the packages field on page load
    packagesField.classList.add('hidden');
    
    // Hide all options inside the packages select menu initially
    packagesSelect.querySelectorAll('option').forEach(option => {
        option.style.display = 'none';
    });
    
    // Re-show the default "choose Packages" option
    packagesSelect.querySelector('option[value=""]').style.display = 'block';

    eventSelect.addEventListener('change', function() {
        const selectedEvent = this.value;

        // Hide all package options and reset the select menu
        packagesSelect.querySelectorAll('option').forEach(option => {
            option.style.display = 'none';
        });
        packagesSelect.value = '';

        if (selectedEvent) {
            // Show the packages field
            packagesField.classList.remove('hidden');

            // Show the "choose Packages" option
            packagesSelect.querySelector('option[value=""]').style.display = 'block';
            
            // Find the optgroup that matches the selected event
            const selectedGroup = packagesSelect.querySelector(`optgroup[data-event="${selectedEvent}"]`);

            if (selectedGroup) {
                // Show all options within the matching optgroup
                selectedGroup.querySelectorAll('option').forEach(option => {
                    option.style.display = 'block';
                });
            }
        } else {
            // If no event is selected, hide the entire packages field
            packagesField.classList.add('hidden');
        }
    });


    
});