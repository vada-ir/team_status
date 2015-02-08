$(document).ready(function(){
    // Request permission for notification access.
    var granted = false;
    Notify.requestPermission(function(){granted = true}, function(){granted= false});

    //Modal onshow event get some default values (defined in config) for inputs from data attributes.
    $('#newState').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var title = button.data('title'); // Extract info from data-* attributes
        var time = button.data('time');
        var id = button.attr('id');
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this)
        modal.find('.modal-title').text('New ' + title + ' Status...');
        modal.find('.modal-body #time').val(time);
        modal.find('.modal-body #state').val(id);
    })

    //flag to show notification just once at end.
    var showForFirstTime = false;

    /*  Init countdown
    *   First set countdown to true to counting down to show time remaining.
    *   Using stop callback to popup a notification.
    * */
    var clock = $('.countdown').FlipClock({
        countdown: true,
        stop : function() {
            // show notification on end
            if (!showForFirstTime) {
                showForFirstTime = true;
                if (granted) {
                    var myNotification = new Notify('Status is changed!', {
                        body: 'The last status was ' + $('em.current_status').text()
                    });
                    myNotification.show();
                }
            }

        }
    });

    // Indicate whether counter must counting up
    var countUp = false;

    // When status time is exceeds the planned time status fall into extra time.
    if(extraTime >= 0)
    {
        clock.setTime(extraTime);
        countUp = true;
        clock.setCountdown(false);
    }
    else
        clock.setTime(status_duration);

    // start counter
    clock.start();

    // reset counter to count up when time is 0
    var reloadcounter = setInterval(function () {
        if(clock.getTime() == 0)
        {
            countUp = true;
            clock.setCountdown(false);
        }
    }, 4000);

    // check if status in extra time and page must reload every x seconds.
    var refreshPage = setInterval(function(){
        if(reloadPage && countUp)
            location.reload();
    },20000);

});

