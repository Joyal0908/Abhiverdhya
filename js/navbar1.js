// Navbar scroll effect
function setupNavbarScroll() {
    const navbar = document.querySelector('.navbar');

    window.addEventListener('scroll', function () {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });
}

// Contact
document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contactForm');
            const submitBtn = document.getElementById('submitBtn');
            const phoneInput = document.getElementById('phone');
            
            // Add event listeners for real-time validation on blur
            document.getElementById('name').addEventListener('blur', validateName);
            document.getElementById('email').addEventListener('blur', validateEmail);
            phoneInput.addEventListener('blur', validatePhone);
            document.getElementById('message').addEventListener('blur', validateMessage);
            
            // Format phone number as user types
            phoneInput.addEventListener('input', function(e) {
                // Remove all non-digit characters
                let phoneNumber = e.target.value.replace(/\D/g, '');
                
                // Format as (XXX) XXX-XXXX if 10 digits
                if (phoneNumber.length === 10) {
                    phoneNumber = phoneNumber.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
                }
                
                e.target.value = phoneNumber;
            });
            
            // Form submission handler
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                event.stopPropagation();
                
                // Validate all fields
                const isNameValid = validateName();
                const isEmailValid = validateEmail();
                const isPhoneValid = validatePhone();
                const isMessageValid = validateMessage();
                
                // Check if all fields are valid
                if (isNameValid && isEmailValid && isPhoneValid && isMessageValid) {
                    // Change button to success state
                    submitBtn.classList.add('btn-success-state');
                    submitBtn.textContent = 'Message Sent!';

                    // Show success alert
                    alert('Thank you for your message! We will get back to you soon.');
                    
                    // Here you would typically send the form data to a server
                    // For demonstration, we'll just log it and reset after 3 seconds
                    const formData = new FormData(form);
                    console.log('Form data:', Object.fromEntries(formData.entries()));
                    
                    // Reset form after 3 seconds
                    setTimeout(() => {
                        form.reset();
                        submitBtn.classList.remove('btn-success-state');
                        submitBtn.textContent = 'Submit';
                        // Remove validation classes
                        document.querySelectorAll('.form-control').forEach(el => {
                            el.classList.remove('is-valid');
                        });
                        // Remove was-validated class to reset validation state
                        form.classList.remove('was-validated');
                    }, 3000);
                } else {
                    // Add Bootstrap's was-validated class to show validation messages
                    form.classList.add('was-validated');
                }
            });
            
            // Validation functions
            function validateName() {
                const nameInput = document.getElementById('name');
                const nameValue = nameInput.value.trim();
                
                if (nameValue.length < 2 || !/^[A-Za-z ]+$/.test(nameValue)) {
                    nameInput.classList.add('is-invalid');
                    nameInput.classList.remove('is-valid');
                    return false;
                } else {
                    nameInput.classList.remove('is-invalid');
                    nameInput.classList.add('is-valid');
                    return true;
                }
            }
            
            function validateEmail() {
                const emailInput = document.getElementById('email');
                const emailValue = emailInput.value.trim();
                const emailRegex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
                
                if (!emailRegex.test(emailValue)) {
                    emailInput.classList.add('is-invalid');
                    emailInput.classList.remove('is-valid');
                    return false;
                } else {
                    emailInput.classList.remove('is-invalid');
                    emailInput.classList.add('is-valid');
                    return true;
                }
            }
            
            function validatePhone() {
                const phoneInput = document.getElementById('phone');
                // Remove all non-digit characters for validation
                const phoneValue = phoneInput.value.replace(/\D/g, '');
                
                // Check if it has 10 digits (standard US number)
                if (phoneValue.length < 10 || phoneValue.length > 15) {
                    phoneInput.classList.add('is-invalid');
                    phoneInput.classList.remove('is-valid');
                    return false;
                } else {
                    phoneInput.classList.remove('is-invalid');
                    phoneInput.classList.add('is-valid');
                    return true;
                }
            }
            
            function validateMessage() {
                const messageInput = document.getElementById('message');
                const messageValue = messageInput.value.trim();
                
                if (messageValue.length < 10) {
                    messageInput.classList.add('is-invalid');
                    messageInput.classList.remove('is-valid');
                    return false;
                } else {
                    messageInput.classList.remove('is-invalid');
                    messageInput.classList.add('is-valid');
                    return true;
                }
            }
        });



/* Back to top */
document.addEventListener('DOMContentLoaded', function () {
    const backToTopButton = document.getElementById('back-to-top');

    // Show/hide button based on scroll position
    window.addEventListener('scroll', function () {
        if (window.pageYOffset > 300) {
            backToTopButton.classList.add('show');
            // Optional: Add pulse effect when button appears
            backToTopButton.classList.add('pulse');
        } else {
            backToTopButton.classList.remove('show');
            backToTopButton.classList.remove('pulse');
        }
    });

    // Smooth scroll to top when clicked
    backToTopButton.addEventListener('click', function (e) {
        e.preventDefault();
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});