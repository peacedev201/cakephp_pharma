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
        var tiny_plugins = "advlist autolink lists link image charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking save table  directionality emoticons template paste textcolor";
        tinyMCE.remove("textarea#editor");
        tinyMCE.init({
            selector: "textarea#editor",
            language : mooConfig.tinyMCE_language,
            theme: "modern",
            skin: 'light',
            plugins: [tiny_plugins],
            toolbar1: "styleselect | bold italic | bullist numlist outdent indent | forecolor backcolor emoticons | link unlink anchor image media | preview fullscreen code",
            image_advtab: true,
            directionality: "ltr",
            width: '100%',
            menubar: false,
            forced_root_block : 'div',
            relative_urls : false,
            remove_script_host : true,
            document_base_url : mooConfig.url.base,
            browser_spellcheck: true,
            contextmenu: false
        });
            
        var uploader1 = new mooFileUploader.fineUploader({
            element: $('#attachments_upload')[0],
            autoUpload: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">attach_file</i>' + mooPhrase.__('upload_button_text') +' </div>'
            },
            validation: {
                allowedExtensions: mooConfig.attachmentExt,
                sizeLimit: mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/feedbacks/attachments/" + $('#plugin_feedback_id').val()
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function(id, fileName, response) {
                    var attachs = $('#attachments').val();

                    if (response.attachment_id){
                        tinyMCE.activeEditor.insertContent('<p><a href="' + mooConfig.url.base + '/attachments/download/' + response.attachment_id + '" class="attached-file">' + response.original_filename + '</a></p><br>');
                        if ( attachs == '' ){
                            $('#attachments').val( response.attachment_id );
                        }
                        else{
                            $('#attachments').val(attachs + ',' + response.attachment_id);
                        }
                    }else if(id || response.thumb){
                        tinyMCE.activeEditor.insertContent('<p align="center"><a href="' + response.large + '" class="attached-image"><img src="' + response.thumb + '" width="100%"></a></p><br>');
                    }
                }
            }
        });
                
        $('#triggerUpload1').unbind('click');
        $('#triggerUpload1').click(function() {
            uploader1.uploadStoredFiles();
        });
              
        // toggleUploader
        $('#toggleUploader').unbind('click');
        $('#toggleUploader').on('click', function(){
            $('#images-uploader1').slideToggle();
        });
        
        
        
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

    var initOnCreateInApp = function () {
        var tiny_plugins = "advlist autolink lists link image charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking save table  directionality emoticons template paste textcolor";
        tinyMCE.remove("textarea#editor");
        tinyMCE.init({
            selector: "textarea#editor",
            language : mooConfig.tinyMCE_language,
            theme: "modern",
            skin: 'light',
            plugins: [tiny_plugins],
            toolbar1: "styleselect | bold italic | bullist numlist outdent indent | forecolor backcolor emoticons | link unlink anchor image media | preview fullscreen code",
            image_advtab: true,
            directionality: "ltr",
            width: '100%',
            menubar: false,
            forced_root_block : 'div',
            relative_urls : false,
            remove_script_host : true,
            document_base_url : mooConfig.url.base,
            browser_spellcheck: true,
            contextmenu: false
        });
            
        var uploader1 = new mooFileUploader.fineUploader({
            element: $('#attachments_upload')[0],
            autoUpload: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">attach_file</i>' + mooPhrase.__('upload_button_text') +' </div>'
            },
            validation: {
                allowedExtensions: mooConfig.attachmentExt,
                sizeLimit: mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/feedbacks/attachments/" + $('#plugin_feedback_id').val()
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function(id, fileName, response) {
                    var attachs = $('#attachments').val();

                    if (response.attachment_id){
                        tinyMCE.activeEditor.insertContent('<p><a href="' + mooConfig.url.base + '/attachments/download/' + response.attachment_id + '" class="attached-file">' + response.original_filename + '</a></p><br>');
                        if ( attachs == '' ){
                            $('#attachments').val( response.attachment_id );
                        }
                        else{
                            $('#attachments').val(attachs + ',' + response.attachment_id);
                        }
                    }else if(id || response.thumb){
                        tinyMCE.activeEditor.insertContent('<p align="center"><a href="' + response.large + '" class="attached-image"><img src="' + response.thumb + '" width="100%"></a></p><br>');
                    }
                }
            }
        });
                
        $('#triggerUpload1').unbind('click');
        $('#triggerUpload1').click(function() {
            uploader1.uploadStoredFiles();
        });
              
        // toggleUploader
        $('#toggleUploader').unbind('click');
        $('#toggleUploader').on('click', function(){
            $('#images-uploader1').slideToggle();
        });
        
        jQuery.feedback.initFeedbackUploader();

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
                        uploadButton: '<div class="upload-section"><i class="icon-camera"></i>' + mooPhrase.__('tdesc') + '</div>'
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
            if (tinyMCE.activeEditor !== null) {
                $('#editor').val(tinyMCE.activeEditor.getContent());
            }
            mooButton.disableButton('createButton');
            jQuery.post(mooConfig.url.base + "/feedbacks/feedbacks/ajax_save", jQuery("#createForm").serialize(), function (data) {
                mooButton.enableButton('createButton');
                var json = $.parseJSON(data);

                if (json.result == 1) {
                    if (json.url)
                    {
                        if (!mooConfig.isApp)
                        {
                            window.location = json.url;
                        }
                        else
                        {
                            window.location = json.url + '?app_no_tab=1';
                        }
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
            id = id.toString();
            jQuery('#attach' + id).remove();
            var ids = jQuery('#FeedbackAttachments').val().split(',');
            var index = ids.indexOf(id);
            if (index > -1) {
                ids.splice(index, 1);
            }
            jQuery('#FeedbackAttachments').val(ids.join());
        }
    }

    var approveFB = function(){
        $('.fb_approve').unbind('click');
        $('.fb_approve').click(function(){
            var data = $(this).data();
            if (!mooConfig.isApp)
            {
                var approveUrl = mooConfig.url.base + '/feedbacks/do_active/' + data.id + '/approved/' + data.status;
                mooAlert.confirm(data.confirm, approveUrl);
            }
            else
            {
                $.fn.SimpleModal({
                    btn_ok : mooPhrase.__('btn_ok'),
                    btn_cancel: mooPhrase.__('btn_cancel'),
                    callback: function () {
                        $.post(mooConfig.url.base + '/feedbacks/do_active/' + data.id + '/approved/' + data.status, function () {
                            window.location.reload();
                        });
                    },
                    title: mooPhrase.__('please_confirm'),
                    contents: data.confirm,
                    model: 'confirm',
                    hideFooter: false,
                    closeButton: false
                }).showModal();
            }
        });
    }
    
    var featureFB = function(){
        $('.fb_feature').unbind('click');
        $('.fb_feature').click(function(){
           var data = $(this).data();
           if (!mooConfig.isApp)
            {
                var approveUrl = mooConfig.url.base + '/feedbacks/do_active/' + data.id + '/featured/' + data.status;
                mooAlert.confirm(data.confirm, approveUrl);
            }
            else
            {
                $.fn.SimpleModal({
                    btn_ok : mooPhrase.__('btn_ok'),
                    btn_cancel: mooPhrase.__('btn_cancel'),
                    callback: function () {
                        $.post(mooConfig.url.base + '/feedbacks/do_active/' + data.id + '/featured/' + data.status, function () {
                            window.location.reload();
                        });
                    },
                    title: mooPhrase.__('please_confirm'),
                    contents: data.confirm,
                    model: 'confirm',
                    hideFooter: false,
                    closeButton: false
                }).showModal();
            }
        });
    }
    
    var deleteFB = function(){
        $('.fb_delete').unbind('click');
        $('.fb_delete').click(function(){
            var data = $(this).data();
            if (!mooConfig.isApp)
            {
                var deleteUrl = mooConfig.url.base + '/feedbacks/delete/' + data.id;
            }
            else
            {
                var deleteUrl = mooConfig.url.base + '/feedbacks/delete/' + data.id + '?app_no_tab=1';
            }
            mooAlert.confirm(data.confirm, deleteUrl);
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
        },
        initOnCreateInApp: function(){
            initOnCreateInApp();
        }
    }


}));
