var ProductService = {
    init: function() {

        // Event listener for product category clicks
        $(document).on('click', '.product-category', function(e) {
            e.preventDefault();
            const categoryId = $(this).data('category-id');
            ProductService.loadProductsByCategory(categoryId);
        });

        // Event listener for individual product clicks
        $(document).on('click', '.product-item', function(e) {
            e.preventDefault();
            const productId = $(this).data('product-id');
            window.location.hash = "#view_product";
            ProductService.loadProductDetails(productId);
        });
    },

    loadProducts: function() {
        $.ajax({
            url: Constants.PROJECT_BASE_URL + "products",
            type: "GET",
            contentType: "application/json",
            success: function(result) {
                // Assuming result is an array of products
                ProductService.displayProducts(result.data);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                toastr.error(XMLHttpRequest?.responseText ? XMLHttpRequest.responseText : "Error loading products");
            }
        });
    },

    loadProductsByCategory: function(categoryId) {
        $.ajax({
            url: Constants.PROJECT_BASE_URL + "products/category/" + categoryId,
            type: "GET",
            contentType: "application/json",
            success: function(result) {
                ProductService.displayProducts(result.data);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                toastr.error(XMLHttpRequest?.responseText ? XMLHttpRequest.responseText : "Error loading products");
            }
        });
    },

    loadProductDetails: function(productId) {
        $.ajax({
            url: Constants.PROJECT_BASE_URL + "products/" + productId,
            type: "GET",
            contentType: "application/json",
            success: function(result) {
                ProductService.displayProductDetails(result.data);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                toastr.error(XMLHttpRequest?.responseText ? XMLHttpRequest.responseText : "Error loading product details");
            }
        });
    },

    displayProducts: function(products) {
        let html = '<div class="row">';
        products.forEach(product => {
            html += `
                <div class="col-md-4 mb-4">
                    <div class="card product-item" data-product-id="${product.id}">
                        <img src="${product.image_url || 'assets/img/default-product.jpg'}" class="card-img-top" alt="${product.name}">
                        <div class="card-body">
                            <h5 class="card-title">${product.name}</h5>
                            <p class="card-text">${product.short_description || ''}</p>
                            <p class="card-text"><strong>Price: $${product.price}</strong></p>
                            <button class="btn btn-primary view-product" data-product-id="${product.id}">View Details</button>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        $('#products-container').html(html);
    },

    displayProductDetails: function(product) {
        let html = `
            <div class="container mt-5">
                <div class="row">
                    <div class="col-md-6">
                        <img src="${product.image_url || 'assets/img/default-product.jpg'}" class="img-fluid" alt="${product.name}">
                    </div>
                    <div class="col-md-6">
                        <h2>${product.name}</h2>
                        <p class="lead">${product.description}</p>
                        <p class="h3 mb-4">Price: $${product.price}</p>
                        
                        <div class="mb-4">
                            <label for="variant">Select Variant:</label>
                            <select class="form-control" id="variant">
                                ${product.variants.map(variant => 
                                    `<option value="${variant.id}">${variant.name} - $${variant.price}</option>`
                                ).join('')}
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label for="quantity">Quantity:</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" id="decrement-qty">-</button>
                                <input type="number" class="form-control" id="quantity" value="1" min="1" max="10">
                                <button class="btn btn-outline-secondary" id="increment-qty">+</button>
                            </div>
                        </div>
                        
                        <button class="btn btn-primary btn-lg">Add to Cart</button>
                    </div>
                </div>
            </div>
        `;
        $('#product-details-container').html(html);
    }
};