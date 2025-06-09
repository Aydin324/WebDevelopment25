var OrderService = {
  placeOrder: function(productId, quantity, paymentMethod) {
    if (!Utils.isAuthenticated()) {
      return; // isAuthenticated will handle the redirect
    }

    const user = Utils.getCurrentUser();
    if (!user) {
      Utils.redirectToLogin();
      return;
    }

    const token = localStorage.getItem("user_token");
    console.log("Placing order with token:", token);
    console.log("Current user:", user);
    
    // Get product price first
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "products/" + productId,
      type: "GET",
      headers: {
        'Authentication': token
      },
      success: function(product) {
        const total_price = product.price * quantity;
        
        const orderData = {
          product_id: productId,
          quantity: quantity,
          order_type: 'product',
          status: 'pending',
          user_id: user.user_id,
          total_price: total_price
        };
        
        console.log("Sending order data:", orderData);

        // Create order first
        $.ajax({
          url: Constants.PROJECT_BASE_URL + "orders",
          type: "POST",
          data: JSON.stringify(orderData),
          contentType: "application/json",
          dataType: "json",
          headers: {
            'Authentication': token
          },
          success: function(orderResult) {
            console.log("Order placed successfully:", orderResult);
            
            // Create payment record
            const paymentData = {
              order_id: typeof orderResult === 'object' ? orderResult.order_id : orderResult,
              user_id: user.user_id,
              amount: total_price,
              payment_method: paymentMethod,
              status: 'pending'
            };
            
            console.log("Sending payment data:", paymentData);

            $.ajax({
              url: Constants.PROJECT_BASE_URL + "payments",
              type: "POST",
              data: JSON.stringify(paymentData),
              contentType: "application/json",
              dataType: "json",
              headers: {
                'Authentication': token
              },
              success: function(paymentResult) {
                console.log("Payment record created:", paymentResult);
                toastr.success("Thank you for your order! Someone will reach out to you soon via email.");
              },
              error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.error("Payment creation failed:", {
                  status: XMLHttpRequest.status,
                  response: XMLHttpRequest.responseText,
                  error: errorThrown
                });
                toastr.error("Order placed but payment processing failed. Our team will contact you.");
              }
            });
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.error("Order placement failed:", {
              status: XMLHttpRequest.status,
              response: XMLHttpRequest.responseText,
              error: errorThrown
            });
            console.error("Request headers:", this.headers);
            toastr.error(XMLHttpRequest?.responseText ? XMLHttpRequest.responseText : "Error placing order");
          }
        });
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        console.error("Failed to get product details:", {
          status: XMLHttpRequest.status,
          response: XMLHttpRequest.responseText,
          error: errorThrown
        });
        toastr.error("Error getting product details");
      }
    });
  },

  requestSubscription: function(productId, subscriptionType) {
    if (!Utils.isAuthenticated()) {
      return; // isAuthenticated will handle the redirect
    }

    const user = Utils.getCurrentUser();
    if (!user) {
      Utils.redirectToLogin();
      return;
    }

    const token = localStorage.getItem("user_token");
    console.log("Requesting subscription with token:", token);
    console.log("Current user for subscription:", user);

    // Get quantity from the counter
    const quantity = parseInt($("#quantity").val()) || 1;
    console.log("Subscription quantity:", quantity);

    // First get product details to get the name
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "products/" + productId,
      type: "GET",
      headers: {
        'Authentication': token
      },
      success: function(product) {
        // Get subscription options for this product name
        const subscriptionOptions = OrderService.getSubscriptionOptions(product.name);
        if (!subscriptionOptions) {
          console.error("No subscription options found for product:", product.name);
          toastr.error("Invalid subscription request");
          return;
        }

        // Get subscription details based on type
        const subscription = subscriptionOptions[subscriptionType];
        if (!subscription) {
          console.error("Invalid subscription type:", subscriptionType);
          toastr.error("Invalid subscription type");
          return;
        }

        const subscriptionData = {
          user_id: user.user_id,
          order_type: 'subscription',
          subscription_id: subscription.id,
          quantity: quantity,
          status: 'pending',
          total_price: 0 // Will be calculated on backend
        };

        console.log("Sending subscription data:", subscriptionData);

        $.ajax({
          url: Constants.PROJECT_BASE_URL + "orders",
          type: "POST",
          data: JSON.stringify(subscriptionData),
          contentType: "application/json",
          dataType: "json",
          headers: {
            'Authentication': token
          },
          success: function(result) {
            console.log("Subscription requested successfully:", result);
            toastr.success("Thank you for your subscription request! Someone will reach out to you soon via email.");
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.error("Subscription request failed:", {
              status: XMLHttpRequest.status,
              response: XMLHttpRequest.responseText,
              error: errorThrown
            });
            console.error("Request headers:", this.headers);
            toastr.error(XMLHttpRequest?.responseText ? XMLHttpRequest.responseText : "Error requesting subscription");
          }
        });
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        console.error("Failed to get product details:", {
          status: XMLHttpRequest.status,
          response: XMLHttpRequest.responseText,
          error: errorThrown
        });
        toastr.error("Error getting product details");
      }
    });
  },

  // Helper function to get subscription name based on product
  getSubscriptionOptions: function(productName) {
    const subscriptionMap = {
      'DeLonghi Office - Dedica EC685': {
        trial: { id: 2, name: 'Dedica EC685 Trial' },
        monthly: { id: 1, name: 'Dedica EC685 Monthly' },
        unlimited: { id: 11, name: 'Dedica EC685 Unlimited' }
      },
      'DeLonghi Single Group – La Specialista Prestigio': {
        trial: { id: 4, name: 'La Specialista Prestigio Trial' },
        monthly: { id: 3, name: 'La Specialista Prestigio Monthly' },
        unlimited: { id: 12, name: 'La Specialista Prestigio Unlimited' }
      },
      'DeLonghi Dual Group – Eletta Explore': {
        trial: { id: 6, name: 'Eletta Explore Trial' },
        monthly: { id: 5, name: 'Eletta Explore Monthly' },
        unlimited: { id: 13, name: 'Eletta Explore Unlimited' }
      },
      'DeLonghi Triple Group - PrimaDonna Elite': {
        trial: { id: 8, name: 'PrimaDonna Elite Trial' },
        monthly: { id: 7, name: 'PrimaDonna Elite Monthly' },
        unlimited: { id: 14, name: 'PrimaDonna Elite Unlimited' }
      },
      'Bosch 500 Series SHP865ZD5N': {
        trial: { id: 10, name: 'Bosch 500 Trial' },
        monthly: { id: 9, name: 'Bosch 500 Dishwasher Monthly' },
        unlimited: { id: 15, name: 'Bosch 500 Dishwasher Unlimited' }
      }
    };

    return subscriptionMap[productName] || null;
  },

  isSubscribable: function(productName) {
    return OrderService.getSubscriptionOptions(productName) !== null;
  }
}; 