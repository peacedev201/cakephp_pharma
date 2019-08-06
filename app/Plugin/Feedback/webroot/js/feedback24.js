(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooBehavior', 'mooFileUploader', 'mooAjax', 'mooOverlay', 'mooAlert', 'mooPhrase', 'mooGlobal', 'tinyMCE', 'mooUser', 'mooButton'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooClassified = factory();
    }
}(this, function ($, mooBehavior, mooFileUploader, mooAjax, mooOverlay, mooAlert, mooPhrase, mooGlobal, tinyMCE, mooUser, mooButton) {
    var initOnCreate = function () {
        jQuery.feedback.initFeedbackUploader();
        jQuery.feedback.initFeedbackImage();

        function doFeedbackAction(action, type)
        {
            switch (action)
            {
                case 'delete':
                    $('#deleteForm').attr('action', mooConfig.url.base + '/admin/' + type + '/delete');
                    confirmSubmitForm(mooPhrase.__('feedback_delete_confirm'), 'deleteForm');
                    break;

                case 'move':
                    $('#deleteForm').attr('action', mooConfig.url.base + '/admin/' + type + '/move');
                    $('#category_id').show();
                    break;

                default:
                    $('#category_id').hide();
            }
        }
    }

    var initOnIndex = function() {
        $('.fb_vote').unbind('click');
        $('.fb_vote').click(function(){
            var fb_id = $(this).attr('ref');
            jQuery.feedback.vote(fb_id, $(this));
        });      
    }

    var initOnView = function() {
        mooOverlay.registerImageOverlay();
        $('.fb_approve').unbind('click');
        $('.fb_approve').click(function(){
            var aprove = $(this).attr('ref');
        });
        approveFB();
        featureFB();
        deleteFB();
        $('.fb_vote').unbind('click');
        $('.fb_vote').click(function(){
            var fb_id = $(this).attr('ref');
            jQuery.feedback.vote(fb_id, $(this));
        });
    }

    var initShortcut = function() {
        jQuery(window).load(function(){
            jQuery.post(mooConfig.url.base + "/feedbacks/popups/load_shortcut", '', function(data){
                jQuery('body').append(data);
                jQuery.feedback.initFeedbackUploader();
                jQuery.feedback.initFeedbackImage();
            });
        })
    }

    jQuery.feedback = {
        vote: function (id, obj)
        {
            obj.addClass('disabled');
            jQuery.post(mooConfig.url.base + '/feedbacks/feedbackvotes/ajax_add/' + id, function (data) {
                var res = jQuery.parseJSON(data);
                if (res.result == 0)
                {
                    mooAlert.alert(res.message);
                }
                else
                {
                    jQuery('.' + res.feedback).html(parseInt(res.total_votes));
                    jQuery('.' + res.feedback).parent().find('span').empty().append(res.text);
                    jQuery('.a_' + res.feedback).html(res.action);
                }
                obj.removeClass('disabled');
            });
        },
        initFeedbackUploader: function (id)
        {
            var errorHandler = function (event, id, fileName, reason) {
                if ($('#attachments_upload .qq-upload-list .errorUploadMsg').length > 0) {
                    $('#attachments_upload .qq-upload-list .errorUploadMsg').html(mooPhrase.__('tmaxsize'));
                } else {
                    $('#attachments_upload .qq-upload-list').prepend('<div class="errorUploadMsg">' + mooPhrase.__('tmaxsize') + '</div>');
                }
                $('#attachments_upload .qq-upload-fail').remove();
            };

            if ($('#uploader').length > 0) {
                var uploader = new mooFileUploader.fineUploader({
                    element: $('#uploader')[0],
                    autoUpload: false,
                    text: {
                        uploadButton: '<div class="upload-section"><i class="material-icons">camera_alt</i>' + mooPhrase.__('tdesc') + '</div>'
                    },
                    validation: {
                        allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
                    },
                    request: {
                        endpoint: mooConfig.url.base + "/feedbacks/upload/",
                    },
                    callbacks: {
                        onError: errorHandler,
                        onComplete: function (id, fileName, response) {
                            var attachs = $('#FeedbackAttachments').val();
                            if (response.feedback_image_id)
                            {
                                if (attachs == '')
                                {
                                    $('#FeedbackAttachments').val(response.feedback_image_id);
                                }
                                else
                                {
                                    $('#FeedbackAttachments').val(attachs + ',' + response.feedback_image_id);
                                }
                            }
                            else if (response.result == 0)
                            {
                                mooAlert.alert(response.message);
                            }
                        }
                    }
                });

                $('#triggerUpload').unbind('click');
                $('#triggerUpload').click(function () {
                    uploader.uploadStoredFiles();
                });
            }
        },
        createFeedback: function (type)
        {
            mooButton.disableButton('createButton');
            jQuery.post(mooConfig.url.base + "/feedbacks/feedbacks/ajax_save", jQuery("#createForm").serialize(), function (data) {
                mooButton.enableButton('createButton');
                var json = $.parseJSON(data);

                if (json.result == 1) {
                    if (json.url)
                    {
                        window.location = json.url;
                    }
                    else
                    {
                        $("#add").attr("href", $("#add").attr("href") + json.id);
                        jQuery.post(mooConfig.url.base + "/feedbacks/feedbacks/ajax_thanks/", 'id=' + json.id + '&approved=' + json.approved, function (data) {
                            jQuery('#themeModal .modal-content').empty().append(data);
                            //jQuery('#themeModal').modal();
                        })
                    }
                }
                else
                {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                    if ($('.spinner').length > 0) {
                        $('.spinner').remove();
                    }
                    if (jQuery('#captcha').length > 0)
                    {
                        grecaptcha.reset();
                    }
                }
            });
        },
        initFeedbackImage: function ()
        {
            jQuery('.attach_remove').each(function () {
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
        removeFeedbackImage: function (id)
        {
            jQuery('#attach' + id).remove();
            var ids = jQuery('#FeedbackAttachments').val().split(',');
            ids.splice(jQuery.inArray(id, ids), 1);
            jQuery('#FeedbackAttachments').val(ids.join());
        }
    }

    var approveFB = function(){
        $('.fb_approve').unbind('click');
        $('.fb_approve').click(function(){
           var data = $(this).data();
           var approveUrl = mooConfig.url.base + '/feedbacks/do_active/' + data.id + '/approved/' + data.status;
           mooAlert.confirm(data.confirm, approveUrl);
        });
    }
    
    var featureFB = function(){
        $('.fb_feature').unbind('click');
        $('.fb_feature').click(function(){
           var data = $(this).data();
           var approveUrl = mooConfig.url.base + '/feedbacks/do_active/' + data.id + '/featured/' + data.status;
           mooAlert.confirm(data.confirm, approveUrl);
        });
    }
    
    var deleteFB = function(){
        $('.fb_delete').unbind('click');
        $('.fb_delete').click(function(){
           var data = $(this).data();
           var approveUrl = mooConfig.url.base + '/feedbacks/delete/' + data.id;
           mooAlert.confirm(data.confirm, approveUrl);
        });
    }

    var initOnLoadMore = function() {
        $('.fb_vote').unbind('click');
        $('.fb_vote').click(function(){
            var fb_id = $(this).attr('ref');
            jQuery.feedback.vote(fb_id, $(this));
        });
        mooBehavior.initMoreResults();
    }

    return {
        initOnCreate: function () {
            initOnCreate();
        },
        initOnIndex: function () {
            initOnIndex();
        },
        initOnView: function () {
            initOnView();
        },
        initShortcut: function() {
            initShortcut();
        },
        initOnLoadMore: function(){
            initOnLoadMore();
        }
    }


}));
