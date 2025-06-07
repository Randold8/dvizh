// Logic for populating the user achievements section

function populateAchievements() {
  const achievementsSection = document.querySelector('.achievements-section');
  if (!achievementsSection) return;

  const titleElement = achievementsSection.querySelector('p');
  if (!titleElement) return;

  Promise.all([
    window.EventsProvider.getUserData(),
    window.EventsProvider.getCurrentUserRating()
  ])
    .then(([userData, ratingData]) => {
      const { stars } = userData;
      const { rating, totalUsers } = ratingData;

      // Prepare achievement text
      let achievementText = '';

      // Line 1: Stars info with conditional congratulation
      if (stars >= 300) {
        achievementText += `У вас ${stars} звёзд! Поздравляем!<br><br>`;
      } else {
        achievementText += `У вас ${stars} звёзд.<br><br>`;
      }

      // Line 2: User rating
      achievementText += `Ваш рейтинг: ${rating} из ${totalUsers}.<br><br>`;

      // Line 3: Call to action (подчеркнутая, с нужными переносами)
      achievementText += '<u>Запишитесь на ближайшие<br>мероприятия чтобы получить<br>больше звёзд!</u>';

      // Update the content
      titleElement.innerHTML = achievementText;
    })
    .catch(err => {
      console.error("Failed to load achievement data:", err);
      titleElement.textContent = "Не удалось загрузить информацию о достижениях.";
    });
}

// Initialize on DOM load
document.addEventListener("DOMContentLoaded", function() {
populateAchievements();
});
