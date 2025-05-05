document.addEventListener("DOMContentLoaded", function () {
  // Sample event data - in a real app, this would likely come from an API
  const eventData = [
    {
      id: 1,
      title: "Инклюзивная тактильная выставка ”Прикоснись к истории Победы”",
      date: "15.11",
      image: "/img/event1.png",
      time: "18:00",
      location: "Аудитория 505",
      type: "lecture",
    },
    {
      id: 2,
      title: "Выставка народного творчества “К истокам РУССКОЙ культуры”",
      date: "20.11",
      image: "/img/event2.png",
      time: "10:00",
      location: "Главный холл",
      type: "exhibition",
    },
    {
      id: 3,
      title: "расширенное заседание коллегии Федеральной антимонопольной службы, посвященное 35-летию со дня создания ведомства",
      date: "25.11",
      image: "/img/event3.png",
      time: "09:30",
      location: "Конференц-зал 2",
      type: "conference",
    },
    {
      id: 4,
      title: "Квиз по истории России",
      date: "03.12",
      image: "/img/event1.png", // Reusing images for demo
      time: "16:00",
      location: "Аудитория 301",
      type: "quiz",
    },
    {
      id: 5,
      title: "Дебаты на тему искусственного интеллекта",
      date: "10.12",
      image: "/img/event2.png",
      time: "15:30",
      location: "Аудитория 405",
      type: "other",
    },
    {
      id: 6,
      title: "Мастер-класс по программированию",
      date: "15.12",
      image: "/img/event3.png",
      time: "14:00",
      location: "Компьютерный класс 3",
      type: "lecture",
    },
    {
      id: 7,
      title: "Художественная выставка 'Цифровая эра'",
      date: "18.12",
      image: "/img/event1.png",
      time: "12:00",
      location: "Галерея HSE",
      type: "exhibition",
    },
    {
      id: 8,
      title: "Конференция по устойчивому развитию",
      date: "22.12",
      image: "/img/event2.png",
      time: "10:00",
      location: "Большой зал",
      type: "conference",
    },
  ];

  // DOM elements
  const eventsList = document.querySelector(".events-list");
  const prevButton = document.querySelector(".nav-prev");
  const nextButton = document.querySelector(".nav-next");
  const filterButtons = document.querySelectorAll(".filter-btn");

  // Variables for navigation
  let currentPosition = 0;
  let eventsPerScreen = 3;
  let cardWidth = 0;
  let maxPosition = 0;

  // Create event cards
  function createEventCards() {
    eventsList.innerHTML = "";

    eventData.forEach((event) => {
      const card = document.createElement("div");
      card.className = "event-card";
      card.dataset.type = event.type;

      card.innerHTML = `
          <div class="event-img-container">
            <img src="${event.image}" alt="${event.title}" class="event-img">
            <div class="event-date">${event.date}</div>
          </div>
          <div class="event-details">
            <h3 class="event-title">${event.title}</h3>
            <div class="event-info">
              <div class="event-time">
                <svg class="event-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                ${event.time}
              </div>
              <div class="event-location">
                <svg class="event-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
                ${event.location}
              </div>
            </div>
          </div>
        `;

      eventsList.appendChild(card);
    });

    updateLayout();
  }

  // Update layout based on device size
  function updateLayout(animate = true) {
    const isMobile = window.innerWidth < 768;

    if (!isMobile) {
      // Desktop: Calculate card width and maximum scroll position
      const visibleCards = document.querySelectorAll(
        ".event-card:not(.hidden)"
      );
      if (visibleCards.length > 0) {
        cardWidth = visibleCards[0].offsetWidth + 20; // width + margin
        eventsPerScreen = 3;
        maxPosition = Math.max(0, visibleCards.length - eventsPerScreen);

        // Reset position if we're beyond the max after filtering
        if (currentPosition > maxPosition) {
          currentPosition = maxPosition;
        }

        // Update scroll position with or without animation
        if (animate) {
          gsap.to(eventsList, {
            x: -currentPosition * cardWidth,
            duration: 1,
            ease: "circ.out",
          });
        } else {
          gsap.set(eventsList, { x: -currentPosition * cardWidth });
        }

        // Update navigation buttons
        updateNavButtons();
      }
    } else {
      // Mobile: Reset any transforms
      gsap.set(eventsList, { x: 0 });
      currentPosition = 0;
    }
  }

// Filter events - completely rewritten to avoid any animations
function filterEvents(type) {
    // First, hide all cards (for an instant effect)
    const allCards = document.querySelectorAll('.event-card');
    allCards.forEach(card => {
      if (type === 'all' || card.dataset.type === type) {
        card.classList.remove('hidden');
        card.style.display = ''; // Use default display
      } else {
        card.classList.add('hidden');
        card.style.display = 'none'; // Completely hide the element
      }
    });
  
    // Reset position without any animation
    currentPosition = 0;
  
    // Directly set the position without animation
    gsap.set(eventsList, { x: 0 });
  
    // Recalculate layout values
    const visibleCards = document.querySelectorAll('.event-card:not(.hidden)');
    if (visibleCards.length > 0) {
      cardWidth = visibleCards[0].offsetWidth + 20;
      eventsPerScreen = 3;
      maxPosition = Math.max(0, visibleCards.length - eventsPerScreen);
    } else {
      maxPosition = 0;
    }
  
    // Update navigation buttons
    updateNavButtons();
  }
  
  

  // Update navigation button states
  function updateNavButtons() {
    prevButton.disabled = currentPosition <= 0;
    nextButton.disabled = currentPosition >= maxPosition;

    if (prevButton.disabled) {
      prevButton.classList.add("disabled");
    } else {
      prevButton.classList.remove("disabled");
    }

    if (nextButton.disabled) {
      nextButton.classList.add("disabled");
    } else {
      nextButton.classList.remove("disabled");
    }
  }

  // Event listeners for navigation buttons
  if (prevButton) {
    prevButton.addEventListener("click", () => {
      if (currentPosition > 0) {
        currentPosition--;
        gsap.to(eventsList, {
          x: -currentPosition * cardWidth,
          duration: 0.5,
          ease: "circ.out", // Changed to circ.out
        });
        updateNavButtons();
        // Arrow pressing animation removed
      }
    });
  }

  if (nextButton) {
    nextButton.addEventListener("click", () => {
      if (currentPosition < maxPosition) {
        currentPosition++;
        gsap.to(eventsList, {
          x: -currentPosition * cardWidth,
          duration: 0.5,
          ease: "circ.out", // Changed to circ.out
        });
        updateNavButtons();
        // Arrow pressing animation removed
      }
    });
  }

  // Event listeners for filter buttons
  filterButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const filterType = this.getAttribute("data-filter");
      filterEvents(filterType);
    });
  });

  // Handle window resize
  let resizeTimer;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      updateLayout(true); // Animate on resize
    }, 250);
  });

  // Initialize
  createEventCards();

  // Initial filter (show all events)
  filterEvents("all");
});
