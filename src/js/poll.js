(function ($, Drupal) {
  ("use strict");

  /**
   *
   * Select Poll to update
   *
   */

  $(document).on("click", ".edit-poll-ajax", function (event) {
    var jsonString = JSON.parse($(this).attr("data-poll"));
    $("input[data-drupal-selector='edit-poll-question']").val(
      jsonString.poll_question
    );
    $("input[data-selector-id='poll-id']").val(jsonString.id);
    event.preventDefault();
  });

  /**
   *
   * Delete a Poll
   *
   */

  $(document).on("click", ".remove-poll", function (event) {
    var tr = this;
    $.ajax({
      url: $(this).attr("data-url"),
      type: "get",
      contentType: "application/json",
      dataType: "text",
      success: function (result) {
        document.getElementById("poll-form").reset();
        $("input[data-selector-id='poll-id']").val("");
        $(tr).closest("tr").remove();
      },
      error: function (result) {},
    });
    event.preventDefault();
  });

  /**
   *
   * Allow multiple choice
   * Activate o page
   * Show results
   */

  $(document).on("click", ".poll-control-ajax", function (event) {
    var btn = this;
    $.ajax({
      url: $(this).attr("data-url"),
      type: "post",
      data: { id: $(this).attr("data-id") },
      dataType: "JSON",
      success: function (result) {
        if ($(btn).text() === "No") {
          $(btn).text("Yes");
          $(btn).removeClass("btn--danger");
          $(btn).addClass("btn--success");
          return false;
        }
        $(btn).text("No");
        $(btn).removeClass("btn--success");
        $(btn).addClass("btn--danger");
      },
      error: function (result) {},
    });
  });


  /**
   *
   * Dropdown
   *
   */


  $(document).on("click", ".action-dropdown", function (event) {
    $(".dropdown-active").find(".action-item-dropdown").stop().slideUp(400);
    $(".action-dropdown").removeClass("dropdown-active");

    $(this).find(".action-item-dropdown").stop().slideDown(400);
    $(this).addClass("dropdown-active");

    event.preventDefault();
  });

  $(document).on("mouseleave", ".action-dropdown", function (event) {
    $(".dropdown-active").find(".action-item-dropdown").stop().slideUp(400);
    $(".action-dropdown").removeClass("dropdown-active");

    event.preventDefault();
  });
})(jQuery, Drupal);
