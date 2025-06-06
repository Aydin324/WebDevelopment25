$(document).ready(function () {
  $("main#spapp > section").height($(document).height() - 60);

  var app = $.spapp({ pageNotFound: "error_404" }); // initialize

  // define routes
  app.route({
    view: "home",
    onCreate: function () {},
    onReady: function () {},
  });
  app.route({ view: "products", load: "products.html" });
  app.route({
    view: "view_profile",
    load: "view_profile.html",
    onReady: function () {
      const token = localStorage.getItem("user_token");
      if (token) {
        const payload = Utils.parseJwt(token);
        if (payload && payload.user && payload.user.username) {
          $("#h2").text("Hello " + payload.user.username);
        }
      }
    },
  });
  app.route({
    view: "reviews",
    load: "reviews.html",
    onCreate: function () {},
  });
  app.route({
    view: "login",
    load: "login.html",
    onReady: function () {
      console.log("Login/Register page loaded.");
      UserService.init();
    },
  });
  app.route({
    view: "admin_panel",
    load: "admin_panel.html",
  });
  app.route({
    view: "view_product",
    load: "view_product.html",
  });

  // run app
  app.run();

  $("main#spapp").on("click", ".portfolio-item", function () {
    window.location.hash = "#view_product"; // Update the URL hash
  });

  $(document).on("click", "#decrement-qty", function () {
    const input = $("#quantity");
    if (parseInt(input.val()) > 1) input.val(parseInt(input.val()) - 1);
  });

  $(document).on("click", "#increment-qty", function () {
    const input = $("#quantity");
    if (parseInt(input.val()) < 10) input.val(parseInt(input.val()) + 1);
  });

  $(document).on("click", "#switch_to_register", function (e) {
    e.preventDefault(); // prevent the default link action

    $("#login-form").hide();
    $("#registration-form").show();
  });

  $(document).on("click", "#switch_to_login", function (e) {
    e.preventDefault(); // prevent the default link action

    $("#registration-form").hide();
    $("#login-form").show();
  });
});
