// Utility to get the closest (upcoming or, if none, the most recent past) events
function parseEventDate(dateString) {
    // Expects "dd.mm"
    const [day, month] = dateString.split('.').map(Number);
    const now = new Date();
    const year = now.getFullYear();
    let eventDate = new Date(year, month - 1, day);
    const today = new Date(now.setHours(0,0,0,0));
    if (eventDate < today) eventDate = new Date(year + 1, month - 1, day);
    return eventDate;
  }
  
  function getNearestEvents(events, limit = 3) {
    const now = new Date();
    const todayStart = new Date(now.setHours(0,0,0,0));
  
    // Annotate each event with its JS date object
    const annotated = events.map(event => ({
      ...event,
      _eventDate: parseEventDate(event.date)
    }));
  
    // 1. Try to get up to 'limit' future events (including today)
    const futureEvents = annotated
      .filter(ev => ev._eventDate >= todayStart)
      .sort((a, b) => a._eventDate - b._eventDate);
  
    if (futureEvents.length > 0) {
      return futureEvents.slice(0, limit);
    }
  
    // 2. If no future events, get up to 'limit' past events, closest to today first
    const pastEvents = annotated
      .filter(ev => ev._eventDate < todayStart)
      .sort((a, b) => b._eventDate - a._eventDate);
  
    return pastEvents.slice(0, limit);
  }
  
  // Renders the main events grid under the header
  function populateMainEvents(mainEvents) {
    // Selectors for main and secondary event slots
    const mainEventAnchor = document.querySelector('.event-main');
    const rightContainer = document.querySelectorAll('.event-secondary-container')[0];
    const leftContainer  = document.querySelectorAll('.event-secondary-container')[1];
  
    // Main event (center slot)
    if (mainEvents[0] && mainEventAnchor) {
      mainEventAnchor.href = "/events/" + mainEvents[0].id; // <-- CHANGED
      const img = mainEventAnchor.querySelector('img');
      if (img) {
        img.src = mainEvents[0].image;
        img.alt = mainEvents[0].title;
      }
    }
    // Secondary event (right slot)
    if (mainEvents[1] && rightContainer) {
      const secAnchor = rightContainer.querySelector('.event-secondary');
      if (secAnchor) {
        secAnchor.href = "/events/" + mainEvents[1].id; // <-- CHANGED
        const img = secAnchor.querySelector('img');
        if (img) {
          img.src = mainEvents[1].image;
          img.alt = mainEvents[1].title;
        }
      }
    }
    // Secondary event (left slot)
    if (mainEvents[2] && leftContainer) {
      const secAnchor = leftContainer.querySelector('.event-secondary');
      if (secAnchor) {
        secAnchor.href = "/events/" + mainEvents[2].id; // <-- CHANGED
        const img = secAnchor.querySelector('img');
        if (img) {
          img.src = mainEvents[2].image;
          img.alt = mainEvents[2].title;
        }
      }
    }
  }
  
  
  // Main logic: get and render nearest main events after DOM loads
  document.addEventListener("DOMContentLoaded", function () {
    window.EventsProvider.getEvents()
      .then(events => {
        const mainEvents = getNearestEvents(events, 3);
        populateMainEvents(mainEvents);
      })
      .catch(err => {
        console.error("Main events loading failed:", err);
        // Optionally show an error to the user here
      });
  });