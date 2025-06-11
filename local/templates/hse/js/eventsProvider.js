// Simple events provider, fetches/caches events for all consumers

window.EventsProvider = (function() {
  // Data caches
  let events = null;
  let userData = null;
  let ratings = null;

  // Fetch state tracking
  let fetchedEvents = false;
  let fetchedUser = false;
  let fetchedRatings = false;

  // Promise caches to prevent duplicate requests
  let eventsPromise = null;
  let userPromise = null;
  let ratingsPromise = null;

  // Fetch events data
  function fetchEvents() {
    if (eventsPromise) return eventsPromise;

    eventsPromise = fetch('/events.json')
      .then(res => {
        if (!res.ok) throw new Error('Failed to fetch events');
        return res.json();
      })
      .then(data => {
        events = data;
        fetchedEvents = true;
        return events;
      });

    return eventsPromise;
  }

  // Fetch current user data
  function fetchUserData() {
    if (userPromise) return userPromise;

    userPromise = fetch('/user.json')
      .then(res => {
        if (!res.ok) throw new Error('Failed to fetch user data');
        return res.json();
      })
      .then(data => {
        userData = data;
        fetchedUser = true;
        return userData;
      });

    return userPromise;
  }

  // Fetch ratings data
  function fetchRatings() {
    if (ratingsPromise) return ratingsPromise;

    ratingsPromise = fetch('/ratings.json')
      .then(res => {
        if (!res.ok) throw new Error('Failed to fetch ratings');
        return res.json();
      })
      .then(data => {
        ratings = data;
        fetchedRatings = true;
        return ratings;
      });

    return ratingsPromise;
  }

  // Public API
  function getEvents() {
    if (fetchedEvents && events) {
      return Promise.resolve(events);
    }
    return fetchEvents();
  }

  function getUserData() {
    if (fetchedUser && userData) {
      return Promise.resolve(userData);
    }
    return fetchUserData();
  }

  function getRatings() {
    if (fetchedRatings && ratings) {
      return Promise.resolve(ratings);
    }
    return fetchRatings();
  }

  function getCurrentUserRating() {
    return Promise.all([fetchUserData(), fetchRatings()])
      .then(([user, ratingsData]) => {
        const userRating = ratingsData.ratings.find(r => r.userId === user.id);
        return {
          rating: userRating ? userRating.rating : 0,
          totalUsers: ratingsData.totalUsers
        };
      });
  }

  return {
    getEvents,
    getUserData,
    getRatings,
    getCurrentUserRating
  };
})();
  