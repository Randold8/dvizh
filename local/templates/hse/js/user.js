document.addEventListener("DOMContentLoaded", () => {
    const userName = document.getElementById("user-name");
    const userEmail = document.getElementById("user-email");
    const userGroup = document.getElementById("user-group");
    const profileImage = document.getElementById("profile-image");
    const uploadAvatar = document.getElementById("upload-avatar");
    const achievementsSummary = document.querySelector(".user-achievements-summary");
  
    // Load and display user data
    EventsProvider.getUserData().then(data => {
      userName.value = data.name;
      userEmail.value = data.email;
      userGroup.value = data.group;
    }).catch(console.error);
  
    profileImage.addEventListener("click", () => uploadAvatar.click());
  
    uploadAvatar.addEventListener("change", () => {
      const file = uploadAvatar.files[0];
      if (!file) return;
  
      const reader = new FileReader();
      reader.onload = e => {
        profileImage.src = e.target.result;
  
        const formData = new FormData();
        formData.append("avatar", file);
        fetch("/upload-avatar", {method: "POST", body: formData})
          .then(res => {
            if (!res.ok) throw new Error("Ошибка загрузки изображения");
            return res.json();
          })
          .then(() => alert("Аватар обновлён!"))
          .catch(err => alert("Ошибка загрузки: " + err.message));
      };
      reader.readAsDataURL(file);
    });
  
    // Load simplified achievements without subscribe message
    EventsProvider.getUserData().then(userData => {
      EventsProvider.getCurrentUserRating().then(ratingData => {
        const { stars } = userData;
        const { rating, totalUsers } = ratingData;
  
        let achievementText = "";
        achievementText += stars >= 300 ?
          `У вас ${stars} звёзд! Поздравляем!<br>` :
          `У вас ${stars} звёзд.<br>`;
        achievementText += `Ваш рейтинг: ${rating} из ${totalUsers}.`;
  
        achievementsSummary.innerHTML = achievementText;
      });
    }).catch(err => {
      achievementsSummary.textContent = "Ошибка загрузки достижений.";
      console.error(err);
    });
  });
  