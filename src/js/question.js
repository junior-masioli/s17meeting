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