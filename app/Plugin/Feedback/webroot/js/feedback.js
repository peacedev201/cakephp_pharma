

function doFeedbackAction( action, type )
{
    switch ( action )
    {
        case 'delete':
            $('#deleteForm').attr('action', mooConfig.url.base + '/admin/' + type + '/delete');
            confirmSubmitForm($('#delete_feedback').val(), 'deleteForm'); 
        break;
        
        case 'move':
            $('#deleteForm').attr('action', mooConfig.url.base + '/admin/' + type + '/move');
            $('#category_id').show();
        break;
        
        default:
            $('#category_id').hide();
    }
}

jQuery.feedback = {
    vote: function(id)
    {
        jQuery.post(mooConfig.url.base + '/feedback/feedbackvotes/ajax_add/' + id, function(data){
            var res = jQuery.parseJSON(data);
            if(res.result == 0)
            {
                mooAlert(res.message);
            }
            else
            {
                jQuery('.'+res.feedback).html( parseInt(res.total_votes) );
                jQuery('.'+res.feedback).parent().find('span').empty().append(res.text);
                jQuery('.a_'+res.feedback).html( res.action );
            }
        });
    },
    
    initFeedbackUploader: function(id)
    {
        var errorHandler = function(event, id, fileName, reason) {
            if ($('#attachments_upload .qq-upload-list .errorUploadMsg').length > 0){
                $('#attachments_upload .qq-upload-list .errorUploadMsg').html(MooPhrase.__('tmaxsize'));
            }else {
                $('#attachments_upload .qq-upload-list').prepend('<div class="errorUploadMsg">'+ MooPhrase.__('tmaxsize') +'</div>');
            }
            $('#attachments_upload .qq-upload-fail').remove();
        }; 

        if($('#uploader').length > 0) {
            var uploader = new qq.FineUploader({
                element: $('#uploader')[0],
                autoUpload: false,
                text: {
                    uploadButton: '<div class="upload-section"><i class="material-icons">camera_alt</i>'+MooPhrase.__('tdesc') +'</div>'
                },
                validation: {
                    allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
                },
                request: {
                    endpoint: mooConfig.url.base+ "/feedback/upload/",
                },
                callbacks: {
                    onError: errorHandler,
                    onComplete: function(id, fileName, response) {
                        var attachs = $('#FeedbackAttachments').val();
                        if (response.feedback_image_id)
                        {
                            if (attachs == '')
                            {
                                $('#FeedbackAttachments').val( response.feedback_image_id );
                            }
                            else
                            {
                                $('#FeedbackAttachments').val(attachs + ',' + response.feedback_image_id);
                            }
                        }
                        else if(response.result == 0)
                        {
                            mooAlert(response.message);
                        }
                    }
                }
            });
            
            $('#triggerUpload').click(function() {
                uploader.uploadStoredFiles();
            });
        }
    },
    
    createFeedback: function( type )
    {
        disableButton('createButton');
        jQuery.post(mooConfig.url.base + "/feedback/feedbacks/ajax_save", jQuery("#createForm").serialize(), function(data){
            enableButton('createButton');
            var json = $.parseJSON(data);

            if ( json.result == 1 ){
                if(json.url)
                {
                    window.location = json.url;
                }
                else
                {
                    $("#add").attr("href", $("#add").attr("href") + json.id);
                    jQuery.post(mooConfig.url.base + "/feedback/feedbacks/ajax_thanks/", 'id=' + json.id + '&approved=' + json.approved, function(data){
                        jQuery('#themeModal .modal-content').empty().append(data);
                        //jQuery('#themeModal').modal();
                    })
                }
            }
            else
            {
                $(".error-message").show();
                $(".error-message").html(json.message);
                if ($('.spinner').length > 0){
                    $('.spinner').remove();
                }
                if (jQuery('#captcha').length > 0) 
                {
                    grecaptcha.reset();
                }
            }
        });
    },
    
    initFeedbackImage: function()
    {
        jQuery('.attach_remove').each(function(){
            var attachs = $('#FeedbackAttachments').val();
            if (attachs == '')
            {
                $('#FeedbackAttachments').val(jQuery(this).data('id'));
            }
            else
            {
                $('#FeedbackAttachments').val(attachs + ',' + jQuery(this).data('id'));
            }
        })
    },
    
    removeFeedbackImage: function(id)
    {
        jQuery('#attach' + id).remove();
        var ids = jQuery('#FeedbackAttachments').val().split(',');
        ids.splice(jQuery.inArray(id, ids), 1 );
        jQuery('#FeedbackAttachments').val(ids.join());
    }
}

jQuery(document).ready(function(){
    $('.fb_vote').click(function(){
        var fb_id = $(this).attr('rel');
        jQuery.feedback.vote(fb_id);
    });
});