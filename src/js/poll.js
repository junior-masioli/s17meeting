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