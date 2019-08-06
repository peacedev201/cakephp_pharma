(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooButton', 'mooPhrase', 'mooGlobal', 'mooFileUploader', 'mooAlert'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooGift = factory();
    }
}(this, function ($, mooButton, mooPhrase, mooGlobal, mooFileUploader, mooAlert) {

    var initSuggestFriend = function () {
        var friend_html = jQuery(jQuery("#friendItemTemplate").html());
        $("#GiftFriend").autocomplete({
            source: function (request, response) {
                $.post(mooConfig.url.base + '/gifts/suggest_friend/', 'keyword=' + $("#GiftFriend").val(), function (data) {
                    response($.parseJSON(data));
                });
            },
            minLength: 2,
            select: function (event, ui) {
                friend_html.find(".name").empty().append(ui.item.label);
                jQuery("#wrapper_friend").empty().append(friend_html);
                $("#GiftFriendId").val(ui.item.value);
                jQuery("#GiftFriend").val("");
                jQuery(".ui-autocomplete.ui-menu").hide();
                return false;
            },
            change: function (event, ui) {
				if (!ui.item) {
					$(this).val('');
				}
				else {

					friend_html.find(".name").empty().append(ui.item.label);
                    jQuery("#wrapper_friend").empty().append(friend_html);
                    $("#GiftFriendId").val(ui.item.value);
                    jQuery("#GiftFriend").val("");
                    jQuery(".ui-autocomplete.ui-menu").hide();
				}
			},
            focus: function (event, ui) {
                event.preventDefault();
                $("#GiftFriendId").val(ui.item.value);
                jQuery("#GiftFriend").val(ui.item.label);
            }
        });
        jQuery(document).on("click", ".friend_item .remove", function(){
            jQuery("#GiftFriendId").val("");
            jQuery(this).parent().remove();
        })
    }

    var selectCreateGiftType = function (params) {
        if (typeof params.is_ffmpeg_installed == 'undefined' || params.is_ffmpeg_installed == '')
        {
            params.is_ffmpeg_installed = '';
        }
        switch (params.type)
        {
            case 'photo':
                initUploader({
                    'extension' : mooPhrase.__('gift_ext_photo'),
                    'btn_text' : mooPhrase.__('tdescphoto'),
                    'type' : params.type
                });
                break;
            case 'audio':
                initUploader({
                    'extension' : mooPhrase.__('gift_ext_audio'),
                    'btn_text' : mooPhrase.__('tdescaudio'),
                    'type' : params.type
                });
                break;
            case 'video':
                if (parseInt(params.is_ffmpeg_installed) == 1)
                {
                    initUploader({
                        'extension' : mooPhrase.__('gift_ext_video'),
                        'btn_text' : mooPhrase.__('tdescvideo'),
                        'type' : params.type
                    });
                }
                else
                {
                    mooAlert.alert(mooPhrase.__('ffmpeg_not_found'));
                }
                break;
        }
        $('#GiftType').val(params.type);
    }

    var initUploader = function (params)
    {
        if (typeof params.extension == 'undefined' || params.extension == '')
        {
            params.extension = mooPhrase.__('gift_ext_photo');
        }
        if (typeof params.btn_text == 'undefined' || params.btn_text == '')
        {
            params.btn_text = mooPhrase.__('tdescphoto');
        }
        if (typeof params.type == 'undefined' || params.type == '')
        {
            params.type = 'photo';
        }
        if (typeof params.uploader_id == 'undefined' || params.uploader_id == '')
        {
            params.uploader_id = 'select-0';
        }
        if (typeof params.value_id == 'undefined' || params.value_id == '')
        {
            params.value_id = 'GiftFilename';
        }
        if (typeof params.preview_id == 'undefined' || params.preview_id == '')
        {
            params.preview_id = 'item-avatar';
        }
        var uploader = new mooFileUploader.fineUploader({
            element: $('#' + params.uploader_id)[0],
            multiple: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="icon-camera"></i>' + params.btn_text + '</div>'
            },
            validation: {
                allowedExtensions: params.extension,
                sizeLimit: mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/gifts/upload_file/" + params.type
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function (id, fileName, response) {
                    $('#' + params.value_id).val(response.filename);
                    $('#' + params.preview_id).hide();
                    if (params.type == 'photo')
                    {
                        $('#' + params.preview_id).attr('src', response.path);
                        $('#' + params.preview_id).show();
                    }
                }
            }
        });
    }

    var saveGift = function (params)
    {
        if (typeof params.save != 'undefined' && params.save == 1)
        {
            $('#createForm #GiftSaved').val(1);
            mooButton.disableButton('button_save');
        }
        else
        {
            $('#createForm #GiftSaved').val(0);
            mooButton.disableButton('button_send');
        }

        if (typeof params.c_type != 'undefined' && params.c_type == 1)
        {
            $('#createForm #GiftCType').val(1);
        }
        else
        {
            $('#createForm #GiftCType').val(0);
        }

        $.post(mooConfig.url.base + '/gifts/save/', $('#createForm').serialize(), function (data) {
            var json = $.parseJSON(data);
            if (json.result == 0)
            {
                if(json.hasOwnProperty("c_type")){
                    mooConfirmBox(json.message, function(){
                        saveGift({
                            'c_type' : json.c_type,
                        })
                    },json.button);
                    $('#portlet-config .modal-footer .default').attr('onclick','window.location.href="'+json.url+'"')
                    // mooAlert.alert(json.message);
                }
                else
                    $('.error-message').empty().append(json.message).show();
    
                mooButton.enableButton('button_send');
                mooButton.enableButton('button_save');
            }
            else
            {
                if (json.url)
                {
                    if (!mooConfig.isApp) {
                        window.location = json.url;
                    }else{
                        window.location = json.url+ '?app_no_tab=1';
                    }
                }
            }
        });
    }

    var previewGift = function () {
        $.post(mooConfig.url.base + '/gifts/preview/', $('#createForm').serialize(), function (data) {
            $('#themeModal .modal-content').empty().append(data);
            $('#themeModal').modal();
        });

        $('#themeModal').on('hidden.bs.modal', function () {
            $('#themeModal .modal-content').empty();
        })
    }
    var initAudio = function (params) {
        if(jQuery("#music").length == 0)
        {
            return;
        }
        var dialog = params.dialog;
        var music = document.getElementById('music'); // id for audio element
        var duration; // Duration of audio clip
        var pButton = document.getElementById('pButton'); // play button

        var playhead = document.getElementById('playhead'); // playhead

        var timeline = document.getElementById('timeline'); // timeline
        // timeline width adjusted for playhead
        if(typeof dialog != 'undefined')
        {
            var wrapper_width = parseFloat(jQuery('.modal-dialog').width());
            if(wrapper_width == 0)
            {
                wrapper_width = jQuery(document).width() - parseFloat(jQuery('.modal-body').css('margin-right')) - parseFloat(jQuery('.modal-body').css('margin-left'));
                var timelineWidth = wrapper_width - 102 - 81;
                var timeline_offset = (jQuery(document).width() - wrapper_width) / 2 + parseFloat(jQuery('.modal-body').css('padding-left')) + 102;
            }
            else
            {
                var timelineWidth = wrapper_width - parseFloat(jQuery('.modal-body').css('padding-right')) - parseFloat(jQuery('.modal-body').css('padding-left')) - 102;
                var timeline_offset = (jQuery(document).width() - wrapper_width) / 2 + parseFloat(jQuery('.modal-body').css('padding-left')) + 102;
            }
        }
        else
        {
            var timelineWidth = timeline.offsetWidth - playhead.offsetWidth;
            var timeline_offset = timeline.offsetLeft;
        }

        // timeupdate event listener
        music.addEventListener("timeupdate", timeUpdate, false);

        //Makes timeline clickable
        timeline.addEventListener("click", function (event) {
            moveplayhead(event);
            music.currentTime = duration * clickPercent(event);
        }, false);

        // returns click as decimal (.77) of the total timelineWidth
        function clickPercent(e) {
            return (e.pageX - timeline_offset) / timelineWidth;
        }

        // Makes playhead draggable
        playhead.addEventListener('mousedown', mouseDown, false);
        window.addEventListener('mouseup', mouseUp, false);

        // Boolean value so that mouse is moved on mouseUp only when the playhead is released
        var onplayhead = false;
        // mouseDown EventListener
        function mouseDown() {
            onplayhead = true;
            window.addEventListener('mousemove', moveplayhead, true);
            music.removeEventListener('timeupdate', timeUpdate, false);
        }
        // mouseUp EventListener
        // getting input from all mouse clicks
        function mouseUp(e) {
            if (onplayhead == true) {
                moveplayhead(e);
                window.removeEventListener('mousemove', moveplayhead, true);
                // change current time
                music.currentTime = duration * clickPercent(e);
                music.addEventListener('timeupdate', timeUpdate, false);
            }
            onplayhead = false;
        }
        // mousemove EventListener
        // Moves playhead as user drags
        function moveplayhead(e) {
            var newMargLeft = e.pageX - timeline_offset;
            if (newMargLeft >= 0 && newMargLeft <= timelineWidth) {
                playhead.style.width = newMargLeft + "px";
            }
            if (newMargLeft < 0) {
                playhead.style.width = "0px";
            }
            if (newMargLeft > timelineWidth) {
                playhead.style.width = timelineWidth + "px";
            }
        }
        function timeUpdate() {
            var playPercent = timelineWidth * (music.currentTime / duration);
            playhead.style.width = playPercent + "px";
            if (music.currentTime == duration) {
                pButton.className = "";
                pButton.className = "play";
            }
        }

        music.addEventListener("canplaythrough", function () {
            duration = music.duration;
        }, false);
        duration = music.duration;
        timeline.style.width = timelineWidth + "px";
    }

    var initCreate = function (setting) {
        $('#choose_photo').click(function() {
            jQuery('.type-img').removeClass('active');
            jQuery(this).addClass('active');
            selectCreateGiftType({'type': 'photo'});
        });
        $('#choose_audio').click(function() {
            jQuery('.type-img').removeClass('active');
            jQuery(this).addClass('active');
            selectCreateGiftType({'type': 'audio'});
        });
        $('#choose_video').click(function() {
            jQuery('.type-img').removeClass('active');
            jQuery(this).addClass('active');
            selectCreateGiftType({
                'type': 'video',
                'is_ffmpeg_installed' : setting.is_ffmpeg_installed,
            });
        });
        $('#button_send').click(function() {
            mooConfirmBox(mooPhrase.__('send_gift_confirm'), function(){
                saveGift({
                    'save' : 0,
                })
            })
        })
        $('#button_save').click(function() {
            saveGift({
                'save' : 0,
            })
        })
        $('#button_preview').click(function() {
            previewGift();
        })
    }

    var myGifts = function (params) {
        $('#my-gift-type li').removeClass('class');
        $('#'+params.item).addClass('active');
        $.post(mooConfig.url.base + '/gifts/ajax_browse/my/' + params.type, '', function (data) {
            $('#list-content').empty().append(data);
        });
    }

    var loadMyGifts = function () {
        $('#saved_gifts').click(function () {
            myGifts({
                'item' : 'saved_gifts',
                'type' : 'saved',
            });
            window.history.pushState({}, "", mooConfig.url.base + '/gifts/index/my/saved');
        });
        $('#received_gifts').click(function () {
            myGifts({
                'item' : 'received_gifts',
                'type' : 'received',
            });
            window.history.pushState({}, "", mooConfig.url.base + '/gifts/index/my/received');
        });
        $('#sent_gifts').click(function () {
            myGifts({
                'item' : 'sent_gifts',
                'type' : 'sent',
            });
            window.history.pushState({}, "", mooConfig.url.base + '/gifts/index/my/sent');
        });
    }

    var initNavGift = function (params) {
        $('.menu-gift li#browse_all a').click(function(){
            $('#center > .bar-content').removeClass('my-gift-wrap');
        });
        $('.menu-gift li#my-gift a').click(function(){
            $('#center > .bar-content').addClass('my-gift-wrap');
        });
        if(params.type == 'my')
        {
            $('#center > .bar-content').addClass('my-gift-wrap');
        }
    }

    var viewGift = function ()
    {
        $('.view_gift').click(function() {
            var data = $(this).data();
            $.post(mooConfig.url.base + '/gifts/ajax_view/' + data.id, '', function (data) {
                $('#themeModal .modal-content').empty().append(data);
                $('#themeModal').modal();
            });

            $('#themeModal').on('hidden.bs.modal', function () {
                $('#themeModal .modal-content').empty();
            })
        });
    }

    var sendGift = function ()
    {
        $('.send_gift').click(function() {
            var data = $(this).data();
            mooConfirmBox(mooPhrase.__('send_gift_confirm'), function(){
                mooButton.disableButton('btnSendGift');
                var id = data.id;
                $.post(mooConfig.url.base + '/gifts/ajax_send_gift/', 'id=' + id, function (data) {
                    var json = jQuery.parseJSON(data);
                    mooButton.enableButton('btnSendGift');
                    if (json.result == 0)
                    {
                        mooAlert.alert(json.message);
                    }
                    else
                    {
                        $('#mygift' + id).remove();
                        mooAlert.alert(json.message);
                    }
                });
            })
        });
    }

    var deleteGift = function () {
        $('.delete_gift').click(function() {
            var data = $(this).data();
            var gift_sent_id = data.gift_sent_id;
            var gift_id = data.gift_id;
            $.fn.SimpleModal({
                btn_ok : mooPhrase.__('btn_ok'),
                btn_cancel: mooPhrase.__('btn_cancel'),
                model: 'confirm',
                callback: function(){
                    $.post(mooConfig.url.base + '/gifts/ajax_delete_gift/', 'gift_sent_id=' + gift_sent_id + '&gift_id=' + gift_id, function (data) {
                        var json = jQuery.parseJSON(data);
                        if (json.result == 0)
                        {
                            mooAlert.alert(json.message);
                        }
                        else
                        {
                            if (gift_sent_id != '')
                            {
                                jQuery('#mygift' + gift_sent_id).remove();
                            }
                            else
                            {
                                jQuery('#mygift' + gift_id).remove();
                            }
                            mooAlert.alert(json.message);
                        }
                    });
                },
                title: mooPhrase.__('please_confirm'),
                contents: mooPhrase.__('delete_gift_confirm'),
                hideFooter: false,
                closeButton: false
            }).showModal();

        });
    }

    var playAudio = function(){
        $('.play_audio').click(function () {
            var music = document.getElementById('music'); // id for audio element
            var pButton = document.getElementById('pButton'); // play button
            // start music
            if (music.paused) {
                music.play();
                // remove play, add pause
                pButton.className = "";
                pButton.className = "pause";
            } else { // pause music
                music.pause();
                // remove pause, add play
                pButton.className = "";
                pButton.className = "play";
            }
        });

    }

    function mooConfirmBox( msg, callback, okText )
    {
        text = 'OK';
        message = mooPhrase.__('please_confirm');
        if(typeof okText != 'undefined'){
            text = okText;
            message = 'Message';
        }

        // Set title
        $($('#portlet-config  .modal-header .modal-title')[0]).html(message);
        // Set content
        $($('#portlet-config  .modal-body')[0]).html(msg);
        // OK callback, remove all events bound to this element
        $('#portlet-config  .modal-footer .ok').off("click").click(function(){
            callback();
            $('#portlet-config').modal('hide');
        });
        $('#portlet-config .blue.ok').text(text);
        $('#portlet-config').modal('show');

    }

    return {
        initSuggestFriend: function () {
            initSuggestFriend();
        },
        selectCreateGiftType: function (params) {
            selectCreateGiftType(params);
        },
        initCreate: function (setting) {
            initCreate(setting);
        },
        loadMyGifts: function () {
            loadMyGifts();
        },
        initNavGift: function (params) {
            initNavGift(params);
        },
        viewGift: function () {
            viewGift();
        },
        sendGift: function () {
            sendGift();
        },
        deleteGift: function () {
            deleteGift();
        },
        playAudio : function () {
            playAudio();
        },
        initAudio : function (params) {
            initAudio(params);
        }
    }

}));