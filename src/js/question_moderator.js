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