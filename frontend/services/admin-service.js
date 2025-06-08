var AdminService = {
    init: function() {
        const user = Utils.getCurrentUser();
        if (!user) {
            window.location.hash = '#login';
            return;
        }
        
        const payload = Utils.parseJwt(localStorage.getItem("user_token"));
        if (!payload || payload.role !== 'admin') {
            window.location.hash = '#home';
            return;
        }
        
        // Initialize the page
        this.loadStats();
        this.loadRecentOrders();
        this.loadUsers();
        this.loadSubscriptions();
        
        // Set up refresh button handlers
        $('#refresh-orders').click(() => this.loadRecentOrders());
        $('#refresh-users').click(() => this.loadUsers());
        $('#refresh-subscriptions').click(() => this.loadSubscriptions());
    },

    loadStats: function() {
        // Load orders stats
        $.ajax({
            url: Constants.PROJECT_BASE_URL + 'orders',
            type: 'GET',
            headers: { 'Authentication': localStorage.getItem('user_token') },
            success: function(orders) {
                if (!orders) return;
                
                const pendingOrders = orders.filter(o => o.status === 'pending').length;
                const totalRevenue = orders.filter(o => o.status === 'completed')
                    .reduce((sum, order) => sum + parseFloat(order.total_price), 0);
                
                $('#pending-orders-count').text(pendingOrders);
                $('#total-revenue').text(totalRevenue.toFixed(2) + ' BAM');
            },
            error: function(xhr) {
                toastr.error('Error loading order statistics');
            }
        });

        // First get all users
        $.ajax({
            url: Constants.PROJECT_BASE_URL + 'users',
            type: 'GET',
            headers: { 'Authentication': localStorage.getItem('user_token') },
            success: function(users) {
                if (!users) return;
                
                // Then get subscriptions for each user
                let activeSubscriptionsCount = 0;
                let loadedUsers = 0;
                
                users.forEach(user => {
                    $.ajax({
                        url: Constants.PROJECT_BASE_URL + 'users/' + user.user_id + '/subscriptions',
                        type: 'GET',
                        headers: { 'Authentication': localStorage.getItem('user_token') },
                        success: function(subs) {
                            if (subs) {
                                activeSubscriptionsCount += subs.filter(s => s.status === 'active').length;
                            }
                            loadedUsers++;
                            
                            // Update count when all users are processed
                            if (loadedUsers === users.length) {
                                $('#active-subscriptions-count').text(activeSubscriptionsCount);
                            }
                        },
                        error: function() {
                            loadedUsers++;
                            // Still update if all users are processed
                            if (loadedUsers === users.length) {
                                $('#active-subscriptions-count').text(activeSubscriptionsCount);
                            }
                        }
                    });
                });
            },
            error: function(xhr) {
                toastr.error('Error loading subscription statistics');
                $('#active-subscriptions-count').text('Error');
            }
        });
    },

    loadRecentOrders: function() {
        $.ajax({
            url: Constants.PROJECT_BASE_URL + 'orders',
            type: 'GET',
            headers: { 'Authentication': localStorage.getItem('user_token') },
            success: function(orders) {
                let html = '<div class="table-responsive"><table class="table table-hover">';
                html += `
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User</th>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                `;

                orders.forEach(order => {
                    html += `
                        <tr>
                            <td>#${order.order_id}</td>
                            <td>${order.user_id}</td>
                            <td>${order.name || 'Unknown Item'}</td>
                            <td>${order.total_price} BAM</td>
                            <td>
                                <span class="badge bg-${order.status === 'completed' ? 'success' : 
                                                      order.status === 'pending' ? 'warning' : 'danger'}">
                                    ${order.status}
                                </span>
                            </td>
                            <td>
                                ${order.status === 'pending' ? 
                                    `<button class="btn btn-sm btn-success" onclick="AdminService.updateOrderStatus(${order.order_id}, 'completed')">
                                        Complete
                                    </button>` : ''}
                            </td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';
                $('#orders-table').html(html);
            },
            error: function(xhr) {
                toastr.error('Error loading orders');
            }
        });
    },

    loadUsers: function() {
        $.ajax({
            url: Constants.PROJECT_BASE_URL + 'users',
            type: 'GET',
            headers: { 'Authentication': localStorage.getItem('user_token') },
            success: function(users) {
                let html = '<div class="table-responsive"><table class="table table-hover">';
                html += `
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                `;

                users.forEach(user => {
                    html += `
                        <tr>
                            <td>#${user.user_id}</td>
                            <td>${user.username}</td>
                            <td>${user.email}</td>
                            <td>${user.role}</td>
                            <td>
                                <span class="badge bg-${user.status === 'active' ? 'success' : 'danger'}">
                                    ${user.status}
                                </span>
                            </td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';
                $('#users-table').html(html);
            },
            error: function(xhr) {
                toastr.error('Error loading users');
            }
        });
    },

    loadSubscriptions: function() {
        // First get all users
        $.ajax({
            url: Constants.PROJECT_BASE_URL + 'users',
            type: 'GET',
            headers: { 'Authentication': localStorage.getItem('user_token') },
            success: function(users) {
                if (!users) {
                    $('#subscriptions-table').html('<div class="alert alert-warning">No users found</div>');
                    return;
                }
                
                let allSubscriptions = [];
                let loadedUsers = 0;
                
                // Get subscriptions for each user
                users.forEach(user => {
                    $.ajax({
                        url: Constants.PROJECT_BASE_URL + 'users/' + user.user_id + '/subscriptions',
                        type: 'GET',
                        headers: { 'Authentication': localStorage.getItem('user_token') },
                        success: function(subs) {
                            if (subs) {
                                // Add user info to each subscription
                                subs.forEach(sub => {
                                    sub.username = user.username;
                                    allSubscriptions.push(sub);
                                });
                            }
                            loadedUsers++;
                            
                            // Render table when all users are processed
                            if (loadedUsers === users.length) {
                                let html = '<div class="table-responsive"><table class="table table-hover">';
                                html += `
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>User</th>
                                            <th>Plan</th>
                                            <th>Start Date</th>
                                            <th>Next Billing</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                `;

                                allSubscriptions.forEach(sub => {
                                    html += `
                                        <tr>
                                            <td>#${sub.subscription_id}</td>
                                            <td>${sub.username}</td>
                                            <td>${sub.name || 'Unknown Plan'}</td>
                                            <td>${new Date(sub.start_date).toLocaleDateString()}</td>
                                            <td>${new Date(sub.next_billing_date).toLocaleDateString()}</td>
                                            <td>
                                                <span class="badge bg-${sub.status === 'active' ? 'success' : 
                                                                      sub.status === 'pending' ? 'warning' : 'danger'}">
                                                    ${sub.status}
                                                </span>
                                            </td>
                                        </tr>
                                    `;
                                });

                                html += '</tbody></table></div>';
                                $('#subscriptions-table').html(html);
                            }
                        },
                        error: function() {
                            loadedUsers++;
                            // Still try to render if all users are processed
                            if (loadedUsers === users.length) {
                                if (allSubscriptions.length === 0) {
                                    $('#subscriptions-table').html('<div class="alert alert-warning">No subscriptions found</div>');
                                }
                            }
                        }
                    });
                });
            },
            error: function(xhr) {
                toastr.error('Error loading users');
                $('#subscriptions-table').html('<div class="alert alert-danger">Error loading subscription data</div>');
            }
        });
    },

    updateOrderStatus: function(orderId, newStatus) {
        $.ajax({
            url: Constants.PROJECT_BASE_URL + 'orders/' + orderId + '/status',
            type: 'PUT',
            headers: { 'Authentication': localStorage.getItem('user_token') },
            data: JSON.stringify({ status: newStatus }),
            contentType: 'application/json',
            success: function(result) {
                toastr.success('Order status updated successfully');
                AdminService.loadRecentOrders(); // Refresh the orders table
                AdminService.loadStats(); // Refresh the stats
            },
            error: function(xhr) {
                toastr.error('Error updating order status');
            }
        });
    }
}; 