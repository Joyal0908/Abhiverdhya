// Animation on scroll functionality
document.addEventListener('DOMContentLoaded', function () {
    // Helper function to check if element is in viewport
    function isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.75 &&
            rect.bottom >= 0
        );
    }

    // Function to add animation class when element is in view
    function animateOnScroll() {
        const elements = document.querySelectorAll('.journey-card, .mv-card, .owner-card');

        elements.forEach(element => {
            if (isInViewport(element)) {
                element.classList.add('animated');
            }
        });
    }

    // Initial check
    animateOnScroll();

    // Check on scroll
    window.addEventListener('scroll', animateOnScroll);

    // Add hover effect to journey cards
    const journeyCards = document.querySelectorAll('.journey-card');
    journeyCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.borderLeftColor = 'var(--accent)';
        });

        card.addEventListener('mouseleave', function () {
            this.style.borderLeftColor = 'var(--secondary)';
        });
    });

    // Add touch support for mobile devices
    // document.querySelectorAll('.flip-card').forEach(card => {
    //     card.addEventListener('touchstart', function () {
    //         this.querySelector('.flip-card-inner').classList.toggle('flipped');
    //     });
    // });

    // Optional: Add click support for desktop if hover isn't enough
    // document.querySelectorAll('.flip-card').forEach(card => {
    //     card.addEventListener('click', function () {
    //         this.querySelector('.flip-card-inner').classList.toggle('flipped');
    //     });
    // });
});


document.addEventListener('DOMContentLoaded', function () {
    // Staggered animation delay
    const cards = document.querySelectorAll('.fade-in');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Card flip functionality
    const flipCards = document.querySelectorAll('.flip-card');
    let currentlyFlipped = null;

    function handleCardFlip(card) {
        // If clicking the currently flipped card, just unflip it
        if (card === currentlyFlipped) {
            card.classList.remove('flipped');
            currentlyFlipped = null;
            return;
        }

        // If another card is flipped, unflip it
        if (currentlyFlipped) {
            currentlyFlipped.classList.remove('flipped');
        }

        // Flip the clicked card and update the reference
        card.classList.add('flipped');
        currentlyFlipped = card;
    }

    // Attach click listeners to all cards but act only on mobile sizes
    flipCards.forEach(card => {
        card.addEventListener('click', function (e) {
            // Only handle taps/clicks for mobile widths
            if (!window.matchMedia("(max-width: 992px)").matches) return;
            handleCardFlip(this);
        });

        // On desktop, hovering is handled by CSS, but when mouseenter occurs
        // ensure any programmatically flipped card is reset.
        card.addEventListener('mouseenter', function () {
            if (window.matchMedia("(max-width: 992px)").matches) return;
            if (currentlyFlipped) {
                currentlyFlipped.classList.remove('flipped');
                currentlyFlipped = null;
            }
        });
    });

    // Close flipped card when clicking/tapping outside any .flip-card
    document.addEventListener('click', function (e) {
        if (!currentlyFlipped) return;
        // If the click is inside any .flip-card, ignore (card handlers manage it)
        if (e.target.closest('.flip-card')) return;
        currentlyFlipped.classList.remove('flipped');
        currentlyFlipped = null;
    });

    // On resize, if layout changes between mobile/desktop, clear any flipped card
    let lastIsMobile = window.matchMedia("(max-width: 992px)").matches;
    window.addEventListener('resize', function () {
        const nowMobile = window.matchMedia("(max-width: 992px)").matches;
        if (nowMobile !== lastIsMobile && currentlyFlipped) {
            currentlyFlipped.classList.remove('flipped');
            currentlyFlipped = null;
        }
        lastIsMobile = nowMobile;
    });
});