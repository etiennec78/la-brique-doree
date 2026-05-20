// Show a notification in the upper-right corner
function toast(message = null, duration = 5000) {
  let toast_div;
  if (message == null) {
    // PHP already has set a message, so we just need to display it
    toast_div = document.getElementById('toast');

  } else {
    // Create the div containing the message
    const container = document.getElementById('toast-container');

    toast_div = document.createElement('div');
    toast_div.id = 'toast';
    toast_div.innerText = message;

    container.appendChild(toast_div);
  }

  setTimeout(() => {
    toast_div.classList.add('show');
  }, 10);

  // Remove the class then the div at the end of the transition
  setTimeout(() => {
    toast_div.classList.remove('show');
    toast_div.addEventListener('transitionend', () => {
      toast_div.remove();
    });
  }, duration);
}


// Search the page for a php div signaling the need for a toast
document.addEventListener("DOMContentLoaded", () => {
  const errorContainer = document.getElementById("toast");

  if (errorContainer) {
    toast();
  }
});
