document.addEventListener("DOMContentLoaded", () => {
  const state = {
    notifications: false,
    currentMonth: new Date().getMonth(),
    currentYear: new Date().getFullYear(),
    selectedDate: null,
    selectedEvent: null,
    showEventPanel: false,
    showEventForm: false,
    isEditingEvent: false,
    eventDetails: {
      name: "",
      day: "",
      date: "",
      time: "",
      location: "",
      involvement: "",
      personInCharge: "",
      unit: "",
    },
    events: [],
  };

  let nodesToDestroy = [];
  let pendingUpdate = false;

  function fetchEventsFromDB() {
    fetch("get_events.php")
      .then((res) => res.json())
      .then((data) => {
        state.events = data.map((event) => ({
          id: event.id,
          title: event.name,
          date: event.date,
          startTime: event.time,
          day: event.day,
          location: event.location,
          involvement: event.involvement,
          personInCharge: event.person_in_charge,
          unit: event.unit,
          description: event.name,
        }));
  
        renderCalendarDays();
        updateEventDetailsPanel();
      })
      .catch((err) => {
        console.error("❌ Failed to load events:", err);
      });
  }
  

  function destroyAnyNodes() {
    // destroy current view template refs before rendering again
    nodesToDestroy.forEach((el) => el.remove());
    nodesToDestroy = [];
  }

  // Function to update data bindings and loops
  function update() {
    if (pendingUpdate === true) {
      return;
    }
    pendingUpdate = true;

    // Notification button
    document.querySelectorAll("[data-el='button-1']").forEach((el) => {
      el.removeEventListener("click", onButton1Click);
      el.addEventListener("click", onButton1Click);
    });

    document.querySelectorAll("[data-el='div-1']").forEach((el) => {
      el.hidden = !state.notifications;

      el.removeEventListener("click", onDiv1Click);
      el.addEventListener("click", onDiv1Click);
    });

    // Add Event button
    document.querySelectorAll("[data-el='button-2']").forEach((el) => {
      el.removeEventListener("click", onButton2Click);
      el.addEventListener("click", onButton2Click);
    });

    // Event form modal
    document.querySelectorAll("[data-el='div-2']").forEach((el) => {
      el.hidden = !state.showEventForm;
    });

    // Close modal button
    document.querySelectorAll("[data-el='button-3']").forEach((el) => {
      el.removeEventListener("click", onButton3Click);
      el.addEventListener("click", onButton3Click);
    });

    // Form inputs
    document.querySelectorAll("[data-el='input-1']").forEach((el) => {
      el.value = state.eventDetails.name;
      el.removeEventListener("input", onInput1Input);
      el.addEventListener("input", onInput1Input);
    });

    document.querySelectorAll("[data-el='input-3']").forEach((el) => {
      el.value = state.eventDetails.day;
      el.removeEventListener("input", onInput3Input);
      el.addEventListener("input", onInput3Input);
    });

    document.querySelectorAll("[data-el='input-5']").forEach((el) => {
      el.value = state.eventDetails.date;
      el.removeEventListener("input", onInput5Input);
      el.addEventListener("input", onInput5Input);
    });

    document.querySelectorAll("[data-el='input-7']").forEach((el) => {
      el.value = state.eventDetails.time;
      el.removeEventListener("input", onInput7Input);
      el.addEventListener("input", onInput7Input);
    });

    document.querySelectorAll("[data-el='input-9']").forEach((el) => {
      el.value = state.eventDetails.location;
      el.removeEventListener("input", onInput9Input);
      el.addEventListener("input", onInput9Input);
    });

    document.querySelectorAll("[data-el='input-11']").forEach((el) => {
      el.value = state.eventDetails.involvement;
      el.removeEventListener("input", onInput11Input);
      el.addEventListener("input", onInput11Input);
    });

    document.querySelectorAll("[data-el='input-13']").forEach((el) => {
      el.value = state.eventDetails.personInCharge;
      el.removeEventListener("input", onInput13Input);
      el.addEventListener("input", onInput13Input);
    });

    document.querySelectorAll("[data-el='input-15']").forEach((el) => {
      el.value = state.eventDetails.unit;
      el.removeEventListener("input", onInput15Input);
      el.addEventListener("input", onInput15Input);
    });

    // Form action buttons
    document.querySelectorAll("[data-el='button-4']").forEach((el) => {
      el.removeEventListener("click", onButton4Click);
      el.addEventListener("click", onButton4Click);
    });

    document.querySelectorAll("[data-el='button-5']").forEach((el) => {
      el.removeEventListener("click", onButton5Click);
      el.addEventListener("click", onButton5Click);
    });

    // Set the current month in the dropdown
    const monthSelect = document.querySelector(".month-select");
    if (monthSelect) {
      monthSelect.value = state.currentMonth;

      // Add event listener for month change
      monthSelect.addEventListener("change", (e) => {
        state.currentMonth = parseInt(e.target.value);
        renderCalendarDays();
      });
    }

    document.querySelectorAll("[data-el='option-1']").forEach((el) => {
      el.selected = true;
    });

    // Populate year dropdown directly
    populateYearDropdown();

    destroyAnyNodes();

    pendingUpdate = false;
  }

  // Populate the year dropdown with options
  function populateYearDropdown() {
    const yearSelect = document.querySelector(".year-select");
    if (!yearSelect) return;

    // Clear existing options
    yearSelect.innerHTML = "";

    // Add year options (5 years before and 10 years after current year)
    const currentYear = new Date().getFullYear();
    for (let year = currentYear - 5; year <= currentYear + 10; year++) {
      const option = document.createElement("option");
      option.value = year;
      option.textContent = year;
      option.selected = year === state.currentYear;
      yearSelect.appendChild(option);
    }

    // Add event listener for year change
    yearSelect.addEventListener("change", (e) => {
      state.currentYear = parseInt(e.target.value);
      renderCalendarDays();
    });
  }

  // Event handler for notification button click
  function onButton1Click(event) {
    state.notifications = !state.notifications;
    update();
  }

  // Event handler for notification dropdown click
  function onDiv1Click(event) {
    event.stopPropagation();
    state.notifications = false;
    update();
  }

  // Event handler for add event button click
  function onButton2Click(event) {
    resetEventForm();
    state.isEditingEvent = false;
  
    // If no date is selected, set it to today's date
    if (!state.selectedDate) {
      const today = new Date();
      state.selectedDate = {
        day: today.getDate(),
        month: today.getMonth(),
        year: today.getFullYear(),
        formatted: formatDate(today.getFullYear(), today.getMonth() + 1, today.getDate())
      };
    }
  
    // Pre-fill date and day
    state.eventDetails.date = "";
    const dayNames = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
    const dayIndex = new Date(state.selectedDate.year, state.selectedDate.month, state.selectedDate.day).getDay();
    state.eventDetails.day = "";
  
    state.showEventForm = true;
    update();
  }
  

  // Event handler for close modal button click
  function onButton3Click(event) {
    state.showEventForm = false;
    update();
  }

  // Event handler for form input changes
  function onInput1Input(event) {
    state.eventDetails.name = event.target.value;
    
  }

  function onInput3Input(event) {
    state.eventDetails.day = event.target.value;
    
  }

  function onInput5Input(event) {
    state.eventDetails.date = event.target.value;
    

    // Update day based on selected date
    if (event.target.value) {
      const date = new Date(event.target.value);
      const dayNames = [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
      ];
      state.eventDetails.day = dayNames[date.getDay()];

      // Update day input field
      document.querySelectorAll("[data-el='input-3']").forEach((el) => {
        el.value = state.eventDetails.day;
      });
    }
    
  }

  function onInput7Input(event) {
    state.eventDetails.time = event.target.value;
    
  }

  function onInput9Input(event) {
    state.eventDetails.location = event.target.value;
    
  }

  function onInput11Input(event) {
    state.eventDetails.involvement = event.target.value;
    
  }

  function onInput13Input(event) {
    state.eventDetails.personInCharge = event.target.value;
    
  }

  function onInput15Input(event) {
    state.eventDetails.unit = event.target.value;
    
  }

  function validateFormWithErrors() {
    let isValid = true;
  
    const fields = [
      { key: 'name', inputEl: 'input-1', errorEl: 'error-name', label: 'Event Name' },
      { key: 'day', inputEl: 'input-3', errorEl: 'error-day', label: 'Day' },
      { key: 'date', inputEl: 'input-5', errorEl: 'error-date', label: 'Date' },
      { key: 'time', inputEl: 'input-7', errorEl: 'error-time', label: 'Time' },
      { key: 'location', inputEl: 'input-9', errorEl: 'error-location', label: 'Location' },
      { key: 'involvement', inputEl: 'input-11', errorEl: 'error-involvement', label: 'Involvement' },
      { key: 'personInCharge', inputEl: 'input-13', errorEl: 'error-person', label: 'Person in Charge' },
      { key: 'unit', inputEl: 'input-15', errorEl: 'error-unit', label: 'Unit' }
    ];
  
    fields.forEach(({ key, inputEl, errorEl, label }) => {
      const value = state.eventDetails[key];
      const error = document.getElementById(errorEl);
      const input = document.querySelector(`[data-el='${inputEl}']`);
  
      if (!value) {
        error.textContent = `${label} is required.`;
        error.style.color = "red";
        input.classList.add("input-error");
        isValid = false;
      } else {
        error.textContent = "";
        input.classList.remove("input-error");
      }
    });
  
    return isValid;
  }
  

  function onButton4Click(event) {
    // ✅ Validate inputs first
    if (!validateFormWithErrors()) return;
  
    const updatedEvent = {
      name: state.eventDetails.name,
      day: state.eventDetails.day,
      date: state.eventDetails.date,
      time: state.eventDetails.time,
      location: state.eventDetails.location,
      involvement: state.eventDetails.involvement,
      person_in_charge: state.eventDetails.personInCharge,
      unit: state.eventDetails.unit
    };
  
    if (state.isEditingEvent && state.selectedEvent) {
      
      const eventIndex = state.events.findIndex(
        (e) => e.id === state.selectedEvent.id
      );
  
      if (eventIndex !== -1) {
        const updated = {
          ...state.events[eventIndex],
          title: updatedEvent.name,
          date: updatedEvent.date,
          startTime: updatedEvent.time,
          day: updatedEvent.day,
          location: updatedEvent.location,
          involvement: updatedEvent.involvement,
          personInCharge: updatedEvent.person_in_charge,
          unit: updatedEvent.unit,
          description: updatedEvent.name,
        };
  
        state.events[eventIndex] = updated;
      }
  
      // 🔄 Send update to server
      fetch("edit_event.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({
          id: state.selectedEvent.id,
          ...updatedEvent,
        }),
      })
        .then((res) => res.text())
        .then((resText) => {
          console.log("Edit response:", resText);
          fetchEventsFromDB(); // reload fresh events from database
        })
        .catch((err) => console.error("Edit failed:", err));
    } else {
      // ➕ Add new event
      const newEvent = {
        id: Date.now(),
        title: updatedEvent.name,
        date: updatedEvent.date,
        startTime: updatedEvent.time,
        endTime: "",
        day: updatedEvent.day,
        location: updatedEvent.location,
        involvement: updatedEvent.involvement,
        personInCharge: updatedEvent.person_in_charge,
        unit: updatedEvent.unit,
        description: updatedEvent.name,
      };
  
      state.events.push(newEvent);
  
      // 📨 Send new event to server
      submitEventToServer(updatedEvent);
  
      // 🔁 Set selectedDate
      const [year, month, day] = updatedEvent.date.split("-").map(Number);
      state.selectedDate = {
        year,
        month: month - 1,
        day,
        formatted: updatedEvent.date,
      };
    }
  
    // ✅ Update UI
    state.showEventForm = false;
    state.showEventPanel = true;
    renderCalendarDays();
    updateEventDetailsPanel();
    update();
  }
  

