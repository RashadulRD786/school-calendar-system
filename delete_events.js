
 const state = {
 notifications: false,

};

function onButton1Click(event) {
 
  state.notifications = !state.notifications;
  
  updateNotificationVisibility(); 
}


function onDiv1Click(event) {
  event.stopPropagation(); 
  state.notifications = false; 
  updateNotificationVisibility(); 
}


function setupNotificationListeners() {
  
  document.querySelectorAll("[data-el='button-1']").forEach((el) => {
    
    el.removeEventListener("click", onButton1Click);
    el.addEventListener("click", onButton1Click);
  });

 
  document.querySelectorAll("[data-el='div-1']").forEach((el) => {
    el.removeEventListener("click", onDiv1Click);
    el.addEventListener("click", onDiv1Click);
  });
}


function updateNotificationVisibility() {
  document.querySelectorAll("[data-el='div-1']").forEach((el) => {
    el.hidden = !state.notifications; 
  });
}


document.addEventListener("click", function (event) {
  const notificationButton = document.querySelector("[data-el='button-1']");
  const notificationDropdown = document.querySelector("[data-el='div-1']");

  
  if (state.notifications && notificationButton && notificationDropdown &&
      !notificationButton.contains(event.target) && !notificationDropdown.contains(event.target)) {
    state.notifications = false;
    updateNotificationVisibility();
  }
});


document.addEventListener("DOMContentLoaded", () => {


  setupNotificationListeners();
  updateNotificationVisibility(); 
});

// --- Notification Dropdown Toggle Logic ---
// This part handles showing/hiding the notification dropdown when the bell icon is clicked.
document.querySelector('.notification-button').addEventListener('click', function(event) {
    const dropdown = document.querySelector('.notification-dropdown');
    dropdown.hidden = !dropdown.hidden;
    event.stopPropagation(); // Prevent immediate closing from window click listener
});

// Close dropdown if clicked anywhere outside the notification container
document.addEventListener('click', function(event) {
    const notificationContainer = document.querySelector('.notification-container');
    const dropdown = document.querySelector('.notification-dropdown');
    if (!notificationContainer.contains(event.target) && !dropdown.hidden) {
        dropdown.hidden = true;
    }
});


// --- Event Detail Modal Logic (Updated for #view-event-modal) ---
let currentEventId = null;// To keep track of the event ID being viewed

// Function to fetch and display event details in the new modal
function viewEventDetails(eventId) {
    fetch('get_event_by_id.php?id=' + eventId) // Fetches event data from PHP
        .then(res => {
            if (!res.ok) {
                // If the HTTP response status is not 2xx (e.g., 404, 500)
                // Throw an error with the status for the catch block to handle
                throw new Error(`HTTP error! Status: ${res.status}`);
            }
            return res.json(); // Attempt to parse the response as JSON
        })
        .then(eventDataArray => { // CHANGED: Renamed parameter from 'event' to 'eventDataArray'
            console.log('Event data received (raw array):', eventDataArray);

            // Check if the event data is valid and contains expected data (assuming it's an array)
            if (!eventDataArray || !Array.isArray(eventDataArray) || eventDataArray.length === 0) {
                console.warn('Received empty or invalid event data for ID:', eventId);
                alert('⚠️ Event details not found for this notification. It might have been deleted.');
                document.getElementById('view-event-modal').hidden = true; // Hide modal if no data
                return; // Stop execution here
            }

            // EXTRACT THE SINGLE EVENT OBJECT:
            const singleEvent = eventDataArray[0]; // CHANGED: Renamed variable from 'event' to 'singleEvent'
            console.log('Processed event object:', singleEvent);

            currentEventId = eventId;

            // Populate the fields of the NEW modal (#view-event-modal)
            // Use 'singleEvent' to access properties
            document.getElementById('view-name').value = singleEvent.name || '';
            document.getElementById('view-day').value = singleEvent.day || '';
            document.getElementById('view-date').value = singleEvent.date || '';
            const formattedTime = formatTimeWithAMPM(singleEvent.time || singleEvent.startTime);
            document.getElementById("view-time").value = formattedTime;
            document.getElementById('view-location').value = singleEvent.location || '';
            // REMOVED: document.getElementById('view-involvement').value = (event.involvement || '').split(',').filter(Boolean).join(', ');

            document.getElementById('view-person').value = singleEvent.person_in_charge || ''; // CORRECTED: from event.personInCharge
            document.getElementById('view-unit').value = singleEvent.unit || '';
            document.getElementById('view-status').value = singleEvent.status || '';

            // This part for involvement is correct for a DIV and should remain:
            const involvementContainer = document.getElementById('view-involvement');
            // Check if element exists before manipulating
            if (involvementContainer) {
                const involvementArr = (singleEvent.involvement || '').split(',').filter(Boolean);
                involvementContainer.innerHTML = involvementArr.length
                    ? involvementArr.map(name => `<span class="tag">${name.trim()}</span>`).join(', ')
                    : '<span class="tag tag-na">N/A</span>';
            } else {
                console.warn('Element with ID "view-involvement" not found in the DOM.');
            }


            document.getElementById('view-event-modal').hidden = false;
        })
        .catch(err => {
            // This catch block will now capture errors from network issues AND JSON parsing errors
            console.error('Error fetching or processing event details for ID', eventId, ':', err);

            let errorMessage = '❌ Failed to load event details. ';

            if (err instanceof TypeError && err.message.includes('JSON')) {
                errorMessage += 'Invalid data received from server. Check get_events.php output.';
            } else if (err.message.startsWith('HTTP error!')) {
                errorMessage += `Server responded with an error: ${err.message}.`;
            } else {
                errorMessage += 'Please check the browser console for more information.';
            }
            alert(errorMessage); // Show a user-friendly alert
            document.getElementById('view-event-modal').hidden = true; // Ensure modal is hidden on error
        });
}
function formatTimeWithAMPM(timeStr) {
    if (!timeStr) return "N/A";
    const [hours, minutes] = timeStr.split(":").map(Number);
    const ampm = hours >= 12 ? "PM" : "AM";
    const adjustedHour = hours % 12 || 12;
    return `${adjustedHour}:${minutes.toString().padStart(2, "0")} ${ampm}`;
  }
