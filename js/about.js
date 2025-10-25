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

    // Handle card flipping
    const isMobile = () => window.innerWidth <= 992;
    let activeCard = null;

    // Function to unflip active card
    const unflipActiveCard = () => {
        if (activeCard) {
            activeCard.classList.remove('flipped');
            activeCard = null;
        }
    };

    // Handle clicks outside cards
    document.addEventListener('click', (e) => {
        if (isMobile() && !e.target.closest('.flip-card')) {
            unflipActiveCard();
        }
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        const wasMobile = activeCard !== null;
        if (wasMobile && !isMobile()) {
            unflipActiveCard(); // Clean up when switching to desktop
        }
    });

    // Add flip card interactions
    document.querySelectorAll('.flip-card').forEach(card => {
        const cardInner = card.querySelector('.flip-card-inner');
        
        const handleInteraction = (e) => {
            if (!isMobile()) return; // Let CSS handle desktop hover
            e.preventDefault();
            e.stopPropagation();
            
            const isClickingActiveCard = activeCard === cardInner;
            
            // Always unflip any currently flipped card
            unflipActiveCard();
            
            // If we didn't click the active card, flip the new one
            if (!isClickingActiveCard) {
                cardInner.classList.add('flipped');
                activeCard = cardInner;
            }
        };

        // Add both touch and click handlers for mobile
        card.addEventListener('touchstart', handleInteraction, { passive: false });
        card.addEventListener('click', handleInteraction);
    });
});