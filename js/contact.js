document.addEventListener('DOMContentLoaded', function () {
    // Expert button animation
    const expertBtn = document.getElementById('expertBtn');

    expertBtn.addEventListener('mouseenter', function () {
        this.classList.add('animate-pulse');
    });

    expertBtn.addEventListener('mouseleave', function () {
        this.classList.remove('animate-pulse');
    });

    expertBtn.addEventListener('click', function (e) {
        e.preventDefault();
        
        // Check if the user is on a mobile device
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        if (isMobile) {
            // On mobile devices, open the phone dialer with the number
            window.location.href = 'tel:6382771386';
        } else {
            // On desktop, keep the original behavior
            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Connecting...';
            this.classList.remove('animate-pulse');

            // Simulate connection - you can integrate with a real booking system later
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-check me-2"></i> Connected!';
                alert('Our representative will call you shortly. Thank you!');
                
                // Reset button after 3 seconds
                setTimeout(() => {
                    this.innerHTML = 'Schedule a Call <i class="fas fa-phone ms-2"></i>';
                    this.classList.add('animate-pulse');
                }, 3000);
            }, 2000);
        }
    });

    // Form validation
    const form = document.getElementById('gcpsForm');

    form.addEventListener('submit', function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (form.checkValidity()) {
            // Form is valid - submit to database
            const submitBtn = form.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Processing...';
            submitBtn.disabled = true;

            // Collect form data
            const formData = {
                name: document.getElementById('nameInput').value,
                email: document.getElementById('emailInput').value,
                phone: document.getElementById('phoneInput').value,
                product_type: document.getElementById('productType').value,
                message: document.getElementById('messageTextarea').value
            };

            // Simulate form submission without backend
            setTimeout(() => {
                submitBtn.innerHTML = '<i class="fas fa-check me-2"></i> Submitted!';
                alert('Thank you for your inquiry! Our team will contact you within 24 hours.');
                
                // Reset form after 2 seconds
                setTimeout(() => {
                    form.reset();
                    form.classList.remove('was-validated');
                    submitBtn.innerHTML = 'Submit Request <i class="fas fa-paper-plane ms-2"></i>';
                    submitBtn.disabled = false;
                    
                    // Reset character counter if it exists
                    const counter = document.querySelector('.form-text');
                    if (counter) {
                        counter.textContent = '0/500';
                        counter.style.color = 'var(--gray)';
                    }
                    
                    // Reset phone input to show +91 prefix
                    phoneInput.value = '';
                }, 2000);
            }, 1500);
        }

        form.classList.add('was-validated');
    }, false);

    // Input field animations
    const inputs = document.querySelectorAll('.form-control-custom');

    inputs.forEach(input => {
        // Add focus/blur effects
        input.addEventListener('focus', function () {
            this.parentElement.querySelector('label').style.color = 'var(--primary)';
        });

        input.addEventListener('blur', function () {
            if (!this.value) {
                this.parentElement.querySelector('label').style.color = 'var(--gray)';
            }
        });

        // Add character counter for message textarea
        if (input.id === 'messageTextarea') {
            const counter = document.createElement('div');
            counter.className = 'form-text text-end text-muted small';
            counter.textContent = '0/500';
            input.parentElement.appendChild(counter);

            input.addEventListener('input', function () {
                // Limit message length to 500 characters
                if (this.value.length > 500) {
                    this.value = this.value.substring(0, 500);
                }
                
                const remaining = 500 - this.value.length;
                counter.textContent = `${this.value.length}/500`;

                if (remaining < 50) {
                    counter.style.color = '#dc3545';
                } else if (remaining < 100) {
                    counter.style.color = '#ffc107';
                } else {
                    counter.style.color = 'var(--gray)';
                }
            });
        }
    });

    // Phone number formatting for international format like +91 9087361936
    const phoneInput = document.getElementById('phoneInput');

    phoneInput.addEventListener('input', function (e) {
        let value = e.target.value;
        
        // Remove all non-digit characters except + at the beginning
        value = value.replace(/[^\d+]/g, '');
        
        // Ensure + is only at the beginning
        if (value.indexOf('+') > 0) {
            value = value.replace(/\+/g, '');
        }
        
        // If it starts with +, format for international
        if (value.startsWith('+')) {
            // Remove the + temporarily to work with digits
            let digits = value.substring(1);
            
            // Limit total length (country code + number)
            if (digits.length > 15) {
                digits = digits.substring(0, 15);
            }
            
            // Format as +XX XXXXXXXXXX (country code + number)
            if (digits.length > 2) {
                // Assume first 2-3 digits are country code
                let countryCode = digits.substring(0, 2);
                let phoneNumber = digits.substring(2);
                
                // For Indian numbers (+91), format as +91 XXXXXXXXXX
                if (countryCode === '91' && phoneNumber.length > 0) {
                    value = '+91 ' + phoneNumber;
                } else if (digits.length > 3) {
                    // For other countries, try 3-digit country code
                    countryCode = digits.substring(0, 3);
                    phoneNumber = digits.substring(3);
                    value = '+' + countryCode + ' ' + phoneNumber;
                } else {
                    value = '+' + digits;
                }
            } else {
                value = '+' + digits;
            }
        } else {
            // If no +, assume it's a local number and add +91 prefix for India
            if (value.length > 0 && !value.startsWith('+')) {
                // Limit to 10 digits for Indian mobile numbers
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }
                
                if (value.length > 0) {
                    value = '+91 ' + value;
                }
            }
        }
        
        e.target.value = value;
    });
    
    // Set placeholder for phone input
    phoneInput.placeholder = '+91 6382771386';
    
    // Auto-focus to after +91 when user clicks on empty field
    phoneInput.addEventListener('focus', function (e) {
        if (this.value === '' || this.value === '+91 ') {
            this.value = '+91 ';
            // Set cursor position after +91 
            setTimeout(() => {
                this.setSelectionRange(4, 4);
            }, 0);
        }
    });

    // Responsive adjustments
    function handleResize() {
        if (window.innerWidth < 768) {
            // Mobile-specific adjustments
        } else {
            // Desktop adjustments
        }
    }

    // Initial call
    handleResize();

    // Listen for resize
    window.addEventListener('resize', handleResize);
});