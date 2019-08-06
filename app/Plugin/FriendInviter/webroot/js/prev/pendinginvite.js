$(document).ready(function () {
        $('.delete_invite').click(function (e) {
            var invite_id = $(this).attr('rel');
            if ($.isNumeric(invite_id)) {
                e.preventDefault();
                $(this).spin('small');
                var url = baseUrl + '/friend_inviters/ajax_delete';
                $.post(url, {id: invite_id}, function (data) {
                    $('#invite_' + invite_id).remove();
                });
            }
        });

        $('.resend_invite').click(function (e) {
            var invite_id = $(this).attr('rel');
            if ($.isNumeric(invite_id)) {
                e.preventDefault();
                $(this).spin('small');
                var url = baseUrl + '/friend_inviters/ajax_resend';
                $.post(url, {id: invite_id}, function (data) {
                    $('#' + invite_id + '_link').remove();
                });
            }
        });
});


