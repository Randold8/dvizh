// Simple events provider, fetches/caches events for all consumers

window.EventsProvider = (function() {
    let events = null;
    let fetched = false;
    let fetchPromise = null;
  
    function fetchEvents() {
      if (fetchPromise) return fetchPromise; // Already fetching
      fetchPromise = fetch('/events.json')
        .then(res => {
          if (!res.ok) throw new Error('Failed to fetch events');
          return res.json();
        })
        .then(data => {
          events = data;
          fetched = true;
          return events;
        });
      return fetchPromise;
    }
  
    function getEvents() {
      if (fetched && events) {
        return Promise.resolve(events);
      }
      return fetchEvents();
    }
  
    return {
      getEvents // returns a Promise<Array>
    };
  })();
  