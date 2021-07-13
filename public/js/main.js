/**
 * main.js
 */

function removeNotifications()
{
    $('.notificationMessages .alert').hide();
}

$(document).ready(function () {
    var form = $('.inputForm').find('form');
    if (form.length === 1) {
        form.on('submit', function (event) {
            event.preventDefault();

            var formData = form.serializeArray();
            var values = {};
            $.map(formData, function (element) {
                values[element['name']] = element['value'];
            });

            $.post('/save', values)
                .done(function() {
                    $('.notificationMessages .alert-success').show();
                    setTimeout(removeNotifications, 5000);
                    $('.inputForm').find('input').val('');
                })
                .fail(function(response) {
                    $('.notificationMessages .alert-danger').find('span').html(response.responseJSON.error);
                    $('.notificationMessages .alert-danger').show();
                    setTimeout(removeNotifications, 30000);
                });
        });
    }
});