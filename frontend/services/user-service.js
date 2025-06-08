var UserService = {
  init: function () {
    var token = localStorage.getItem("user_token");
    if (token && token !== undefined) {
      const payload = Utils.parseJwt(token);
      if (payload && payload.role === 'admin') {
        window.location.hash = '#admin_panel';
      } else {
        window.location.hash = '#view_profile';
      }
    }
    UserService.updateNavigation();

    // Login form validation and submit handler
    $("#login-form").validate({
      submitHandler: function (form) {
        var entity = Object.fromEntries(new FormData(form).entries());
        console.log("Submitting login with entity:", entity);
        UserService.login(entity);
      },
    });

    // Registration form validation and submit handler
    $("#registration-form").validate({
      submitHandler: function (form) {
        var entity = Object.fromEntries(new FormData(form).entries());
        console.log("Submitting registration with entity:", entity);
        UserService.register(entity);
      },
    });
  },

  login: function (entity) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "auth/login",
      type: "POST",
      data: JSON.stringify(entity),
      contentType: "application/json",
      dataType: "json",
      success: function (result) {
        console.log("Login response:", result);
        localStorage.setItem("user_token", result.data.token);
        const payload = Utils.parseJwt(result.data.token);
        console.log("Decoded payload:", payload);
        UserService.updateNavigation();
        
        if (payload && payload.role === 'admin') {
          console.log("Admin user detected, redirecting to admin panel");
          window.location.hash = '#admin_panel';
        } else {
          console.log("Regular user detected, redirecting to profile");
          window.location.hash = '#view_profile';
        }
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(
          XMLHttpRequest?.responseText ? XMLHttpRequest.responseText : "Error"
        );
      },
    });
  },

  register: function (entity) {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "auth/register", // Adjust URL as per your backend
      type: "POST",
      data: JSON.stringify(entity),
      contentType: "application/json",
      dataType: "json",
      success: function (result) {
        console.log(result);
        // If no token, you may want to redirect or show message
        toastr.success("Registration successful! Please login.");
        // Optionally, switch back to login form
        $("#registration-form").hide();
        $("#login-form").show();
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(
          XMLHttpRequest?.responseText
            ? XMLHttpRequest.responseText
            : "Registration failed"
        );
      },
    });
  },

  logout: function () {
    localStorage.clear();
    UserService.updateNavigation();
    window.location.hash = "#login";
  },

  updateNavigation: function () {
    const navLink = $("#nav-auth-link");
    const token = localStorage.getItem("user_token");
    
    if (!token) {
      navLink.text("Login").attr("href", "#login");
      return;
    }

    const payload = Utils.parseJwt(token);
    if (!payload || !payload.role) {
      navLink.text("Login").attr("href", "#login");
      return;
    }

    if (payload.role === 'admin') {
      navLink.text("Admin Panel").attr("href", "#admin_panel");
    } else {
      navLink.text("Profile").attr("href", "#view_profile");
    }
  },
};
