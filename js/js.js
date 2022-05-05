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
(function($, Drupal) {
    ("use strict");

    /**
     *
     * Select Poll to update
     *
     */

    $(document).on("click", ".edit-poll-ajax", function(event) {
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

    $(document).on("click", ".remove-poll", function(event) {
        var tr = this;
        $.ajax({
            url: $(this).attr("data-url"),
            type: "get",
            contentType: "application/json",
            dataType: "text",
            success: function(result) {
                document.getElementById("poll-form").reset();
                $("input[data-selector-id='poll-id']").val("");
                $(tr).closest("tr").remove();
            },
            error: function(result) {},
        });
        event.preventDefault();
    });

    /**
     *
     * Allow multiple choice
     * Activate o page
     * Show results
     */

    $(document).on("click", ".poll-control-ajax", function(event) {
        var btn = this;
        var uuid = $(this).attr("data-uuid");
        $.ajax({
            url: $(this).attr("data-url"),
            type: "post",
            data: { id: $(this).attr("data-id") },
            dataType: "JSON",
            success: function(result) {
                if (result.data === 1) {
                    emitData("Room_" + uuid, "Username_" + uuid, "RefreshPoll");
                } else {
                    emitData("Room_" + uuid, "Username_" + uuid, "RemovePoll");
                }
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
            error: function(result) {},
        });
    });


    /**
     *
     * Dropdown
     *
     */


    $(document).on("click", ".action-dropdown", function(event) {
        $(".dropdown-active").find(".action-item-dropdown").stop().slideUp(400);
        $(".action-dropdown").removeClass("dropdown-active");

        $(this).find(".action-item-dropdown").stop().slideDown(400);
        $(this).addClass("dropdown-active");

        event.preventDefault();
    });

    $(document).on("mouseleave", ".action-dropdown", function(event) {
        $(".dropdown-active").find(".action-item-dropdown").stop().slideUp(400);
        $(".action-dropdown").removeClass("dropdown-active");

        event.preventDefault();
    });
    load_partial_poll_votes();
})(jQuery, Drupal);


function load_partial_poll_votes() {
    var id = jQuery(".poll-result .poll-id").attr("data-id");
    jQuery.ajax({
        url: Drupal.url('admin/meeting/poll/' + id + '/result/votes'),
        type: "GET",
        contentType: "application/json",
        dataType: "text",
        success: function(response) {
            var markup = "";
            const alphabet = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z"];
            jQuery.map(JSON.parse(response).data, function(a, index) {
                markup += resultMarkup(a, alphabet[index]);
            });
            jQuery('#poll_results').html(markup ? markup : "<li class='no-results'>No results</li>");

        },
    });

}

function resultMarkup(a, alphabet) {
    var markup = "";
    var totalVotes = a.total_votes != 0 ? (parseInt(a.votes) / parseInt(a.total_votes)) * 100 : 0;

    markup += '<li>';
    markup += '<div class="poll-result-content__label">';
    markup += '<span><strong>' + alphabet + '.</strong> ' + a.answer + '</span>';
    markup += '<span>' + totalVotes + '%</span>';
    markup += '</div>';
    markup += '<div class="poll-result-content__bar" style="background-color:' + jQuery(".meeting-shadow-colour").attr("data-colour") + ';">';
    markup += '<span style="width: ' + totalVotes + '%; background-color:' + jQuery(".meeting-bar-colour").attr("data-colour") + ';"></span>';
    markup += '</div>';
    markup += '</li>';
    return markup;
}


function emitData(room, username, message) {
    const data = {
        room,
        username,
        message,
    };
    socket.emit("sidebar", data);
}
(function($, Drupal) {
    "use strict";

    /**
     *
     * Update Question
     *
     */

    $(document).on("click", ".update-question-ajax", function(event) {
        var tr = this;
        var id = JSON.parse($(this).attr("data-id"));
        var status = JSON.parse($(this).attr("data-status"));

        $.ajax({
            url: $(this).attr("data-url"),
            data: { id: id, status: status },
            type: "POST",
            dataType: "JSON",
            success: function(result) {
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
            error: function(result) {},
        });
        event.preventDefault();
    });


    /**
     *
     * Delete Question
     *
     */


    $(document).on("click", ".delete-question-ajax", function(event) {
        var tr = this;
        var id = JSON.parse($(this).attr("data-id"));
        var status = JSON.parse($(this).attr("data-status"));
        $.ajax({
            url: $(this).attr("data-url"),
            data: { id: id, status: status },
            type: "POST",
            dataType: "JSON",
            success: function(result) {
                $(tr).closest("tr").remove();
            },
            error: function(result) {},
        });
        event.preventDefault();
    });

    refreshAdminQuestions();
})(jQuery, Drupal);


function refreshQuestionDataTable() {
    var id = jQuery("input[data-drupal-selector='edit-id']").val();

    jQuery.ajax({
        url: Drupal.url("admin/meeting/question/" + id + "/true/list"),
        type: "GET",
        contentType: "application/json",
        dataType: "text",
        success: function(response) {
            jQuery("#display_question").html(response);
        },
    });
}


function refreshAdminQuestions() {
    var uuid = jQuery("input[data-drupal-selector='edit-uuid']").val();
    socket = io("https://s-17-websocket-x682q.ondigitalocean.app");
    var MEETING_ID = uuid;
    var room = "Room_" + MEETING_ID;
    var username = "Username_" + MEETING_ID;

    socket.emit(
        "select_room", {
            username,
            room,
        },
        (messages) => {
            messages.forEach((message) => createMessage(message));
        }
    );

    socket.on("question", function(data) {
        let { text } = data;
        if (text == "question") {
            refreshQuestionDataTable()
        }
    });
}
(function($, Drupal) {
    "use strict";
    /**
     *
     * Enable actions button
     *
     */

    $(document).on("click", ".col-question .textarea", function(event) {
        var id = JSON.parse($(this).attr("data-id"));

        $("#actions_" + id + " button").attr("disabled", false);

    });

    $(document).on("mousedown", ".col-question .textarea", function(event) {
        var id = JSON.parse($(this).attr("data-id"));

        $("#actions_" + id + " button").attr("disabled", false);

    });



    /**
     *
     * Disable actions button
     *
     */

    $(document).on("click", ".col-question .cancel", function(event) {
        var id = JSON.parse($(this).attr("data-id"));

        $("#actions_" + id + " button").attr("disabled", true);
        load_partial_question();

        event.preventDefault();
    });

    /**
     *
     * Edit question
     *
     */
    $(document).on("click", ".col-question .save", function(event) {
        var id = JSON.parse($(this).attr("data-id"));
        var question = $("#question_" + id).html();
        var data = { id: id, question: question };
        var url = "admin/meeting/moderator/question/update";
        submitForm(url, data);

        setTimeout(function() {
            emitSpeakerQuestion();
        }, 200);
        event.preventDefault();
    });

    /**
     *
     * Update question status
     *
     */
    $(document).on("click", ".col-question .up-status", function(event) {
        var id = JSON.parse($(this).attr("data-id"));
        var status = JSON.parse($(this).attr("data-status"));
        var data = { id: id, status: status };
        var url = "admin/meeting/moderator/question/update";
        submitForm(url, data);

        setTimeout(function() {
            emitSpeakerQuestion();
        }, 200);
        event.preventDefault();
    });



    function submitForm(url, data) {
        $.ajax({
            url: Drupal.url(url),
            data: data,
            type: "POST",
            dataType: "JSON",
            success: function(result) {
                console.log(result.message)
            },
            error: function(result) {
                console.log(result);
            },
        });
    }


    /**
     * Load partial question
     */
    $(document).ready(function() {
        load_partial_question();
    });



    refreshModerateQuestions()

})(jQuery, Drupal);

function load_partial_question() {
    var uuid = jQuery(".question-meeting-uuid").attr("data-uuid");
    jQuery.ajax({
        url: Drupal.url('admin/meeting/' + uuid + '/questions/moderator/json'),
        type: "GET",
        contentType: "application/json",
        dataType: "text",
        success: function(response) {
            var markup_new = "";
            var markup_approved = "";
            var markup_archived = "";
            jQuery.map(JSON.parse(response).data, function(a) {
                if (a.status == 0) {
                    markup_new += questionMarkup(a);
                }
                if (a.status == 1) {
                    markup_approved += questionMarkup(a);
                }
                if (a.status == 2) {
                    markup_archived += questionMarkup(a);
                }
            });

            jQuery('#question_new').html(markup_new ? markup_new : "<li class='no-results'>No results</li>");
            jQuery('#question_approved ').html(markup_approved ? markup_approved : "<li class='no-results'>No results</li>");
            jQuery('#question_archived').html(markup_archived ? markup_archived : "<li class='no-results'>No results</li>");

        },
    });

}

function questionMarkup(a) {
    var markup = "";
    var isEditable = a.status == 0 ? 'contenteditable' : false;
    var setColumn = a.status < 2 ? 'column' : 'full';
    var setBorder = a.status > 0 ? 'border-bottom-1' : '';
    var setBorderNone = a.status > 0 ? 'border-0' : '';
    markup += "<li class='" + setBorder + ' ' + setColumn + "'>";
    markup += "<div class='col-left'>";
    markup += "<p data-id=" + a.id + " class='textarea " + setBorderNone + "' role='textbox' " + isEditable + " id='question_" + a.id + "'>" + a.question + "</p>";
    if (a.status == 0) {
        markup += "<div class='actions' id='actions_" + a.id + "'>";
        markup += "<button data-id='" + a.id + "' class='btn-moderate cancel' disabled>Cancel</button>";
        markup += "<button data-id='" + a.id + "' class='btn-moderate save' disabled>Save</button>";
        markup += "</div>";
    }
    markup += "</div>";
    if (a.status < 2) {
        markup += "<div class='col-right'>";
        if (a.status < 1) {
            markup += "<button data-status='1' data-id='" + a.id + "' class='btn-moderate up-status approve'>Approve</button>";
        }
        markup += "<button data-status='2' data-id='" + a.id + "' class='btn-moderate up-status archive'>Archive</button>";
        markup += "</div>";
    }
    markup += "</li>";

    return markup;
}

function refreshModerateQuestions() {
    socket = io("https://s-17-websocket-x682q.ondigitalocean.app");
    var MEETING_ID = jQuery(".question-meeting-uuid").attr("data-uuid");
    var room = "Room_" + MEETING_ID;
    var username = "Username_" + MEETING_ID;

    socket.emit(
        "select_room", {
            username,
            room,
        },
        (messages) => {
            messages.forEach((message) => createMessage(message));
        }
    );

    socket.on("question", function(data) {
        let { text } = data;
        if (text == "question") {
            load_partial_question()
        }
    });
}

//emita data
function emitSpeakerQuestion() {
    var MEETING_ID = jQuery(".question-meeting-uuid").attr("data-uuid");
    var room = "Room_" + MEETING_ID;
    var username = "Username_" + MEETING_ID;
    var message = "question";

    const data = {
        room,
        username,
        message,
    };

    socket.emit("question", data);
}