document.addEventListener("DOMContentLoaded", function () {
  const errorDiv = document.querySelector(".error-message");
  const successDiv = document.querySelector(".success-message");

  // Auto-hide error/success messages
  if (errorDiv) {
    setTimeout(() => {
      errorDiv.style.transition = "opacity 0.5s ease";
      errorDiv.style.opacity = 0;

      // Setelah animasi selesai, sembunyikan elemen
      setTimeout(() => {
        errorDiv.style.display = "none";
      }, 500);
    }, 3500);
  }

  if (successDiv) {
    setTimeout(() => {
      successDiv.style.transition = "opacity 0.5s ease";
      successDiv.style.opacity = 0;

      // Setelah animasi selesai, sembunyikan elemen
      setTimeout(() => {
        successDiv.style.display = "none";
      }, 500);
    }, 3500);
  }
});
