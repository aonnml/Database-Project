document.addEventListener("DOMContentLoaded", () => {
  const reviewList = document.getElementById("review-list");
  const tabs = document.querySelectorAll(".tab");

  function loadReviews(tabType) {
    fetch("../backend/fetch_reviews.php")
      .then(res => res.json())
      .then(data => {
        reviewList.innerHTML = "";
        const filtered = data.filter(r =>
          tabType === "reviewed" ? r.is_reviewed == 1 : r.is_reviewed == 0
        );

        filtered.forEach(r => {
          reviewList.innerHTML += `
            <div class="review-card">
              <img src="${r.product_image}" alt="${r.product_name}">
              <h3>${r.product_name}</h3>
              <input type="number" min="1" max="5" value="${r.rate || ''}" id="rate-${r.id}">
              <textarea id="desc-${r.id}" placeholder="Write your review...">${r.description || ''}</textarea>
              <button onclick="saveReview(${r.id})">Save</button>
            </div>
          `;
        });
      });
  }

  window.saveReview = function (id) {
    const rate = document.getElementById(`rate-${id}`).value;
    const desc = document.getElementById(`desc-${id}`).value;

    fetch("../backend/update_review.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `id=${id}&rate=${rate}&description=${encodeURIComponent(desc)}`
    })
    .then(res => res.text())
    .then(resp => {
      if (resp === "success") {
        alert("Review saved!");
        loadReviews("unreviewed");
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

  loadReviews("unreviewed");
});
