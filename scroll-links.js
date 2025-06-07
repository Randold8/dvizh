document.addEventListener("DOMContentLoaded", function() {


    // 1. Define button-section pairs here
    const scrollPairs = [
        { buttonId: "afishaBtn", sectionClass: "list-section" },
        { buttonId: "calendarBtn", sectionClass: "calendar-section" },
        { buttonId: "achievementsBtn", sectionClass: "achievements-section" },
    ];

    console.log(`Scroll-links.js: Setting up ${scrollPairs.length} scroll links`);

    scrollPairs.forEach(pair => {
        const button = document.getElementById(pair.buttonId);
        const section = document.querySelector(`.${pair.sectionClass}`);
        if (button && section) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                // Using GSAP for smooth scrolling animation
                gsap.to(window, {
                    duration: 1,
                    scrollTo: {
                        y: section,
                        offsetY: 20
                    },
                    ease: "power3.inOut"
                });
            });
        }
    });
});
