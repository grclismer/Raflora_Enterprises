const inputElement = document.getElementById('client_number');
        const errorMessage = document.getElementById('warning-message');

        // Add an input event listener to the element
        inputElement.addEventListener('input', function() {
            // Remove any non-digit characters
            this.value = this.value.replace(/[^0-9]/g, '');

            // Check if the number of digits is not 11
            if (this.value.length > 0 && this.value.length !== 11) {
                // If it's not 11, show the error message
                errorMessage.classList.remove('hidden');
            } else {
                // Otherwise, hide the error message
                errorMessage.classList.add('hidden');
            }
        });
        
        // Tag seclection for payments 
function logSelection() {
            const selectElement = document.getElementById('payment-select','theme-select');
            const selectedValue = selectElement.options[selectElement.selectedIndex].text;
            const displayElement = document.getElementById('selected-value');
            
            if (selectElement.value) {
                displayElement.classList.remove('hidden');
                displayElement.querySelector('span').textContent = selectedValue;
            } else {
                displayElement.classList.add('hidden');
            }
        }