const navToggle = document.getElementById("navToggle");
const navbarLinks = document.getElementById("navbarLinks");

// Toggle open/close
navToggle.addEventListener("click", () => {
    navbarLinks.classList.toggle("show");
});

// Close on scroll
window.addEventListener("scroll", () => {
    if (navbarLinks.classList.contains("show")) {
        navbarLinks.classList.remove("show");
    }
});

const swiper = new Swiper('.swiper', {
    loop: true,
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },
    effect: 'fade',
    fadeEffect: {
        crossFade: true
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
});

const myCarousel = document.querySelector('#mainCarousel');
const carousel = new bootstrap.Carousel(myCarousel, {
    interval: 4000,
    wrap: true
});

// Viddeo 
document.addEventListener('DOMContentLoaded', function () {
    const video = document.getElementById('manufacturingVideo');
    const playButton = document.getElementById('playButton');

    // Play/Pause functionality
    playButton.addEventListener('click', function () {
        if (video.paused) {
            video.play();
            playButton.innerHTML = '<i class="fas fa-pause"></i>';
        } else {
            video.pause();
            playButton.innerHTML = '<i class="fas fa-play"></i>';
        }
    });

    // Animation for process steps
    const processSteps = document.querySelectorAll('.process-step');

    function checkScroll() {
        processSteps.forEach(step => {
            const position = step.getBoundingClientRect();

            // If the element is visible in the viewport
            if (position.top < window.innerHeight - 100) {
                step.classList.add('visible');
            }
        });
    }

    // Check on initial load
    checkScroll();

    // Check on scroll
    window.addEventListener('scroll', checkScroll);
});



// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function () {
    // loadProducts();
    setupContactForm();
    setupNavbarScroll();
});


// Popup Contact Form
document.addEventListener('DOMContentLoaded', function () {
    const popup = document.getElementById('contactPopup');
    const closeBtn = document.getElementById('closePopup');
    const contactForm = document.getElementById('contactForm1');

    // Show popup after 60 seconds
    setTimeout(function () {
        popup.classList.add('active');
        document.body.style.overflow = 'hidden'; // Prevent scrolling when popup is open
    }, 60000);

    // Close popup when X button is clicked
    closeBtn.addEventListener('click', function () {
        popup.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
    });

    // Close popup when clicking outside the popup content
    popup.addEventListener('click', function (e) {
        if (e.target === popup) {
            popup.classList.remove('active');
            document.body.style.overflow = ''; // Restore scrolling
        }
    });

    // Form submission
    contactForm.addEventListener('submit', function (e) {
        e.preventDefault();

        // Get form values
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const phone = document.getElementById('phone').value;
        const message = document.getElementById('message').value;
        const product = document.getElementById('productType').value;

        // Here you would typically send the data to a server
        console.log('Form submitted:', { name, email, phone, message, product });

        // Show success message
        alert('Thank you for your message! We will get back to you soon.');

        // Reset form and close popup
        contactForm.reset();
        popup.classList.remove('active');
        document.body.style.overflow = ''; // Restore scrolling
    });

    // Close popup when pressing Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && popup.classList.contains('active')) {
            popup.classList.remove('active');
            document.body.style.overflow = ''; // Restore scrolling
        }
    });
});