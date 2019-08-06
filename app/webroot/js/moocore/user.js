/* Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooPhrase', 'mooFileUploader', 'mooAlert', 'mooGlobal', 'mooButton', 'mooOverlay', 'mooBehavior', 'mooAjax', 'mooTooltip',
            'spinner', 'tipsy', 'multiselect', 'Jcrop', 'bootstrap'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooUser = factory();
    }
}(this, function ($, mooPhrase, mooFileUploader, mooAlert, mooGlobal, mooButton, mooOverlay, mooBehavior, mooAjax, mooTooltip) {
    
    var validateUser = function(){
        if (typeof(mooViewer) == 'undefined'){
            $.fn.SimpleModal({
                btn_ok : mooPhrase.__('btn_ok'),
                btn_cancel: mooPhrase.__('btn_cancel'),
                model: 'modal',
                title: mooPhrase.__('warning'),
                contents: mooPhrase.__('please_login')
            }).showModal();
                    return false;
        }
        else if (mooCore['setting.require_email_validation'] && !mooViewer['is_confirmed']){
            $.fn.SimpleModal({
                btn_ok : mooPhrase.__('btn_ok'),
                btn_cancel: mooPhrase.__('btn_cancel'),
                model: 'modal',
                title: mooPhrase.__('warning'),
                contents: mooPhrase.__('please_confirm_your_email')
            }).showModal();
                    return false;
        }
        else if (mooCore['setting.approve_users'] && !mooViewer['is_approved']){
            $.fn.SimpleModal({
                btn_ok : mooPhrase.__('btn_ok'),
                btn_cancel: mooPhrase.__('btn_cancel'),
                model: 'modal',
                title: mooPhrase.__('warning'),
                contents: mooPhrase.__('your_account_is_pending_approval')
            }).showModal();
                    return false;
        }
        
        var result = {status:true,message:''};
        
        $('body').trigger('validateUser',[result]);
        
        if (!result.status)
    	{
        	$.fn.SimpleModal({
                btn_ok : mooPhrase.__('btn_ok'),
                btn_cancel: mooPhrase.__('btn_cancel'),
                model: 'modal',
                title: mooPhrase.__('warning'),
                contents: result.message
            }).showModal();
            return false;
    	}

        return true;
    };
    
    // app/View/Elements/registration.ctp
    var initOnRegistration = function(){
    	
    	$('#username').keyup(function() {
        	var value = '';
        	if ($('#username').val().trim() != '')
        		value = '-' + $('#username').val().trim();
    		$('#profile_user_name').html(value);
    	});
        
        $("#submitFormsignup").unbind('click');
	$("#submitFormsignup").click(function(){
            $('#step1Box').spin('small');

            $('#submitFormsignup').attr('disabled', 'disabled');
            $.post(mooConfig.url.base + "/users/ajax_signup_step1", $("#regForm").serialize(), function(data){
                
                $('#step1Box').spin(false);
                var response = JSON.parse(data);
                if (response.result == 0) {
                    $("#regError").fadeIn();
                    $("#regError").html(response.error);
                    $('#submitFormsignup').removeAttr('disabled');
                } else {
                    $("#regError").fadeOut();
                    window.location = response.redirect;
                }
            });

	});

        $("#step2Submit").unbind('click');
	$("#step2Submit").click(function(){
            if($('#tos').is(':checked')){
                
                $('#step2Box').spin('small');

                $('#step2Submit').attr('disabled', 'disabled');
                
                $.post(mooConfig.url.base + "/users/ajax_signup_step2", $("#regForm").serialize(), function(data){
                    $('#step2Box').spin(false);
                    var result = '';
                    var isJson = false;
                    
                    try
                    {
                        result = JSON.parse(data);
                        isJson = true;
                    }
                    catch(e)
                    {
                        
                    }
                    
                    if(isJson)
                    {
                        if(result.redirect)
                        {
                            window.location = result.redirect;
                        }
                    }
                    else
                    {
                        if (data != '') {
                            $("#regError").fadeIn();
                            $("#regError").html(data);
                            $('#step2Submit').removeAttr('disabled');
                            grecaptcha.reset(); // FIXED_JS if ($this->Moo->isRecaptchaEnabled()):

                        } else {
                            window.location = mooConfig.url.base + '/';
                        }
                    }
                });
            }else{
                $("#regError").fadeIn();
                $("#regError").html(mooPhrase.__('you_have_to_agree_with_term_of_service'));
            }
	});
    }

    var initOnSignupStep1FieldCountry = function()
    {
    	$('#country_id').unbind('change');
        $('#country_id').change(function(){
            $('.country_state').hide();
            $('#state_id').html("<option value=''></option>");
            if ($('#country').val() != '') {
                $.getJSON(mooConfig.url.base + "/countries/ajax_get_state/" + $('#country_id').val(), function (result) {
                    if (result.count > 0)
                    {
                        $('.country_state').show();                        
                        $.each(result.data, function(field){
                            $('#state_id').append("<option value='"+result.data[field].id+"'>" + result.data[field].name + "</option>");
                        });
                    }
                });
            }
        });
    }
    
    // app/Plugin/SocialIntegration/View/Auths/signup_step2.ctp
    var initOnSocialRegistration = function(is_recaptcha, provider){
    	$('#username').keyup(function() {
        	var value = '';
        	if ($('#username').val().trim() != '')
        		value = '-' + $('#username').val().trim();
    		$('#profile_user_name').html(value);
    	});
    	
    	$(".multi").multiSelect({
            selectAll: false,
            noneSelected: '',
            oneOrMoreSelected: mooPhrase.__('per_selected')
        });
        $("#step2Submit").click(function(){
            if($('#tos').is(':checked')){
                $('#step2Box').spin('small');

                $('#step2Submit').attr('disabled', 'disabled');
                $.post(mooConfig.url.base + "/social/auths/ajax_signup_step2/" + provider, $("#regForm").serialize(), function(data){
                    $('#step2Box').spin(false);
                    var result = '';
                    var isJson = false;
                    try{
                        result = JSON.parse(data);
                        isJson = true;
                    }
                    catch(e){
                        isJson = false;
                    }
                    if(isJson){
                        if(result.redirect){
                            window.location = result.redirect;
                        }
                    }
                    else{
                        if (data != '') {
                            $("#regError").fadeIn();
                            $("#regError").html(data);
                            $('#step2Submit').removeAttr('disabled');
                            if (is_recaptcha){
                                grecaptcha.reset();
                            }
                        } else {
                            window.location = mooConfig.url.base + '/';
                        }
                    }
                });
            }else{
                $("#regError").fadeIn();
                $("#regError").html('You have to agree with term of service');
            }
        });
    }
    
    // app/View/Users/ajax_signup_step1.ctp
    var initOnSignupStep1 = function(){
        
        var uploader = new mooFileUploader.fineUploader({
            element: $('#profile_picture')[0],
            request: {
                endpoint: mooConfig.url.base + "/upload/avatar_tmp"
            },
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i> ' + mooPhrase.__('drag_or_click_here_to_upload_photo') + '</div>'
            },
            validation: {
                allowedExtensions: mooConfig.photoExt,
                sizeLimit : mooConfig.sizeLimit
            },
            multiple: false,
            callbacks:{
                onError: mooGlobal.errorHandler,
                onComplete: function(id, fileName, response) {
                    $('#profile_picture_preview > img').attr('src', mooConfig.url.base + '/uploads/tmp/'+response.filename);
                    $('#profile_picture_preview > img').show();
                    $('#profile_picture_preview > img').css({height:'150px'});
                    $('#avatar').val(response.filepath);
                }
            }
        });
    }
    
    var storeCoords = function(c) {
        x = c.x;
        y = c.y;
        w = c.w;
        h = c.h;
    }
    
    // app/View/Users/avatar.ctp
    var jcrop_api;
        var x = 0,
            y = 0,
            w = 0,
            h = 0;
    var initOnProfilePicture = function(){
        
        $('#save-avatar').unbind('click');
        $('#save-avatar').click(function () {
            
            var data = $(this).data();
            if (x == 0 && y == 0 && w == 0 && h == 0 && data.upload == 1){
                mooAlert.alert(mooPhrase.__('please_select_area_for_cropping'));
            }
            else{
                if(data.upload == 1) {
                    $('#avatar_wrapper').spin('large');
                    var modal = $('#portlet-config');

                    $.post(mooConfig.url.base + '/upload/thumb', {x: x, y: y, w: w, h: h}, function (response) {
                        $('#avatar_wrapper').spin(false);
                        if (response != '') {
                            var json = $.parseJSON(response);
                            $('#member-avatar').attr('src', json.thumb);
                            // window.location = data.url; //
                        }
                    });
                }
                $.post(mooConfig.url.base + '/users/save_setting', $('#form_show_info').serialize(), function (response) {
                    window.location = data.url;
                });
            }
        });

        if( ! mooConfig.isMobile ) {
            $('#av-img2').Jcrop({
                aspectRatio: 1,
                onSelect: storeCoords,
                minSize: [180, 180]
            }, function () {
                jcrop_api = this;
            });
        }
        else
        {
            $('#save-avatar').addClass('hide');
            $('#submit-avatar').removeClass('hide');
        }

        var uploader = new mooFileUploader.fineUploader({
            element: $('#select-0')[0],
            multiple: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i> ' + mooPhrase.__('drag_or_click_here_to_upload_photo') + '</div>'
            },
            validation: {
                allowedExtensions: mooConfig.photoExt,
                sizeLimit : mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/upload/avatar"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function(id, fileName, response) {
                    $('#av-img').attr('src', response.avatar);
                    $('#av-img2').attr('src', response.avatar);
                    $('#member-avatar').attr('src', response.thumb);
                    $('#save-avatar').attr('data-upload',1);
                    jcrop_api.setImage(response.avatar);
                }
            }
        });
    }
    
    // app/View/Users/view.ctp
    var jcrop_api;
    var x = 0,
        y = 0,
        w = 0,
        h = 0;
    var initOnUserView = function(){
       
        $('#themeModal').on('click',' .save-avatar',function() {
            $('#avatar_wrapper').spin('large');
            var modal = $('#themeModal');

            $.post(mooConfig.url.base + '/upload/thumb', {x: x, y: y, w: w, h: h}, function(data) {

                modal.modal('hide');

                if ( data != '' ){
                    var json = $.parseJSON(data);
                    $('#member-avatar').attr('src', json.thumb);
                    $('#av-img').attr('src', json.avatar_mini);
                }
            });

        });
        
        $('#themeModal').on('click',' .save-cover',function() {
            var modal = $('#themeModal');
            $('#cover_wrapper').spin('large');

            var jcrop_width = $('#cover_wrapper .jcrop-holder').width();
            var jcrop_height = $('#cover_wrapper .jcrop-holder').height();

            $.post(mooConfig.url.base + '/upload/thumb_cover', {x: x, y: y, w: w, h: h, jcrop_width: jcrop_width, jcrop_height: jcrop_height}, function(data) {

                modal.modal('hide');

                if ( data != '' ){
                    var json = $.parseJSON(data);
                    $('#cover_img_display').attr("src",json.thumb);
                }
            });
        });
    }
    
    // app/View/Friends/ajax_add.ctp
    var initAddFriend = function(){
        
        $('#sendReqAddFriendBtn').unbind('click');
        $('#sendReqAddFriendBtn').click(function(){
            
            var data = $(this).data();
            var uid = data.uid;
            $('#sendReqAddFriendBtn').spin('small');
            
            mooButton.disableButton('sendReqAddFriendBtn');
            
            $.post(mooConfig.url.base + "/friends/ajax_sendRequest", $("#addFriendForm").serialize(), function(data){
                
                if ($('.suggestion_block').length){
                    $('.suggestion_block #addFriend_' + uid).parents('li:first').remove();
                    if ($('.suggestion_block li').length == 0){
                        $('.suggestion_block').remove();
                    }
                }
                
                mooButton.enableButton('sendReqAddFriendBtn');
                
                $('#themeModal').modal('hide');
                
                //mooAlert.alert(data);
                
                $('#addFriend_' + uid).parents('div.user-idx-item').append('<a href="' + mooConfig.url.base + '/friends/ajax_cancel/' + uid + '" id="cancelFriend_' + uid +'" class="add_people" title="' + mooPhrase.__('cancel_a_friend_request') + '"><i class="material-icons">clear</i>' + mooPhrase.__('cancel_request') + '</a>');
                $('#addFriend_' + uid).remove();
                if ($('#blogAddFriend').length){
                    $('#blogAddFriend').parents('.blog_view_leftnav').append('<li><a href="' + mooConfig.url.base + '/friends/ajax_cancel/' + uid + '" id="blogCancelFriend" class="" title="' + mooPhrase.__('cancel_a_friend_request') + '"><i class="material-icons icon-small">clear</i>' + mooPhrase.__('cancel_request') + '</a></li>');
                    $('#blogAddFriend').parents('li:first').remove();
                }
                if ($('#userAddFriend').length){
                    $('#userAddFriend').parents('.profile-action').append('<a id="userCancelFriend" href="' + mooConfig.url.base + '/friends/ajax_cancel/' + uid + '" class="topButton button button-action" title="' + mooPhrase.__('cancel_a_friend_request') + '"><i class="visible-xs visible-sm material-icons">clear</i><i class="hidden-xs hidden-sm">' + mooPhrase.__('cancel_request') + '</i></a>');
                    $('#userAddFriend').remove();
                }
                
                location.reload();
            });
            return false;
	});
    }
    
    // app/View/Friends/ajax_remove.ctp
    var initRemoveFriend = function(){
        
        $('#removeFriendButton').unbind('click');
        $('#removeFriendButton').click(function(){
            
            var data = $(this).data();
            var uid = data.uid;
            
            mooButton.disableButton('removeFriendButton');
            
            $.post(mooConfig.url.base + "/friends/ajax_removeRequest", $("#removeFriendForm").serialize(), function(data){
                
                mooButton.enableButton('removeFriendButton');
                
                $('#themeModal').modal('hide');
                
                // mooAlert.alert(data);
                
                var liUser = $('#removeFriend_'  + uid).parents('li:first')
                var liUserParent = liUser.parents('li[id^="activity_"]');
                
                liUser.remove();

                //remove this out of activity
                liUserParent.remove();
                
                location.reload();
            });
            
            return false;
        });
    }

    var initRemoveFollow = function(){

        $('#removeFollowButton').unbind('click');
        $('#removeFollowButton').click(function(){

            var data = $(this).data();
            var uid = data.uid;

            mooButton.disableButton('removeFollowButton');

            $.post(mooConfig.url.base + "/follows/ajax_removeRequest", $("#removeFollowForm").serialize(), function(data){

                mooButton.enableButton('removeFollowButton');

                $('#themeModal').modal('hide');

                $('#profile_follow').click();
                var count = parseInt($('#profile_follow .badge_counter').html());
                count--;
                $('#profile_follow .badge_counter').html(count);
            });

            return false;
        });
    }
    
    // app/View/Friends/ajax_requests.ctp
    var initAjaxRequest = function(){
        $('.respondRequest').unbind('click');
        $('.respondRequest').on('click', function(){
        	$(this).addClass('disabled');
            var data = $(this).data();
            if ($('#friend_request_count').length > 0){
                var current_request = $('#friend_request_count').html();
                var new_request = parseInt(current_request - 1);
                if (new_request <= 0){
                    $('#friend_request_count').parents('li:first').remove();
                }else {
                    $('#friend_request_count').html(new_request);
                }
            }

            $.post(mooConfig.url.base + '/friends/ajax_respond', {id: data.id, status: data.status}, function(response){
                $('#request_'+ data.id).html(response);
            });
        });
        
    }
    
    var initAjaxRequestPopup = function(){
        $('.respondRequest').unbind('click');
        $('.respondRequest').on('click', function(){
        	$(this).addClass('disabled');
            var data = $(this).data();
            
            $.post(mooConfig.url.base + '/friends/ajax_respond', {id: data.id, status: data.status}, function(response){
                $('#request_'+ data.id).html(response);
                
                location.reload();
            });
        });
        
    }
    
    // app/View/Users/ajax_birthday_more.ctp
    var initBirthdayPopup = function(){
        $('.more-birthday-email').unbind('click');
        $('.more-birthday-email').click(function(){
            if($('#langModal').modal('show')){
                $('#langModal').modal('hide');
            }
        })
        
        $('.postFriendWall').unbind('click');
        $('.postFriendWall').click(function(){
            var id = $(this).data('id');
            var me = $(this);
            var msg = $('#message_'+id).val();
            if ($.trim(msg) != '')
            {
                mooButton.disableButton('status_btn_'+id);
                $.post(mooConfig.url.base + "/activities/send_birthday_wish", $("#wallForm_"+id).serialize(), function(response){
                    var json = $.parseJSON(response);
                    mooButton.enableButton('status_btn_'+id);
                    $('#message_'+id).val("");
                    if (json.success)
                    {
                        mooButton.enableButton('status_btn_'+id);
                        me.parent(".birthday-wish").html("<div style='padding:5px 0px;'>" + mooPhrase.__('birthday_wish_is_sent')+ "</div>");
                    }
                });
            }
        })
    }
    
    var respondRequest =  function(id, status){
        $.post(mooConfig.url.base + '/friends/ajax_respond', {id: id, status: status}, function(response){
            location.reload();
        });
    }
    
    // app/View/Users/ajax_cover.ctp
    var initEditCoverPicture = function(){
        
        var JCropper;
        if( !mooConfig.isMobile ) {
            $('#cover-img').Jcrop({
                aspectRatio: 4,
                onSelect: storeCoords,
                minSize: [ 400, 200 ],
                boxWidth: 570
            }, function(){
                JCropper = this;
            });
        }

        var uploader = new mooFileUploader.fineUploader({
            element: $('#select-1')[0],
            multiple: false,
            autoUpload: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>' + mooPhrase.__('drag_or_click_here_to_upload_photo') + '</div>'
            },
            validation: {
                allowedExtensions: mooConfig.photoExt,
                sizeLimit : mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/upload/cover"
            },
            callbacks: {
                onSubmit: function(id, fileName){
                    var promise = validateFileDimensions(id, [400, 150],this);
                    return promise;
                },
                onError: mooGlobal.errorHandler,
                onComplete: function(id, fileName, response) {
                    $('#cover_img_display').attr("src",response.cover);

                    if( !mooConfig.isMobile ) {
                        JCropper.setImage(response.photo);
                    }else{
                        $('#cover_wrapper img').attr('src', response.photo);
                    }
                }
            }
        });
        function validateFileDimensions(id, dimensionsLimits,obj)
        {
            window.URL = window.URL || window.webkitURL;
            var file = obj.getFile(id);

            var image = new Image();
            var status = false;
            var sizeDetermination = {};

            image.onerror = function(e) {
                sizeDetermination['error'] = mooPhrase.__('cannot_determine_dimensions_for_image_may_be_too_large');
            };

            image.onload = function() {
                sizeDetermination = { width: this.width, height: this.height };

                var minWidth = sizeDetermination.width >= dimensionsLimits[0],
                    minHeight = sizeDetermination.height >= dimensionsLimits[1];

                // if min-width or min-height satisfied the limits, then approve the image
                if( minWidth && minHeight ){
                    uploader.uploadStoredFiles();
                }
                else{
                    uploader.clearStoredFiles();
                    mooAlert.alert(mooPhrase.__('please_choose_an_image_that_s_at_least_400_pixels_wide_and_at_least_150_pixels_tall'));
                }
            };
            image.src = window.URL.createObjectURL(file);
        }
    }
    
    // app/View/Users/ajax_avatar.ctp
    var initEditProfilePicture = function(){
        var JCropperAvatar;
        if( !mooConfig.isMobile ) {
            $('#av-img2').Jcrop({
                aspectRatio: 1,
                onSelect: storeCoords,
                minSize: [ 180, 180 ],
                boxWidth: 570
            }, function(){
                JCropperAvatar = this;
            });
        }
        else{
            $('.modal-footer').addClass('hide');
        }

        var uploader = new mooFileUploader.fineUploader({
            element: $('#select-0')[0],
            multiple: false,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">photo_camera</i>' + mooPhrase.__('drag_or_click_here_to_upload_photo') + '</div>'
            },
            validation: {
                allowedExtensions: mooConfig.photoExt,
                sizeLimit : mooConfig.sizeLimit
            },
            request: {
                endpoint: mooConfig.url.base + "/upload/avatar"
            },
            callbacks: {
                onError: mooGlobal.errorHandler,
                onComplete: function(id, fileName, response) {
                    $('#av-img').attr('src', response.avatar_mini);
                    if( !mooConfig.isMobile ) {
                       JCropperAvatar.setImage(response.avatar);
                    }else{
                        $('#avatar_wrapper img').attr('src', response.avatar);
                    }
                    $('#member-avatar').attr('src', response.thumb);   
                }
            }
        });
    }
    
    // app/View/Elements/ajax/profile_edit.ctp
    var initOnProfileEdit = function(){
        
        $(".multi").multiSelect({
            selectAll: false,
            noneSelected: '',
            oneOrMoreSelected: mooPhrase.__('per_selected'),
        });
        
        $('#username').keyup(function() {
        	var value = '';
        	if ($('#username').val().trim() != '')
        		value = '-' + $('#username').val().trim();
    		$('#profile_user_name').html(value);
    	});
        
        // bind action check username
        $('#checkButton').unbind('click');
        $('#checkButton').click(function(){
            checkUsername();
        });
    }
    
    var checkUsername = function(){
        
        mooButton.disableButton('checkButton');

        $.post(mooConfig.url.base + "/users/ajax_username", {username: $('#username').val()}, function(data){

            mooButton.enableButton('checkButton');

            var res = $.parseJSON(data);

            $('#message').html( res.message );

            if ( res.result == 1 ){
                $('#message').css('color', 'green');
            }
            else {
                $('#message').css('color', 'red');
            }

            $('#message').show();			
        });
    }
    
    // app/View/Elements/lists/users_list.ctp
    var initOnUserList = function(){
        
        $("#list-content li").hover(
            function () {
		$(this).contents().find('.delete-icon').show();
            },
            function () {
		$(this).contents().find('.delete-icon').hide();
            }
	);

        // app/View/Elements/lists/users_list_bit.ctp
        initRespondRequest();
        
        // init remove member
        initRemoveMember();
        
        // init change admin action
        initChangeAdmin();
        
        mooBehavior.initMoreResults();
    }
    
    // app/View/Elements/lists/users_list_bit.ctp
    var initRemoveMember = function(){
        $('.removeMember').unbind('click');
        $('.removeMember').on('click', function(){
            var data = $(this).data();
            removeMember(data.id);
        });
    }
    
    // app/View/Elements/lists/users_list_bit.ctp
    var initChangeAdmin = function(){
        $('.changeAdmin').unbind('click');
        $('.changeAdmin').on('click', function(){
            var data = $(this).data();
            changeAdmin(data.id, data.type);
        });
    }

    var removeMember = function (id)
    {
        $.fn.SimpleModal({
            btn_ok : mooPhrase.__('btn_ok'),
            btn_cancel: mooPhrase.__('btn_cancel'),
            callback: function () {
                $.post(mooConfig.url.base + '/groups/ajax_remove_member', {id: id}, function () {
                    $('#member_' + id).fadeOut();

                    if ($("#group_user_count").html() != '0') {
                        $("#group_user_count").html(parseInt($("#group_user_count").html()) - 1);
                    }
                });
            },
            title: mooPhrase.__('please_confirm'),
            contents: mooPhrase.__('are_you_sure_you_want_to_remove_this_member'),
            model: 'confirm',
            hideFooter: false,
            closeButton: false
        }).showModal();

        return false;
    }

    var changeAdmin = function (id, type)
    {
        var msg = mooPhrase.__('are_you_sure_you_want_to_make_this_member_a_group_admin');
        if (type == 'remove') {
            msg = mooPhrase.__('are_you_sure_you_want_to_demote_this_group_admin');
        }

        $.fn.SimpleModal({
            btn_ok: mooPhrase.__('btn_ok'),
            btn_cancel: mooPhrase.__('btn_cancel'),
            callback: function () {
                $.post(mooConfig.url.base + '/groups/ajax_change_admin', {id: id, type: type}, function () {
                    window.location.reload();
                });
            },
            title: mooPhrase.__('please_confirm'),
            contents: msg,
            model: 'confirm',
            hideFooter: false,
            closeButton: false
        }).showModal();

        return false;
    }
    
    var initRespondRequest = function(){
        $('.respondRequest').unbind('click');
        $('.respondRequest').on('click', function(){
           $('.respondRequest').unbind('click');
           var data = $(this).data();
           respondRequest(data.id, data.status);
        });

        $('.user_action_follow').unbind('click');
        $('.user_action_follow').click(function(){
            element = $(this);
            $.ajax({
                type: 'POST',
                url: mooConfig.url.base + '/follows/ajax_update_follow',
                data : {user_id:$(this).data('uid')},
                success: function (data) {
                    if (element.data('follow'))
                    {
                        element.data('follow',0);
                        element.find('.hidden-xs').html(mooPhrase.__('text_follow'));
                        element.find('.visible-xs').html('rss_feed');
                    }
                    else
                    {
                        element.data('follow',1);
                        element.find('.hidden-xs').html(mooPhrase.__('text_unfollow'));
                        element.find('.visible-xs').html('check');
                    }
                }
            });
        });
    }
    
    var initShowAlbums = function(){
        $('.showAlbums').unbind('click');
        $('.showAlbums').on('click', function(){
            showAlbums($(this).data('userId'));
        });
    }
    
    // app/View/Users/view.ctp
    var showAlbums = function (uid){
        
        $('#user_photos').spin('tiny');
        $('#user_photos').children('.badge_counter').hide();
        
        $('#profile-content').load(mooConfig.url.base + '/photos/profile_user_album/' + uid, {noCache: 1}, function (response) {
            $(this).html(response);
            $('#user_photos').spin(false);
            $('#user_photos').children('.badge_counter').fadeIn();
        });
    }
    
    // app/View/Users/view.ctp
    var requestJoinGroup = function(group_id){
        
        $.post(mooConfig.url.base + '/groups/request_to_join', {group_id: group_id}, function() {
            $.fn.SimpleModal({
                btn_ok: mooPhrase.__('btn_done'),
                btn_cancel: mooPhrase.__('btn_cancel'),
                callback: function(){
                    window.location = "";// "<?php echo $this->Html->url(array('plugin' => 'group', 'controller' => 'groups', 'action' => 'view', $groupTypeItem['id'])); ?>";
                },
                title: mooPhrase.__('join_group_request'),
                contents: mooPhrase.__('your_request_to_join_group_sent_successfully'),
                model: 'confirm', 
                hideFooter: false, 
                closeButton: false
            }).showModal();
        });
    }
    
    // app/View/Users/index.ctp
    // app/View/Landing/index.ctp
    var initOnUserIndex = function(){
        
        $(".multi").multiSelect({
            selectAll: false,
            noneSelected: '',
            oneOrMoreSelected: mooPhrase.__('per_selected')
        });
    
        $("#searchPeople").unbind('click');
        $("#searchPeople").click(function(){

            $('#everyone a').spin('tiny');
            $('#browse .current').removeClass('current');
            $('#everyone').addClass('current');

            $.post(mooConfig.url.base + '/users/ajax_browse/search', $("#filters").serialize(), function(data){
                $('#everyone a').spin(false);
                $('#list-content').html(data);
                mooOverlay.registerOverlay();
            });

            if($(window).width() < 992){
                $('#leftnav').modal('hide');
                $('body').scrollTop(0);
            }
        });
    }
    
    // app/View/Users/profile.ctp
    var initOnUserProfile = function(){
        $('.deactiveMyAccount').unbind('click');
        $('.deactiveMyAccount').on('click', function(){
            mooAlert.confirm(mooPhrase.__('confirm_deactivate_account'), mooConfig.url.base + '/users/deactivate');
        });
        
        $('.deleteMyAccount').unbind('click');
        $('.deleteMyAccount').on('click', function(){
            mooAlert.confirm(mooPhrase.__('confirm_delete_account'), mooConfig.url.base + '/users/delete_account');
        });

        var SPECIALTY_PHARMACIST = 1;
        var SPECIALTY_STUDENT = 2;
        var SPECIALTY_OTHER = 3;

        $('.specialty').on('click', function () {
            var specialty = $(this).val();
            switch (parseInt(specialty)){
                case SPECIALTY_PHARMACIST:
                    $('#university_info').show();

                    break;
                case SPECIALTY_STUDENT:
                    $('#university_info').show();
                    break;
                case SPECIALTY_OTHER:
                    $('#university_info').hide();
                    break;
            }
        });

        $('.univer_radio').on('change', function () {
            $('#university').val('');
        });

        $('#save_profile').click(function( event ) {
        	event.preventDefault();
        	button = $(this);
	        button.addClass('disabled');
	        mooAjax.post({
		        url : mooConfig.url.base + '/users/ajax_save_profile',
		        data: jQuery("#form_edit_user").serialize()
		    }, function(data){
		        var json = $.parseJSON(data);
		
		        if ( json.status )
				{
		        	location.reload();
				}
		        else
		        {
		            button.removeClass('disabled');
		            $(".error-message").show();
		            $(".error-message").html(json.message);
		        }
		    });
	        return false;
        });

        $('#save_profile_1').click(function( event ) {
            event.preventDefault();
            button = $(this);
            button.addClass('disabled');
            var btn_data = $(this).data();
            mooAjax.post({
                url : mooConfig.url.base + '/users/ajax_save_profile_1',
                data: jQuery("#form_edit_user").serialize()
            }, function(data){
                var json = $.parseJSON(data);

                if ( json.status )
                {
                    if(json.message){
                        // mooAlert.alert(json.message);
                        $.fn.SimpleModal({
                            btn_ok: mooPhrase.__('btn_ok'),
                            btn_cancel: mooPhrase.__('cancel'),
                            title: mooPhrase.__('message'),
                            hideFooter: false,
                            closeButton: false,
                            model: 'alert',
                            contents: json.message,
                            reload: 1,
                        }).showModal();
                    }else{
                        location.reload();
                    }
                }
                else
                {
                    button.removeClass('disabled');
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });

        $('#save_profile_2').click(function( event ) {
            event.preventDefault();
            button = $(this);
            button.addClass('disabled');
            mooAjax.post({
                url : mooConfig.url.base + '/users/ajax_save_profile_2',
                data: jQuery("#form_edit_user").serialize()
            }, function(data){
                var json = $.parseJSON(data);

                if ( json.status )
                {
                    location.reload();
                }
                else
                {
                    button.removeClass('disabled');
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });


        $('#cancel_validation').click(function( event ) {
            event.preventDefault();
            button = $(this);
            button.addClass('disabled');
            mooAjax.post({
                url : mooConfig.url.base + '/users/ajax_revert/0',
            }, function(data){
                var json = $.parseJSON(data);

                if ( json.status )
                {
                    location.reload();
                }
                else
                {
                    button.removeClass('disabled');
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });

        $('#cancel_sub_validation').click(function( event ) {
            event.preventDefault();
            button = $(this);
            button.addClass('disabled');
            mooAjax.post({
                url : mooConfig.url.base + '/users/ajax_revert/1',
            }, function(data){
                var json = $.parseJSON(data);

                if ( json.status )
                {
                    location.reload();
                }
                else
                {
                    button.removeClass('disabled');
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });
            return false;
        });
    }

    // app/View/Widgets/user/closeNetworkSignup.ctp
    var initOnCloseNetworkSignup = function(is_recaptcha){
    	$('#username').keyup(function() {
        	var value = '';
        	if ($('#username').val().trim() != '')
        		value = '-' + $('#username').val().trim();
    		$('#profile_user_name').html(value);
    	});
    	
        $("#submitFormsignup").click(function(){
            $('#step1Box').spin('small');

            $('#submitFormsignup').attr('disabled', 'disabled');
            $.post(mooConfig.url.base + "/users/ajax_signup_step1", $("#regForm").serialize(), function(data){
                $('#step1Box').spin(false);
                var response = JSON.parse(data);
                if (response.result == 0) {
                    $("#regError").fadeIn();
                    $("#regError").html(response.error);
                    $('#submitFormsignup').removeAttr('disabled');
                } else {
                    $("#regError").fadeOut();
                    window.location = response.redirect;
                }
            });

        });

        $("#step2Submit").click(function(){
            if($('#tos').is(':checked')){
                
                var isJson = false;
                
                $('#step2Box').spin('small');

                $('#step2Submit').attr('disabled', 'disabled');
                $.post(mooConfig.url.base + "/users/ajax_signup_step2", $("#regForm").serialize(), function(data){
                    $('#step2Box').spin(false);
                    var result = '';
                    
                    try
                    {
                        result = JSON.parse(data);
                        isJson = true;
                    }
                    catch(e)
                    {
                        isJson = false;
                    }
                    
                    if(isJson)
                    {
                        if(result.redirect)
                        {
                            window.location = result.redirect;
                        }
                    }
                    else
                    {
                        if (data != '') {
                            $("#regError").fadeIn();
                            $("#regError").html(data);
                            $('#step2Submit').removeAttr('disabled');
                            if (is_recaptcha){
                                grecaptcha.reset();
                            }
                            
                        } else {
                            window.location = mooConfig.url.webroot;
                        }
                    }
                });
                
            }else{
                $("#regError").fadeIn();
                $("#regError").html(mooPhrase.__('you_have_to_agree_with_term_of_service'));
            }
        });
    }
    
    var resendValidationLink = function(){
        $('#resend_validation_link').unbind('click');
        $('#resend_validation_link').on('click', function(){
            $.post(mooConfig.url.base + '/users/resend_validation', {}, function(data){
                mooAlert.alert(mooPhrase.__('validation_link_has_been_resend'));
            });
        });

        $('#resend_sub_validation_link_0').unbind('click');
        $('#resend_sub_validation_link_0').on('click', function(){
            $.post(mooConfig.url.base + '/users/resend_sub_validation/0', {}, function(data){
                mooAlert.alert(mooPhrase.__('validation_link_has_been_resend'));
            });
        });

        $('#email').on('keyup', function () {
            if($(this).val() == ''){
                $('.btn-submit').attr('disabled','disabled');
            }else{
                $('.btn-submit').removeAttr('disabled');
            }
        });

        $('#resend_validation_link_1').unbind('click');
        $('#resend_validation_link_1').on('click', function(){
            $.post(mooConfig.url.base + '/users/resend_validation/0', {email: $('#email').val()}, function(result){
                //mooAlert.alert(mooPhrase.__('an_email_has_been_sent_to_your_new_email'));
                var json = $.parseJSON(result);
                if ( json.status == 1 ) {
                    $('#resend_validation_link_1').hide();
                    $('#mail_input').html($('#email').val());
                    $('#v_email').val($('#email').val());
                    $('#input_mail').hide();
                    $('#sent_mail').show();
                    $('#btn_ok').show();
                    $('#error_message').hide();
                    $('#wrap_resend').show();
                    $('#btn_change_mail').hide();

                }else{
                    $('#error_message').html(json.message);
                    $('#error_message').show();
                }
            });
        });

        $('#resend_sub_validation_link').unbind('click');
        $('#resend_sub_validation_link').on('click', function(){
            $.post(mooConfig.url.base + '/users/resend_sub_validation/0', {email: $('#email').val()}, function(result){
                // mooAlert.alert(mooPhrase.__('an_email_has_been_sent_to_your_new_email'));
                var json = $.parseJSON(result);
                if ( json.status == 1 ) {
                    $('#resend_sub_validation_link').hide();
                    $('#mail_input').html($('#email').val());
                    $('#input_mail').hide();
                    $('#sent_mail').show();
                    $('#error_message').hide();
                    $('#btn_ok').show();
                    $('#wrap_resend_sub').show();
                    $('#btn_change_submail').hide();
                    $('#v_sub_mail').val($('#email').val());
                }else{
                    $('#error_message').html(json.message);
                    $('#error_message').show();
                }
            });
        });

        $('#update_email').unbind('click');
        $('#update_email').on('click', function(){
            var data = $(this).data();
            $.post(mooConfig.url.base + '/users/ajax_update_mail', {email: $('#email').val(), type: data.type}, function(result){
                var json = $.parseJSON(result);

                if ( json.status == 1 )
                {
                    $('#btn_cancel').trigger('click');
                    // mooAlert.alert(mooPhrase.__('your_email_has_been_updated'));
                    location.reload();
                }else{
                    $('#error_message').html(json.message);
                    $('#error_message').show();
                }
            });
        });

        $('#sms_verify_phone').on('keyup', function () {
            if($(this).val() == ''){
                $('#send_verify_mobile').attr('disabled','disabled');
            }else{
                $('#send_verify_mobile').removeAttr('disabled');
            }
        });

        $('#send_verify_mobile').unbind('click');
        $('#send_verify_mobile').on('click', function(){
            $.post(mooConfig.url.base + '/sms_verifys/resend', {phone: $('#sms_verify_phone').val()}, function(data){
                var json = $.parseJSON(data);
                if ( json.status == 1 )
                {
                    $('#send_verify_mobile').html(mooPhrase.__('resend'));
                    $('#input_phone').hide();
                    $('#update_phone_section').show();
                    $('#btn_update_phone').show();
                    $('#error_message').hide();
                }else{
                    if(typeof json.message != 'undefined'){
                        $('#error_message').html(json.message);
                    }else{
                        $('#error_message').html(mooPhrase.__('something_went_wrong'));
                    }

                    $('#error_message').show();
                }
            });
        });

        $('#btn_update_phone').unbind('click');
        $('#btn_update_phone').on('click', function(){
            $.post(mooConfig.url.base + '/users/ajax_update_phone', {sms_verify_code: $('#sms_verify_code').val()}, function(result){
                var json = $.parseJSON(result);

                if ( json.status == 1 )
                {
                    $('#btn_cancel').trigger('click');
                    // mooAlert.alert(mooPhrase.__('your_email_has_been_updated'));
                    location.reload();
                }else{
                    $('#error_message').html(json.message);
                    $('#error_message').show();
                }
            });
        });

    }
    
    var initBlockUser = function(){
        
        $('#blockUserButton').unbind('click');
        $('#blockUserButton').click(function(){
            
            $('#blockUserButton').spin('small');
            
            mooButton.disableButton('blockUserButton');
            
            $.post(mooConfig.url.base + "/user_blocks/ajax_do_add", $("#blockUserForm").serialize(), function(data){
                
                mooButton.enableButton('blockUserButton');
                
                $('#themeModal').modal('hide');
                window.location.href = mooConfig.url.base + '/';
            });
            return false;
	});
    }
    
     var initUnBlockUser = function(){
        
        $('#unBlockUserButton').unbind('click');
        $('#unBlockUserButton').click(function(){
            
            $('#unBlockUserButton').spin('small');
            
            mooButton.disableButton('unBlockUserButton');
            
            $.post(mooConfig.url.base + "/user_blocks/ajax_do_remove", $("#unBlockUserForm").serialize(), function(data){
                
                mooButton.enableButton('unBlockUserButton');
                
                $('#themeModal').modal('hide');
                
                location.reload();
            });
            return false;
	});
    }
    
    var delay = (function(){
	  var timer = 0;
	  return function(callback, ms){
	    clearTimeout (timer);
	    timer = setTimeout(callback, ms);
	  };
	})();
    var initSearchFriend = function(id)
    {    	
     	$('#search_friend').unbind('keyup');
     	$('#search_friend').keyup(function() {
     	    delay(function(){
 	    		if (id)
    			{
 	    			url = mooConfig.url.base + "/users/profile_user_friends/"+id;
    			}
 	    		else
 	    		{
 	    			url = mooConfig.url.base + "/users/ajax_browse/home";
 	    		}
     	    	$.post(url, {is_search: true,search: $('#search_friend').val()}, function (data) {
                    $('#list-content').html(data);
					mooBehavior.initMoreResults();
					mooTooltip.init();
                });
     	    }, 200 );
     	});
    }
    
    var loadProfileType = function(type){
        $('#profile_type_id').change(function(){
            var profile_type_id = $('#profile_type_id').val();
            $.get( mooConfig.url.base + "/users/ajax_loadfields/" + profile_type_id + '/' + type, function( data ) {
                $( ".custom-field" ).html( data );
                
                $(".multi").multiSelect({
                    selectAll: false,
                    noneSelected: '',
                    oneOrMoreSelected: mooPhrase.__('per_selected'),
                });
            });
        });
    }

    var initRegStep2 = function(type){
        initOnSignupStep1();

        var SPECIALTY_PHARMACIST = 1;
        var SPECIALTY_STUDENT = 2;
        var SPECIALTY_OTHER = 3;

        $("#btn_submit_step_2").unbind('click');
        $("#btn_submit_step_2").click(function() {
            $('#btn_submit_step_2').spin('small');
            $.post( mooConfig.url.base + "/users/save_step_2/", $('#form_reg_step_2').serialize(), function( data ) {
                var json = $.parseJSON(data);

                if ( json.result == 1 )
                {
                        window.location = mooConfig.url.base + '/users/step_3';
                }
                else
                {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
                $('#btn_submit_step_2').spin(false);
            });
        });

        $('.specialty').on('click', function () {
            var specialty = $(this).val();
            switch (parseInt(specialty)){
                case SPECIALTY_PHARMACIST:
                    $('#university_info').show();
                    $('.hide-sale').show();

                    break;
                case SPECIALTY_STUDENT:
                    $('#university_info').show();
                    $('.hide-sale').show();
                    break;
                case SPECIALTY_OTHER:
                    $('#university_info').hide();
                    $('.hide-sale').show();
                    break;
            }
        });

        $('#sub_mail').on('keyup' ,function () {
            var txt = $(this).val();
            $('#sub_mail_txt').html(txt);
            $('#sub_mail_to').val(txt);
        });
    }

    var initRegStep3 = function(type){
        var pharma = ['pharmacy'];
        var hospital = ['hosp_pharmacy'];
        var gov = ['government','newspaper','other_company'];
        var it = ['pharmacy_chain','it_software'];
        var other = ['others'];
        $('.job-item').click(function () {
            var job = $(this).val();
            $('#require_department').val('0');
            if($.inArray(job, pharma) > -1){
                $('#type_name').html(mooPhrase.__('phar_name'));
                $('#type_phone').html(mooPhrase.__('phar_phone'));
                $('#section_work_at_home').hide();
                $('#section_work_at_home input').attr('disabled','disabled');
                $('#wrap_form_info').show();
                $('#section_department').hide();
                $('#sale_area').val('');
                $('.department').removeAttr('checked');
                $('.sale-area-section').hide();
                $('#form_reg_step_3 input[type="text"]').removeAttr('disabled');

                $('#title_input input').attr('disabled','disabled');
                $('#title_radio input').removeAttr('disabled');
                $('#title_input').hide();
                $('#title_radio').show();
            }else if($.inArray(job, hospital) > -1){
                $('#type_name').html(mooPhrase.__('hosp_name'));
                $('#type_phone').html(mooPhrase.__('hosp_phone'));
                $('#section_work_at_home').hide();
                $('#section_work_at_home input').attr('disabled','disabled');
                $('#wrap_form_info').show();
                $('#section_department').hide();
                $('#sale_area').val('');
                $('.department').removeAttr('checked');
                $('.sale-area-section').hide();
                $('#form_reg_step_3 input[type="text"]').removeAttr('disabled');

                $('#title_input input').attr('disabled','disabled');
                $('#title_radio input').removeAttr('disabled');
                $('#title_input').hide();
                $('#title_radio').show();
            }else if($.inArray(job, gov) > -1){
                $('#type_name').html(mooPhrase.__('com_name'));
                $('#type_phone').html(mooPhrase.__('com_phone'));
                $('#section_work_at_home').hide();
                $('#section_work_at_home input').attr('disabled','disabled');
                $('#wrap_form_info').show();
                $('#section_department').hide();
                $('#sale_area').val('');
                $('.department').removeAttr('checked');
                $('.sale-area-section').hide();
                $('#form_reg_step_3 input[type="text"]').removeAttr('disabled');

                $('#title_radio input').attr('disabled','disabled');
                $('#title_input input').removeAttr('disabled');
                $('#title_input').show();
                $('#title_radio').hide();
            }else if($.inArray(job, other) > -1){
                $('#type_name').html(mooPhrase.__('com_name'));
                $('#type_phone').html(mooPhrase.__('com_phone'));

                $('#section_work_at_home').show();
                $('#section_work_at_home input').removeAttr('disabled');

                $('#section_department').hide();
                $('#sale_area').val('');
                $('.department').removeAttr('checked');
                $('.sale-area-section').hide();
                $('#wrap_form_info').hide();

                $('#title_radio input').attr('disabled','disabled');
                $('#title_input input').removeAttr('disabled');
                $('#title_input').show();
                $('#title_radio').hide();

                if($('#work_at_home').is(':checked')){
                    $('#wrap_form_info input[type="text"]').val('');
                    $('#wrap_form_info input[type="text"]').attr('disabled','disabled');
                }
            }else{
                $('#type_name').html(mooPhrase.__('com_name'));
                $('#type_phone').html(mooPhrase.__('com_phone'));
                $('#section_work_at_home').hide();
                $('#wrap_form_info').show();
                $('#section_department').show();
                $('#require_department').val('1');
                $('.department').removeAttr('checked');
                $('#form_reg_step_3 input[type="text"]').removeAttr('disabled');

                $('#title_radio input').attr('disabled','disabled');
                $('#title_input input').removeAttr('disabled');
                $('#title_input').show();
                $('#title_radio').hide();

                if($.inArray(job, it) > -1){
                    $('#sale_area').val('');
                    $("#sale_area").tokenInput('clear');
                    $('.sale-area-section').hide();
                }
            }
        });

        $(".com-phone").keyup(function () {
            if ($(this).val().length == $(this).attr('maxLength')) {
                $(this).next("input").focus();
            }
        });

        $('.job-interest').change(function () {
           $('#job_interest_text').val('');
        });
        
        $("#btn_submit_step_3").unbind('click');
        $("#btn_submit_step_3").click(function() {
            $('#btn_submit_step_3').spin('small');

            var i = 0;
            $('.com-phone').each(function () {
                if (i == 0) {
                    $('#temp_phone').val('');
                }
                if(i < 3) {
                    $('#temp_phone').val($('#temp_phone').val() + $(this).val());
                }
                i++;
            });
            $.post( mooConfig.url.base + "/users/save_step_3/", $('#form_reg_step_3').serialize(), function( data ) {
                var json = $.parseJSON(data);

                if ( json.result == 1 )
                {
                    if(type == 'profile'){
                        location.reload();
                    }else {
                        window.location = mooConfig.url.base + '/users/step_4';
                    }
                }
                else
                {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
                $('#btn_submit_step_3').spin(false);
            });
        });

        $("#btn_find_phone").unbind('click');
        $("#btn_find_phone").click(function() {
            $('#btn_find_phone').spin('small');
            var phone = '';
            var i = 0;
            $('.com-phone').each(function () {
                if (i < 3) {
                    phone += $(this).val();
                }
                if (i < 2) {
                    phone += '-';
                }
                i++;
            });

            $.post( mooConfig.url.base + "/users/find_by_phone/", {'phone': phone}, function( data ) {
                $('#btn_find_phone').spin(false);
                var json = $.parseJSON(data);
                if ( json.result == 1 )
                {
                    $('#com_name').val(json.name);
                    $('#com_address_1').val(json.addr_1);
                    $('#com_address_2').val(json.addr_2);
                    $('#com_fax').val(json.fax);
                    $('#com_zip').val(json.zip);
                }else{
                    $('#com_name').focus();
                }
            });
        });

        $('.department').on('click', function () {
            if($(this).val() == 'sales' && $.inArray( $('.job-item:checked').val(), it) < 0){
                $('.sale-area-section').show();
            }else{
                $('.sale-area-section').hide();
            }
        });

        $(document).ready(function() {
            var json = $('#prepopulate').val();
            if(json) {
                json = $.parseJSON(json);
                $("#sale_area").tokenInput(mooConfig.url.base + "/users/search_sale_area",
                    {
                        preventDuplicates: true,
                        hintText: '',
                        noResultsText: mooPhrase.__('no_results'),
                        resultsFormatter: function (item) {
                            return '<li>' + item.name + '</li>';
                        },
                        prePopulate: json,
                        onAdd: function (item) {

                        },
                        onDelete: function () {

                        }
                    }
                );
            }
        });

        $('#work_at_home').click(function () {
            if($('#work_at_home').is(':checked')) {
                $('#wrap_form_info input[type="text"]').val('');
                $('#wrap_form_info input[type="text"]').attr('disabled', 'disabled');
            }else{
                $('#wrap_form_info input[type="text"]').removeAttr('disabled');
            }
        });
        if($('#work_at_home').is(':checked')){
            $('#wrap_form_info input[type="text"]').val('');
            $('#wrap_form_info input[type="text"]').attr('disabled','disabled');
        }

        $('#com_zip').keyup(function (event) {
            var searchVal = $(this).val();
            if (searchVal != '') {
                $.post(mooConfig.url.base + "/users/zip_suggestion", {'searchVal': searchVal}, function (data) {
                    $('#zipcode-suggestion').html(data).show();
                });
            }
        });

        $('#zipcode-suggestion').on('click', 'li', function () {
            $('#com_zip').val($(this).html());
        });

        $('body').click(function () {
            $('#zipcode-suggestion').hide();
        });
    }
    
    return{
        validateUser : validateUser,
        initOnRegistration : initOnRegistration,
        initOnSignupStep1 : initOnSignupStep1,
        initOnSignupStep1FieldCountry : initOnSignupStep1FieldCountry,
        initOnProfilePicture : initOnProfilePicture,
        initAddFriend : initAddFriend,
        initBirthdayPopup : initBirthdayPopup,
        initEditCoverPicture : initEditCoverPicture,
        initEditProfilePicture : initEditProfilePicture,
        initRemoveFriend : initRemoveFriend,
        initOnProfileEdit : initOnProfileEdit,
        initOnUserList : initOnUserList,
        initAjaxRequest : initAjaxRequest,
        initAjaxRequestPopup : initAjaxRequestPopup,
        initShowAlbums : initShowAlbums,
        initRespondRequest : initRespondRequest,
        initOnUserIndex : initOnUserIndex,
        initOnUserProfile : initOnUserProfile,
        initOnCloseNetworkSignup : initOnCloseNetworkSignup,
        resendValidationLink : resendValidationLink,
        initOnSocialRegistration : initOnSocialRegistration,
        initOnUserView : initOnUserView,
        initBlockUser : initBlockUser,
        initUnBlockUser : initUnBlockUser,
        initRemoveFollow: initRemoveFollow,
        initSearchFriend: initSearchFriend,
        loadProfileType: loadProfileType,
        initRegStep2: initRegStep2,
        initRegStep3: initRegStep3,
    }
}));
