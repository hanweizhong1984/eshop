$(document).ready(function () {
    /* ---- Countdown timer ---- */


    $('#counter').countdown({
        timestamp: (new Date()).getTime() + $('#counter').data('second') * 1000,
        callback: function (days, hours, minutes, seconds) {
            if (days == 0 && hours == 0 && minutes == 0 && seconds == 0) {
                window.location.reload();
            }
        }
    });


    /* ---- Animations ---- */

    $('#links a').hover(
        function () {
            $(this).animate({left: 3}, 'fast');
        },
        function () {
            $(this).animate({left: 0}, 'fast');
        }
    );

    $('footer a').hover(
        function () {
            $(this).animate({top: 3}, 'fast');
        },
        function () {
            $(this).animate({top: 0}, 'fast');
        }
    );


});
