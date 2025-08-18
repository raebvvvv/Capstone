const cart = [];
    
function decreaseQuantity(button) {
    const span = button.nextElementSibling;
    let quantity = parseInt(span.textContent);
    if (quantity > 1) {
        span.textContent = quantity - 1;
    }
}

function increaseQuantity(button) {
    const span = button.previousElementSibling;
    let quantity = parseInt(span.textContent);
    const card = button.closest('.card');
    const stockQuantity = parseInt(card.querySelector('.description').textContent.split(': ')[1]);
    

    if (quantity < stockQuantity) {
        span.textContent = quantity + 1;
    }

    // Disable the button if max stock is reached
    button.disabled = quantity + 1 >= stockQuantity;
}

function addToCart(category, name, quantity, img) {
    const existing = cart.find(item => item.name === name);
    const cards = Array.from(document.querySelectorAll('.card'));
    const card = cards.find(c => c.querySelector('h3').textContent.trim() === name);
    if (!card) {
        console.error(`Card with name "${name}" not found.`);
        return;
    }
    const maxQuantity = parseInt(card.querySelector('.description').textContent.split(': ')[1]);

    if (existing) {
        if (existing.quantity + quantity <= maxQuantity) {
            existing.quantity += quantity;
        } else {
            alert(`Cannot add more than ${maxQuantity} items of ${name} to the cart.`);
        }
    } else {
        if (quantity <= maxQuantity) {
            cart.push({ category, name, quantity, img });
        } else {
            alert(`Cannot add more than ${maxQuantity} items of ${name} to the cart.`);
        }
    }
    alert(`${name} has been added to the cart!`); // Optional: Notify the user
    updateTotalQuantity();
    updateCartCount(); // Update cart count immediately
    updateQuantitySelector(name); // Update quantity selector
}

function updateQuantitySelector(name) {
    const item = cart.find(i => i.name === name);
    if (item) {
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            if (card.querySelector('h3').textContent === name) {
                card.querySelector('.quantity-selector span').textContent = item.quantity;
            }
        });
    }
}

function updateCartCount() {
    let totalQuantity = 0;
    cart.forEach(item => {
        totalQuantity += item.quantity;
    });
    document.getElementById('cart-count').textContent = totalQuantity; // Update cart count
}

function openCart() {
    const cartPopup = document.getElementById('cart-popup');
    const cartItems = document.getElementById('cart-items');
    const submitButton = document.querySelector('.submit-rental-btn');
    cartItems.innerHTML = ''; // Clear existing items
    cart.forEach(item => {
        const div = document.createElement('div');
        div.classList.add('cart-item');
        div.innerHTML = `
            <img src="${item.img}" alt="${item.name}">
            <span><b>Item:</b> ${item.name} | ${item.category}</span>
            <div class="quantity-selector">
                <button onclick="updateQuantity('${item.name}', -1)">-</button>
                <span>${item.quantity}</span> 
                <button onclick="updateQuantity('${item.name}', 1)">+</button>
            </div>
        `;
        cartItems.appendChild(div);
    });
    updateTotalQuantity();
    submitButton.style.display = cart.length > 0 ? 'block' : 'none'; // Show or hide the submit button
    cartPopup.style.display = 'flex'; // Show the cart popup
}

function closeCart() {
    const cartPopup = document.getElementById('cart-popup');
    cartPopup.style.display = 'none'; // Hide the cart popup
}

function openAccount() {
    const accountPopup = document.getElementById('account-popup');
    accountPopup.style.display = 'flex'; // Show the account popup
}
        
function closeAccount() {
    const accountPopup = document.getElementById('account-popup');
    accountPopup.style.display = 'none'; // Hide the account popup
}

function updateQuantity(name, change) {
    const item = cart.find(i => i.name === name);
    if (item) {
        const card = [...document.querySelectorAll('.card')].find(c => c.querySelector('h3').textContent.trim() === name);
        const quantitySpan = card.querySelector('.quantity-selector span');
        const increaseButton = card.querySelector('.quantity-selector button:last-child');
        const stockQuantity = parseInt(card.querySelector('.description').textContent.split(': ')[1]);

        let newQuantity = item.quantity + change;
        if (newQuantity >= 1 && newQuantity <= stockQuantity) {
            item.quantity = newQuantity;
            quantitySpan.textContent = newQuantity;
        } else if (newQuantity === 0) {
            // Remove item from cart if quantity is 0
            const index = cart.indexOf(item);
            if (index > -1) {
                cart.splice(index, 1);
            }
            card.querySelector('.quantity-selector span').textContent = '1'; // Reset quantity selector
        }

        // Enable or disable the + button
        increaseButton.disabled = item.quantity >= stockQuantity;
    }
    updateCartPopup(); // Ensure the cart popup is updated
}

