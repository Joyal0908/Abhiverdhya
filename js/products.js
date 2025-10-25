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