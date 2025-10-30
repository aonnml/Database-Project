function initHeaderMenu() {
  const btn = document.getElementById("menuBtn");
  const menu = document.getElementById("profileMenu");

  if (!btn || !menu) {
    console.warn("⚠ menuBtn หรือ profileMenu ไม่พบใน DOM. ตรวจสอบว่า header โหลดก่อน header.js");
    return;
  }

  if (!btn.dataset.bound) {
    btn.dataset.bound = "true";

    btn.addEventListener("click", (e) => {
      e.stopPropagation();
      menu.toggleAttribute("hidden");
    });

    document.addEventListener("click", (e) => {
      if (!menu.contains(e.target) && !btn.contains(e.target)) {
        menu.setAttribute("hidden", "");
      }
    });
  }
}

window.initHeaderMenu = initHeaderMenu;

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", initHeaderMenu);
} else {
  initHeaderMenu();
}
