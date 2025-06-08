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

        // Load subscription stats
        $.ajax({
            url: Constants.PROJECT_BASE_URL + 'user-subscriptions',
            type: 'GET',
            headers: { 'Authentication': localStorage.getItem('user_token') },
            success: function(subs) {
                if (!subs) return;
                const activeSubscriptions = subs.filter(s => s.status === 'active').length || 0;
                $('#active-subscriptions-count').text(activeSubscriptions);
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
                            <td>${order.username || order.user_id}</td>
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
                $('#orders-table').html('<div class="alert alert-danger">Error loading orders data</div>');
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
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';
                $('#users-table').html(html);
            },
            error: function(xhr) {
                toastr.error('Error loading users');
                $('#users-table').html('<div class="alert alert-danger">Error loading users data</div>');
            }
        });
    },

    loadSubscriptions: function() {
        $.ajax({
            url: Constants.PROJECT_BASE_URL + 'user-subscriptions',
            type: 'GET',
            headers: { 'Authentication': localStorage.getItem('user_token') },
            success: function(subscriptions) {
                if (!subscriptions || subscriptions.length === 0) {
                    $('#subscriptions-table').html('<div class="alert alert-warning">No subscriptions found</div>');
                    return;
                }

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

                const activeSubscriptions = subscriptions.filter(sub => sub.status === 'active');
                if (activeSubscriptions.length === 0) {
                    $('#subscriptions-table').html('<div class="alert alert-warning">No active subscriptions found</div>');
                    return;
                }

                activeSubscriptions.forEach(sub => {
                    const startDate = sub.start_date ? new Date(sub.start_date).toLocaleDateString() : 'N/A';
                    const nextBillingDate = sub.next_billing_date ? new Date(sub.next_billing_date).toLocaleDateString() : 'N/A';
                    
                    html += `
                        <tr>
                            <td>#${sub.user_subscription_id || sub.subscription_id}</td>
                            <td>${sub.username || sub.user_id}</td>
                            <td>${sub.name || 'Unknown Plan'}</td>
                            <td>${startDate}</td>
                            <td>${nextBillingDate}</td>
                            <td>
                                <span class="badge bg-success">Active</span>
                            </td>
                        </tr>
                    `;
                });

                html += '</tbody></table></div>';
                $('#subscriptions-table').html(html);
            },
            error: function(xhr) {
                toastr.error('Error loading subscriptions');
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