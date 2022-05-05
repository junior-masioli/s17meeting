(function($) {
    $('.btn-open-fullscreen').on('click', function() {
        if (jQuery('.meeting').hasClass('meeting-fullscreen')) {
            removeFullScreen();
        } else {
            addFullScreen();
        }

        document.fullScreenElement && null !== document.fullScreenElement || !document.mozFullScreen && !document.webkitIsFullScreen ? document.documentElement.requestFullScreen ? document.documentElement.requestFullScreen() : document.documentElement.mozRequestFullScreen ? document.documentElement.mozRequestFullScreen() : document.documentElement.webkitRequestFullScreen && document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT) : document.cancelFullScreen ? document.cancelFullScreen() : document.mozCancelFullScreen ? document.mozCancelFullScreen() : document.webkitCancelFullScreen && document.webkitCancelFullScreen()
    });

    $('.btn-close-sidebar, .tab_poll, .tab_question, .tab_survey, .tab_info').on('click', function() {
        jQuery('.meeting__sidebar').toggleClass("meeting__sidebar_closed");
    });
    $('.tab_poll, .tab_question, .tab_survey, .tab_info').on('click', function() {
        if (jQuery('.meeting__sidebar').hasClass("meeting__sidebar_closed")) {
            jQuery('.meeting__sidebar').toggleClass("meeting__sidebar_closed");
        }
    });

})(jQuery);

function exitFullscreen() {
    if (document.exitFullscreen) {
        document.exitFullscreen();
    } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
    } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
    }
}

document.addEventListener('fullscreenchange', exitHandler);
document.addEventListener('webkitfullscreenchange', exitHandler);
document.addEventListener('mozfullscreenchange', exitHandler);
document.addEventListener('MSFullscreenChange', exitHandler);


function exitHandler() {
    if (!document.fullscreenElement && !document.webkitIsFullScreen && !document.mozFullScreen && !document.msFullscreenElement) {
        removeFullScreen();
    }
}

function addFullScreen() {
    jQuery('.meeting').addClass('meeting-fullscreen');
    jQuery('.meeting__sidebar').addClass('meeting__sidebar-fullscreen');
    jQuery('.site-footer').hide();
    jQuery('body').css('overflow', 'hidden');
    jQuery('.btn-close-sidebar').css('display', 'flex');
}

function removeFullScreen() {
    jQuery('.meeting').removeClass('meeting-fullscreen');
    jQuery('.meeting__sidebar').removeClass('meeting__sidebar-fullscreen');
    jQuery('.site-footer').show();
    jQuery('body').css('overflow', 'auto');
    jQuery('.btn-close-sidebar').css('display', 'none');
    if (jQuery('.meeting__sidebar').hasClass("meeting__sidebar_closed")) {
        jQuery('.meeting__sidebar').toggleClass("meeting__sidebar_closed");
    }

}
//sidebar functions
document.addEventListener('alpine:init', () => {
    Alpine.store('getContent', {
        isLoading: false,
        question: null,
        survey: null,
        info: null,
        fetchMeeting() {
            this.isLoading = true;
            fetch(`/api/meeting/${MEETING_ID}/meeting`).then(res => res.json()).then(data => {
                this.isLoading = false;
                this.question = data.question;
                this.survey = data.survey;
                this.info = data.info;
                this.checkIfMenuIsEnabled(data)
                this.getVideoUrl(data)
            });
        },
        menuPoll: false,
        menuQuestion: false,
        menuSurvey: false,
        menuInfo: false,
        checkIfMenuIsEnabled(data) {
            if (data) {
                this.menuPoll = data.module_poll;
                this.menuQuestion = data.module_question;
                this.menuSurvey = data.module_survey;
                this.menuInfo = data.module_info;
            }
        },
        video_url: false,
        getVideoUrl(data) {
            if (data) {
                this.video_url = data.video_url;
            }
        }
    });
});