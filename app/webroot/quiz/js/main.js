/* Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooUser', 'mooBehavior', 'mooButton', 'mooFileUploader', 'mooAjax', 'mooAlert', 'mooPhrase', 'mooGlobal', 'tinyMCE', 'mooTooltip', 'mooJqueryUi', 'mooCountdown'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooQuiz = factory();
    }
}(this, function ($, mooUser, mooBehavior, mooButton, mooFileUploader, mooAjax, mooAlert, mooPhrase, mooGlobal, tinyMCE, mooTooltip) {

    var initOnCreate = function () {
        $('#saveBtn').unbind('click');
        $('#saveBtn').click(function () {
            mooButton.disableButton('saveBtn');
            mooButton.disableButton('nextBtn');
            mooButton.disableButton('cancelBtn');
            mooButton.disableButton('deleteBtn');
            if (tinyMCE.activeEditor !== null) {
                var sContent = tinyMCE.activeEditor.getContent();
                sContent = sContent.replace(/(<div>&nbsp;<\/div>)+/gi, " ");
                sContent = sContent.replace(/(<p>&nbsp;<\/p>)+/gi, " ");
                sContent = sContent.replace(/^\s+|\s+$/g, "");

                if (sContent === null || sContent === '') {
                    $('#editor').val('');
                } else {
                    $('#editor').val(tinyMCE.activeEditor.getContent());
                }
            }

            mooAjax.post({
                url: mooConfig.url.base + "/quizzes/save",
                data: $("#createForm").serialize()
            }, function (data) {
                var json = $.parseJSON(data);
                if (json.result === 1) {
                    if (mooConfig.isApp) {
                        window.location.href = mooConfig.url.base + '/quizzes/create/' + json.id + '?app_no_tab=1';
                    } else {
                        window.location.href = mooConfig.url.base + '/quizzes/create/' + json.id;
                    }
                } else {
                    mooButton.enableButton('saveBtn');
                    mooButton.enableButton('nextBtn');
                    mooButton.enableButton('cancelBtn');
                    mooButton.enableButton('deleteBtn');
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
        });

        // attachedments to tinyMCE
        var uploader = new mooFileUploader.fineUploader({
            element: $('#attachments_upload')[0],
            autoUpload: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">insert_drive_file</i>' + mooPhrase.__('quiz_drag_ok_click_here_to_upload_files') + ' </div>'
            },
            validation: {
                allowedExtensions: mooConfig.photoExt,
                sizeLimit: mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/quiz/quiz_upload/attachments"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function (id, fileName, response) {
                    tinyMCE.activeEditor.insertContent('<p align="center"><a href="' + response.large + '" class="attached-image"><img src="' + response.thumb + '"></a></p><br>');
                }
            }
        });

        $('#triggerUpload').click(function () {
            uploader.uploadStoredFiles();
        });

        // upload thumnail
        new mooFileUploader.fineUploader({
            element: $('#quiz_thumnail')[0],
            multiple: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>' + mooPhrase.__('drag_or_click_here_to_upload_photo') + '</div>'
            },
            validation: {
                allowedExtensions: mooConfig.photoExt,
                sizeLimit: mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/quiz/quiz_upload/avatar"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function (id, fileName, response) {
                    $('#quiz_thumnail_preview > img').attr('src', response.thumb);
                    $('#quiz_thumnail_preview > img').show();
                    $('#thumbnail').val(response.file_path);
                }
            }
        });

        // bind action to button delete
        deleteQuiz();

        // toggle uploader
        $('#toggleUploader').unbind('click');
        $('#toggleUploader').on('click', function () {
            $('#images-uploader').slideToggle();
        });

        // free time
        $('#unlimit_timer').unbind('click');
        $('#unlimit_timer').click(function () {
            if (this.checked === true) {
                $('#timer').attr('disabled', true);
            } else {
                $('#timer').attr('disabled', false);
            }
        });
    };

    var initOnListingQuiz = function () {

        // load block user
        mooTooltip.init();

        // bind action to button delete
        deleteQuiz();

        // bind load more
        mooBehavior.initMoreResults();

        // init auto loadmore
        mooBehavior.initAutoLoadMore();

        $('.browseQuizzes a:not(.overlay):not(.no-ajax)').unbind('click');
        $('.browseQuizzes a:not(.overlay):not(.no-ajax)').click(function (oEvent) {
            // disable href
            oEvent.preventDefault();

            var _this = $(this);
            _this.spin('tiny');
            _this.children('.badge_counter').hide();
            if (_this.parent().is("li:not(.no-current)")) {
                $('.browseQuizzes li:not(.no-current)').removeClass('current');
                _this.parent().addClass('current');
            }

            var div = _this.attr('rel');
            if (div === undefined) {
                div = 'list-content';
            }

            $('#' + div).load(_this.attr('data-url') + '?' + $.now(), function (response) {

                try {
                    $.parseJSON(response).data;
                } catch (error) {
                    response;
                }

                _this.children('.badge_counter').fadeIn();
                _this.spin(false);

                $(".tip").tipsy({html: true, gravity: 's'});
                $('.truncate').each(function () {
                    if (parseInt(_this.css('height')) >= 145) {
                        var element = $('<a href="javascript:void(0)" class="show-more">' + _this.data('more-text') + '</a>');
                        _this.after(element);
                        element.click(function (e) {
                            showMore(this);
                        });
                    }
                });

                window.history.pushState({}, "", _this.attr('href'));
                if ($(window).width() < 992) {
                    $('#leftnav').modal('hide');
                    $('body').scrollTop(0);
                }
            });

            return false;
        });

        $('.likeItem').unbind('click');
        $('.likeItem').click(function () {

            var obj = $(this);
            var data = $(this).data();

            var type = data.type;
            var item_id = data.id;
            var thumb_up = data.status;

            $.post(mooConfig.url.base + '/likes/ajax_add/' + type + '/' + item_id + '/' + thumb_up, {noCache: 1}, function (data) {
                try {
                    var res = $.parseJSON(data);
                    obj.parents('.like-section:first').find('.likeCount:first').html(parseInt(res.like_count));
                    obj.parents('.like-section:first').find('.dislikeCount:first').html(parseInt(res.dislike_count));

                    if (thumb_up) {
                        obj.toggleClass('active');
                        obj.next().next().removeClass('active');
                    } else {
                        obj.toggleClass('active');
                        obj.prev().prev().removeClass('active');
                    }
                } catch (err) {
                    mooUser.validateUser();
                }
            });
        });
    };

    var initOnView = function () {

        // load block user
        mooTooltip.init();

        // hide duration
        $('#duration_element').addClass('hidden');

        // load element
        $('.loadQuizView a:not(.overlay):not(.no-ajax)').unbind('click');
        $('.loadQuizView a:not(.overlay):not(.no-ajax)').click(function (oEvent) {
            // disable href
            oEvent.preventDefault();
            
            var _this = $(this);
            _this.spin('tiny');

            if (_this.parent().is("li:not(.no-current)")) {
                $('.loadQuizView li:not(.no-current)').removeClass('current');
                _this.parent().addClass('current');
            }
            
            if (_this.parent().is(".app-load-quiz-view")) {
                $('.loadQuizView .app-load-quiz-view').removeClass('current');
                _this.parent().addClass('current');
            }

            var div = _this.attr('rel');
            if (div === undefined) {
                div = 'quiz-content';
            }

            $('#' + div).load(_this.attr('data-url') + '?' + $.now(), function (response) {

                try {
                    $.parseJSON(response).data;
                } catch (error) {
                    response;
                }

                _this.spin(false);
                $('textarea:not(.no-grow)').autogrow();
                $(".tip").tipsy({html: true, gravity: 's'});

                window.history.pushState({}, "", _this.attr('href'));
                if ($(window).width() < 992) {
                    $('#leftnav').modal('hide');
                    $('body').scrollTop(0);
                }
            });

            return false;
        });

        // reset take
        $('#resetBtn').unbind('click');
        $('#resetBtn').click(function () {
            $("#takeForm").trigger('reset');
        });

        // bind action to button delete
        deleteQuiz();
    };

    var initTakePrivacy = function () {
        $('#startBtn').unbind('click');
        $('#startBtn').click(function () {
            $('#startBtn').spin('small');
            mooButton.disableButton('startBtn');

            $.post(mooConfig.url.base + "/quizzes/do_take_privacy", $("#takePrivacyForm").serialize(), function (data) {
                var json = $.parseJSON(data);

                if (json.result == 1) {
                    $('#quiz-content').load(mooConfig.url.base + "/quizzes/view_take?" + $.now(), {quiz_id: json.quiz_id, privacy_hash: json.privacy_hash}, function (response) {
                        try {
                            $.parseJSON(response).data;
                        } catch (error) {
                            response;
                        }

                        if ($(window).width() < 992) {
                            $('#leftnav').modal('hide');
                            $('body').scrollTop(0);
                        }
                    });

                    $('#themeModal').modal('hide');
                } else {
                    window.location.reload();
                }

            });

            return false;
        });
    };

    var initTakeQuiz = function (iTime) {

        // bind countdown
        if (iTime > 0) {
            countdown(iTime);
        }

        // submit
        $('#submitAnswerBtn').unbind('click');
        $('#submitAnswerBtn').click(function () {
            mooButton.disableButton('submitAnswerBtn');

            var data = $(this).data();
            mooAjax.post({
                url: mooConfig.url.base + "/quizzes/do_take/" + data.id,
                data: $("#takeForm").serialize()
            }, function (data) {
                var json = $.parseJSON(data);
                if (json.result === 1) {
                    // load detail
                    $('.loadQuizView #quizDetail').trigger('click');

                    // load modal
                    $.fn.SimpleModal().hideModal();
                    $.fn.SimpleModal({
                        title: mooPhrase.__('quiz_thank_you_for_taking_our_quiz'),
                        model: "modal-ajax",
                        param: {
                            url: mooConfig.url.base + "/quizzes/view_finish/" + json.take_id
                        },
                        onAppend: function () {
                            var _seft = this;
                            var btnConfirm = $("<a>").attr({
                                "title": mooPhrase.__('quiz_confirm'),
                                "class": "btn btn-action btn-not-margin"
                            }).click(function () {
                                _seft.hideModal();
                                $(window).scrollTop(0);

                                if (mooConfig.isApp) {
                                    window.location.reload();
                                }
                            }).text(mooPhrase.__('quiz_confirm'));

                            // add button
                            $("#simple-modal").find(".simple-modal-header").addClass('text-center');
                            $("#simple-modal").find(".simple-modal-footer").addClass('text-center').append(btnConfirm);
                        }
                    }).showModal();
                } else if (json.result === 2) {
                    $.fn.SimpleModal().hideModal();
                    $.fn.SimpleModal({
                        title: mooPhrase.__('quiz_please_confirm'),
                        contents: '<div style="margin-bottom: 10px">' + mooPhrase.__('quiz_you_are_not_finish_all_question_are_you_sure_you_want_to_submit_your_answers') + '</div>',
                        model: "content",
                        onAppend: function () {
                            var _seft = this;
                            var sClassConfirm = 'btn btn-action';

                            if (mooConfig.isApp) {
                                sClassConfirm = 'mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1';
                            }

                            var btnConfirm = $("<a>").attr({
                                "title": mooPhrase.__('quiz_confirm'),
                                "class": sClassConfirm
                            }).click(function () {
                                _seft.hideModal();

                                // submit again
                                $('#direct_point').remove();
                                $('#submitAnswerBtn').trigger('click');
                            }).text(mooPhrase.__('quiz_confirm'));

                            var btnCancel = $("<a>").attr({
                                "title": mooPhrase.__('quiz_cancel'),
                                "class": "button"
                            }).click(function () {
                                _seft.hideModal();
                            }).text(mooPhrase.__('quiz_cancel'));

                            $("#simple-modal").find(".simple-modal-footer").append(btnConfirm).append(btnCancel);
                        }
                    }).showModal();

                    mooButton.enableButton('submitAnswerBtn');
                } else if (json.result === 3) {
                    window.location.reload();
                } else {
                    mooButton.enableButton('submitAnswerBtn');

                    // show error
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
        });
    };

    var countdown = function (iTime) {

        // bind duration
        $('#duration_element').removeClass('hidden');
        $('#duration_element .duration').empty().append($('<div>', {'id': 'duration', 'class': 'countdown-container'}));

        // Define var
        var labels = ['hours', 'minutes', 'seconds'];
        var durationTime = new Date().getTime() + (iTime * 1000);
        var currTime = '00:00:00';
        var nextTime = '00:00:00';
        var parser = /([0-9]{2})/gi;
        var $countdown = $('#duration');

        // Parse countdown string to an object
        function strfobj(str) {
            var parsed = str.match(parser), obj = {};
            labels.forEach(function (label, i) {
                obj[label] = parsed[i];
            });
            return obj;
        }

        // Return the time components that diffs
        function diff(obj1, obj2) {
            var diff = [];
            labels.forEach(function (key) {
                if (obj1[key] !== obj2[key]) {
                    diff.push(key);
                }
            });
            return diff;
        }

        // Build the layout
        labels.forEach(function (label) {
            $countdown.append('<div class="time ' + label + '"><span class="count curr top">00</span><span class="count next top">00</span><span class="count next bottom">00</span><span class="count curr bottom">00</span><span class="label">' + mooPhrase.__('quiz_' + label) + '</span></div>');
        });

        // Starts the countdown
        $countdown.countdown(durationTime, function (event) {
            var newTime = event.strftime('%I:%M:%S'), data;
            if (newTime !== nextTime) {
                currTime = nextTime;
                nextTime = newTime;

                // Setup the data
                data = {
                    'curr': strfobj(currTime),
                    'next': strfobj(nextTime)
                };

                // Apply the new values to each node that changed
                diff(data.curr, data.next).forEach(function (label) {
                    var selector = '.%s'.replace(/%s/, label);
                    var $node = $countdown.find(selector);

                    // Update the node
                    $node.removeClass('flip');
                    $node.find('.curr').text(data.curr[label]);
                    $node.find('.next').text(data.next[label]);

                    // Wait for a repaint to then flip
                    setTimeout(function () {
                        $node.addClass('flip');
                    }, 50);
                });
            }
        }).on('finish.countdown', function () {
            this.remove();

            // action
            $('#direct_point').remove();
            $('#submitAnswerBtn').trigger('click');
        });
    };

    var deleteQuiz = function () {
        $('.deleteQuiz').unbind('click');
        $('.deleteQuiz').click(function () {
            var data = $(this).data();
            var sClassConfirm = 'btn btn-action';
            var deleteUrl = mooConfig.url.base + '/quizzes/delete/' + data.id;

            if (mooConfig.isApp) {
                deleteUrl = mooConfig.url.base + '/quizzes/confirm_delete/' + data.id + '?app_no_tab=1';
                sClassConfirm = 'mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1';
            }

            $.fn.SimpleModal({
                title: mooPhrase.__('quiz_please_confirm'),
                contents: '<div style="margin-bottom: 10px">' + mooPhrase.__('quiz_are_you_sure_you_want_to_remove_this_quiz') + '</div>',
                model: "content",
                onAppend: function () {
                    var _seft = this;
                    var btnConfirm = $("<a>").attr({
                        "title": mooPhrase.__('quiz_confirm'),
                        "class": sClassConfirm
                    }).click(function () {
                        _seft.hideModal();
                        window.location.href = deleteUrl;
                    }).text(mooPhrase.__('quiz_confirm'));

                    var btnCancel = $("<a>").attr({
                        "title": mooPhrase.__('quiz_cancel'),
                        "class": "button"
                    }).click(function () {
                        _seft.hideModal();
                    }).text(mooPhrase.__('quiz_cancel'));

                    $("#simple-modal").find(".simple-modal-footer").append(btnConfirm).append(btnCancel);
                }
            }).showModal();
        });
    };

    // Question
    var initOnListingQuestion = function () {
        $('.createQuestion').unbind('click');
        $('.createQuestion').click(function () {
            var _this = $(this);
            _this.spin('small');
            var data = _this.data();

            mooAjax.post({url: mooConfig.url.base + "/quizzes/question/create/" + data.quiz_id + '/' + data.quiz_question_id}, function (data) {
                _this.spin(false);
                $("#questions-create").html(data);

                // scroll create
                $('html, body').animate({
                    scrollTop: $("#questions-create").offset().top - ($("#header").height() / 2)
                }, 500);
            });
        });

        $('#orderQuestion').unbind('click');
        $('#orderQuestion').click(function () {
            var data = $(this).data();
            var aListId = $('#sortable_questions').sortable('toArray');
            mooAjax.post({url: mooConfig.url.base + "/quizzes/question/sort/" + data.quiz_id, data: {ids: aListId}}, function (data) {
                var json = $.parseJSON(data);
                if (json.result === 1) {
                    window.location.reload();
                } else {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
        });

        // Bind sort
        $('#sortable_questions').sortable({
            items: "li.item_question",
            handle: ".reorder"
        });
    };

    var initOnCreateQuestion = function () {
        $('#saveBtn').unbind('click');
        $('#saveBtn').click(function () {
            mooButton.disableButton('saveBtn');
            mooButton.disableButton('nextBtn');
            mooButton.disableButton('cancelBtn');
            mooButton.disableButton('orderQuestion');
            mooButton.disableButton('createQuestion');

            mooAjax.post({
                url: mooConfig.url.base + "/quizzes/question/save",
                data: $("#createForm").serialize()
            }, function (data) {
                var json = $.parseJSON(data);
                if (json.result === 1) {
                    window.location.reload();
                } else {
                    mooButton.enableButton('saveBtn');
                    mooButton.enableButton('nextBtn');
                    mooButton.enableButton('cancelBtn');
                    mooButton.enableButton('orderQuestion');
                    mooButton.enableButton('createQuestion');
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
        });

        $('#cancelBtn').unbind('click');
        $('#cancelBtn').click(function () {
            $("#questions-create").empty();

            // scroll to list
            $('html, body').animate({
                scrollTop: $("#questions-list-content").offset().top - ($("#header").height() / 2)
            }, 500);
        });

        $('.tip').tipsy({html: true, gravity: 's'});

        $('#sortable_answers').sortable({
            handle: ".reorder"
        });

        $('#addNewAnswer').unbind('click');
        $('#addNewAnswer').click(function () {
            var liElClone = $('#addNew').clone();
            liElClone.removeAttr('id').removeClass('hide');
            liElClone.appendTo($('#sortable_answers'));
        });

        $('body').off('click', '.remove_answer');
        $('body').on('click', '.remove_answer', function () {
            $(this).parents('li.placeholder').remove();
        });

        $('body').off('click', '.resulttmp');
        $('body').on('click', '.resulttmp', function () {

            /* One Answer for one Questtion */
            $('#sortable_answers input:checkbox').prop('checked', false);
            $('#sortable_answers #answersCorrect').val(0);
            $(this).prop('checked', true);
            /* One Answer for one Questtion */

            var inputElResult = $(this).prev();
            if (inputElResult.is("input")) {
                inputElResult.val($(this).is(":checked") ? 1 : 0);
            }
        });
    };

    var initOnDeleteQuestion = function () {
        $('#deleteQuestion').unbind('click');
        $('#deleteQuestion').click(function () {
            var data = $(this).data();
            $.post(data.url, {noCache: 1}, function () {
                window.location.reload();
            });
        });
    };

    // Publish
    var initOnPublish = function () {
        $('#unPublishBtn').unbind('click');
        $('#unPublishBtn').click(function () {
            var data = $(this).data();
            var unPublishUrl = mooConfig.url.base + '/quizzes/publish/' + data.id + '/0';
            mooAlert.confirm(mooPhrase.__('quiz_are_you_sure_you_want_to_unpublish_this_quiz'), unPublishUrl);
        });
    };


    var initOnViewAppProfile = function () {
        if ($('#profile-content').length === 0) {
            $('#profile-content-wrap').wrapAll('<div id="profile-content" />');
        }
    };

    return {
        initOnCreate: function () {
            initOnCreate();
        },
        initOnListingQuiz: function () {
            initOnListingQuiz();
        },
        initOnListingQuestion: function () {
            initOnListingQuestion();
        },
        initOnCreateQuestion: function () {
            initOnCreateQuestion();
        },
        initOnDeleteQuestion: function () {
            initOnDeleteQuestion();
        },
        initOnPublish: function () {
            initOnPublish();
        },
        initOnView: function () {
            initOnView();
        },
        initOnViewAppProfile: function () {
            initOnViewAppProfile();
        },
        initTakePrivacy: function () {
            initTakePrivacy();
        },
        initTakeQuiz: function (iTime) {
            initTakeQuiz(iTime);
        }
    };
}));