// Background image slider
const backgrounds = ["assets/img/bg1.jpg", "assets/img/bg3.jpg"];

let current = 0;
const bgDiv = document.querySelector(".background-slider");

function changeBackground() {
  bgDiv.style.backgroundImage = `url('${backgrounds[current]}')`;
  current = (current + 1) % backgrounds.length;
}

changeBackground();
setInterval(changeBackground, 4000);

// Password toggle
const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("passwordInput");

const eyeIcon = `
            <svg viewBox="0 0 24 24">
                <path d="M12 5c-7 0-10 7-10 7s3 7 10 7 10-7 10-7-3-7-10-7zm0 12c-2.76 0-5-2.24-5-5s2.24-5 
                5-5 5 2.24 5 5-2.24 5-5 5zm0-8a3 3 0 100 6 3 3 0 000-6z"/>
            </svg>`;

const eyeOffIcon = `
            <svg viewBox="0 0 24 24">
                <path d="M12 6a9.77 9.77 0 018.94 6A9.77 9.77 0 0112 18a9.77 9.77 0 01-8.94-6A9.77 9.77 0 0112 6m0-2C6 4 
                2 12 2 12s4 8 10 8 10-8 10-8-4-8-10-8zm0 5a3 3 0 100 6 3 3 0 000-6z"/>
            </svg>`;

togglePassword.addEventListener("click", function () {
  const isPassword = passwordInput.type === "password";
  passwordInput.type = isPassword ? "text" : "password";
  togglePassword.innerHTML = isPassword ? eyeOffIcon : eyeIcon;
  togglePassword.title = isPassword ? "Hide password" : "Show password";
});

// Auto-hide alerts after 3 seconds
setTimeout(() => {
  const success = document.getElementById("successAlert");
  const error = document.getElementById("errorAlert");
  [success, error].forEach((el) => {
    if (el) {
      el.classList.add("fade-out");
      setTimeout(() => el.remove(), 500); // Remove from DOM after fade
    }
  });
}, 3500);
