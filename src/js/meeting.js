(function($, Drupal) {
    ("use strict");
    /**
     * meeting functions
     */
    Drupal.behaviors.meeting = {
        attach: function(context, settings) {
            const websocketUrl = "https://s-17-websocket-x682q.ondigitalocean.app";

            socket = io(websocketUrl);

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