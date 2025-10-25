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

    // Card flip functionality for mobile devices
    const isMobile = () => window.innerWidth <= 992;
    let activeCard = null;

    // Handle card interactions
    document.querySelectorAll('.flip-card').forEach(card => {
        const cardInner = card.querySelector('.flip-card-inner');
        const frontFace = card.querySelector('.flip-card-front');
        const backFace = card.querySelector('.flip-card-back');

        function unflipCard() {
            cardInner.classList.remove('flipped');
            if (activeCard === cardInner) {
                activeCard = null;
            }
        }

        function flipCard() {
            // Close any other open card first
            if (activeCard && activeCard !== cardInner) {
                activeCard.classList.remove('flipped');
            }
            cardInner.classList.add('flipped');
            activeCard = cardInner;
        }

        // Handle tap to flip (front face)
        frontFace.addEventListener('click', function(e) {
            if (!isMobile()) return;
            e.preventDefault();
            e.stopPropagation();
            flipCard();
        });

        // Handle tap to return (back face)
        backFace.addEventListener('click', function(e) {
            if (!isMobile()) return;
            e.preventDefault();
            e.stopPropagation();
            unflipCard();
        });

        // Touch events
        frontFace.addEventListener('touchstart', function(e) {
            if (!isMobile()) return;
            e.preventDefault();
            e.stopPropagation();
            flipCard();
        }, { passive: false });

        backFace.addEventListener('touchstart', function(e) {
            if (!isMobile()) return;
            e.preventDefault();
            e.stopPropagation();
            unflipCard();
        }, { passive: false });
    });

    // Handle clicks outside cards
    document.addEventListener('click', function(e) {
        if (!isMobile() || !activeCard) return;
        if (!e.target.closest('.flip-card')) {
            activeCard.classList.remove('flipped');
            activeCard = null;
        }
    });
});