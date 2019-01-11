$('body').on('submit', 'form', function(e) {
    e.preventDefault();
    $('button[type="submit"]').append('<i class="fa fa-spinner fa-spin" id="loader"></i>');

    $.ajax({
        url: $(this).attr('action'),
        dataType: 'json',
        method: 'POST',
        data: {
            name: $('input[name="name"]').val(),
            phoneNumber: $('input[name="phoneNumber"]').val(),
            email: $('input[name="email"]').val(),
            subject: $('input[name="subject"]').val(),
            message: $('textarea[name="message"]').val(),
        },
        success: function(response) {
            $('#loader').remove();
            showAlert(response, 'alert-success', 'fa-check');
            $('form')[0].reset();
        },
        error: function(e) {
            $('#loader').remove();
            showAlert(unescape(JSON.parse(e.responseText)), 'alert-danger', 'fa-times');
        }
    });
});

/**
* Show alert
**/
function showAlert(content, alertClass, iconClass) {
    var prototype = '<span class="ml-3" id="request-alert"><em class="%alertClass%"><i class="fa %iconClass%"></i> %alertText%</em></span>';
    prototype = prototype.replace('%alertClass%', alertClass)
                         .replace('%alertText%', content)
                         .replace('%iconClass%', iconClass);

    $('#request-alert').remove();                     
    $(prototype).insertAfter('button[type="submit"]');
}