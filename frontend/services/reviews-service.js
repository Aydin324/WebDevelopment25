var ReviewService = {
  init: function () {
    this.loadReviews();
  },

  loadReviews: function () {
    $.ajax({
      url: Constants.PROJECT_BASE_URL + "reviews",
      type: "GET",
      contentType: "application/json",
      success: function (result) {
        this.displayReviews(result.data);
      },
      error: function (XMLHttpRequest, textStatus, errorThrown) {
        toastr.error(
          XMLHttpRequest?.responseText
            ? XMLHttpRequest.responseText
            : "Error loading products"
        );
      },
    });
  },

  displayReviews: function (reviews) {

  },
};
