document.addEventListener("DOMContentLoaded", () => {
    // 1. Initialize the Date Picker
    flatpickr("#event-date-picker", {
      locale: "ru", // Use the Russian language pack
      dateFormat: "d.m.Y", // Set the date format
      altInput: true, // Show a user-friendly date format
      altFormat: "F j, Y", // The format the user sees
      minDate: "today" // Prevent selecting past dates
    });
  
    // 2. Logic for auto-expanding textarea
    const textarea = document.querySelector(".description-box");
    textarea.addEventListener("input", () => {
      textarea.style.height = "auto"; // Reset height to recalculate
      textarea.style.height = `${textarea.scrollHeight}px`; // Set to content height
    });
  
    // 3. Logic to display the selected file name
    const fileInput = document.getElementById("event-image-upload");
    const fileNameDisplay = document.getElementById("file-name-display");
  
    fileInput.addEventListener("change", () => {
      if (fileInput.files.length > 0) {
        fileNameDisplay.textContent = fileInput.files[0].name;
      } else {
        fileNameDisplay.textContent = "Файл не выбран";
      }
    });
  });
  