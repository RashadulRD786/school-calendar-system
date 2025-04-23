document.addEventListener("DOMContentLoaded", () => {
  const state = {
    notifications: false,
    currentMonth: new Date().getMonth(),
    currentYear: new Date().getFullYear(),
    selectedDate: null,
    selectedEvent: null,
    showEventPanel: true,
    showEventForm: false,
    isEditingEvent: false,
    eventDetails: {
      name: "",
      date: "",
      time: "",
      day: "",
      location: "",
      involvement: "",
      personInCharge: "",
      unit: "",
    },
    events: [
      {
        id: 1,
        title: "Team Meeting",
        date: "2024-05-15",
        startTime: "10:00",
        endTime: "11:30",
        day: "Wednesday",
        location: "Conference Room A",
        involvement: "All Team Members",
        personInCharge: "John Smith",
        unit: "Marketing",
        description:
          "Weekly team sync to discuss project progress and roadblocks.",
      },
      {
        id: 2,
        title: "Client Presentation",
        date: "2024-05-22",
        startTime: "14:00",
        endTime: "15:30",
        day: "Wednesday",
        location: "Main Boardroom",
        involvement: "Client, Executive Team",
        personInCharge: "Sarah Johnson",
        unit: "Sales",
        description:
          "Present the new dashboard design to the client for feedback.",
      },
      {
        id: 3,
        title: "Product Launch",
        date: "2024-05-30",
        startTime: "09:00",
        endTime: "12:00",
        day: "Thursday",
        location: "Exhibition Hall",
        involvement: "All Departments",
        personInCharge: "Michael Brown",
        unit: "Product",
        description:
          "Official launch of the new product line with press conference.",
      },
    ],
  };

  let nodesToDestroy = [];
  let pendingUpdate = false;

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

    // If a date is selected, pre-fill the date field
    if (state.selectedDate) {
      state.eventDetails.date = state.selectedDate.formatted;

      // Get day name
      const dayNames = [
        "Sunday",
        "Monday",
        "Tuesday",
        "Wednesday",
        "Thursday",
        "Friday",
        "Saturday",
      ];
      const dayIndex = new Date(
        state.selectedDate.year,
        state.selectedDate.month,
        state.selectedDate.day,
      ).getDay();
      state.eventDetails.day = dayNames[dayIndex];
    }

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

  // Event handler for confirm button click
  function onButton4Click(event) {
    if (state.isEditingEvent && state.selectedEvent) {
      // Update existing event
      const eventIndex = state.events.findIndex(
        (e) => e.id === state.selectedEvent.id,
      );
      if (eventIndex !== -1) {
        state.events[eventIndex] = {
          ...state.events[eventIndex],
          title: state.eventDetails.name,
          date: state.eventDetails.date,
          startTime: state.eventDetails.time,
          day: state.eventDetails.day,
          location: state.eventDetails.location,
          involvement: state.eventDetails.involvement,
          personInCharge: state.eventDetails.personInCharge,
          unit: state.eventDetails.unit,
        };
      }
    } else {
      // Add new event
      const newEvent = {
        id: Date.now(), // Use timestamp as unique ID
        title: state.eventDetails.name,
        date: state.eventDetails.date,
        startTime: state.eventDetails.time,
        endTime: "", // Could be added to form
        day: state.eventDetails.day,
        location: state.eventDetails.location,
        involvement: state.eventDetails.involvement,
        personInCharge: state.eventDetails.personInCharge,
        unit: state.eventDetails.unit,
        description: state.eventDetails.name, // Using name as description for simplicity
      };

      state.events.push(newEvent);
    }

    // Close form and update UI
    state.showEventForm = false;
    renderCalendarDays();
    updateEventDetailsPanel();
    update();
  }

  // Event handler for cancel button click
  function onButton5Click(event) {
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
    return `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
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
      dayCell.textContent = day;

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

  // Get events for a specific date
  function getEventsForDate(dateString) {
    return state.events.filter((event) => event.date === dateString);
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
            <div class="event-time">${event.startTime}${event.endTime ? " - " + event.endTime : ""}</div>
            <div class="event-title">${event.title}</div>
            <div class="event-description">${event.description || ""}</div>
            <div class="event-location"><strong>Location:</strong> ${event.location || "N/A"}</div>
            <div class="event-person"><strong>Person in Charge:</strong> ${event.personInCharge || "N/A"}</div>
          `;

          // Add click event to select this event
          eventElement.addEventListener("click", () => {
            handleEventSelection(event.id);
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
          if (
            confirm(
              `Are you sure you want to delete "${state.selectedEvent.title}"?`,
            )
          ) {
            // Remove event from state
            state.events = state.events.filter(
              (e) => e.id !== state.selectedEvent.id,
            );
            // Reset selected event
            state.selectedEvent = null;
            // Update UI
            renderCalendarDays();
            updateEventDetailsPanel();
          }
        }
      });
    }
  }

  // Initialize the application
  function init() {
    // Manually render calendar days on first load
    renderCalendarDays();

    // Update with initial state
    update();

    // Initialize calendar
    initializeCalendar();
  }

  // Start the application
  init();
});
