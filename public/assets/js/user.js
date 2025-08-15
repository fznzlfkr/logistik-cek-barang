// feather-icons
feather.replace();

setTimeout(() => {
  const success = document.getElementById("successAlert");
  const error = document.getElementById("errorAlert");
  [success, error].forEach((el) => {
    if (el) {
      el.classList.add("fade-out");
      setTimeout(() => el.remove(), 500);
    }
  });
}, 3000);

const passwordBaru = document.querySelector('input[name="password_baru"]');
const konfirmasi = document.querySelector('input[name="konfirmasi_password"]');
const passwordError = document.getElementById("passwordError");
const confirmPasswordError = document.getElementById("confirmPasswordError");

if (passwordBaru && passwordError) {
  passwordBaru.addEventListener("input", function () {
    if (this.value.length < 8) {
      passwordError.innerText = "Password baru minimal 8 karakter.";
    } else {
      passwordError.innerText = "";
    }
  });
}
if (konfirmasi && confirmPasswordError && passwordBaru) {
  konfirmasi.addEventListener("input", function () {
    if (this.value !== passwordBaru.value) {
      confirmPasswordError.innerText = "Konfirmasi password tidak sama.";
    } else {
      confirmPasswordError.innerText = "";
    }
  });
}

function setTogglePassword(inputId, toggleId) {
  const input = document.getElementById(inputId);
  const toggle = document.getElementById(toggleId);
  if (!input || !toggle) return;

  const iconHide = toggle.querySelector(".icon-hide");
  const iconShow = toggle.querySelector(".icon-show");

  toggle.addEventListener("click", function () {
    const isHidden = input.type === "password";
    input.type = isHidden ? "text" : "password";
    if (iconHide && iconShow) {
      iconHide.style.display = isHidden ? "none" : "inline";
      iconShow.style.display = isHidden ? "inline" : "none";
    }
    toggle.title = isHidden ? "Sembunyikan password" : "Tampilkan password";
  });
}

// Terapkan untuk semua input password
setTogglePassword("passwordLama", "togglePasswordLama");
setTogglePassword("passwordBaru", "togglePasswordBaru");
setTogglePassword("konfirmasiPassword", "toggleKonfirmasiPassword");

// SweetAlert untuk konfirmasi hapus
document.querySelectorAll(".btn-hapus").forEach((button) => {
  button.addEventListener("click", function () {
    const id = this.getAttribute("data-id");
    Swal.fire({
      title: "Yakin ingin menghapus?",
      text: "Data yang dihapus tidak dapat dikembalikan!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      cancelButtonColor: "#3085d6",
      confirmButtonText: "Ya, hapus!",
      cancelButtonText: "Batal",
    }).then((result) => {
      if (result.isConfirmed) {
        const form = document.createElement("form");
        form.method = "POST";
        form.action = `/user/hapus-riwayat/${id}`;
        form.innerHTML = `<?= csrf_field() ?>`;
        document.body.appendChild(form);
        form.submit();
      }
    });
  });
});
