var ReviewService = {
  init: function () {
    this.loadReviews();
  },

  loadReviews: function () {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "reviews",
      type: "GET",
      contentType: "application/json",
      success: function (result) {
        ReviewService.displayReviews(result.data || []);
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(
          XMLHttpRequest?.responseText
            ? XMLHttpRequest.responseText
            : "Error loading reviews"
        );
      },
    });
  },

  displayReviews: function (reviews) {
    let html = '<div class="container mt-4"><div class="row">';

    if (!reviews || reviews.length === 0) {
      html += `
        <div class="col-12 text-center">
          <p>No reviews yet. Be the first to review!</p>
        </div>
      `;
    } else {
      reviews.forEach(function(review) {
        // Generate stars based on rating
        let stars = '';
        for (let i = 1; i <= 5; i++) {
          if (i <= review.rating) {
            stars += '<i class="fas fa-star"></i>';
          } else if (i - 0.5 === review.rating) {
            stars += '<i class="fas fa-star-half-alt"></i>';
          } else {
            stars += '<i class="far fa-star"></i>';
          }
        }

        const itemTypeIcon = review.review_type === 'subscription' ? 
          '<i class="fas fa-sync-alt"></i>' : 
          '<i class="fas fa-box"></i>';

        html += `
          <div class="col-md-3 col-sm-6 mb-4">
            <div class="review-box">
              <h5>${review.user_name || 'Anonymous'}</h5>
              <p class="product-name">
                <strong>${itemTypeIcon} ${review.review_type === 'subscription' ? 'Subscription' : 'Product'}:</strong> 
                ${review.item_name}
              </p>
              <p class="review-text">${review.comment || 'No comment provided.'}</p>
              <div class="stars">
                ${stars}
              </div>
              <small class="text-muted">${new Date(review.created_at).toLocaleDateString()}</small>
            </div>
          </div>
        `;
      });
    }

    html += '</div></div>';
    $("#reviews-container").html(html);
  },

  // Helper function to generate stars HTML
  generateStars: function(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
      if (i <= rating) {
        stars += '<i class="fas fa-star"></i>';
      } else if (i - 0.5 === rating) {
        stars += '<i class="fas fa-star-half-alt"></i>';
      } else {
        stars += '<i class="far fa-star"></i>';
      }
    }
    return stars;
  }
};
