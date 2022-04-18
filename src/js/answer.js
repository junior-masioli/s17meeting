(function ($, Drupal) {
  "use strict";
  /**
   * meeting functions
   */
  $(document).on("click", ".answer-edit-ajax", function (event) {
    var jsonString = JSON.parse($(this).attr("data-answer"));
    $("input[data-drupal-selector='edit-answer']").val(jsonString.answer);
    $("input[data-selector-id='poll-answer-id']").val(jsonString.id);
    event.preventDefault();
  });

  $(document).on("click", ".remove-answer-ajax", function (event) {
    var tr = this;
    var jsonString = JSON.parse($(this).attr("data-answer"));
    $.ajax({
      url: $(this).attr("data-answer-url"),
      data: { id: jsonString.id },
      type: "POST",
      dataType: "JSON",
      success: function (result) {
        $(".poll-answer-form")[0].reset();
        $("input[data-selector-id='poll-answer-id']").val("");
        $(tr).closest("tr").remove();
      },
      error: function (result) {},
    });
    event.preventDefault();
  });

})(jQuery, Drupal);
