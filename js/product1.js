// Simple script to add staggered animation delay
document.addEventListener('DOMContentLoaded', function () {
    const cards = document.querySelectorAll('.fade-in');
    cards.forEach((card, index) => {
        // Add delay based on index for staggered effect
        card.style.animationDelay = `${index * 0.1}s`;
    });
});