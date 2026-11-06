document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.querySelector(".menu-toggle"),
    nav = document.querySelector(".nav");
  if (toggle && nav)
    toggle.addEventListener("click", () => nav.classList.toggle("open"));
  document.querySelectorAll("[data-confirm]").forEach((el) =>
    el.addEventListener("click", (e) => {
      if (
        !confirm(
          el.dataset.confirm || "Bạn chắc chắn muốn thực hiện thao tác này?",
        )
      )
        e.preventDefault();
    }),
  );
  const password = document.querySelector("#password"),
    confirmPassword = document.querySelector("#confirm_password");
  const form = document.querySelector("[data-password-match]");
  if (form && password && confirmPassword)
    form.addEventListener("submit", (e) => {
      if (password.value !== confirmPassword.value) {
        e.preventDefault();
        alert("Mật khẩu xác nhận không khớp.");
      }
    });
  document.querySelectorAll("[data-quantity]").forEach((input) =>
    input.addEventListener("change", () => {
      const row = input.closest("[data-cart-row]");
      if (!row) return;
      const price = Number(row.dataset.price || 0),
        qty = Math.max(1, Number(input.value || 1));
      const total = row.querySelector("[data-line-total]");
      if (total)
        total.textContent = (price * qty).toLocaleString("vi-VN") + " ₫";
    }),
  );
  const contact = document.querySelector("#contact-message");
  if (contact)
    contact.addEventListener("input", () => {
      contact.setCustomValidity(
        contact.value.trim().length < 10
          ? "Nội dung phải có ít nhất 10 ký tự."
          : "",
      );
    });
});
