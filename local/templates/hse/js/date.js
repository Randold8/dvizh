document.addEventListener("DOMContentLoaded", function () {
    const today = new Date();
    const day = today.getDate();
    const month = today.getMonth() + 1; // JavaScript months are 0-indexed

    // Set the date numbers
    const dayElement = document.getElementById("day-number");
    const monthElement = document.getElementById("month-number");

    dayElement.textContent = day;
    monthElement.textContent = month;

    // Adjust font size based on the combined length of day and month digits
    const totalDigits = day.toString().length + month.toString().length;

    if (totalDigits > 3) {
      // For dates like 10.10, 12.12, etc.
      dayElement.style.fontSize = "120px";
      monthElement.style.fontSize = "80px";

      // Adjust divider width for balance
      document.querySelector(".date-divider hr").style.width = "60px";
    } else if (totalDigits > 2) {
      // For dates like 5.10 or 10.5
      dayElement.style.fontSize = "135px";
      monthElement.style.fontSize = "90px";
    }
    // Default sizes are already set in CSS for short dates like 5.5
  });