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
    view: "orders",
    load: "orders.html",
    onCreate: function () {},
  });
  app.route({
    view: "reviews",
    load: "reviews.html",
    onCreate: function () {},
  });
  app.route({
    view: "login",
    load: "login.html",
  });

  // run app
  app.run();

  $("main#spapp").on("click", "#loginbtn", function () {
    window.location.hash = "#login"; // Update the URL hash
  });
});
