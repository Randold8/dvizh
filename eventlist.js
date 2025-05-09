// Utility: Convert "dd.mm" to "YYYY-MM-DD" ISO string (like FullCalendar)
function parseEventDateString(dateString) {
  const [day, month] = dateString.split('.').map(Number);
  const now = new Date();
  const year = now.getFullYear();
  let eventDate = new Date(year, month - 1, day);
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
  if (eventDate < today) eventDate = new Date(year + 1, month - 1, day);
  return eventDate.toISOString().split('T')[0];
}

// Select up to `limit` future events, or if none, up to `limit` past events
function getNearestEvents(events, limit = 10) {
  const todayStr = new Date().toISOString().split('T')[0];
  const annotated = events.map(ev => ({
    ...ev,
    _eventDateStr: parseEventDateString(ev.date)
  }));

  const futureEvents = annotated
    .filter(ev => ev._eventDateStr >= todayStr)
    .sort((a, b) => a._eventDateStr.localeCompare(b._eventDateStr));

  if (futureEvents.length > 0) return futureEvents.slice(0, limit);

  const pastEvents = annotated
    .filter(ev => ev._eventDateStr < todayStr)
    .sort((a, b) => b._eventDateStr.localeCompare(a._eventDateStr));
  return pastEvents.slice(0, limit);
}

document.addEventListener("DOMContentLoaded", function () {
  const eventsList = document.querySelector(".events-list");
  const prevButton = document.querySelector(".nav-prev");
  const nextButton = document.querySelector(".nav-next");
  const filterButtons = document.querySelectorAll(".filter-btn");

  let nearestEvents = [];
  let currentPosition = 0, eventsPerScreen = 3, cardWidth = 0, maxPosition = 0;

  function createEventCards(events) {
    eventsList.innerHTML = "";
    events.forEach(event => {
      const card = document.createElement("div");
      card.className = "event-card";
      card.dataset.type = event.type;

      card.innerHTML = `
  <a href="/${event.id}" class="event-link">
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
  </a>
`;

      eventsList.appendChild(card);
    });
    updateLayout();
  }

  function filterEvents(type) {
    let filtered = (type === "all")
      ? nearestEvents
      : nearestEvents.filter(ev => ev.type === type);

    createEventCards(filtered);
    currentPosition = 0;
    gsap.set(eventsList, {x: 0});
    updateNavButtons();
  }

  function updateLayout(animate = true) {
    const isMobile = window.innerWidth < 768;
    if (!isMobile) {
      const visibleCards = document.querySelectorAll(".event-card:not(.hidden)");
      if (visibleCards.length > 0) {
        cardWidth = visibleCards[0].offsetWidth + 20;
        eventsPerScreen = 3;
        maxPosition = Math.max(0, visibleCards.length - eventsPerScreen);
        if (currentPosition > maxPosition) currentPosition = maxPosition;
        if (animate) {
          gsap.to(eventsList, {
            x: -currentPosition * cardWidth,
            duration: 1,
            ease: "circ.out"
          });
        } else {
          gsap.set(eventsList, { x: -currentPosition * cardWidth });
        }
        updateNavButtons();
      }
    } else {
      gsap.set(eventsList, { x: 0 });
      currentPosition = 0;
    }
  }

  function updateNavButtons() {
    prevButton.disabled = currentPosition <= 0;
    nextButton.disabled = currentPosition >= maxPosition;
    prevButton.classList.toggle("disabled", prevButton.disabled);
    nextButton.classList.toggle("disabled", nextButton.disabled);
  }

  if (prevButton) {
    prevButton.addEventListener("click", () => {
      if (currentPosition > 0) {
        currentPosition--;
        gsap.to(eventsList, {
          x: -currentPosition * cardWidth,
          duration: 0.5,
          ease: "circ.out"
        });
        updateNavButtons();
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
          ease: "circ.out"
        });
        updateNavButtons();
      }
    });
  }

  filterButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const filterType = this.getAttribute("data-filter");
      filterEvents(filterType);
    });
  });

  let resizeTimer;
  window.addEventListener("resize", () => {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(() => {
      updateLayout(true);
    }, 250);
  });

  // MAIN: use EventsProvider (same as calendar)
  window.EventsProvider.getEvents()
    .then(events => {
      nearestEvents = getNearestEvents(events, 10);
      filterEvents("all");
    })
    .catch(err => {
      eventsList.innerHTML = '<p style="color:red;">Could not load events</p>';
      console.error("Event list loading failed:", err);
    });

});
