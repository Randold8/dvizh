document.addEventListener("DOMContentLoaded", function () {
    const hash = window.location.hash.replace('#', '');

    if (hash) {
        const target = document.getElementById(hash);
        if (target) {
            // Прячем target на момент загрузки (чтобы браузер не проскроллил сам)
            window.scrollTo(0, 0);

            // Плавно скроллим через GSAP
            setTimeout(() => {
                gsap.to(window, {
                    duration: 1,
                    scrollTo: {
                        y: target,
                        offsetY: 20
                    },
                    ease: "power3.inOut"
                });
            }, 100);
        }
    }

    // Обработка кнопок внутри страницы
    const scrollPairs = [
        { buttonId: "afishaBtn", sectionId: "afisha" },
        { buttonId: "calendarBtn", sectionId: "calendar-section" },
        { buttonId: "achievementsBtn", sectionId: "achievements" },
    ];

    scrollPairs.forEach(pair => {
        const button = document.getElementById(pair.buttonId);
        const target = document.getElementById(pair.sectionId);
        if (button && target) {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                history.replaceState(null, null, `#${pair.sectionId}`);
                gsap.to(window, {
                    duration: 1,
                    scrollTo: {
                        y: target,
                        offsetY: 20
                    },
                    ease: "power3.inOut"
                });
            });
        }
    });
});
