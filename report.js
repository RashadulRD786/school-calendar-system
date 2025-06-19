
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

