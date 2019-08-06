/* Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

(function(root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooBehavior', 'mooButton', 'mooFileUploader', 'mooAjax', 'mooAlert', 'mooPhrase', 'mooGlobal', 'mooTooltip', 'mooReviewRating'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooReview = factory();
    }
}(this, function($, mooBehavior, mooButton, mooFileUploader, mooAjax, mooAlert, mooPhrase, mooGlobal, mooTooltip) {

    var initReviewStar = function(bDisabled) {
        var eInput = $('input[type="number"][class^="rating"]');
        if (eInput.length) {
            eInput.rating('refresh', {
                disabled: bDisabled,
                showCaption: false,
                showClear: false,
                step: 1,
                max: 5
            });
        }
    };

    var initWriteReview = function() {

        if ($(window).width() < 992) {
            $('#right').modal('hide');
            $('body').scrollTop(0);
        }

        // render star
        initReviewStar(false);

        // attachedments photo
        var uploader = new mooFileUploader.fineUploader({
            element: $('#attachments_upload')[0],
            autoUpload: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>' + mooPhrase.__('drag_or_click_here_to_upload_photo') + '</div>'
            },
            validation: {
                allowedExtensions: mooConfig.photoExt,
                sizeLimit: mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/reviews/attachments"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function(id, fileName, response) {
                    if (response.filename) {
                        if ($('#attachments').length > 0) {
                            var attachments = $('#attachments').val();
                            if (attachments === '') {
                                $('#attachments').val(response.photo);
                            } else {
                                $('#attachments').val(attachments + ',' + response.photo);
                            }
                        }
                    } else if (response.result === 0) {
                        mooAlert.alert(response.message);
                    }
                }
            }
        });

        $('#triggerUpload').click(function() {
            uploader.uploadStoredFiles();
        });

        $('.removePhoto').unbind('click');
        $('.removePhoto').click(function() {
            var data = $(this).data();
            var attachments = $('#attachments_remain').val();

            if (attachments === '') {
                attachments = [];
            } else {
                attachments = attachments.split(',');
            }

            if ($.inArray(data.id.toString(), attachments) !== -1) {
                attachments.splice(attachments.indexOf(data.id.toString()), 1);
            }

            $(this).closest('li').remove();
            $('#attachments_remain').val(attachments.join());
        });

        $('#reviewBtn').unbind('click');
        $('#reviewBtn').click(function() {
            mooButton.disableButton('reviewBtn');
            $('#reviewBtn').spin('tiny');
            mooAjax.post({
                url: mooConfig.url.base + "/reviews/save",
                data: $("#reviewForm").serialize()
            }, function(data) {
                var json = $.parseJSON(data);
                if (json.result === 1) {
                    if (mooConfig.isApp) {
                        window.location.reload();
                    }
                    
                    $('#themeModal').modal('hide');
                    $('#reloadReviewWidget').trigger('click');

                    if ($('.browseReviews').length) {
                        if (json.type === 'reviewed') {
                            $('#reviewedReviewProfile').trigger('click');
                        } else {
                            $('#userReviewProfile').trigger('click');
                        }
                    } else {
                        $('#reviewProfile').trigger('click');
                    }

                    if ($(window).width() < 992) {
                        $('#right').modal('hide');
                        $('body').scrollTop(0);
                    }
                } else {
                    $('#reviewBtn').spin(false);
                    mooButton.enableButton('reviewBtn');

                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
        });
    };

    var initBrowseReview = function() {

        // load more less
        initShowMoreLess();

        // load profile popup
        mooTooltip.init();

        // init Modal
        initModalContent();
        
        // init delete
        initDeleteReview();

        // init enable
        initEnableReview();

        // render star
        initReviewStar(true);

        // bind load more
        mooBehavior.initMoreResults();

        // init auto loadmore
        mooBehavior.initAutoLoadMore();

        $('.browseReviews a').unbind('click');
        $('.browseReviews a').click(function() {
            var _this = $(this);
            _this.spin('tiny');
            if (_this.parent().is("li")) {
                $('.browseReviews .current').removeClass('current');
                _this.parent().addClass('current');
            }

            var div = _this.attr('rel');
            if (div === undefined) {
                div = 'list-content';
            }

            $('#' + div).load(_this.attr('data-url') + '?' + $.now(), function(response) {

                try {
                    $.parseJSON(response).data;
                } catch (error) {
                    response;
                }

                _this.spin(false);
                window.history.pushState({}, "", _this.attr('href'));
                if ($(window).width() < 992) {
                    $('#leftnav').modal('hide');
                    $('body').scrollTop(0);
                }
            });

            return false;
        });
    };

    var initReviewDetail = function() {
        var elReviewDetail = $('#reviewDetail');
        var elReviewProfile = $('#reviewProfile');
        if (elReviewDetail.length > 0) {
            elReviewProfile.spin('tiny');
            elReviewProfile.children('.badge_counter').hide();

            $('#browse .current').removeClass('current');
            elReviewProfile.parent().addClass('current');

            $('#profile-content').load(elReviewDetail.attr('data-url') + '?' + $.now(), function(response) {

                try {
                    $.parseJSON(response).data;
                } catch (error) {
                    response;
                }

                elReviewProfile.spin(false);
                elReviewProfile.children('.badge_counter').fadeIn();

                if ($(window).width() < 992) {
                    $('#leftnav').modal('hide');
                    $('body').scrollTop(0);
                }

            });
        }
    };

    var initReviewProfile = function() {
        var elReviewProfile = $('#reviewProfile');
        if (elReviewProfile.length > 0) {
            elReviewProfile.spin('tiny');
            elReviewProfile.children('.badge_counter').hide();

            $('#browse .current').removeClass('current');
            elReviewProfile.parent().addClass('current');

            $('#profile-content').load(elReviewProfile.attr('data-url') + '?' + $.now(), function(response) {

                try {
                    $.parseJSON(response).data;
                } catch (error) {
                    response;
                }

                elReviewProfile.spin(false);
                elReviewProfile.children('.badge_counter').fadeIn();

                if ($(window).width() < 992) {
                    $('#leftnav').modal('hide');
                    $('body').scrollTop(0);
                }

            });
        }
    };

    var initModalContent = function() {
        $('.loadModalContent').unbind('click');
        $('.loadModalContent').click(function () {
            var data = $(this).data();
            $('#themeModal .modal-content').html('');
            $('#themeModal .modal-content').spin('small');
            $('#themeModal .modal-content').load(data.url, function () {
                $('#themeModal .modal-content').spin(false);
                $('#themeModal').modal('show');
            });
        });
    };
    
    var initDeleteReview = function() {
        $('.deleteReview').unbind('click');
        $('.deleteReview').click(function() {
            var data = $(this).data();
            var sClassConfirm = 'btn btn-action';

            if (mooConfig.isApp) {
                sClassConfirm = 'mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1';
            }
            
            $.fn.SimpleModal({
                callback: function() {
                    $.post(mooConfig.url.base + '/reviews/delete', {id: data.id, noCache: 1}, function() {
                        if (mooConfig.isApp) {
                            window.location.reload();
                        } else {
                            if ($('.browseReviews').length) {
                                if (data.type === 'reviewed') {
                                    $('#reviewedReviewProfile').trigger('click');
                                } else {
                                    $('#userReviewProfile').trigger('click');
                                }
                            } else {
                                $('#reviewProfile').trigger('click');
                            }

                            $('#reloadReviewWidget').trigger('click');
                        }
                    });
                },
                model: "content",
                title: mooPhrase.__('please_confirm'),
                contents: '<div style="margin-bottom: 10px">' + mooPhrase.__('review_are_you_sure_you_want_to_remove_this_' + data.delete) + '</div>',
                onAppend: function () {
                    var _seft = this;
                    var btnConfirm = $("<a>").attr({
                        "title": mooPhrase.__('btn_ok'),
                        "class": sClassConfirm
                    }).click(function () {
                        _seft.options.callback();
                        _seft.hideModal();
                    }).text(mooPhrase.__('btn_ok'));

                    var btnCancel = $("<a>").attr({
                        "title": mooPhrase.__('btn_ok'),
                        "class": "button"
                    }).click(function () {
                        _seft.hideModal();
                    }).text(mooPhrase.__('review_cancel'));

                    $("#simple-modal").find(".simple-modal-footer").append(btnConfirm).append(btnCancel);
                }
            }).showModal();
        });
    };

    var initEnableReview = function() {
        $('#enableReview').unbind('click');
        $('#enableReview').click(function() {
            var data = $(this).data();
            var sClassConfirm = 'btn btn-action';

            if (mooConfig.isApp) {
                sClassConfirm = 'mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1';
            }
            
            $.fn.SimpleModal({
                callback: function() {
                    $.post(mooConfig.url.base + '/reviews/enable', {noCache: 1}, function() {
                        window.location.reload();
                    });
                },
                model: "content",
                title: mooPhrase.__('please_confirm'),
                contents: '<div style="margin-bottom: 10px">' + mooPhrase.__('review_are_you_sure_you_want_to_' + data.review + '_rating') + '</div>',
                onAppend: function () {
                    var _seft = this;
                    var btnConfirm = $("<a>").attr({
                        "title": mooPhrase.__('btn_ok'),
                        "class": sClassConfirm
                    }).click(function () {
                        _seft.options.callback();
                        _seft.hideModal();
                    }).text(mooPhrase.__('btn_ok'));

                    var btnCancel = $("<a>").attr({
                        "title": mooPhrase.__('btn_ok'),
                        "class": "button"
                    }).click(function () {
                        _seft.hideModal();
                    }).text(mooPhrase.__('review_cancel'));

                    $("#simple-modal").find(".simple-modal-footer").append(btnConfirm).append(btnCancel);
                }
            }).showModal();
        });
    };

    var initWidgetReview = function() {
        initWidgetReload();
        initReviewStar(true);

        $('#reviewProfileWidget').unbind('click');
        $('#reviewProfileWidget').click(function() {
            $('#reviewProfile').trigger('click');
            if ($(window).width() < 992) {
                $('#right').modal('hide');
                $('body').scrollTop(0);
            }
        });
    };

    var initWidgetReload = function() {
        $('#reloadReviewWidget').unbind('click');
        $('#reloadReviewWidget').click(function() {
            $('#reviewWidgetContent').load($('#reloadReviewWidget').attr('data-url') + '?' + $.now(), function(response) {

                try {
                    $.parseJSON(response).data;
                } catch (error) {
                    response;
                }

                if ($(window).width() < 992) {
                    $('#right').modal('hide');
                    $('body').scrollTop(0);
                }

            });
        });
    };

    var initShowMoreLess = function() {
        $('.review-truncate').each(function() {
            if (parseInt($(this).css('height')) >= 45) {
                var element = $('<a href="javascript:void(0)" class="show-more">' + $(this).data('more-text') + '</a>');
                $(this).after(element);
                element.click(function(e) {
                    showMore(this);
                });
            }
        });
    };

    var showMore = function(obj) {
        $(obj).prev().css('max-height', 'none');
        var element = $('<a href="javascript:void(0)" class="show-more">' + $(obj).prev().data('less-text') + '</a>');
        $(obj).replaceWith(element);
        element.click(function(e) {
            showLess(this);
        });
    };

    var showLess = function(obj) {
        $(obj).prev().css('max-height', '');
        var element = $('<a href="javascript:void(0)" class="show-more">' + $(obj).prev().data('more-text') + '</a>');
        $(obj).replaceWith(element);
        element.click(function(e) {
            showMore(this);
        });
    };
    
    var initOnViewAppProfile = function () {
        if($('#profile-content').length === 0) {
            $('#profile-content-wrap').wrapAll('<div id="profile-content" />');
        }
    };

    return {
        initWidgetReview: function() {
            initWidgetReview();
        },
        initOnViewAppProfile: function() {
            initOnViewAppProfile();
        },
        initReviewProfile: function() {
            initReviewProfile();
        },
        initReviewDetail: function() {
            initReviewDetail();
        },
        initBrowseReview: function() {
            initBrowseReview();
        },
        initWriteReview: function() {
            initWriteReview();
        },
        initReviewStar: function() {
            initReviewStar();
        }
    };
}));