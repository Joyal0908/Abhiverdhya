// Scroll animation trigger
document.addEventListener('DOMContentLoaded', function() {
    const animatedElements = document.querySelectorAll('.slide-in-left, .slide-in-right, .slide-in-up, .fade-in');
    
    // Function to check if element is in viewport
    function isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top <= (window.innerHeight || document.documentElement.clientHeight) * 0.75 &&
            rect.bottom >= 0
        );
    }
    
    // Function to handle scroll events
    function handleScroll() {
        animatedElements.forEach(element => {
            if (isInViewport(element)) {
                element.style.animationPlayState = 'running';
            }
        });
    }
    
    // Initially check elements in viewport
    handleScroll();
    
    // Add scroll event listener
    window.addEventListener('scroll', handleScroll);
});