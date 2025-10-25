document.addEventListener('DOMContentLoaded', function() {
            const dropdown = document.getElementById('categoriesDropdown');
            const button = dropdown.querySelector('.categories-btn');
            const menu = dropdown.querySelector('.categories-menu');
            
            // Initialize Bootstrap dropdown
            const bsDropdown = new bootstrap.Dropdown(button);
            
            // Toggle animation classes when dropdown is shown/hidden
            button.addEventListener('click', function() {
                // Handled by Bootstrap's JS
            });
            
            // Close when clicking outside
            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target)) {
                    menu.classList.remove('show');
                    button.classList.remove('show');
                    button.setAttribute('aria-expanded', 'false');
                }
            });
            
            // Keyboard navigation
            button.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    button.click();
                }
            });
            
            // Enhanced hover effects for desktop
            if (window.matchMedia("(hover: hover) and (pointer: fine)").matches) {
                button.addEventListener('mouseenter', function() {
                    if (!menu.classList.contains('show')) {
                        bsDropdown.show();
                    }
                });
                
                dropdown.addEventListener('mouseleave', function() {
                    if (menu.classList.contains('show')) {
                        bsDropdown.hide();
                    }
                });
            }
            
            // Add active class to clicked item
            const items = menu.querySelectorAll('.categories-item');
            items.forEach(item => {
                item.addEventListener('click', function() {
                    items.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                });
                
                // Keyboard navigation for items
                item.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        this.click();
                    }
                });
            });
        });

document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const productCards = document.querySelectorAll('[data-size]');

    // Filter products based on size
    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const filterValue = this.getAttribute('data-filter');

            // Filter products
            productCards.forEach(card => {
                if (filterValue === 'all' || card.getAttribute('data-size') === filterValue) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Animate cards when they come into view
    const animateOnScroll = () => {
        productCards.forEach(card => {
            const cardPosition = card.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.3;

            if (cardPosition < screenPosition) {
                card.querySelector('.product-card').style.animation = 'fadeInUp 0.6s ease forwards';
            }
        });
    };

    // Initial animation check
    animateOnScroll();

    // Animate on scroll
    window.addEventListener('scroll', animateOnScroll);
});