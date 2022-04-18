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

(function($, Drupal) {
    ("use strict");
    /**
     * meeting functions
     */
    Drupal.behaviors.meeting = {
        attach: function(context, settings) {
            // Get all elements with class="closebtn"
            var close = document.getElementsByClassName("closebtn");
            var i;

            // Loop through all close buttons
            for (i = 0; i < close.length; i++) {
                // When someone clicks on a close button
                close[i].onclick = function() {
                    // Get the parent of <span class="closebtn"> (<div class="alert">)
                    var div = this.parentElement;

                    // Set the opacity of div to 0 (transparent)
                    div.style.opacity = "0";

                    // Hide the div after 600ms (the same amount of milliseconds it takes to fade out)
                    div.style.display = "none";
                };
            }
            ///component color
            $(".component-color", context)
                .once("click-color")
                .click(function() {
                    $(this).find("input", context).once("click-color").click();
                    $(context)
                        .find(".component-color", context)
                        .find("input")
                        .removeOnce("click-color");
                });
        }
    };



    /**
     *
     * Activate Meeting Module
     *
     */
    $(document).on("click", ".module-activate-ajax", function(event) {
        var module_id = $(this).attr("data-id").split("|");
        $.ajax({
            url: $(this).attr("data-url"),
            type: "post",
            data: {
                meeting_id: module_id[0],
                module: module_id[1],
                order: module_id[2],
            },
            dataType: "JSON",
            success: function(result) {

            },
            error: function(result) {},
        });
    });

    //stop propagation form
    $(document).ready(function() {
        $(".action_ajax").click(function(e) {
            e.preventDefault();
        });
    });

    Drupal.behaviors.AjaxLinkChange = {
        attach: function(context) {
            $(".turbolink", context)
                .once()
                .bind("click", function(event) {
                    event.preventDefault();
                    var href = $(this).attr("href");
                    var ajax_settings = {
                        url: href,
                        element: this,
                    };
                    $(this).closest("tr").remove();
                    Drupal.ajax(ajax_settings).execute();
                });
        },
    };

    Drupal.AjaxCommands.prototype.RemoveTr = function(ajax, response, status) {};

    /**
     * load partial table
     * @param {*} ajax
     * @param {*} response
     * @param {*} status
     */
    Drupal.AjaxCommands.prototype.load_partial = function(
        ajax,
        response,
        status
    ) {
        var renderDiv = response.div;

        $.ajax({
            url: response.url,
            type: "GET",
            contentType: "application/json",
            dataType: "text",
            success: function(response) {
                $(renderDiv).html(response);

                Drupal.settings = response[0].settings;
                Drupal.attachBehaviors($(renderDiv)[0], Drupal.settings);
            },
        });
        if (response.form) {
            $(response.form)[0].reset();
        }
    };
})(jQuery, Drupal);
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
