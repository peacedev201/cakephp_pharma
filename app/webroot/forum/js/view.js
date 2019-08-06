(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooBehavior', 'mooPhrase', 'mooAjax', 'mooGlobal', 'mooTooltip', 'mooAlert', 'mooButton', 'mooFileUploader', 'tinyMCE', 'tagsinput'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooViewForum = factory();
    }
}(this, function ($, mooBehavior, mooPhrase, mooAjax, mooGlobal, mooTooltip, mooAlert, mooButton, mooFileUploader, tinyMCE) {

    var initReplyTopic = function(item_id,params){
        tinyMCE.remove();
        tinyMCE.init({
            selector: ".forum-textarea",
            language : mooConfig.tinyMCE_language,
            theme: "modern",
            skin: 'light',
            plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table contextmenu directionality",
                "emoticons template paste textcolor"
            ],
            toolbar1: "styleselect | bold italic | bullist numlist outdent indent | forecolor backcolor emoticons | link unlink anchor image media | preview fullscreen code | tagmember",
            image_advtab: true,
            height: 200,
            relative_urls : false,
            remove_script_host : false,
            document_base_url : '<?php echo FULL_BASE_URL . $this->request->root?>',
            setDirty: true,
            hidden: false,
            setup: function (editor) {
                editor.addButton('tagmember', {
                    tooltip: 'Tag member',
                    image: mooConfig.url.base + "/forum/img/user-64x64.png",
                    onclick: function () {
                        $.ajax({url: mooConfig.url.base + "/forums/topic/ajax_tag_member", success: function(result){
                            //alert("success"+result);
                            $("#langModal .modal-content").html(result);
                            $("#langModal").modal('show');

                        }});
                    }
                });
                editor.on('focus', function(e) {
                    console.log('focus event', e);
                });
            }
        });
        $('.topic_reply_button').on('click',function(){
            var data = $(this).data();

            if( typeof data.id !== undefined){
                if (tinyMCE.activeEditor !== null) {
                    var description = tinyMCE.activeEditor.getContent();
                    description = description.replace(/&nbsp;|<p>|<\/p>|<ul[^>]*>|<\/ul>|<li>|<\/li>|<ol[^>]*>|<\/ol>| /g,"").trim();
                    if(description.length){
                        $('#description'+data.id).val(tinyMCE.activeEditor.getContent());
                    }else{
                        $('#description'+data.id).val('');
                    }
                }
                $(this).spin('small');

                $.post(mooConfig.url.base + "/forums/topic/save_reply/",$('#topicReplyForm'+data.id).serialize(), function (data) {
                    data = $.parseJSON(data);
                    if(data.result == '1'){
                        window.location = data.redirect;
                    }else if(data.result == '2'){
                        location.reload();
                    }else{
                        $(".forum-error-message").show();
                        $(".forum-error-message").html(data.message);
                    }
                    $('.topic_reply_button').spin(false);
                });
            }
        });

        var newFiles = [];
        var newOriginalFiles = [];

        $('.btn-delete-file').on('click', function(){
            $(this).spin('small');
            var data = $(this).data();
            $.post(mooConfig.url.base + "/forums/topic/delete_file/"+ data.file +'/'+data.id, function (result) {
                newFiles = removeArr(newFiles, data.file);
                newOriginalFiles = removeArr(newOriginalFiles, data.originalfile);
                $('#new_files_'+item_id).val( newFiles.join(',') );
                $('#new_original_files_'+item_id).val( newOriginalFiles.join(',') );

                $('#file_item_'+data.id).remove();
            });
        });

        if($('#new_files_'+item_id).val() != '' && $('#new_original_files_'+item_id).val() != ''){
            var str = $('#new_files_'+item_id).val();
            newFiles = str.split(",");
            str = $('#new_original_files_'+item_id).val();
            newOriginalFiles = str.split(",");
        }

        var uploader = new mooFileUploader.fineUploader({
            element: $('#topic_file_upload_'+item_id)[0],
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>'+mooPhrase.__('drag_file')+'</div>'
            },
            validation: {
                allowedExtensions:  params.split(','),
                sizeLimit: mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/forums/topic/upload"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function(id, fileName, response) {
                    if (jQuery.isEmptyObject(response))
                    {
                        return;
                    }
                    file = jQuery(this.getItemByFileId(id));
                    element_delete = $('<a href="javascript:void(0);" file="'+response.document_file+'" original-file="'+response.original_filename+'">'+mooPhrase.__('delete')+'<a/>');
                    file.find('.qq-upload-status-text').append(element_delete);
                    element_delete.click(function(){
                        mooAjax.post({
                            url : mooConfig.url.base + '/forums/topic/delete_file/'+$(this).attr('file')
                        }, function(data){

                        });
                        newFiles = removeArr(newFiles, $(this).attr('file'));
                        newOriginalFiles = removeArr(newOriginalFiles, $(this).attr('original-file'));
                        $('#new_files_'+item_id).val( newFiles.join(',') );
                        $('#new_original_files_'+item_id).val( newOriginalFiles.join(',') );
                        $(this).parent().parent().remove();
                    });

                    newFiles.push( response.document_file );
                    newOriginalFiles.push( response.original_filename );
                    $('#new_files_'+item_id).val( newFiles.join(',') );
                    $('#new_original_files_'+item_id).val( newOriginalFiles.join(',') );
                    $('#nextStep').show();
                }
            }
        });

        var uploader2 = new mooFileUploader.fineUploader({
            element: $('#attachments_upload_'+item_id)[0],
            autoUpload: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="fa fa-file-text-o"></i>' + mooPhrase.__('drag_photo') +' </div>'
            },
            validation: {
                allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
                sizeLimit: mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/forum/forum_upload/attachments/"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function(id, fileName, response) {
                    if(response.thumb){
                        $('#forum_topic_photo_ids_'+item_id).val($('#forum_topic_photo_ids_'+item_id).val() + ',' + response.photo_id);
                        tinyMCE.activeEditor.insertContent('[url=' + response.large + '][img]' + response.thumb + '[/img][/url]');
                    }
                }
            }
        });

        $('#triggerUpload_'+item_id).click(function() {
            uploader2.uploadStoredFiles();
        });

        // toggleUploader
        $('#toggleUploader_'+item_id).unbind('click');
        $('#toggleUploader_'+item_id).on('click', function(){
            $('#images-uploader-'+item_id).slideToggle();
        });

        $('.quote-reply').on('click', function(){
            goToByScroll('post_reply_form');
            var data = $(this).data();
            $.post(mooConfig.url.base + "/forums/topic/get_quote/"+ data.id, function (data) {
                data = $.parseJSON(data);
                tinyMCE.activeEditor.insertContent(data.content);
                reFocus();
            });
        });

        reFocus();
    }

    var initCreateTopic = function(params){
        tinyMCE.init({
            selector: ".forum-textarea",
            language : mooConfig.tinyMCE_language,
            theme: "modern",
            skin: 'light',
            plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table contextmenu directionality",
                "emoticons template paste textcolor"
            ],
            toolbar1: "styleselect | bold italic | bullist numlist outdent indent | forecolor backcolor emoticons | link unlink anchor image media | preview fullscreen code | tagmember",
            image_advtab: true,
            height: 200,
            relative_urls : false,
            remove_script_host : false,
            document_base_url : '<?php echo FULL_BASE_URL . $this->request->root?>',
            setup: function (editor) {
                editor.addButton('tagmember', {
                    tooltip: 'Tag member',
                    image: mooConfig.url.base + "/forum/img/user-64x64.png",
                    onclick: function () {
                        $.ajax({url: mooConfig.url.base + "/forums/topic/ajax_tag_member", success: function(result){
                            //alert("success"+result);
                            $("#langModal .modal-content").html(result);
                            $("#langModal").modal('show');

                        }});
                    }
                });
                editor.on('focus', function(e) {
                    console.log('focus event', e);
                });
            }
        });

        $('#button_save_topic').on('click', function(){

            var description = tinyMCE.activeEditor.getContent();
            description = description.replace(/&nbsp;|<p>|<\/p>|<ul[^>]*>|<\/ul>|<li>|<\/li>|<ol[^>]*>|<\/ol>| /g,"").trim();

            if(description.length){
                $('#description').val(tinyMCE.activeEditor.getContent());
            }else{
                $('#description').val('');
            }

            $(this).spin('small');

            $.post(mooConfig.url.base + "/forums/topic/save/",$('#createTopicForm').serialize(), function (data) {
                data = $.parseJSON(data);
                if(data.result == '1'){
                    if (!mooConfig.isApp) {
                        window.location = data.redirect;
                    }else{
                        window.location = data.redirect+ '?app_no_tab=1';
                    }
                }else{
                    $(".error-message").show();
                    $(".error-message").html(data.message);
                }
                $('#button_save_topic').spin(false);
            });
        });

        var newFiles = [];
        var newOriginalFiles = [];
        if($('#new_files').val() != '' && $('#new_original_files').val() != ''){
            var str = $('#new_files').val();
            newFiles = str.split(",");
            str = $('#new_original_files').val();
            newOriginalFiles = str.split(",");
        }

        $('.btn-delete-file').on('click', function(){
            $(this).spin('small');
            var data = $(this).data();
            $.post(mooConfig.url.base + "/forums/topic/delete_file/"+ data.file +'/'+data.id, function (result) {
                newFiles = removeArr(newFiles, data.file);
                newOriginalFiles = removeArr(newOriginalFiles, data.originalfile);
                $('#new_files').val( newFiles.join(',') );
                $('#new_original_files').val( newOriginalFiles.join(',') );

                $('#file_item_'+data.id).remove();
            });
        });
        var uploader = new mooFileUploader.fineUploader({
            element: $('#topic_file_upload')[0],
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>'+mooPhrase.__('drag_file')+'</div>'
            },
            validation: {
                allowedExtensions:  params.split(','),
                sizeLimit: mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/forums/topic/upload"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function(id, fileName, response) {
                    if (jQuery.isEmptyObject(response))
                    {
                        return;
                    }
                    file = jQuery(this.getItemByFileId(id));
                    element_delete = $('<a href="javascript:void(0);" file="'+response.document_file+'" original-file="'+response.original_filename+'">'+mooPhrase.__('delete')+'<a/>');
                    file.find('.qq-upload-status-text').append(element_delete);
                    element_delete.click(function(){
                        mooAjax.post({
                            url : mooConfig.url.base + '/forums/topic/delete_file/'+$(this).attr('file')
                        }, function(data){

                        });
                        newFiles = removeArr(newFiles, $(this).attr('file'));
                        newOriginalFiles = removeArr(newOriginalFiles, $(this).attr('original-file'));
                        $('#new_files').val( newFiles.join(',') );
                        $('#new_original_files').val( newOriginalFiles.join(',') );
                        $(this).parent().parent().remove();
                    });

                    newFiles.push( response.document_file );
                    newOriginalFiles.push( response.original_filename );
                    $('#new_files').val( newFiles.join(',') );
                    $('#new_original_files').val( newOriginalFiles.join(',') );
                    $('#nextStep').show();
                }
            }
        });

        if (mooPhrase.__('drag_photo') != '')
            text_upload_button = '<div class="upload-section"><i class="material-icons">photo_camera</i>'+ mooPhrase.__('drag_photo') +'</div>';
        else
            text_upload_button = '<div class="upload-section"></div>';

        var uploader1 = new mooFileUploader.fineUploader({
            element: $('#topic_thumnail')[0],
            multiple: false,
            text: {
                uploadButton: text_upload_button
            },
            validation: {
                allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
                sizeLimit: mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/forums/topic/upload_avatar"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function(id, fileName, response) {
                    $('#topic_thumnail_preview > img').attr('src', response.thumb);
                    $('#topic_thumnail_preview > img').show();
                    $('#thumb').val(response.file);
                }
            }
        });

        var uploader2 = new mooFileUploader.fineUploader({
            element: $('#attachments_upload')[0],
            autoUpload: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="fa fa-file-text-o"></i>' + mooPhrase.__('drag_photo') +' </div>'
            },
            validation: {
                allowedExtensions: ['jpg', 'jpeg', 'gif', 'png'],
                sizeLimit: mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/forum/forum_upload/attachments/"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function(id, fileName, response) {
                    if(response.thumb){
                            $('#forum_topic_photo_ids').val($('#forum_topic_photo_ids').val() + ',' + response.photo_id);
                            tinyMCE.activeEditor.insertContent('[url=' + response.large + '][img]' + response.thumb + '[/img][/url]');
                        }
                    }
            }
        });

        $('#triggerUpload').click(function() {
            uploader2.uploadStoredFiles();
        });

        // toggleUploader
        $('#toggleUploader').unbind('click');
        $('#toggleUploader').on('click', function(){
            $('#images-uploader').slideToggle();
        });
    }

    var initSignature = function(params){
        tinyMCE.remove();
        tinyMCE.init({
            selector: ".forum-textarea",
            language : mooConfig.tinyMCE_language,
            theme: "modern",
            skin: 'light',
            plugins: [
                "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars code fullscreen",
                "insertdatetime media nonbreaking save table contextmenu directionality",
                "emoticons template paste textcolor"
            ],
            toolbar1: "styleselect | bold italic | bullist numlist outdent indent | forecolor backcolor emoticons | link unlink anchor image media | preview fullscreen code",
            image_advtab: true,
            height: 200,
            relative_urls : false,
            remove_script_host : false,
            document_base_url : '<?php echo FULL_BASE_URL . $this->request->root?>'
        });

        $('#btn_signature').on('click', function(){
            var description = tinyMCE.activeEditor.getContent();
            description = description.replace(/&nbsp;|<p>|<\/p>/g,"").trim();

            if(description.length){
                $('#signature').val(tinyMCE.activeEditor.getContent());
            }else{
                $('#signature').val('');
            }

            $(this).spin('small');

            $.post(mooConfig.url.base + "/forums/topic/signature/",$('#formSignature').serialize(), function (data) {
                data = $.parseJSON(data);
                if(data.result == '1'){
                    $('#msg_success').show();
                }else{
                    $('#msg_success').hide();
                    $(".error-message").show();
                    $(".error-message").html(data.message);
                }
                $('#btn_signature').spin(false);
            });
        });
    }

    function removeArr(array, element) {
        const index = array.indexOf(element);

        if (index !== -1) {
            array.splice(index, 1);
        }
        return array;
    }

    function reFocus() {
        setTimeout( function() {
            tinyMCE.execCommand('mceFocus', false, 'description' + $('.topic_reply_button').data('id'));
        }, 1);
    }
    function goToByScroll(id){
        // Scroll
        $('html,body').animate({
                scrollTop: $("#"+id).offset().top},
            'slow');
    }
    return{
        initReplyTopic : initReplyTopic,
        initCreateTopic : initCreateTopic,
        initSignature : initSignature,
    }
}));