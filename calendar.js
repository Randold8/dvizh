// Utility: Convert "dd.mm" to YYYY-MM-DD string for FullCalendar
function parseEventDate(dateString) {
  const [day, month] = dateString.split('.').map(Number);
  const now = new Date();
  const year = now.getFullYear();
  let eventDate = new Date(year, month - 1, day);
  const today = new Date(now.setHours(0, 0, 0, 0));
  if (eventDate < today) eventDate = new Date(year + 1, month - 1, day);

  // Use local time, not UTC
  const y = eventDate.getFullYear();
  const m = String(eventDate.getMonth() + 1).padStart(2, '0');
  const d = String(eventDate.getDate()).padStart(2, '0');
  return `${y}-${m}-${d}`;
}
  // Map to FullCalendar events: just the type (in Russian) and colored background
  const eventTypeMap = {
    lecture:    {name: 'Лекция',      color: '#1C00EF'},
    exhibition: {name: 'Выставка',    color: '#f39c12'},
    conference: {name: 'Конференция', color: '#16a085'},
    quiz:       {name: 'Квиз',        color: '#d35400'},
    other:      {name: 'Другое',      color: '#8e44ad'}
  };
  
  function toFullCalendarEvents(events) {
    return events.map(ev => {
      const typeDef = eventTypeMap[ev.type] || {name: ev.type, color: '#888'};
      return {
        id: ev.id,
        title: typeDef.name,
        start: parseEventDate(ev.date),
        url: "/events/" + ev.id, // Placeholder; customize as needed
        color: typeDef.color
      };
    });
  }
  
  // --- MAIN LOGIC: Render FullCalendar after loading events ---
  document.addEventListener('DOMContentLoaded', function() {
    window.EventsProvider.getEvents()
      .then(events => {
        const calendarEl = document.getElementById('calendar');
        const calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          height: 'auto',
          handleWindowResize: true,  // Ensures calendar adjusts on window resize
          windowResizeDelay: 100, 
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
          },
          locale: 'ru',
          buttonText: {
            today:    'Сегодня',
            month:    'Месяц',
            week:     'Неделя',
            day:      'День',
            list:     'Список'
          },
          events: toFullCalendarEvents(events),
          eventClick: function(info) {
            // Open in a new tab
            window.open(info.event.url, '_blank', 'noopener');
            info.jsEvent.preventDefault(); // Prevent default navigation
          }
        });
        calendar.render();
      })
      .catch(err => {
        document.getElementById('calendar').innerHTML = '<p style="color:red;">Не удалось загрузить события календаря</p>';
        console.error("Calendar loading failed:", err);
      });
  });