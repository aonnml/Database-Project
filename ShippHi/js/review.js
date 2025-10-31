document.addEventListener("DOMContentLoaded", () => {
  const reviewList = document.getElementById("review-list");
  const tabs = document.querySelectorAll(".tab");
  const MAX_RATING = 5;
  let activeTab = "unreviewed";

  function createStarButtons(currentRate = 0) {
    const rating = Number(currentRate) || 0;
    return Array.from({ length: MAX_RATING }, (_, idx) => {
      const value = idx + 1;
      const isFilled = rating >= value;
      const starChar = isFilled ? "★" : "☆";
      return `<button type="button" class="star-btn${isFilled ? " filled" : ""}" data-star="${value}" role="radio" aria-label="${value} star${value > 1 ? "s" : ""}" aria-checked="${isFilled}" tabindex="0">${starChar}</button>`;
    }).join("");
  }

  function updateStarSelection(container, ratingValue) {
    container.querySelectorAll(".star-btn").forEach(btn => {
      const starValue = Number(btn.dataset.star);
      const isActive = starValue <= ratingValue;
      btn.classList.toggle("filled", isActive);
      btn.setAttribute("aria-checked", isActive);
      btn.textContent = isActive ? "★" : "☆";
    });
  }

  function loadReviews(tabType) {
    activeTab = tabType;
    fetch("../backend/fetch_reviews.php")
      .then(res => res.json())
      .then(data => {
        reviewList.innerHTML = "";
        const filtered = data.filter(r =>
          tabType === "reviewed" ? r.is_reviewed == 1 : r.is_reviewed == 0
        );

        if (filtered.length === 0) {
          reviewList.innerHTML = `<p class="empty-state">ไม่มีรายการรีวิวในหมวดนี้</p>`;
          return;
        }

        filtered.forEach(r => {
          const safeImage = r.product_image || "image/user.jpg";
          const currentRate = Number(r.rate) || 0;
          reviewList.innerHTML += `
            <div class="review-card" data-review-id="${r.id}">
              <img src="${safeImage}" alt="${r.product_name}">
              <h3>${r.product_name}</h3>
              <div class="rating-stars" data-review-id="${r.id}" role="radiogroup" aria-label="Rate ${r.product_name}">
                ${createStarButtons(currentRate)}
              </div>
              <input type="hidden" id="rate-${r.id}" value="${currentRate || ""}">
              <textarea id="desc-${r.id}" placeholder="Write your review...">${r.description || ""}</textarea>
              <button onclick="saveReview(${r.id})">Save</button>
            </div>
          `;
        });
      });
  }

  reviewList.addEventListener("click", event => {
    const starBtn = event.target.closest(".star-btn");
    if (!starBtn) return;

    const container = starBtn.closest(".rating-stars");
    if (!container) return;

    const ratingValue = Number(starBtn.dataset.star);
    const reviewId = container.dataset.reviewId;
    const hiddenInput = document.getElementById(`rate-${reviewId}`);
    if (!hiddenInput) return;

    hiddenInput.value = ratingValue;
    updateStarSelection(container, ratingValue);
  });

  reviewList.addEventListener("keydown", event => {
    const starBtn = event.target.closest(".star-btn");
    if (!starBtn) return;
    if (event.key === "Enter" || event.key === " ") {
      event.preventDefault();
      starBtn.click();
    }
  });

  window.saveReview = function (id) {
    const rate = document.getElementById(`rate-${id}`).value;
    const desc = document.getElementById(`desc-${id}`).value;

    if (!rate) {
      alert("กรุณาให้คะแนนก่อนบันทึกรีวิว");
      return;
    }

    fetch("../backend/update_review.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `id=${id}&rate=${rate}&description=${encodeURIComponent(desc)}`
    })
      .then(res => res.text())
      .then(resp => {
        if (resp === "success") {
          alert("Review saved!");
          loadReviews(activeTab);
        }
      });
  };

  tabs.forEach(tab => {
    tab.addEventListener("click", () => {
      document.querySelector(".tab.active").classList.remove("active");
      tab.classList.add("active");
      loadReviews(tab.dataset.tab);
    });
  });

  loadReviews(activeTab);
});
