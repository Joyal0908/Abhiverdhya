// Contact Form Handler for Admin Panel Integration
// Handles all contact form submissions and sends data to admin panel

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all contact forms
    initializeContactForms();
});

function initializeContactForms() {
    // Determine the current page source
    const currentPage = window.location.pathname.split('/').pop() || 'index.php';
    let pageSource = 'unknown';
    
    // Map page names to sources
    if (currentPage === 'index.php' || currentPage === '') {
        pageSource = 'home';
    } else if (currentPage === 'about.html') {
        pageSource = 'about';
    } else if (currentPage === 'contact.html') {
        pageSource = 'contact_page';
    } else {
        pageSource = currentPage.replace('.html', '');
    }
    
    // Main contact page form (GCPS Form)
    const gcpsForm = document.getElementById('gcpsForm');
    if (gcpsForm) {
        gcpsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmission(this, 'contact_page');
        });
    }

    // Index page popup form
    const contactForm1 = document.getElementById('contactForm1');
    if (contactForm1) {
        contactForm1.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmission(this, 'popup');
        });
    }

    // Handle contactForm based on current page
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        // Determine the correct source based on page
        let formSource = pageSource;
        
        if (currentPage === 'index.php' || currentPage === '') {
            formSource = 'home';
        } else if (currentPage === 'about.html') {
            formSource = 'about';
        } else {
            formSource = pageSource;
        }
        
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmission(this, formSource);
        });
    }
}

function handleFormSubmission(form, formSource) {
    // Debug log
    console.log('Form submission started:', {
        formSource: formSource,
        formId: form.id,
        currentPage: window.location.pathname
    });
    
    // Get form elements
    const submitButton = form.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    
    // Show loading state
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
    submitButton.disabled = true;
    
    // Clear previous alerts
    clearFormAlerts(form);
    
    // Validate form
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        resetSubmitButton(submitButton, originalButtonText);
        return;
    }
    
    // Collect form data
    const formData = new FormData();
    
    // Get field values based on form source
    const nameField = form.querySelector('#nameInput, #name');
    const emailField = form.querySelector('#emailInput, #email');
    const phoneField = form.querySelector('#phoneInput, #phone');
    const productTypeField = form.querySelector('#productType');
    const messageField = form.querySelector('#messageTextarea, #message');
    
    if (!nameField || !emailField || !phoneField || !messageField) {
        showFormAlert(form, 'error', 'Form fields not found. Please try again.');
        resetSubmitButton(submitButton, originalButtonText);
        return;
    }
    
    formData.append('name', nameField.value.trim());
    formData.append('email', emailField.value.trim());
    formData.append('phone', phoneField.value.trim());
    formData.append('productType', productTypeField ? productTypeField.value : '');
    formData.append('message', messageField.value.trim());
    formData.append('formSource', formSource);
    
    // Submit to admin panel handler
    fetch('admin/handlers/contact_handler.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showFormAlert(form, 'success', data.message);
            form.reset();
            form.classList.remove('was-validated');
            
            // Close popup if it's the popup form
            if (formSource === 'popup_form') {
                const popup = document.getElementById('contactPopup');
                if (popup) {
                    popup.style.display = 'none';
                }
            }
        } else {
            showFormAlert(form, 'error', data.message);
        }
    })
    .catch(error => {
        console.error('Form submission error:', error);
        showFormAlert(form, 'error', 'An error occurred while submitting your request. Please try again or contact us directly.');
    })
    .finally(() => {
        resetSubmitButton(submitButton, originalButtonText);
    });
}

function showFormAlert(form, type, message) {
    // Remove existing alerts
    clearFormAlerts(form);
    
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const alertHtml = `
        <div class="alert ${alertClass} form-alert" role="alert">
            <i class="fas ${iconClass} me-2"></i>${message}
        </div>
    `;
    
    // Insert alert at the top of the form
    form.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Scroll to alert
    const alert = form.querySelector('.form-alert');
    if (alert) {
        alert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Auto-remove success alerts after 5 seconds
        if (type === 'success') {
            setTimeout(() => {
                alert.remove();
            }, 5000);
        }
    }
}

