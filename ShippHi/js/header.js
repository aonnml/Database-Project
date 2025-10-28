document.addEventListener("DOMContentLoaded", () => {

  const btn = document.getElementById("menuBtn");
  const menu = document.getElementById("profileMenu");

  if (!btn || !menu) {
    console.warn("⚠ menuBtn หรือ profileMenu ไม่พบใน DOM. ตรวจสอบว่า header โหลดก่อน header.js");
    return;
  }

  // เปิด/ปิดเมนูเมื่อกดปุ่ม
  btn.addEventListener("click", (e) => {
    e.stopPropagation();
    menu.toggleAttribute("hidden");
  });

  // ปิดเมนูเมื่อคลิกนอกเมนู
  document.addEventListener("click", (e) => {
    if (!menu.contains(e.target) && !btn.contains(e.target)) {
      menu.setAttribute("hidden", "");
    }
  });

});
