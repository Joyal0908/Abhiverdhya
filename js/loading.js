document.addEventListener('DOMContentLoaded', function () {
    const loadingScreen = document.getElementById('loadingScreen');
    const mainContent = document.getElementById('mainContent');

    // Minimum display time for loader (3 seconds)
    const minDisplayTime = 2000;
    const startTime = Date.now();

    // Function to hide loader and show content
    function showContent() {
        loadingScreen.classList.add('hidden');
        mainContent.classList.add('visible');

        // Enable scrolling
        document.body.style.overflow = 'auto';
    }

    // Wait for either page to fully load or minimum display time
    window.addEventListener('load', function () {
        const elapsed = Date.now() - startTime;
        const remainingTime = Math.max(0, minDisplayTime - elapsed);

        setTimeout(showContent, remainingTime);
    });

    // Fallback in case load event doesn't fire
    setTimeout(showContent, 4000);
});