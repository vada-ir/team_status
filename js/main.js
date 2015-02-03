$(document).ready(function(){
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

    //count down
    var clock = $('.countdown').FlipClock({
        countdown: true
       // clockFace: 'MinuteCounter'
    });
    clock.setTime(status_duration);
    clock.start();

});