function clearFormAlerts(form) {
    const existingAlerts = form.querySelectorAll('.form-alert');
    existingAlerts.forEach(alert => alert.remove());
}

function resetSubmitButton(button, originalText) {
    button.innerHTML = originalText;
    button.disabled = false;
}

// Enhanced form validation
function validateForm(form) {
    const nameField = form.querySelector('#nameInput, #name');
    const emailField = form.querySelector('#emailInput, #email');
    const phoneField = form.querySelector('#phoneInput, #phone');
    const messageField = form.querySelector('#messageTextarea, #message');
    
    let isValid = true;
    
    // Validate name (at least 2 characters, letters only)
    if (nameField) {
        const nameValue = nameField.value.trim();
        const namePattern = /^[A-Za-z\s]{2,}$/;
        if (!namePattern.test(nameValue)) {
            setFieldError(nameField, 'Please enter a valid name (at least 2 letters)');
            isValid = false;
        } else {
            clearFieldError(nameField);
        }
    }
    
    // Validate email
    if (emailField) {
        const emailValue = emailField.value.trim();
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(emailValue)) {
            setFieldError(emailField, 'Please enter a valid email address');
            isValid = false;
        } else {
            clearFieldError(emailField);
        }
    }
    
    // Validate phone (10-15 digits)
    if (phoneField) {
        const phoneValue = phoneField.value.trim().replace(/\D/g, '');
        if (phoneValue.length < 10 || phoneValue.length > 15) {
            setFieldError(phoneField, 'Please enter a valid phone number (10-15 digits)');
            isValid = false;
        } else {
            clearFieldError(phoneField);
        }
    }
    
    // Validate message (at least 10 characters)
    if (messageField) {
        const messageValue = messageField.value.trim();
        if (messageValue.length < 10) {
            setFieldError(messageField, 'Please enter a message with at least 10 characters');
            isValid = false;
        } else {
            clearFieldError(messageField);
        }
    }
    
    return isValid;
}

function setFieldError(field, message) {
    field.classList.add('is-invalid');
    field.classList.remove('is-valid');
    
    const feedback = field.parentNode.querySelector('.invalid-feedback');
    if (feedback) {
        feedback.textContent = message;
    }
}

function clearFieldError(field) {
    field.classList.remove('is-invalid');
    field.classList.add('is-valid');
}

// Real-time validation
document.addEventListener('DOMContentLoaded', function() {
    const formFields = document.querySelectorAll('input[required], textarea[required], select[required]');
    
    formFields.forEach(field => {
        field.addEventListener('blur', function() {
            if (this.value.trim()) {
                validateField(this);
            }
        });
        
        field.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                validateField(this);
            }
        });
    });
});

function validateField(field) {
    const fieldType = field.type || field.tagName.toLowerCase();
    const value = field.value.trim();
    
    switch(fieldType) {
        case 'text':
            if (field.id.includes('name') || field.id.includes('Name')) {
                const namePattern = /^[A-Za-z\s]{2,}$/;
                if (namePattern.test(value)) {
                    clearFieldError(field);
                } else {
                    setFieldError(field, 'Please enter a valid name (at least 2 letters)');
                }
            }
            break;
            
        case 'email':
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailPattern.test(value)) {
                clearFieldError(field);
            } else {
                setFieldError(field, 'Please enter a valid email address');
            }
            break;
            
        case 'tel':
            const phoneValue = value.replace(/\D/g, '');
            if (phoneValue.length >= 10 && phoneValue.length <= 15) {
                clearFieldError(field);
            } else {
                setFieldError(field, 'Please enter a valid phone number (10-15 digits)');
            }
            break;
            
        case 'textarea':
            if (value.length >= 10) {
                clearFieldError(field);
            } else {
                setFieldError(field, 'Please enter a message with at least 10 characters');
            }
            break;
    }
}