function updateCartPopup() {
    const cartItems = document.getElementById('cart-items');
    cartItems.innerHTML = ''; // Clear existing items
    cart.forEach(item => {
        const div = document.createElement('div');
        div.classList.add('cart-item');
        div.innerHTML = `
            <img src="${item.img}" alt="${item.name}">
            <span><b>Item:</b> ${item.name} | ${item.category}</span>
            <div class="quantity-selector">
                <button onclick="updateQuantity('${item.name}', -1)">-</button>
                <span>${item.quantity}</span> 
                <button onclick="updateQuantity('${item.name}', 1)">+</button>
            </div>
        `;
        cartItems.appendChild(div);
    });
    updateTotalQuantity();
}
function clearFilters() {
    document.querySelectorAll('.filter-group input').forEach(input => (input.checked = false));
    filterCards(); // Call filterCards to reset the view
}

// Ensure the DOM is loaded before attaching the event listener
document.addEventListener('DOMContentLoaded', () => {
    const cartIcon = document.getElementById('cart-icon');
    cartIcon.addEventListener('click', openCart); // Attach openCart() to the cart icon

    const searchInput = document.querySelector('.search-bar input');
    searchInput.addEventListener('input', filterCards); // Attach filterCards() to the search input

    const filterInputs = document.querySelectorAll('.filter-group input[type="checkbox"]');
    filterInputs.forEach(input => input.addEventListener('change', filterCards)); // Attach filterCards() to the filter inputs
});

function updateTotalQuantity() {
    const cartItems = document.querySelectorAll('#cart-items .cart-item');
    let totalQuantity = 0;
    cartItems.forEach(item => {
        const quantity = parseInt(item.querySelector('.quantity-selector span').textContent);
        totalQuantity += quantity;
    });
    document.getElementById('total-quantity').textContent = totalQuantity;
    updateCartCount(); // Update cart count
    cart.forEach(item => updateQuantitySelector(item.name)); // Update quantity selectors for all items
}

function removeFromCart(itemElement) {
    // ...existing code...
    updateTotalQuantity();
}

function submitRentalRequest() {
    openTerms();
}

function openTerms() {
    const termsPopup = document.getElementById('terms-popup');
    termsPopup.style.display = 'flex'; // Show the terms popup
}

function closeTerms() {
    const termsPopup = document.getElementById('terms-popup');
    termsPopup.style.display = 'none'; // Hide the terms popup
}

function confirmBorrowRequest() {
    closeTerms(); // Close the terms popup
    showConfirmation('Are you sure you want to submit the rental request?', () => {
        alert('Rental request submitted!');
        closeCart();
    });
}

function showConfirmation(message, onConfirm) {
    const confirmationPopup = document.getElementById('confirmation-popup');
    const confirmationMessage = document.getElementById('confirmation-message');
    confirmationMessage.textContent = message;
    confirmationPopup.style.display = 'flex'; // Show the confirmation popup

    // Set the confirm action
    window.confirmAction = () => {
        onConfirm();
        closeConfirmation();
    };
}

function closeConfirmation() {
    const confirmationPopup = document.getElementById('confirmation-popup');
    confirmationPopup.style.display = 'none'; // Hide the confirmation popup
}

function filterCards() {
    const searchTerm = document.querySelector('.search-bar input').value.toLowerCase();
    const filters = Array.from(document.querySelectorAll('.filter-group input[type="checkbox"]:checked')).map(input => input.parentElement.textContent.trim().toLowerCase());
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => {
        const cardText = card.textContent.toLowerCase();
        const stockQuantity = parseInt(card.querySelector('.description').textContent.split(': ')[1]);
        const matchesSearch = cardText.includes(searchTerm);
        const matchesFilter = filters.length === 0 || filters.some(filter => cardText.includes(filter));
        const inStockFilter = filters.includes('in stock');
        const matchesStock = !inStockFilter || stockQuantity > 0;

        if (matchesSearch && matchesFilter && matchesStock) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

function proceedToForm() {
    closeTerms(); // Close the terms popup
    const formPopup = document.getElementById('form-popup');
    formPopup.style.display = 'flex'; // Show the form popup

    // Serialize the cart data and insert it into the form's hidden field
    const cartData = JSON.stringify(cart); // Convert the cart array into a JSON string
    document.getElementById('cart-data').value = cartData;

    // Optionally set the timestamp for the request
    const requestTimestamp = new Date().toISOString(); // Get current date and time in ISO format
    document.getElementById('request-timestamp').value = requestTimestamp;
}

function closeForm() {
    const formPopup = document.getElementById('form-popup');
    formPopup.style.display = 'none'; // Hide the form popup
}

function submitBorrowForm() {
    const form = document.getElementById('borrow-form');
    if (form.checkValidity()) {
        const requestDate = new Date().toLocaleString(); // Get current date and time
        alert(`Borrow request form submitted on ${requestDate}!`);
        closeForm();
        closeCart();
    } else {
        alert('Please fill out all required fields.');
    }
}

function confirmLogout() {
    const logoutConfirmationPopup = document.getElementById('logout-confirmation-popup');
    logoutConfirmationPopup.style.display = 'flex'; // Show the logout confirmation popup
}

function closeLogoutConfirmation() {
    const logoutConfirmationPopup = document.getElementById('logout-confirmation-popup');
    logoutConfirmationPopup.style.display = 'none'; // Hide the logout confirmation popup
}

function logout() {
    alert('You have been logged out.');
    closeLogoutConfirmation();
    closeAccount();
}
