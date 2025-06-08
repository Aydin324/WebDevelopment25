var ProfileService = {
  init: function () {
    const user = Utils.getCurrentUser();
    if (!user) {
      Utils.redirectToLogin();
      return;
    }

    // Update header
    $("#h2").text("Hello " + user.username);

    // Load active subscriptions
    this.loadActiveSubscriptions(user.user_id);

    // Load order history
    this.loadOrderHistory(user.user_id);
  },

  loadActiveSubscriptions: function (userId) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "users/" + userId + "/subscriptions",
      type: "GET",
      headers: {
        Authentication: localStorage.getItem("user_token"),
      },
      success: function (result) {
        let html = "";
        if (result && result.length > 0) {
          result.forEach((subscription) => {
            html += `
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">${subscription.name || 'Subscription #' + subscription.subscription_id}</h5>
                            <p class="card-text">
                                <small class="text-muted">Created: ${new Date(subscription.created_at).toLocaleDateString()}</small>
                            </p>
                            <p class="card-text">
                                <small class="text-muted">Last Updated: ${new Date(subscription.updated_at).toLocaleDateString()}</small>
                            </p>
                            <p class="card-text">Quantity: ${subscription.quantity}</p>
                            <p class="card-text">Total Price: $${subscription.total_price || 0}</p>
                            <p class="card-text">Status: <span class="badge ${subscription.status === "active" ? "bg-success" : 
                                                                           subscription.status === "pending" ? "bg-warning" : 
                                                                           subscription.status === "completed" ? "bg-info" : "bg-secondary"
                                                             }">${subscription.status}</span></p>
                        </div>
                    </div>
                </div>
            `;
          });
        } else {
          html =
            '<div class="col-12"><p class="text-muted">No active subscriptions</p></div>';
        }
        $("#active-subscriptions").html(html);
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        console.error("Error loading subscriptions:", textStatus, errorThrown);
        $("#active-subscriptions").html(
          '<div class="col-12"><p class="text-danger">Error loading subscriptions. Please try again later.</p></div>'
        );
      },
    });
  },

  loadOrderHistory: function (userId) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "orders/user/" + userId,
      type: "GET",
      headers: {
        Authentication: localStorage.getItem("user_token"),
      },
      success: function (result) {
        let html = "";
        if (result && result.length > 0) {
          html =
            '<div class="table-responsive"><table class="table table-hover">';
          html += `
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                    `;

          result.forEach((order) => {
            console.log(order);
            html += `
                            <tr>
                                <td>#${order.order_id}</td>
                                <td>${new Date(
                                  order.created_at
                                ).toLocaleDateString()}</td>
                                <td>${order.order_type}</td>
                                <td>${
                                  order.product_name || order.subscription_name
                                }</td>
                                <td>${order.quantity}</td>
                                <td>$${order.total_price}</td>
                                <td><span class="badge bg-${
                                  order.status === "completed"
                                    ? "success"
                                    : order.status === "pending"
                                    ? "warning"
                                    : "danger"
                                }">${order.status}</span></td>
                            </tr>
                        `;
          });

          html += "</tbody></table></div>";
        } else {
          html = '<p class="text-muted">No orders found</p>';
        }

        $("#order-history").html(html);
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        console.error("Error loading order history:", textStatus, errorThrown);
        $("#order-history").html(
          '<p class="text-danger">Error loading order history. Please try again later.</p>'
        );
      },
    });
  },
};
