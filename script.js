document.addEventListener("DOMContentLoaded", function () {
    // Initialize state
    const state = {
      selectedDate: new Date(),
      events: [
        {
          id: 1,
          title: "Team Meeting",
          date: "2024-01-7",
          time: "10:00 AM",
          description: "Weekly team sync",
        },
        {
          id: 2,
          title: "Product Launch",
          date: "2024-01-10",
          time: "2:00 PM",
          description: "New feature release",
        },
      ],
      selectedEvent: null,
    };
  
    // DOM elements
    const monthSelect = document.getElementById("month-select");
    const yearSelect = document.getElementById("year-select");
    const prevMonthButton = document.querySelector(".prev-month-button");
    const calendarGrid = document.querySelector(".calendar-grid");
    const eventDetails = document.getElementById("event-details");
    const noEventMessage = document.getElementById("no-event-message");
    const closeDetailsButton = document.querySelector(".close-details-button");
  
    // Initialize selects with current date
    monthSelect.value = state.selectedDate.getMonth();
    yearSelect.value = state.selectedDate.getFullYear();
  
    // Event listeners
    monthSelect.addEventListener("change", handleMonthChange);
    yearSelect.addEventListener("change", handleYearChange);
    prevMonthButton.addEventListener("click", handlePrevMonth);
    closeDetailsButton.addEventListener("click", closeEventDetails);
  
    // Initial render
    renderCalendar();
  
    // Functions
    function handleMonthChange(e) {
      const newDate = new Date(state.selectedDate);
      newDate.setMonth(parseInt(e.target.value));
      state.selectedDate = newDate;
      renderCalendar();
    }
  
    function handleYearChange(e) {
      const newDate = new Date(state.selectedDate);
      newDate.setFullYear(parseInt(e.target.value));
      state.selectedDate = newDate;
      renderCalendar();
    }
  
    function handlePrevMonth() {
      const newDate = new Date(state.selectedDate);
      newDate.setMonth(newDate.getMonth() - 1);
      state.selectedDate = newDate;
      monthSelect.value = state.selectedDate.getMonth();
      yearSelect.value = state.selectedDate.getFullYear();
      renderCalendar();
    }
  
    function renderCalendar() {
      // Clear calendar grid
      calendarGrid.innerHTML = "";
  
      // Create 35 day cells (5 weeks x 7 days)
      for (let i = 0; i < 35; i++) {
        const dayCell = document.createElement("div");
        dayCell.className = "calendar-day";
  
        const dayNumber = document.createElement("span");
        dayNumber.className = "day-number";
        dayNumber.textContent = i + 1;
        dayCell.appendChild(dayNumber);
  
        // Add events for this day
        const dayEvents = state.events.filter((event) => {
          // In a real app, you'd compare actual dates
          // This is simplified for the demo
          return event.date === `2024-01-${i + 1}`;
        });
  
        dayEvents.forEach((event) => {
          const eventElement = document.createElement("div");
          eventElement.className = "event-item";
          eventElement.textContent = event.title;
          eventElement.addEventListener("click", () => {
            showEventDetails(event);
          });
          dayCell.appendChild(eventElement);
        });
  
        calendarGrid.appendChild(dayCell);
      }
    }
  
    function showEventDetails(event) {
      state.selectedEvent = event;
  
      // Update event details
      document.querySelector(".event-title").textContent = event.title;
      document.querySelector(".event-date").textContent = event.date;
      document.querySelector(".event-time").textContent = event.time;
      document.querySelector(".event-description").textContent =
        event.description;
  
      // Show details, hide message
      eventDetails.style.display = "block";
      noEventMessage.style.display = "none";
    }
  
    function closeEventDetails() {
      state.selectedEvent = null;
  
      // Hide details, show message
      eventDetails.style.display = "none";
      noEventMessage.style.display = "block";
    }
  });
  