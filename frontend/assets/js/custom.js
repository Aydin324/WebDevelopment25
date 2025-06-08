$(document).ready(function () {
  $("main#spapp > section").height($(document).height() - 60);

  var app = $.spapp({ pageNotFound: "error_404" }); // initialize

  // define routes
  app.route({
    view: "home",
    onCreate: function () {},
    onReady: function () {},
  });
  app.route({ view: "products", load: "products.html", onReady: function() {
    ProductService.init();
  } });
  app.route({
    view: "view_profile",
    load: "view_profile.html",
    onReady: function () {
      if (!Utils.isAuthenticated()) {
        return;  // isAuthenticated will handle the redirect
      }
      
      const user = Utils.getCurrentUser();
      if (user && user.username) {
        $("#h2").text("Hello " + user.username);
      } else {
        Utils.redirectToLogin();
      }
    },
  });
  app.route({
    view: "reviews",
    load: "reviews.html",
    onCreate: function () { ReviewService.init(); },
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
    onReady: function() {
      const selectedType = sessionStorage.getItem('selected_product_type');
      if (selectedType) {
        ProductService.loadProductsByType(selectedType);
      }
    }
  });

  // run app
  app.run();

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
