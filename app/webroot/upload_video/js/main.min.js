/* Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooPhrase', 'mooButton', 'mooGlobal', 'mooFileUploader', 'mooAjax', 'mooAlert'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooUploadVideo = factory();
    }
}(this, function ($, mooPhrase, mooButton, mooGlobal, mooFileUploader, mooAjax, mooAlert) {
    var _plugin3rd_name = 'UploadVideo';
    var initAjaxUpload = function () {
        $('#saveBtn').unbind('click');
        $('#saveBtn').click(function () {
            mooButton.disableButton('saveBtn');
            $('#saveBtn').spin('tiny');
            mooAjax.post({
                url: window.mooConfig.url.base + "/upload_video/upload_videos/save_upload",
                data: $("#createForm").serialize()
            }, function (data) {
                var json = $.parseJSON(data);
                if (json.result === 1) {
                    if (window.mooConfig.isApp) {
                        window.location.href = window.mooConfig.url.base + '/upload_video/upload_videos/successfully?app_no_tab=1';
                    } else {
                        $(".error-message").hide();
                        $('#createForm').html(mooPhrase.__('upload_video_phrase_0'));
                    }
                } else {
                    $('#saveBtn').spin(false);
                    mooButton.enableButton('saveBtn');

                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
        });

        var oUploader = new mooFileUploader.fineUploader({
            element: $('#video_upload')[0],
            autoUpload: false,
            multiple: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">file_upload</i>' + mooPhrase.__('upload_video_phrase_2') + '</div>'
            },
            validation: {
                multiple: false,
                acceptFiles: 'video/*',
                sizeLimit: window.mooVideoConfig.videoSizeLimit,
                allowedExtensions: window.mooVideoConfig.videoExtentsion
            },
            request: {
                endpoint: window.mooConfig.url.base + "/upload_video/upload_videos/process_upload"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function (id, fileName, response) {
                    if (response.success) {
                        $('#saveBtn').show();
                        $('#destination').val(response.filename);
                    }
                }
            }
        });

        $('#triggerUpload').unbind('click');
        $('#triggerUpload').click(function () {
            if (oUploader._storedFileIds.length) {
                oUploader.uploadStoredFiles();
                $(".error-message").hide();
            } else {
                $(".error-message").html(mooPhrase.__('upload_video_phrase_3'));
                $(".error-message").show();
            }
        });
    };
    function showNoAlert(){
        message = mooPhrase.__('information');
        msg = mooPhrase.__("please_contact_any_sales_reps_visiting_you")+". <br><br>"+mooPhrase.__("gif_msg_part2")+".<br>("+mooPhrase.__("gif_msg_part3")+").<br>"+mooPhrase.__("gif_msg_part4")+". <br><br><a href='/faqs/view/20/how-to-get-free-item-from-sales-member'>"+mooPhrase.__("gif_msg_part5")+"</a>, "+mooPhrase.__("or")+", <a href='javascript:void(0);'>"+mooPhrase.__("gif_msg_part6")+"</a>";
        // text = 'close';
        // Set title
        $($('#portlet-config  .modal-header .modal-title')[0]).html(message);
        // Set content
        $($('#portlet-config  .modal-body')[0]).html(msg);
        // OK callback, remove all events bound to this element
        $('#portlet-config  .modal-footer .ok').off("click").click(function(){
            callback();
            $('#portlet-config').modal('hide');
        });
        $('#portlet-config .blue.ok').css('display','none');
        $('#portlet-config').modal('show');
    }
    var initVideoUploadActivityForm = function () {
        var sUploadButton = '<div class="upload-section"><i class="material-icons">videocam</i></div>';
        if (window.mooConfig.isApp) {
            sUploadButton = '<div class="upload-section"><span class="mdl-button mdl-js-button mdl-button--icon"><i class="material-icons">videocam</i></span></div>';
        }
        if($('#videoPcFeed').hasClass('show-video')){
            new mooFileUploader.fineUploader({
                element: $('#videoPcFeed')[0],
                multiple: false,
                text: {
                    uploadButton: sUploadButton
                },
                validation: {
                    multiple: false,
                    acceptFiles: 'video/*',
                    sizeLimit: window.mooVideoConfig.videoSizeLimit,
                    allowedExtensions: window.mooVideoConfig.videoExtentsion
                },
                request: {
                    endpoint: window.mooConfig.url.base + "/upload_video/upload_videos/process_upload"
                },
                callbacks: {
                    onError: mooGlobal.errorHandler,
                    onSubmit: function (id, fileName) {
                        $('#video_pc_feed_preview').show();
                        $('#video_thumb_preview').spin(true);
                    },
                    onComplete: function (id, fileName, response) {
                        if (response.success) {                    
                            $('#video_thumb_preview > img').attr('src', response.thumb);
                            $('#video_destination').val(response.filename);
                            $('#video_thumb_preview > img').show();
                            $('#video_thumb_preview').spin(false);

                            // Hide photo, trigger 3dr
                            $('body').trigger('startFeedPlugin3drCallback', [{plugin_name: _plugin3rd_name}]);
                            hideUploadPhoto();
                        }

                        if (response.limitation === 1) {
                            resetVideoUploadActivityForm();

                            // show modal
                            $.get(window.mooConfig.url.base + "/upload_video/upload_videos/limitation", {isFeed: 1}, function (data) {
                                $("#themeModal").html('<div class="modal-dialog"><div class="modal-content">' + data + '</div></div>');
                                $("#themeModal").modal();
                            });
                        }
                        
                        // Hide tooltip
                        $('#videoPcFeed').tooltip('hide');
                    }
                }
            });

            $('body').on('afterPostWallCallbackSuccess', function () {
                $('body').trigger('stopFeedPlugin3drCallback', [{plugin_name: _plugin3rd_name}]);
                resetVideoUploadActivityForm();
            });

            $('body').on('afterUploadWallPhotoCallback', function () {
                resetVideoUploadActivityForm();
            });

            $('body').on('enablePluginsCallback', function (e, data) {
                var _plugins = data.plugins;
                if (_plugins.indexOf(_plugin3rd_name) > -1) {
                    showUploadVideoOnWall();
                }
            });

            $('body').on('disablePluginsCallback', function (e, data) {
                var _plugins = data.plugins;
                if (_plugins.indexOf(_plugin3rd_name) > -1) {
                    hideUploadVideoOnWall();
                }
            });

            $('#closeUploadVideo').unbind('click');
            $('#closeUploadVideo').click(function () {
                showUploadPhoto();
                resetVideoUploadActivityForm();
                $('#closeUploadVideo').tooltip('hide');
                $('body').trigger('stopFeedPlugin3drCallback', [{plugin_name: _plugin3rd_name}]);
            });
        }
        else{
            $('#videoPcFeed').append(sUploadButton);
            $('#videoPcFeed').on('click',function(){
                showNoAlert();
            });
        }
    };

    var hideUploadVideoOnWall = function () {
        $('#videoPcFeed').hide();
        resetVideoUploadActivityForm();
    };

    var showUploadVideoOnWall = function () {
        $('#videoPcFeed').show();
    };

    var resetVideoUploadActivityForm = function () {
        $('#title').val('');
        $('#description').val('');
        $('#video_destination').val('');
        $('#video_pc_feed_preview').hide();
    };
    
    var showUploadPhoto = function () {
        $('#select-2').show();
        $('#wall_photo').val('');
        $('#wall_photo_preview').hide();
    };
    
    var hideUploadPhoto = function () {
        $('#select-2').hide();
        $('#wall_photo_preview').hide();
        $('#wall_photo_preview').find('span').each(function () {
            if ($(this).attr('id') !== 'addMoreImage') {
                $(this).remove();
            }
        });

        $('#wall_photo_preview').find('#addMoreImage').hide();
    };

    return{
        initAjaxUpload: initAjaxUpload,
        initVideoUploadActivityForm: initVideoUploadActivityForm
    };
}));
