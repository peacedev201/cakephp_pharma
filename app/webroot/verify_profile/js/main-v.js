(function(root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooFileUploader', 'mooAlert', 'mooPhrase', 'mooGlobal', 'mooButton'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooVerifyProfile = factory();
    }
}(this, function($, mooFileUploader, mooAlert, mooPhrase, mooGlobal, mooButton) {
    
    var initAppMemberProfile = function () {
        $('#requestVerification').unbind('click');
        $('#requestVerification').click(function () {
            $.post(mooConfig.url.base + '/profile/verify/ajax_request_verification', {noCache: 1}, function () {
                window.location.href = mooConfig.url.base + '/profile/verify/success_verification?app_no_tab=1';
            });
        });
        
        $('#reverifyVerification').unbind('click');
        $('#reverifyVerification').click(function () {
            $.post(mooConfig.url.base + '/profile/verify/ajax_reverification', {noCache: 1}, function () {
                window.location.reload();
            });
        });
        
        $('#verifyVerification').unbind('click');
        $('#verifyVerification').click(function () {
            var data = $(this).data();
            $.post(mooConfig.url.base + '/profile/verify/ajax_verify', {id: data.id, noCache: 1}, function () {
                window.location.href = mooConfig.url.base + '/profile/verify/success_verify/' + data.id + ' ?app_no_tab=1';
            });
        });
        
        $('#denyVerification').unbind('click');
        $('#denyVerification').click(function () {
            var data = $(this).data();
            $('#themeModal .modal-content').html('');
            $('#themeModal .modal-content').spin('small');
            $('#themeModal .modal-content').load(mooConfig.url.base + '/profile/verify/ajax_unverify/' + data.id, function () {
                $('#themeModal .modal-content').spin(false);
                $('#themeModal').modal('show');
            });
        });
    };
    
    var initMemberProfile = function() {
        $('#verifyByAdmin').unbind('click');
        $('#verifyByAdmin').click(function() {            
            var data = $(this).data();
            $.fn.SimpleModal({
                callback: function() {
                    $.post(mooConfig.url.base + '/profile/verify/ajax_verify', {id: data.id}, function() {
                        window.location.reload();
                    });
                },
                btn_ok: mooPhrase.__('confirm'),
                btn_cancel: mooPhrase.__('cancel'),
                title: mooPhrase.__('verify_by_admin'),
                contents: mooPhrase.__('are_you_sure_verify_this_profile'),
                model: "confirm"
            }).showModal();
        });

        $('#reverifyProfile').unbind('click');
        $('#reverifyProfile').click(function() {            
            $.fn.SimpleModal({
                callback: function() {
                    $.post(mooConfig.url.base + '/profile/verify/ajax_reverification', {noCache: 1}, function() {
                        window.location.reload();
                    });
                },
                btn_ok: mooPhrase.__('confirm'),
                btn_cancel: mooPhrase.__('cancel'),
                title: mooPhrase.__('verification_pending'),
                contents: mooPhrase.__('are_you_sure_cancel_and_verify_again'),
                model: "confirm"
            }).showModal();
        });

        $('#requestVerification').unbind('click');
        $('#requestVerification').click(function() {            
            $.fn.SimpleModal({
                callback: function() {
                    $.post(mooConfig.url.base + '/profile/verify/ajax_request_verification', {noCache: 1}, function() {
                        window.location.reload();
                    });
                },
                btn_ok: mooPhrase.__('confirm'),
                btn_cancel: mooPhrase.__('cancel'),
                title: mooPhrase.__('request_verification'),
                contents: mooPhrase.__('are_you_sure_request_verification'),
                model: "confirm"
            }).showModal();
        });

        $('#requestPending').unbind('click');
        $('#requestPending').click(function() {            
            var data = $(this).data();
            $.fn.SimpleModal({
                callback: function() {
                    $.post(mooConfig.url.base + '/profile/verify/ajax_verify', {id: data.id}, function() {
                        window.location.reload();
                    });
                },
                title: mooPhrase.__('verification_pending'),
                contents: mooPhrase.__('are_you_sure_verify_this_profile'),
                model: "content",
                onAppend: function() {
                    var _seft = this;
                    var btVerify = $("<a>").attr({
                        "title": mooPhrase.__('verify'),
                        "class": "btn btn-action"
                    }).click(function() {
                        _seft.options.callback();
                        _seft.hideModal();
                    }).text(mooPhrase.__('verify'));
                    var btUnverify = $("<a>").attr({
                        "data-backdrop": "true",
                        "data-dismiss": "",
                        "data-toggle": "modal",
                        "data-target": "#themeModal",
                        "href": mooConfig.url.base + '/profile/verify/ajax_unverify/' + data.id,
                        "title": mooPhrase.__('deny'),
                        "class": "btn btn-action"
                    }).click(function() {
                        _seft.hideModal();
                    }).text(mooPhrase.__('deny'));
                    var btCancel = $("<a>").attr({
                        "title": mooPhrase.__('cancel'),
                        "class": "button button-action"
                    }).click(function() {
                        _seft.hideModal();
                    }).text(mooPhrase.__('cancel'));
                    $("#simple-modal").find(".simple-modal-footer").append(btVerify).append(btUnverify).append(btCancel);
                }
            }).showModal();
        });
    };

    var initEditProfile = function() {        
        var sClass = 'btn btn-action';
        var sContent = mooPhrase.__('edit_these_verified_information');
        if (mooConfig.isApp) {
            sContent = '<div class="mooApp_pv_simple-modal-body">' + mooPhrase.__('edit_these_verified_information') + '</div>';
            sClass = 'mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored';
        }

        $.fn.SimpleModal({
            title: mooPhrase.__('please_confirm'),
            contents: sContent,
            model: "content",
            onAppend: function() {
                var _seft = this;
                var btConfirm = $("<a>").attr({
                    "title": mooPhrase.__('confirm'),
                    "class": sClass
                }).click(function() {
                    _seft.hideModal();
                }).text(mooPhrase.__('confirm'));
                $("#simple-modal").find(".simple-modal-footer").append(btConfirm);
            }
        }).showModal();
    };

    var initAjaxAvatar = function() {
        $('#loadAjaxAvatar').unbind('click');
        $('#loadAjaxAvatar').click(function() {
            $('#themeModal .modal-content').html('');
            $('#themeModal .modal-content').spin('small');	
            $('#themeModal .modal-content').load(mooConfig.url.base + '/profile/verify/ajax_avatar', function(){
                $('#themeModal .modal-content').spin(false);
                $('#themeModal').modal('show');
            });
        });
    };

    var initAjaxUnverify = function() {
        $('#otherReason').unbind('click');
        $('#otherReason').click(function() {
            if (this.checked === true) {
                $('#otherReasonContent').show();
            } else {
                $('#otherReasonContent').hide();
            }
        });

        $('#unverifyAction').unbind('click');
        $('#unverifyAction').click(function() {
            var iUserId = $(this).data().id;
            $('#unverifyAction').spin('small');
            mooButton.disableButton('unverifyAction');
            $.post(mooConfig.url.base + '/profile/verify/ajax_unverify_process', $("#unverifyForm").serialize(), function(data) {
                mooButton.enableButton('unverifyAction');
                $('#unverifyAction').spin(false);
                var json = $.parseJSON(data);
                if (json.result === 1) {
                    if (mooConfig.isApp) {
                        window.location.href = mooConfig.url.base + '/profile/verify/deny_verification/' + iUserId + ' ?app_no_tab=1';
                    } else {
                        window.location.reload();
                    }
                } else {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });
    };

    var initVerifyProfile = function(iItemLimit) {        
        var iItemLimit = iItemLimit;
        var newPhotos = new Array();
        var uploaderVerify = new mooFileUploader.fineUploader({
            element: $('#photos_upload')[0],
            autoUpload: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>' + mooPhrase.__('drag_or_click_here_to_upload_photo') + '</div>'
            },
            validation: {
                allowedExtensions: mooConfig.photoExt,
                sizeLimit: mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/profile/verify/ajax_upload"
            },
            callbacks: {
                onComplete: function(id, fileName, response) {
                    newPhotos.push(response.photo);
                    $('#new_photos').val(newPhotos.join(','));
                    $('#nextStep').show();
                },
                onSubmit: function(id, fileName) {
                    if (typeof this.iItemCount === 'undefined') {
                        this.iItemCount = 0;
                        this.iShowMessage = 0;
                    }
                    
                    this.iItemCount++;
                    if (iItemLimit > 0 && iItemLimit < this.iItemCount) {
                        this.iItemCount--;
                        if (this.iShowMessage === 0) {
                            this.iShowMessage = 1;
                            
                            var sClass = 'btn btn-action';
                            var sContent = mooPhrase.__('the_number_documents_for_verification_request');
                            if (mooConfig.isApp) {
                                sContent = '<div class="mooApp_pv_simple-modal-body">' + mooPhrase.__('the_number_documents_for_verification_request') + '</div>';
                                sClass = 'mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored';
                            }
                            
                            $.fn.SimpleModal({
                                title: mooPhrase.__('please_confirm'),
                                contents: sContent,
                                model: "content",
                                onAppend: function() {
                                    var _seft = this;
                                    var btConfirm = $("<a>").attr({
                                        "title": mooPhrase.__('confirm'),
                                        "class": sClass
                                    }).click(function() {
                                        _seft.hideModal();
                                    }).text(mooPhrase.__('confirm'));
                                    $("#simple-modal").find(".simple-modal-footer").append(btConfirm);
                                }
                            }).showModal();
                        }
                        return false;
                    }
                },
                onCancel: function(id, fileName) {
                    if (typeof this.iItemCount !== 'undefined') {
                        this.iItemCount--;
                    }
                }
            }
        });

        $('#triggerUpload').unbind('click');
        $('#triggerUpload').click(function() {        
            if (typeof uploaderVerify.iItemCount !== 'undefined' && uploaderVerify.iItemCount === iItemLimit) {
                uploaderVerify.uploadStoredFiles();
            } else {
                var sClass = 'btn btn-action';
                var sContent = mooPhrase.__('the_number_documents_for_verification_request');
                if (mooConfig.isApp) {
                    sContent = '<div class="mooApp_pv_simple-modal-body">' + mooPhrase.__('the_number_documents_for_verification_request') + '</div>';
                    sClass = 'mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored';
                }

                $.fn.SimpleModal({
                    title: mooPhrase.__('please_confirm'),
                    contents: sContent,
                    model: "content",
                    onAppend: function() {
                        var _seft = this;
                        var btConfirm = $("<a>").attr({
                            "title": mooPhrase.__('confirm'),
                            "class": sClass
                        }).click(function() {
                            _seft.hideModal();
                        }).text(mooPhrase.__('confirm'));
                        $("#simple-modal").find(".simple-modal-footer").append(btConfirm);
                    }
                }).showModal();
                
                return false;
            }
        });

        $('#nextStep').unbind('click');
        $('#nextStep').click(function() {
            mooButton.disableButton('nextStep');
            $('#nextStep').spin('small');
            $('#uploadPhotoForm').submit();
        });
    };

    return{
        initAppMemberProfile: function () {
            initAppMemberProfile();
        },
        initMemberProfile: function() {
            initMemberProfile();
        },
        initEditProfile: function() {
            initEditProfile();
        },
        initAjaxAvatar: function() {
            initAjaxAvatar();
        },
        initAjaxUnverify: function() {
            initAjaxUnverify();
        },
        initVerifyProfile: function(iItemLimit) {
            initVerifyProfile(iItemLimit);
        }
    };
}));