function submitEventToServer(data) {
    fetch("add_event.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: new URLSearchParams(data),
    })
      .then((response) => response.text())
      .then((result) => {
        console.log("✅ Server response:", result);
        fetchEventsFromDB(); // reload events from DB after saving
      })
      .catch((error) => {
        console.error("❌ Failed to submit event:", error);
      });
  }
  
  
  
  

  // Event handler for cancel button click
  function onButton5Click(event) {
    resetEventForm();
    state.showEventForm = false;
    update();
  }

  // Reset event form
  function resetEventForm() {
    state.eventDetails = {
      name: "",
      date: "",
      time: "",
      day: "",
      location: "",
      involvement: "",
      personInCharge: "",
      unit: "",
    };
  
    const fields = [
      'input-1', 'input-3', 'input-5', 'input-7', 
      'input-9', 'input-11', 'input-13', 'input-15'
    ];
  
    const errorSpans = [
      'error-name', 'error-day', 'error-date', 'error-time',
      'error-location', 'error-involvement', 'error-person', 'error-unit'
    ];
  
    fields.forEach((inputEl) => {
      const input = document.querySelector(`[data-el='${inputEl}']`);
      if (input) input.classList.remove("input-error");
    });
  
    errorSpans.forEach((errorId) => {
      const span = document.getElementById(errorId);
      if (span) span.textContent = "";
    });
  }
  


  // Helper text DOM nodes
  function renderTextNode(el, text) {
    const textNode = document.createTextNode(text);
    if (el?.scope) {
      textNode.scope = el.scope;
    }
    el.after(textNode);
    nodesToDestroy.push(el.nextSibling);
  }

  // Helper to render loops
  function renderLoop(template, array, itemName, itemIndex, collectionName) {
    const collection = [];
    for (let [index, value] of array.entries()) {
      const elementFragment = template.content.cloneNode(true);
      const children = Array.from(elementFragment.childNodes);
      const localScope = {};
      let scope = localScope;

      if (template?.scope) {
        const getParent = {
          get(target, prop, receiver) {
            if (prop in target) {
              return target[prop];
            }
            if (prop in template.scope) {
              return template.scope[prop];
            }
            return target[prop];
          },
        };
        scope = new Proxy(localScope, getParent);
      }

      children.forEach((child) => {
        if (itemName !== undefined) {
          scope[itemName] = value;
        }
        if (itemIndex !== undefined) {
          scope[itemIndex] = index;
        }
        if (collectionName !== undefined) {
          scope[collectionName] = array;
        }
        child.scope = scope;
        if (template.context) {
          child.context = template.context;
        }
        nodesToDestroy.push(child);
        collection.unshift(child);
      });

      collection.forEach((child) => template.after(child));
    }
  }

  function getScope(el, name) {
    do {
      let value = el?.scope?.[name];
      if (value !== undefined) {
        return value;
      }
    } while ((el = el.parentNode));
    return undefined;
  }

  // Get the number of days in a month
  function getDaysInMonth(month, year) {
    return new Date(year, month + 1, 0).getDate();
  }

  // Get the first day of the month (0 = Sunday, 1 = Monday, etc.)
  function getFirstDayOfMonth(month, year) {
    return new Date(year, month, 1).getDay();
  }

  // Format date as YYYY-MM-DD
  function formatDate(year, month, day) {
    return `${String(day).padStart(2, "0")}-${String(month + 1).padStart(2, "0")}-${year}`;
  }
  

  // Check if a date has events
  function hasEvents(year, month, day) {
    const dateString = formatDate(year, month + 1, day);
    return state.events.some((event) => event.date === dateString);
  }

  // Handle day click
  function handleDayClick(day, month, year) {
    // Clear previous selection
    document.querySelectorAll(".calendar-day.selected").forEach((el) => {
      el.classList.remove("selected");
    });

    // Set new selected date
    state.selectedDate = {
      day,
      month,
      year,
      formatted: formatDate(year, month + 1, day),
    };

    // Reset selected event
    state.selectedEvent = null;

    // Show event panel if hidden
    state.showEventPanel = true;

    // Update UI
    updateEventDetailsPanel();

    // Add selected class to the clicked day
    const dayElements = document.querySelectorAll(".calendar-day:not(.empty)");
    dayElements.forEach((el) => {
      if (parseInt(el.textContent) === day) {
        el.classList.add("selected");
      }
    });
  }

  // Handle event selection
  function handleEventSelection(eventId) {
    // Clear previous selection
    document.querySelectorAll(".event-item.selected").forEach((el) => {
      el.classList.remove("selected");
    });

    // Set selected event
    state.selectedEvent = state.events.find((event) => event.id === eventId);

    // Update UI
    updateEventActionButtons();

    // Add selected class to the clicked event
    document.querySelectorAll(".event-item").forEach((el) => {
      if (parseInt(el.dataset.eventId) === eventId) {
        el.classList.add("selected");
      }
    });
  }

  // Update event action buttons based on selection
  function updateEventActionButtons() {
    const editButton = document.getElementById("event-edit-button");
    const deleteButton = document.getElementById("event-delete-button");

    if (!editButton || !deleteButton) return;

    if (state.selectedEvent) {
      editButton.disabled = false;
      deleteButton.disabled = false;
    } else {
      editButton.disabled = true;
      deleteButton.disabled = true;
    }
  }

  // Render calendar days based on current month and year
  function renderCalendarDays() {
    const calendarGrid = document.querySelector(".calendar-grid");
    if (!calendarGrid) return;

    // Clear existing content
    calendarGrid.innerHTML = "";

    const daysInMonth = getDaysInMonth(state.currentMonth, state.currentYear);
    const firstDay = getFirstDayOfMonth(state.currentMonth, state.currentYear);

    // Add empty cells for days before the first day of the month
    for (let i = 0; i < firstDay; i++) {
      const emptyCell = document.createElement("div");
      emptyCell.className = "calendar-day empty";
      calendarGrid.appendChild(emptyCell);
    }

    // Add cells for each day of the month
    for (let day = 1; day <= daysInMonth; day++) {
      const dayCell = document.createElement("div");
      dayCell.className = "calendar-day";
      const dayNumber = document.createElement("span");
      dayNumber.className = "day-number";
      dayNumber.textContent = day;
      dayCell.appendChild(dayNumber);

      const dateString = formatDate(state.currentYear, state.currentMonth + 1, day);
      const eventsToday = getEventsForDate(dateString);
      
      
    
      if (eventsToday.length > 0) {
  eventsToday.forEach((event) => {
    const eventLabel = document.createElement("div");
    eventLabel.className = "event-label";
    eventLabel.textContent = event.title;
    eventLabel.style.cursor = "pointer";

    // Make event label clickable
    eventLabel.addEventListener("click", (e) => {
      e.stopPropagation();
      openViewEventModal(event); // You define this function separately
    });

    dayCell.appendChild(eventLabel);
  });
}

    


      // Check if this day has events
      if (hasEvents(state.currentYear, state.currentMonth, day)) {
        dayCell.classList.add("has-events");
      }

      // Highlight today's date if it's the current month and year
      const today = new Date();
      if (
        day === today.getDate() &&
        state.currentMonth === today.getMonth() &&
        state.currentYear === today.getFullYear()
      ) {
        dayCell.classList.add("today");
      }

      // Check if this is the selected date
      if (
        state.selectedDate &&
        day === state.selectedDate.day &&
        state.currentMonth === state.selectedDate.month &&
        state.currentYear === state.selectedDate.year
      ) {
        dayCell.classList.add("selected");
      }

      // Add click event to select the date
      dayCell.addEventListener("click", () => {
        handleDayClick(day, state.currentMonth, state.currentYear);
      });

      calendarGrid.appendChild(dayCell);
    }

    // Fill remaining cells to complete the grid (7x6 grid = 42 cells)
    const totalCells = firstDay + daysInMonth;
    const remainingCells = 42 - totalCells;

    for (let i = 0; i < remainingCells; i++) {
      const emptyCell = document.createElement("div");
      emptyCell.className = "calendar-day empty";
      calendarGrid.appendChild(emptyCell);
    }
  }

  function formatDate(year, month, day) {
    return `${year}-${String(month).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
  }
  

  // Get events for a specific date
  function getEventsForDate(dateString) {
    return state.events.filter((event) => event.date === dateString);
  }

  function formatTimeWithAMPM(timeStr) {
    if (!timeStr) return "N/A";
    const [hours, minutes] = timeStr.split(":").map(Number);
    const ampm = hours >= 12 ? "PM" : "AM";
    const adjustedHour = hours % 12 || 12;
    return `${adjustedHour}:${minutes.toString().padStart(2, "0")} ${ampm}`;
  }
  

  // Update the event details panel
  function updateEventDetailsPanel() {
    const eventPanel = document.getElementById("event-details-panel");
    const dateDisplay = document.getElementById("selected-date-display");
    const noEventsMessage = document.querySelector(".no-events-message");
    const eventsContainer = document.getElementById("events-container");

    if (!eventPanel || !dateDisplay || !noEventsMessage || !eventsContainer)
      return;

    // Toggle panel visibility
    if (state.showEventPanel) {
      eventPanel.style.display = "flex";
    } else {
      eventPanel.style.display = "none";
      return;
    }

    if (state.selectedDate) {
      // Format the date for display
      const options = {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
      };
      const displayDate = new Date(
        state.selectedDate.year,
        state.selectedDate.month,
        state.selectedDate.day,
      ).toLocaleDateString("en-US", options);

      dateDisplay.textContent = displayDate;

      // Get events for the selected date
      const events = getEventsForDate(state.selectedDate.formatted);

      if (events.length > 0) {
        // Hide no events message and show events container
        noEventsMessage.style.display = "none";
        eventsContainer.style.display = "block";

        // Clear existing events
        eventsContainer.innerHTML = "";

        // Add each event to the container
        events.forEach((event) => {
          const eventElement = document.createElement("div");
          eventElement.className = "event-item";
          eventElement.dataset.eventId = event.id;

          if (state.selectedEvent && state.selectedEvent.id === event.id) {
            eventElement.classList.add("selected");
          }
          eventElement.innerHTML = `
          <div class="event-title"><strong>${event.title}</strong></div><br>
          <div class="event-line"><strong>Date:</strong> ${formatDateForDisplay(event.date)} (${event.day})</div><br>
          <div class="event-line"><strong>Time:</strong> ${formatTimeWithAMPM(event.startTime)}${event.endTime ? " - " + formatTimeWithAMPM(event.endTime) : ""}</div><br>
          <div class="event-line"><strong>Location:</strong> ${event.location || "N/A"}</div><br>
          <div class="event-line"><strong>Involvement:</strong> ${event.involvement || "N/A"}</div><br>
          <div class="event-line"><strong>Person in Charge:</strong> ${event.personInCharge || "N/A"}</div><br>
          <div class="event-line"><strong>Unit:</strong> ${event.unit || "N/A"}</div><br>
        `;
        
          // Add click event to select this event
          eventElement.addEventListener("click", (e) => {
  e.stopPropagation(); // Prevent background click logic
  state.selectedEvent = event;
  openViewEventModal(event); // Reuse your modal opening logic
});


          eventsContainer.appendChild(eventElement);
        });
      } else {
        // Show no events message and hide events container
        noEventsMessage.style.display = "block";
        eventsContainer.style.display = "none";

        // Reset selected event
        state.selectedEvent = null;
      }
    } else {
      // No date selected
      dateDisplay.textContent = "No date selected";
      noEventsMessage.style.display = "block";
      eventsContainer.style.display = "none";

      // Reset selected event
      state.selectedEvent = null;
    }

    // Update action buttons
    updateEventActionButtons();
  }
  function formatDateForDisplay(dateStr) {
    const [year, month, day] = dateStr.split("-");
    return `${day}-${month}-${year}`;
  }
  
  // Initialize calendar with navigation
  function initializeCalendar() {
    // Add event listeners for month navigation
    const prevMonthBtn = document.querySelector(".prev-month");
    const nextMonthBtn = document.querySelector(".next-month");

    if (prevMonthBtn) {
      prevMonthBtn.addEventListener("click", () => {
        if (state.currentMonth === 0) {
          state.currentMonth = 11;
          state.currentYear--;
        } else {
          state.currentMonth--;
        }

        // Update month and year selects
        const monthSelect = document.querySelector(".month-select");
        const yearSelect = document.querySelector(".year-select");

        if (monthSelect) {
          monthSelect.value = state.currentMonth;
        }

        if (yearSelect) {
          yearSelect.value = state.currentYear;
        }

        renderCalendarDays();
      });
    }

    if (nextMonthBtn) {
      nextMonthBtn.addEventListener("click", () => {
        if (state.currentMonth === 11) {
          state.currentMonth = 0;
          state.currentYear++;
        } else {
          state.currentMonth++;
        }

        // Update month and year selects
        const monthSelect = document.querySelector(".month-select");
        const yearSelect = document.querySelector(".year-select");

        if (monthSelect) {
          monthSelect.value = state.currentMonth;
        }

        if (yearSelect) {
          yearSelect.value = state.currentYear;
        }

        renderCalendarDays();
      });
    }

    // Add event listener for close panel button
    const closePanelButton = document.getElementById("close-panel-button");
    if (closePanelButton) {
      closePanelButton.addEventListener("click", () => {
        state.showEventPanel = false;
        updateEventDetailsPanel();
      });
    }

    // Add event listeners for edit and delete buttons
    const editButton = document.getElementById("event-edit-button");
    const deleteButton = document.getElementById("event-delete-button");

    if (editButton) {
      editButton.addEventListener("click", () => {
        if (state.selectedEvent) {
          // Populate form with event details
          state.eventDetails = {
            name: state.selectedEvent.title,
            date: state.selectedEvent.date,
            time: state.selectedEvent.startTime,
            day: state.selectedEvent.day || "",
            location: state.selectedEvent.location || "",
            involvement: state.selectedEvent.involvement || "",
            personInCharge: state.selectedEvent.personInCharge || "",
            unit: state.selectedEvent.unit || "",
          };

          state.isEditingEvent = true;
          state.showEventForm = true;
          update();
        }
      });
    }

    if (deleteButton) {
      deleteButton.addEventListener("click", () => {
        if (state.selectedEvent) {
          setupDeleteConfirmation();
        }
      });
    }
    
  }

  
    
  
  
  // This JS code assumes your HTML structure matches the one you shared
// It manages: 
// - View/Edit/Delete modal logic
// - Calendar event click -> View modal -> Edit/Delete actions



// UTIL: Toggle modal visibility
function toggleModal(modalId, show = true) {
  const modal = document.getElementById(modalId);
  if (modal) modal.hidden = !show;
}

// UTIL: Populate view modal with event data
function populateViewModal(event) {
  document.getElementById("view-name").value = event.title;
  document.getElementById("view-date").value = event.date;
  document.getElementById("view-day").value = event.day;
  const formattedTime = formatTimeWithAMPM(event.time || event.startTime);
  document.getElementById("view-time").value = formattedTime;
  document.getElementById("view-location").value = event.location;
  document.getElementById("view-involvement").value = event.involvement;
  document.getElementById("view-person").value = event.personInCharge;
  document.getElementById("view-unit").value = event.unit;
}

// UTIL: Populate edit modal with event data
function populateEditModal(event) {
  document.getElementById("edit-name").value = event.title;
  document.getElementById("edit-date").value = event.date;
  const selectedDate = new Date(event.date);
  const dayName = selectedDate.toLocaleDateString("en-US", { weekday: 'long' });
  document.getElementById("edit-day").value = dayName;
  document.getElementById("edit-time").value = event.time || event.startTime;
  document.getElementById("edit-location").value = event.location;
  document.getElementById("edit-involvement").value = event.involvement;
  document.getElementById("edit-person").value = event.personInCharge;
  document.getElementById("edit-unit").value = event.unit;
}

// EVENT: When user clicks a calendar event label
function onEventClick(event) {
  state.selectedEvent = event;
  populateViewModal(event);
  toggleModal("view-event-modal", true);
}

// EVENT: Edit -> populate edit modal
function handleEditClick() {
  if (!state.selectedEvent) return;
  populateEditModal(state.selectedEvent);
  toggleModal("view-event-modal", false);
  toggleModal("edit-event-modal", true);
}

// EVENT: Cancel edit -> go back to view
function handleEditCancel() {
  toggleModal("edit-event-modal", false);
  toggleModal("view-event-modal", true);
}

// EVENT: Confirm edit -> save and show view
function handleEditConfirm() {
  const updated = {
    id: state.selectedEvent.id,
    name: document.getElementById("edit-name").value.trim(),
    date: document.getElementById("edit-date").value,
    day: document.getElementById("edit-day").value,
    time: document.getElementById("edit-time").value.trim(),
    location: document.getElementById("edit-location").value.trim(),
    involvement: document.getElementById("edit-involvement").value.trim(),
    person_in_charge: document.getElementById("edit-person").value.trim(),
    unit: document.getElementById("edit-unit").value.trim(),
  };

  // ✅ Validation
  let isValid = true;
  const fields = [
    { id: "edit-name", label: "Event Name" },
    { id: "edit-date", label: "Date" },
    { id: "edit-day", label: "Day" },
    { id: "edit-time", label: "Time" },
    { id: "edit-location", label: "Location" },
    { id: "edit-involvement", label: "Involvement" },
    { id: "edit-person", label: "Person in Charge" },
    { id: "edit-unit", label: "Unit" }
  ];

  fields.forEach(field => {
    const input = document.getElementById(field.id);
    const error = document.getElementById(`error-${field.id}`);
    if (!input.value.trim()) {
      input.classList.add("input-error");
      if (error) error.textContent = `${field.label} is required`;
      isValid = false;
    } else {
      input.classList.remove("input-error");
      if (error) error.textContent = "";
    }
  });

  if (!isValid) return;

  // 🔄 Backend update
  fetch("edit_event.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams(updated),
  })
    .then(res => res.text())
    .then(resText => {
      console.log("✅ Edit response:", resText);

      // 🔁 Update in state
      const idx = state.events.findIndex(e => e.id === updated.id);
      if (idx !== -1) {
        const updatedEvent = {
          ...state.events[idx],
          title: updated.name,
          date: updated.date,
          startTime: updated.time,
          day: updated.day,
          location: updated.location,
          involvement: updated.involvement,
          personInCharge: updated.person_in_charge,
          unit: updated.unit
        };
        state.events[idx] = updatedEvent;
        state.selectedEvent = updatedEvent;

        // ✅ UI updates
        toggleModal("edit-event-modal", false);
        openViewEventModal(updatedEvent);
        toggleModal("view-event-modal", true);
        updateEventDetailsPanel();
        renderCalendarDays();
      }

      // Optional: also refresh full DB if needed
      fetchEventsFromDB();
    })
    .catch(err => {
      console.error("❌ Edit failed:", err);
    });
}




// EVENT: Delete -> open delete modal
function handleDeleteClick() {
  toggleModal("view-event-modal", true);
  toggleModal("delete-confirmation-modal", true);
}

// EVENT: Confirm deletion
function handleDeleteConfirm() {
  const eventId = state.selectedEvent?.id;
  if (!eventId) return;

  // 📨 Send delete request to server
  fetch("delete_event.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded"
    },
    body: new URLSearchParams({ id: eventId })
  })
    .then((res) => res.text())
    .then((resText) => {
      console.log("✅ Delete response:", resText);

      // ❌ Remove event from local state
      state.events = state.events.filter((e) => e.id !== eventId);
      state.selectedEvent = null;

      // 🧼 Close modal and refresh
      toggleModal("delete-confirmation-modal", false);
      toggleModal("view-event-modal", false);
      updateEventDetailsPanel();
      renderCalendarDays();
    })
    .catch((err) => {
      console.error("❌ Delete failed:", err);
    });
}


// EVENT: Cancel deletion
function handleDeleteCancel() {
  toggleModal("delete-confirmation-modal", false);
  toggleModal("view-event-modal", true);
}

// EVENT: Close all modals
function setupCloseButtons() {
  document.getElementById("view-close-button").onclick = () => toggleModal("view-event-modal", false);
  document.getElementById("edit-close-button").onclick = () =>{
  toggleModal("edit-event-modal", false);
  toggleModal("view-event-modal", true);
  };
  document.getElementById("delete-close-button").onclick = () => {
  toggleModal("delete-confirmation-modal", false);
  toggleModal("view-event-modal", true); // return to view
};

}

function setupModalButtons() {
  document.getElementById("view-edit-button").onclick = handleEditClick;
  document.getElementById("view-delete-button").onclick = handleDeleteClick;
  document.getElementById("edit-confirm-button").onclick = handleEditConfirm;
  document.getElementById("edit-cancel-button").onclick = handleEditCancel;
  document.getElementById("delete-confirm-button").onclick = handleDeleteConfirm;
  document.getElementById("delete-cancel-button").onclick = handleDeleteCancel;
}

window.addEventListener("DOMContentLoaded", () => {
  setupCloseButtons();
  setupModalButtons();

  // ✅ Update edit-day when edit-date changes
  const editDateInput = document.getElementById("edit-date");
  const editDayInput = document.getElementById("edit-day");

  if (editDateInput && editDayInput) {
    editDateInput.addEventListener("change", function () {
      const selectedDate = new Date(this.value);
      const dayName = selectedDate.toLocaleDateString("en-US", { weekday: 'long' });
      editDayInput.value = dayName;
    });
  }
});


function openViewEventModal(event) {
  document.getElementById('view-name').value = event.title;
  document.getElementById('view-date').value = event.date;
  document.getElementById('view-day').value = event.day;
   const formattedTime = formatTimeWithAMPM(event.time || event.startTime);
  document.getElementById("view-time").value = formattedTime;
  document.getElementById('view-location').value = event.location;
  document.getElementById('view-involvement').value = event.involvement;
  document.getElementById('view-person').value = event.personInCharge;
  document.getElementById('view-unit').value = event.unit;

  document.getElementById('view-event-modal').removeAttribute('hidden');

  // Store current event for editing/deleting
  state.selectedEvent = event;
}

document.addEventListener("click", function (e) {
  const panel = document.getElementById("event-details-panel");
  const modal = document.getElementById("view-event-modal");
  const editModal = document.getElementById("edit-event-modal");
  
  const isInsidePanel = panel?.contains(e.target);
  const isInsideModal = modal?.contains(e.target);

  // Only clear selection if click is outside both panel and modal
  if (!isInsidePanel && !isInsideModal && !editModal?.contains(e.target)) {
    state.selectedEvent = null;
    updateEventDetailsPanel(); // Remove selected highlight
  }
});







  // Initialize the application
  function init() {
    // Manually render calendar days on first load
    renderCalendarDays();

    // Update with initial state
    update();

    // Initialize calendar
    initializeCalendar();

    fetchEventsFromDB();

    
  }

  // Start the application
  init();

  
});