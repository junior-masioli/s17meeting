(function ($, Drupal) {
  "use strict";

  /**
   *
   * Update Question
   *
   */

  $(document).on("click", ".update-question-ajax", function (event) {
    var tr = this;
    var id = JSON.parse($(this).attr("data-id"));
    var status = JSON.parse($(this).attr("data-status"));

    $.ajax({
      url: $(this).attr("data-url"),
      data: { id: id, status: status },
      type: "POST",
      dataType: "JSON",
      success: function (result) {
        $(tr).closest("tr").find(".update-question-ajax").attr("data-url", "");
        $(tr)
          .closest("tr")
          .find(".update-question-ajax")
          .removeClass("btn--primary");
        $(tr)
          .closest("tr")
          .find(".update-question-ajax")
          .removeClass("update-question-ajax");
      },
      error: function (result) {},
    });
    event.preventDefault();
  });


  /**
   *
   * Delete Question
   *
   */


  $(document).on("click", ".delete-question-ajax", function (event) {
    var tr = this;
    var id = JSON.parse($(this).attr("data-id"));
    var status = JSON.parse($(this).attr("data-status"));
    $.ajax({
      url: $(this).attr("data-url"),
      data: { id: id, status: status },
      type: "POST",
      dataType: "JSON",
      success: function (result) {
        $(tr).closest("tr").remove();
      },
      error: function (result) {},
    });
    event.preventDefault();
  });

})(jQuery, Drupal);
