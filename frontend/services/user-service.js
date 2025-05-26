var UserService = {
  init: function () {
    var token = localStorage.getItem("user_token");
    if (token && token !== undefined) {
      UserService.loadPanel();
    }

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
        console.log(result);
        localStorage.setItem("user_token", result.data.token);
        UserService.loadPanel();
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
    window.location.replace("login.html");
  },

  loadPanel: function () {
    const payload = Utils.parseJwt(localStorage.getItem("user_token"));
    const role = payload?.role;
    if (role == "user") {
      window.location.hash = "#view_profile";
    } else if (role == "admin") {
      window.location.hash = "#admin_panel";
    } else {
      console.log("Error in token decoding - Can't find role");
    }
  },
};
