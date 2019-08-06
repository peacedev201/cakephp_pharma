var gift_ext_photo = String(mooPhrase.__('gift_ext_photo')).split(',');
var gift_ext_audio = String(mooPhrase.__('gift_ext_audio')).split(',');
var gift_ext_video = String(mooPhrase.__('gift_ext_video')).split(',');
jQuery.gift = {
    selectCreateGiftType: function (type, is_ffmpeg_installed)
    {
        if (typeof is_ffmpeg_installed == 'undefined' || is_ffmpeg_installed == '')
        {
            is_ffmpeg_installed = '';
        }
        switch (type)
        {
            case 'photo':
                this.initUploader(gift_ext_photo, mooPhrase.__('tdescphoto'), type);
                break;
            case 'audio':
                this.initUploader(gift_ext_audio, mooPhrase.__('tdescaudio'), type, '', '', 'file_name');
                break;
            case 'video':
                if (parseInt(is_ffmpeg_installed) == 1)
                {
                    this.initUploader(gift_ext_video, mooPhrase.__('tdescvideo'), type, '', '', 'file_name');
                }
                else
                {
                    mooAlert(mooPhrase.__('ffmpeg_not_found'));
                }
                break;
        }
        jQuery('#GiftType').val(type);
    },
    initUploader: function (extension, btn_text, type, uploader_id, value_id, preview_id)
    {
        if (typeof extension == 'undefined' || extension == '')
        {
            extension = gift_ext_photo;
        }
        if (typeof btn_text == 'undefined' || btn_text == '')
        {
            btn_text = mooPhrase.__('tdescphoto');
        }
        if (typeof type == 'undefined' || type == '')
        {
            type = 'photo';
        }
        if (typeof uploader_id == 'undefined' || uploader_id == '')
        {
            uploader_id = 'select-0';
        }
        if (typeof value_id == 'undefined' || value_id == '')
        {
            value_id = 'GiftFilename';
        }
        if (typeof preview_id == 'undefined' || preview_id == '')
        {
            preview_id = 'item-avatar';
        }

        var uploader_thumbnail = new qq.FineUploader({
            element: $('#' + uploader_id)[0],
            multiple: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="icon-camera"></i><span>' + btn_text + '</span></div>'
            },
            validation: {
                allowedExtensions: extension,
            },
            request: {
                endpoint: baseUrl + "/gifts/upload_file/" + type
            },
            callbacks: {
                //onError: errorHandler1,
                onComplete: function (id, fileName, response) {
                    if (response.success == 0)
                    {
                        mooAlert(response.message);
                    }
                    else
                    {
                        $('#' + value_id).val(response.filename);
                        $('#' + preview_id).hide();
                        if (type == 'photo')
                        {
                            $('#' + preview_id).attr('src', response.path);
                            $('#' + preview_id).show();
                        }
                        else
                        {
                            $('#' + preview_id).empty().append(response.filename);
                            $('#' + preview_id).show();
                        }
                    }
                }
            }
        });
    },
    initSuggestFriend: function ()
    {
        jQuery("#GiftFriend").autocomplete({
            source: function (request, response) {
                jQuery.post(baseUrl + '/gifts/suggest_friend/', 'keyword=' + jQuery("#GiftFriend").val(), function (data) {
                    response(jQuery.parseJSON(data));
                });
            },
            minLength: 2,
            select: function (event, ui) {
                $("#GiftFriend").val(ui.item.label);
                $("#GiftFriendId").val(ui.item.value);
                return false;
            }
        });
    },
    saveGift: function (saved)
    {
        if (typeof saved != 'undefined' && saved == 1)
        {
            jQuery('#createForm #GiftSaved').val(1);
        }
        else
        {
            jQuery('#createForm #GiftSaved').val(0);
        }
        disableButton('sendButton');
        disableButton('saveButton');
        disableButton('previewButton');
        disableButton('cancelButton');
        jQuery.post(baseUrl + '/gifts/save/', jQuery('#createForm').serialize(), function (data) {
            var json = jQuery.parseJSON(data);
            if (json.result == 0)
            {
                jQuery('.error-message').empty().append(json.message).show();
                enableButton('sendButton');
                enableButton('saveButton');
                enableButton('previewButton');
                enableButton('cancelButton');
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
    },
    sendGift: function (item, id)
    {
        jQuery(item).attr('disabled', 'disabled').addClass('disabled');
        jQuery.post(baseUrl + '/gifts/ajax_send_gift/', 'id=' + id, function (data) {
            var json = jQuery.parseJSON(data);
            if (json.result == 0)
            {
                mooAlert(json.message);
            }
            else
            {
                mooAlert(json.message);
                jQuery('#mygift' + id).remove();
            }
        });
    },
    previewGift: function ()
    {
        jQuery.post(baseUrl + '/gifts/preview/', jQuery('#createForm').serialize(), function (data) {
            jQuery('#themeModal .modal-content').empty().append(data);
            jQuery('#themeModal').modal();
            jQuery.gift.initAudio(1);
        });
    },
    viewGift: function (gift_sent_id)
    {
        jQuery.post(baseUrl + '/gifts/ajax_view/' + gift_sent_id, '', function (data) {
            jQuery('#themeModal .modal-content').empty().append(data);
            jQuery('#themeModal').modal();
        });
    },
    deleteGift: function (gift_sent_id, gift_id)
    {
        if (typeof gift_sent_id == 'undefined')
        {
            gift_sent_id = '';
        }
        if (typeof gift_id == 'undefined')
        {
            gift_id = '';
        }
        jQuery.post(baseUrl + '/gifts/ajax_delete_gift/', 'gift_sent_id=' + gift_sent_id + '&gift_id=' + gift_id, function (data) {
            var json = jQuery.parseJSON(data);
            if (json.result == 0)
            {
                mooAlert(json.message);
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
                mooAlert(json.message);
            }
        });
    },
    loadMyGifts: function (item, type)
    {
        jQuery('#my-gift-type li').removeClass('class');
        jQuery(item).addClass('active');
        jQuery.post(baseUrl + '/gifts/ajax_browse/my/' + type, '', function (data) {
            jQuery('#list-content').empty().append(data);
        });
    },
    isJson: function (str) {
        try
        {
            JSON.parse(str);
        }
        catch (e)
        {
            return false;
        }
        return true;
    },
    initAudio: function (dialog) {
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
    },

    playAudio: function(){
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
    }
}

function mooConfirmBox(msg, callback)
{
    // Set title
    $($('#portlet-config  .modal-header .modal-title')[0]).html(mooPhrase.__('please_confirm'));
    // Set content
    $($('#portlet-config  .modal-body')[0]).html(msg);
    // OK callback, remove all events bound to this element
    $('#portlet-config  .modal-footer .ok').off("click").click(function () {
        callback();
        $('#portlet-config').modal('hide');
    });
    $('#portlet-config').modal('show');

}

$('#themeModal').on('hidden.bs.modal', function () {
    jQuery(this).find('.modal-body').empty();
})