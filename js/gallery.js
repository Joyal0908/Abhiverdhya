document.addEventListener('DOMContentLoaded', function () {
    const processSteps = document.querySelectorAll('.process-step');
    const stepDescriptions = document.querySelectorAll('.step-description');
    const prevBtn = document.getElementById('prevStep');
    const nextBtn = document.getElementById('nextStep');
    const timeline = document.getElementById('processTimeline');

    let currentStep = 1;
    let autoSlideInterval;
    const totalSteps = processSteps.length;
    const slideDuration = 5000; // 5 seconds

    // Initialize the timeline
    function initTimeline() {
        updateActiveStep();
        startAutoSlide();

        // Add click event listeners to steps
        processSteps.forEach(step => {
            step.addEventListener('click', function () {
                const stepNumber = parseInt(this.getAttribute('data-step'));
                goToStep(stepNumber);
            });
        });

        // Add navigation button events
        prevBtn.addEventListener('click', goToPrevStep);
        nextBtn.addEventListener('click', goToNextStep);
    }

    // Update active step and description
    function updateActiveStep() {
        // Update process steps
        processSteps.forEach(step => {
            const stepNumber = parseInt(step.getAttribute('data-step'));
            if (stepNumber === currentStep) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });

        // Update descriptions
        stepDescriptions.forEach(desc => {
            const descNumber = parseInt(desc.id.replace('desc', ''));
            if (descNumber === currentStep) {
                desc.classList.add('active');
            } else {
                desc.classList.remove('active');
            }
        });

        // Update navigation buttons
        prevBtn.classList.toggle('disabled', currentStep === 1);
        nextBtn.classList.toggle('disabled', currentStep === totalSteps);

        // Scroll timeline to show active step (for mobile)
        const activeStep = document.querySelector(`.process-step[data-step="${currentStep}"]`);
        if (activeStep) {
            const containerWidth = document.querySelector('.timeline-wrapper').offsetWidth;
            const stepWidth = activeStep.offsetWidth;
            const scrollPosition = (currentStep - 1) * stepWidth - (containerWidth / 2 - stepWidth / 2);

            timeline.scrollTo({
                left: scrollPosition,
                behavior: 'smooth'
            });
        }
    }

    // Go to specific step
    function goToStep(stepNumber) {
        currentStep = stepNumber;
        updateActiveStep();
        resetAutoSlide();
    }

    // Go to next step
    function goToNextStep() {
        if (currentStep < totalSteps) {
            currentStep++;
        } else {
            currentStep = 1;
        }
        updateActiveStep();
        resetAutoSlide();
    }

    // Go to previous step
    function goToPrevStep() {
        if (currentStep > 1) {
            currentStep--;
        } else {
            currentStep = totalSteps;
        }
        updateActiveStep();
        resetAutoSlide();
    }

    // Start auto-sliding
    function startAutoSlide() {
        autoSlideInterval = setInterval(goToNextStep, slideDuration);
    }

    // Reset auto-slide timer
    function resetAutoSlide() {
        clearInterval(autoSlideInterval);
        startAutoSlide();
    }

    // Initialize the timeline
    initTimeline();
});