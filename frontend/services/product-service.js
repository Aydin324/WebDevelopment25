var ProductService = {
  init: function () {
    console.log("ProductService initialized");

    // Event listener for product category clicks
    $(document).on("click", ".portfolio-item", function (e) {
      e.preventDefault();
      const type = $(this).data("type");
      console.log("Clicked product type:", type);

      if (!type) {
        console.error("No type found on clicked element");
        return;
      }

      // Store the type in sessionStorage so we can load it when view_product is ready
      sessionStorage.setItem("selected_product_type", type);
      window.location.hash = "#view_product";
    });

    // Event listener for variant selection
    $(document).on("change", "#variant", function () {
      const selectedOption = $(this).find("option:selected");
      const productId = selectedOption.data("product-id");
      console.log("Selected variant product ID:", productId);
      if (productId) {
        ProductService.displayProductDetails(
          selectedOption.data("product"),
          ProductService.currentProducts
        );
      }
    });

    // If we're on the view_product page and have a stored type, load it
    if (window.location.hash === "#view_product") {
      const selectedType = sessionStorage.getItem("selected_product_type");
      if (selectedType) {
        ProductService.loadProductsByType(selectedType);
      }
    }
  },

  currentProducts: null, // Store current products list

  loadProducts: function () {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "products",
      type: "GET",
      contentType: "application/json",
      headers: {
        Authentication: localStorage.getItem("user_token"),
      },
      success: function (result) {
        ProductService.displayProducts(result.data);
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(
          XMLHttpRequest?.responseText
            ? XMLHttpRequest.responseText
            : "Error loading products"
        );
      },
    });
  },

  loadProductsByCategory: function (categoryId) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "products/category/" + categoryId,
      type: "GET",
      contentType: "application/json",
      headers: {
        Authentication: localStorage.getItem("user_token"),
      },
      success: function (result) {
        ProductService.displayProducts(result.data);
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(
          XMLHttpRequest?.responseText
            ? XMLHttpRequest.responseText
            : "Error loading products"
        );
      },
    });
  },

  loadProductsByType: function (type) {
    console.log("Loading products for type:", type);
    $.ajax({
      url:
        Constants.PROJECT_BASE_URL +
        "products/type/" +
        encodeURIComponent(type),
      type: "GET",
      contentType: "application/json",
      headers: {
        Authentication: localStorage.getItem("user_token"),
      },
      success: function (result) {
        console.log("Received products:", result);
        if (result.data && result.data.length > 0) {
          ProductService.currentProducts = result.data;
          // Load the first product's details
          ProductService.displayProductDetails(result.data[0], result.data);
        } else {
          toastr.error("No products found for this category");
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        console.error("Error loading products:", XMLHttpRequest.responseText);
        toastr.error(
          XMLHttpRequest?.responseText
            ? XMLHttpRequest.responseText
            : "Error loading products"
        );
      },
    });
  },

  loadProductDetails: function (productId) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "products/" + productId,
      type: "GET",
      contentType: "application/json",
      headers: {
        Authentication: localStorage.getItem("user_token"),
      },
      success: function (result) {
        if (result.data) {
          // Get all products of the same type to populate variants
          ProductService.loadProductsByType(result.data.type);
        } else {
          toastr.error("Product not found");
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(
          XMLHttpRequest?.responseText
            ? XMLHttpRequest.responseText
            : "Error loading product details"
        );
      },
    });
  },

  displayProducts: function (products) {
    let html = '<div class="row">';
    products.forEach((product) => {
      html += `
                <div class="col-md-4 mb-4">
                    <div class="card product-item" data-product-id="${
                      product.id
                    }">
                        <img src="${
                          product.image_url || "assets/img/default-product.jpg"
                        }" class="card-img-top" alt="${product.name}">
                        <div class="card-body">
                            <h5 class="card-title">${product.name}</h5>
                            <p class="card-text">${
                              product.short_description || ""
                            }</p>
                            <p class="card-text"><strong>Price: $${
                              product.price
                            }</strong></p>
                            <button class="btn btn-primary view-product" data-product-id="${
                              product.id
                            }">View Details</button>
                        </div>
                    </div>
                </div>
            `;
    });
    html += "</div>";
    $("#products-container").html(html);
  },

  displayProductDetails: function (product, allProductsOfType) {
    console.log("Displaying product:", product);

    // Map product names to their image files
    const imageMap = {
      "DeLonghi Office - Dedica EC685": "DeLonghi Office - Dedica EC685.jpg",
      "DeLonghi Single Group – La Specialista Prestigio":
        "DeLonghi Single Group – La Specialista Prestigio.jpg",
      "DeLonghi Dual Group – Eletta Explore":
        "DeLonghi Dual Group – Eletta Explore.jpg",
    };

    // Map products to their subscription IDs
    const subscriptionMap = {
      "DeLonghi Office - Dedica EC685": {
        trial: 2,
        monthly: 1,
        unlimited: 11
      },
      "DeLonghi Single Group – La Specialista Prestigio": {
        trial: 4,
        monthly: 3,
        unlimited: 12
      },
      "DeLonghi Dual Group – Eletta Explore": {
        trial: 6,
        monthly: 5,
        unlimited: 13
      }
    };

    // Determine the image path
    let imagePath;
    if (imageMap[product.name]) {
      imagePath = `assets/img/products-images/${imageMap[product.name]}`;
    } else if (product.type === "Coffee Machine") {
      imagePath = "assets/img/products-images/coffee-machine-3-group.jpg";
    } else {
      imagePath = `assets/img/products-images/${product.type
        .toLowerCase()
        .replace(" ", "-")}.jpg`;
    }

    let html = `
            <div class="container mt-5">
                <div class="row">
                    <!-- Left Column: Image and Reviews -->
                    <div class="col-md-6">
                        <div class="product-image-wrapper mb-4">
                            <img src="${imagePath}" 
                                 alt="${product.type}" 
                                 class="img-fluid rounded"/>
                        </div>
                        
                        <!-- Reviews Section -->
                        <div class="reviews-section">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4>Product Reviews</h4>
                                <button class="btn btn-primary" onclick="ProductService.showReviewModal()">
                                    Write a Review
                                </button>
                            </div>
                            <div id="product-reviews" class="reviews-container">
                                <!-- Reviews will be loaded here -->
                                <div class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading reviews...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Product Details -->
                    <div class="col-md-6">
                        <div class="product-details">
                            <h2 class="product-name mb-4">${product.name}</h2>
                            <div class="product-info mb-4">
                                <p class="mb-2"><strong>Type:</strong> ${product.type}</p>
                                ${
                                  product.weight
                                    ? `<p class="mb-2"><strong>Weight:</strong> ${product.weight}</p>`
                                    : ""
                                }
                                ${
                                  product.dimensions
                                    ? `<p class="mb-2"><strong>Dimensions:</strong> ${product.dimensions}</p>`
                                    : ""
                                }
                                ${
                                  product.specifications
                                    ? `<p class="mb-2"><strong>Specifications:</strong> ${product.specifications}</p>`
                                    : ""
                                }
                            </div>

                            <!-- Variant Selection -->
                            <div class="variant-selection mb-4">
                                <h5 class="mb-3">Select Variant</h5>
                                <select class="form-control" id="variant">
                                    ${allProductsOfType
                                      .map(
                                        (variant) => `
                                        <option value="${variant.name}" 
                                                data-product-id="${
                                                  variant.product_id
                                                }"
                                                data-product='${JSON.stringify(
                                                  variant
                                                )}'
                                                ${
                                                  variant.product_id ===
                                                  product.product_id
                                                    ? "selected"
                                                    : ""
                                                }>
                                            ${variant.name} - $${variant.price}
                                        </option>
                                    `
                                      )
                                      .join("")}
                                </select>
                            </div>

                            <!-- Quantity Counter -->
                            <div class="quantity-counter mb-4">
                                <h5 class="mb-3">Quantity</h5>
                                <div class="input-group" style="width: 150px">
                                    <button class="btn btn-outline-secondary" type="button" id="decrement-qty">-</button>
                                    <input type="number" class="form-control text-center" value="1" min="1" max="10" id="quantity" style="margin: 0"/>
                                    <button class="btn btn-outline-secondary" type="button" id="increment-qty">+</button>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="product-description mb-4">
                                <h5 class="mb-3">Description</h5>
                                <p class="text-muted">${
                                  product.description ||
                                  "No description available."
                                }</p>
                            </div>

                            <!-- Price & Actions -->
                            <div class="product-price mb-4">
                                <h3 class="price">$${product.price}</h3>
                                <p class="text-muted small">Free Shipping</p>
                            </div>
                            <div class="product-actions">
                                <button class="btn btn-primary btn-lg me-3" onclick="ProductService.showBuyModal()">Buy Now</button>
                                ${
                                  OrderService.isSubscribable(product.name)
                                    ? `<button class="btn btn-outline-secondary btn-lg" onclick="ProductService.showSubscribeModal()">Subscribe</button>`
                                    : ""
                                }
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Buy Now Modal -->
            <div class="modal fade" id="buyModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Purchase</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Please confirm your purchase of <span id="modal-quantity">1</span> unit(s) of ${
                              product.name
                            }.</p>
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select class="form-control" id="payment-method">
                                    <option value="cash">Cash</option>
                                    <option value="credit_card">Credit Card</option>
                                </select>
                            </div>
                            <p class="total-price">Total: $<span id="modal-total">${
                              product.price
                            }</span></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="ProductService.confirmPurchase()">Confirm Purchase</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscribe Modal -->
            ${
              OrderService.isSubscribable(product.name)
                ? `
            <div class="modal fade" id="subscribeModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Choose Subscription Plan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="subscription-options">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="subscriptionType" id="trial" value="trial" checked>
                                    <label class="form-check-label" for="trial">
                                        Trial Period
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="subscriptionType" id="monthly" value="monthly">
                                    <label class="form-check-label" for="monthly">
                                        Monthly Subscription
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="subscriptionType" id="unlimited" value="unlimited">
                                    <label class="form-check-label" for="unlimited">
                                        Unlimited Access
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="ProductService.confirmSubscription()">Request Subscription</button>
                        </div>
                    </div>
                </div>
            </div>`
                : ""
            }

            <!-- Review Modal -->
            <div class="modal fade" id="reviewModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Write a Review</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <form id="review-form">
                                <div class="mb-3">
                                    <label class="form-label">Rating</label>
                                    <div class="rating">
                                        <input type="radio" name="rating" value="5" id="5"><label for="5">☆</label>
                                        <input type="radio" name="rating" value="4" id="4"><label for="4">☆</label>
                                        <input type="radio" name="rating" value="3" id="3"><label for="3">☆</label>
                                        <input type="radio" name="rating" value="2" id="2"><label for="2">☆</label>
                                        <input type="radio" name="rating" value="1" id="1"><label for="1">☆</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Your Review</label>
                                    <textarea class="form-control" id="review-comment" rows="3" required></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" onclick="ProductService.submitReview()">Submit Review</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    $("#product-details-container").html(html);

    // Load reviews for this product
    ProductService.loadProductReviews(product.product_id);

    // Update quantity change handlers
    $(document).on("change", "#quantity", function () {
      const price = parseFloat(product.price);
      const quantity = parseInt($(this).val());
      $("#modal-quantity").text(quantity);
      $("#modal-total").text((price * quantity).toFixed(2));
    });
  },

  showBuyModal: function () {
    if (!Utils.isAuthenticated()) {
      window.location.hash = "#login";
      return;
    }
    const buyModal = new bootstrap.Modal(document.getElementById("buyModal"));
    buyModal.show();
  },

  showSubscribeModal: function () {
    if (!Utils.isAuthenticated()) {
      window.location.hash = "#login";
      return;
    }
    const subscribeModal = new bootstrap.Modal(
      document.getElementById("subscribeModal")
    );
    subscribeModal.show();
  },

  confirmPurchase: function () {
    const selectedOption = $("#variant option:selected");
    const productId = selectedOption.data("product-id");
    const quantity = parseInt($("#quantity").val());
    const paymentMethod = $("#payment-method").val();

    OrderService.placeOrder(productId, quantity, paymentMethod);
    const modal = bootstrap.Modal.getInstance(
      document.getElementById("buyModal")
    );
    if (modal) modal.hide();
  },

  confirmSubscription: function () {
    const selectedOption = $("#variant option:selected");
    const productId = selectedOption.data("product-id");
    const subscriptionType = $("input[name='subscriptionType']:checked").val();

    OrderService.requestSubscription(productId, subscriptionType);
    const modal = bootstrap.Modal.getInstance(
      document.getElementById("subscribeModal")
    );
    if (modal) modal.hide();
  },

  loadProductReviews: function(productId) {
    console.log("Loading reviews for product ID:", productId);
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "reviews/product/" + productId,
      type: "GET",
      success: function(result) {
        console.log("Received reviews response:", result);
        ProductService.displayReviews(result);
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        console.error("Error loading reviews:", {
          status: XMLHttpRequest.status,
          response: XMLHttpRequest.responseText,
          error: errorThrown
        });
        toastr.error(XMLHttpRequest?.responseText || "Error loading reviews");
      }
    });
  },

  displayReviews: function(reviews) {
    console.log("Displaying reviews:", reviews);
    if (!reviews || !reviews.length) {
      $("#product-reviews").html('<p class="text-muted">No reviews yet. Be the first to review this product!</p>');
      return;
    }

    let html = '<div class="reviews-list">';
    reviews.forEach(review => {
      console.log("Processing review:", review);
      const stars = '★'.repeat(review.rating) + '☆'.repeat(5 - review.rating);
      html += `
        <div class="review-card mb-3">
          <div class="review-header">
            <div class="rating-stars">${stars}</div>
            <div class="review-date">${new Date(review.created_at).toLocaleDateString()}</div>
          </div>
          <div class="review-content">
            <p class="review-text">${review.comment}</p>
            <p class="reviewer-name text-muted">By ${review.username || 'Anonymous'}</p>
          </div>
        </div>
      `;
    });
    html += '</div>';
    console.log("Setting reviews HTML");
    $("#product-reviews").html(html);
  },

  showReviewModal: function() {
    if (!Utils.isAuthenticated()) {
      window.location.hash = "#login";
      return;
    }
    const reviewModal = new bootstrap.Modal(document.getElementById("reviewModal"));
    reviewModal.show();
  },

  submitReview: function() {
    if (!Utils.isAuthenticated()) {
      window.location.hash = "#login";
      return;
    }

    const rating = $('input[name="rating"]:checked').val();
    const comment = $("#review-comment").val();
    const user = Utils.getCurrentUser();
    const selectedOption = $("#variant option:selected");
    const productId = selectedOption.data("product-id");

    if (!rating) {
      toastr.error("Please select a rating");
      return;
    }

    if (!comment) {
      toastr.error("Please write a review comment");
      return;
    }

    // Submit review directly without checking purchase
    const reviewData = {
      user_id: user.user_id,
      product_id: productId,
      rating: parseInt(rating),
      comment: comment
    };

    $.ajax({
      url: Constants.PROJECT_BASE_URL + "reviews",
      type: "POST",
      data: JSON.stringify(reviewData),
      contentType: "application/json",
      headers: {
        Authentication: localStorage.getItem("user_token")
      },
      success: function(result) {
        toastr.success("Review submitted successfully");
        const modal = bootstrap.Modal.getInstance(document.getElementById("reviewModal"));
        if (modal) modal.hide();
        // Reload reviews
        ProductService.loadProductReviews(productId);
      },
      error: function(XMLHttpRequest) {
        toastr.error(XMLHttpRequest?.responseText || "Error submitting review");
      }
    });
  },
};
