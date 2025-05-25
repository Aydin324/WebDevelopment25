// Prevent redeclaration by checking if Utils already exists
var Utils = window.Utils || {
    parseJwt: function(token) {
        if (!token) return null;
        try {
            const payload = token.split('.')[1];
            const decoded = atob(payload);
            return JSON.parse(decoded);
        } catch (e) {
            console.error("Invalid JWT token", e);
            return null;
        }
    },
    isAuthenticated: function() {
        const token = localStorage.getItem("user_token");
        if (!token) return false;

        const decoded = Utils.parseJwt(token);
        if (!decoded) return false;

        const currentTime = Date.now() / 1000;
        return decoded.exp > currentTime;
    },
    getCurrentUser: function() {
        if (!Utils.isAuthenticated()) return null;
        
        const userData = localStorage.getItem("user_token");
        return userData ? JSON.parse(userData) : null;
    },
    hasRole: function(role) {
        const user = Utils.getCurrentUser();
        return user && user.role === role;
    },
    hasAnyRole: function(roles) {
        const user = Utils.getCurrentUser();
        return user && roles.includes(user.role);
    },
    hasPermission: function(permission) {
        const user = Utils.getCurrentUser();
        return user && user.permissions && user.permissions.includes(permission);
    },
    logout: function() {
        // Clear all auth data
        localStorage.removeItem("user_token");
        localStorage.removeItem("user_token");
        
        // Force redirect to login page
        window.location.replace(window.location.pathname + '#register_login');
    },
    showError: function(message) {
        toastr.error(message || 'An error occurred');
    },
    showSuccess: function(message) {
        toastr.success(message);
    }
};

// Make Utils available globally
window.Utils = Utils;
 