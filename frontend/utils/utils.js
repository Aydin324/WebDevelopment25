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
        if (!token) {
            Utils.redirectToLogin();
            return false;
        }

        const decoded = Utils.parseJwt(token);
        if (!decoded) {
            Utils.redirectToLogin();
            return false;
        }

        const currentTime = Date.now() / 1000;
        if (decoded.exp <= currentTime) {
            Utils.logout(); // Clear token and redirect
            return false;
        }

        return true;
    },
    getCurrentUser: function() {
        if (!Utils.isAuthenticated()) return null;
        
        const decoded = Utils.parseJwt(localStorage.getItem("user_token"));
        return decoded ? decoded.user : null;
    },
    hasRole: function(role) {
        const decoded = Utils.parseJwt(localStorage.getItem("user_token"));
        return decoded && decoded.role === role;
    },
    hasAnyRole: function(roles) {
        const decoded = Utils.parseJwt(localStorage.getItem("user_token"));
        return decoded && roles.includes(decoded.role);
    },
    hasPermission: function(permission) {
        const user = Utils.getCurrentUser();
        return user && user.permissions && user.permissions.includes(permission);
    },
    logout: function() {
        // Clear all auth data
        localStorage.removeItem("user_token");
        
        // Force redirect to login page
        window.location.hash = "#login";
    },
    showError: function(message) {
        toastr.error(message || 'An error occurred');
    },
    showSuccess: function(message) {
        toastr.success(message);
    },
    redirectToLogin: function() {
        localStorage.removeItem("user_token");
        window.location.hash = "#login";
    }
};

// Make Utils available globally
window.Utils = Utils;
 