// Close the NEW modal when 'x' is clicked
document.getElementById('view-close-button').onclick = function() {
    document.getElementById('view-event-modal').hidden = true;
};

// Close the NEW modal when clicking anywhere outside of it
window.onclick = function(event) {
    const modal = document.getElementById('view-event-modal');
    if (event.target === modal) {
        modal.hidden = true;
    }
};



// Handle delete button click in the NEW modal


// --- Notification Badge and Dropdown Population Logic ---

// Function to update the unread notification count badge
function updateNotificationBadge(count) {
    const badge = document.getElementById('notification-badge');
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }
}

// Main function to fetch and display notifications
function fetchNotifications() {
    fetch('get_notifications.php') // Fetches notification data from PHP
        .then(res => res.json())
        .then(data => {
            const dropdown = document.querySelector('[data-el="div-1"]');
            const badgeCount = data.unread_count || 0;
            updateNotificationBadge(badgeCount); // Update the badge

            let scrollableDiv = dropdown.querySelector('.notification-scroll');
            let prevScrollTop = scrollableDiv ? scrollableDiv.scrollTop : 0;

            if (data.notifications && data.notifications.length > 0) {
                // Construct the HTML for notifications within the dropdown
                dropdown.innerHTML = `
                    <h3 class="notification-title">Notifications</h3>
                    <div style="margin-bottom:10px;text-align:right;">
                        <button id="mark-read-btn" style="background:#008080;color:white;border:none;padding:6px 14px;border-radius:6px;cursor:pointer;font-size:13px;font-weight:500;">Mark all as read</button>
                    </div>
                    <div class="notification-scroll" style="max-height:320px;overflow-y:auto;">
                        ${data.notifications.map(n => `
                            <div class="notification-card" style="background:${n.read ? '#fff' : '#ECECEC'};font-weight:${n.read ? 'normal' : 'bold'};margin-bottom:10px;padding:13px 14px 10px 14px;border-radius:9px;box-shadow:0 2px 8px rgba(32,178,170,0.10);cursor:pointer;transition:box-shadow 0.2s;display:flex;flex-direction:column;gap:4px;">
                                <div style="display:flex;justify-content:space-between;align-items:center;">
                                    <span style="text-align:left;color:#008080;font-size:15px;">New event added: ${n.eventName}</span>
                                </div>
                                <div style="display:flex;justify-content:flex-end;align-items:center;margin-top:2px;">
                                    <span style="font-size:12px;color:#888;text-align:right;">${n.eventDate} ${n.eventTime}</span>
                                </div>
                                <div style="display:flex;justify-content:flex-end;align-items:center;margin-top:2px;">
                                    <button style="background:none;border:none;color:#20B2AA;font-size:13px;cursor:pointer;padding:0;" onclick="viewEventDetails(${n.eventId})">View Details</button>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                `;
                // Restore scroll position after content update
                scrollableDiv = dropdown.querySelector('.notification-scroll');
                if (scrollableDiv) scrollableDiv.scrollTop = prevScrollTop;

                // Attach event listener for "Mark all as read" button
                const markBtn = document.getElementById('mark-read-btn');
                if (markBtn) {
                    markBtn.onclick = function() {
                        fetch('mark_notifications_read.php', { method: 'POST' }) // Request to mark all as read
                            .then(res => res.json())
                            .then(() => fetchNotifications()); // Refresh notifications after marking
                    };
                }
            } else {
                // Display "No new notifications" if there are none
                dropdown.innerHTML = '<h3 class="notification-title">Notifications</h3><p class="notification-message">No new notifications</p>';
            }
        });
}

// --- Background Refresh and Initial Load ---
// Fetch notifications every 1 second (1000ms)
setInterval(fetchNotifications, 1000);
// Fetch notifications when the page initially loads
document.addEventListener('DOMContentLoaded', fetchNotifications);