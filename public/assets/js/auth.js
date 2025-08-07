document.addEventListener("DOMContentLoaded", function () {
  // Background image slider
  const backgrounds = ["assets/img/bg1.jpg", "assets/img/bg3.jpg"];
  let current = 0;
  const bgDiv = document.querySelector(".background-slider");

  function changeBackground() {
    if (bgDiv) {
      bgDiv.style.backgroundImage = `url('${backgrounds[current]}')`;
      current = (current + 1) % backgrounds.length;
    }
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

  if (togglePassword && passwordInput) {
    togglePassword.addEventListener("click", function () {
      const isPassword = passwordInput.type === "password";
      passwordInput.type = isPassword ? "text" : "password";
      togglePassword.innerHTML = isPassword ? eyeOffIcon : eyeIcon;
      togglePassword.title = isPassword ? "Hide password" : "Show password";
    });
  }

  // Auto-hide alerts
  setTimeout(() => {
    const success = document.getElementById("successAlert");
    const error = document.getElementById("errorAlert");
    [success, error].forEach((el) => {
      if (el) {
        el.classList.add("fade-out");
        setTimeout(() => el.remove(), 500);
      }
    });
  }, 3500);

  // Ambil elemen input jika ada
  const nama = document.getElementById("nama");
  const email = document.getElementById("email");
  const password = document.getElementById("passwordInput");
  const no_hp = document.getElementById("no_hp");

  const namaError = document.getElementById("namaError");
  const emailError = document.getElementById("emailError");
  const passwordError = document.getElementById("passwordError");
  const nohpError = document.getElementById("nohpError");

  function validateNama() {
    if (!nama) return;
    if (nama.value.trim() === "") {
      namaError.innerText = "Nama wajib diisi.";
      nama.classList.add("invalid");
    } else {
      namaError.innerText = "";
      nama.classList.remove("invalid");
    }
  }

  function validateEmail() {
    if (!email) return;
    const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/i;
    if (!emailPattern.test(email.value.trim())) {
      emailError.innerText = "Email tidak valid.";
      email.classList.add("invalid");
    } else {
      emailError.innerText = "";
      email.classList.remove("invalid");
    }
  }

  function validatePassword() {
    if (!password) return;
    if (password.value.length < 8) {
      passwordError.innerText = "Password minimal 8 karakter.";
      password.classList.add("invalid");
    } else {
      passwordError.innerText = "";
      password.classList.remove("invalid");
    }
  }

  function validateNoHp() {
    if (!no_hp) return;
    const noHpPattern = /^\d{10,}$/;
    if (!noHpPattern.test(no_hp.value.trim())) {
      nohpError.innerText = "No HP harus berupa angka minimal 10 digit.";
      no_hp.classList.add("invalid");
    } else {
      nohpError.innerText = "";
      no_hp.classList.remove("invalid");
    }
  }

  // Event listener hanya jika elemen ada
  if (nama) nama.addEventListener("input", validateNama);
  if (email) email.addEventListener("input", validateEmail);
  if (password) password.addEventListener("input", validatePassword);
  if (no_hp) no_hp.addEventListener("input", validateNoHp);

  // Register form
  const registerForm = document.getElementById("registerForm");
  if (registerForm) {
    registerForm.addEventListener("submit", function (event) {
      validateNama();
      validateEmail();
      validatePassword();
      validateNoHp();

      if (
        (nama && nama.classList.contains("invalid")) ||
        (email && email.classList.contains("invalid")) ||
        (password && password.classList.contains("invalid")) ||
        (no_hp && no_hp.classList.contains("invalid"))
      ) {
        event.preventDefault();
      }
    });
  }

  // Login form
  const loginForm = document.getElementById("loginForm");
  if (loginForm) {
    loginForm.addEventListener("submit", function (event) {
      validateEmail();
      validatePassword();

      if (
        (email && email.classList.contains("invalid")) ||
        (password && password.classList.contains("invalid"))
      ) {
        event.preventDefault();
      }
    });
  }
});
