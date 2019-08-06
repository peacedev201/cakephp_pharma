/* Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooPhrase', 'mooFileUploader', 'mooAlert', 'mooGlobal', 'mooButton', 'mooOverlay', 'mooBehavior', 'mooAjax',
            'spinner', 'tipsy', 'multiselect', 'Jcrop', 'bootstrap'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooUser = factory();
    }
}(this, function ($, mooPhrase, mooFileUploader, mooAlert, mooGlobal, mooButton, mooOverlay, mooBehavior, mooAjax) {

    // app/View/Users/index.ctp
    // app/View/Landing/index.ctp
    var initOnUserIndex = function () {

        $(".multi").multiSelect({
            selectAll: false,
            noneSelected: '',
            oneOrMoreSelected: mooPhrase.__('per_selected')
        });

        $("#searchPeople").unbind('click');
        $("#searchPeople").click(function () {
            
            $('#everyone a').spin('tiny');
            $('#browse .current').removeClass('current');
            $('#everyone').addClass('current');
            $.post(mooConfig.url.base + '/usernotess/ajax_browse/search', $("#filters").serialize(), function (data) {
                $('#everyone a').spin(false);
                $('#list-content').html(data);
                mooOverlay.registerOverlay();
            });

            if ($(window).width() < 992) {
                $('#leftnav').modal('hide');
                $('body').scrollTop(0);
            }
        });
    }

    var initOnUserList = function () {

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

        // init delete/event action

        initNoteAction();

        initNoteActionMobile();

        //init add note in mobile app
        initAddNoteApp();

        mooBehavior.initMoreResults();
    }

    var initAddNoteApp = function (){
        $('.usernote-leave-note').on('click',function(){
            var dom_element =  $(this).closest('.usernote-widget');
            dom_element.find('.usernotes .usernote-content').removeAttr('readonly').focus();
            dom_element.find('.usernotes .usernote-content').css('pointer-events', 'auto');
            dom_element.find('.usernote-action').show();
        });
        $('.usernote-cancel').off().on('click',function(){
            var dom_element =  $(this).closest('.usernote-widget');
            dom_element.find('.usernotes .usernote-content').prop('readonly',true);
            dom_element.find('.usernotes .usernote-content').css('pointer-events', 'none');
            dom_element.find('.usernote-action').hide();
            dom_element.find('.usernote-content').val(dom_element.find('.usernote-text-hidden').val());
            dom_element.find('.unoteErrorMessage').hide();
           
        });
        $('.usernote-save').off().on('click',function(){
            var ajax_url = mooConfig.url.base+"/usernotes/usernotess/save_note";
            var dom_element =  $(this).closest('.usernote-widget');
            var note_content = dom_element.find('.usernote-content').val();
            var target_id = $('.usernote-target-id').val();
            var content = {
                'content':note_content,
                'target_id': target_id
            };
            $('.usernotes').spin();
            $.post(ajax_url,content,function(data){
                $('.usernotes .spinner').remove();
                data= JSON.parse(data);
                if(data.result == 0){
                    dom_element.find('.unoteErrorMessage').show();
                    dom_element.find('.unoteErrorMessage').html(data.message);
                    return false;
                }else{
                    dom_element.find('.unoteErrorMessage').hide();
                }
                $('.usernote-content').val(note_content);
                $('.usernote-action').hide();

                dom_element.find('.usernotes .usernote-content').prop('readonly',true);
                dom_element.find('.usernotes .usernote-content').css('pointer-events', 'none');
                jQuery('.usernote-widget .usernote-text-hidden').val(note_content);
               
                // show note delete icon
                jQuery('.usernote-widget .usernote-remove').show();
            })
        });
        $('.usernote-remove').off().on('click',function(){
             $.fn.SimpleModal({
                    btn_ok: mooPhrase.__('ok'),
                    btn_cancel: mooPhrase.__('cancel'),
                    callback: function(){
                        var target_id = $('.usernote-target-id').val();
                        $.post(mooConfig.url.base + '/usernotess/ajax_remove_note', {target_id: target_id}, function() {
                            $('.usernote-widget .usernote-content').val("");
                            
                        });
                        jQuery('.usernote-widget .usernote-text-hidden').val("");
                        // show note delete icon
                         jQuery('.usernote-widget .usernote-remove').hide();
                         jQuery('.usernote-widget .unoteErrorMessage').hide();
                       
                    },
                    title: mooPhrase.__('confirm'),
                    contents: mooPhrase.__('are_you_sure_you_want_to_remove_note'),
                    model: 'confirm', 
                    hideFooter: false, 
                    closeButton: false
                }).showModal();
        });
    }


    var initChangeAdmin = function () {
        $('.changeAdmin').unbind('click');
        $('.changeAdmin').on('click', function () {
            var data = $(this).data();
            changeAdmin(data.id, data.type);
        });
    }
    var initRemoveMember = function () {
        $('.removeMember').unbind('click');
        $('.removeMember').on('click', function () {
            var data = $(this).data();
            removeMember(data.id);
        });
    }
    var initRespondRequest = function () {
        $('.respondRequest').unbind('click');
        $('.respondRequest').on('click', function () {
            var data = $(this).data();
            respondRequest(data.id, data.status);
        });

        $('.user_action_follow').unbind('click');
        $('.user_action_follow').click(function () {
            element = $(this);
            $.ajax({
                type: 'POST',
                url: mooConfig.url.base + '/follows/ajax_update_follow',
                data: {user_id: $(this).data('uid')},
                success: function (data) {
                    if (element.data('follow'))
                    {
                        element.data('follow', 0);
                        element.find('.hidden-xs').html(mooPhrase.__('text_follow'));
                        element.find('.visible-xs').html('rss_feed');
                    } else
                    {
                        element.data('follow', 1);
                        element.find('.hidden-xs').html(mooPhrase.__('text_unfollow'));
                        element.find('.visible-xs').html('check');
                    }
                }
            });
        });
    }

    var initNoteActionMobile = function(){
        $('.unote-delete-mobile').on('click', function () {
            var data = $(this).data();
           var deleteUrl = mooConfig.url.base + '/usernotess/remove_note/' + data.id +'/'+data.action;
           mooAlert.confirm(mooPhrase.__('usn_please_confirm_remove_this_note'), deleteUrl);
        });
    }

    var initNoteAction = function () {

        $('.unote-delete').unbind('click');
        $('.unote-edit').unbind('click');
        $('#unote-btn-save').unbind('click');
        $('.unote-delete').on('click', function () {
            var note_element = $(this).closest('li.user-list-index');

            var data = $(this).data();


            $.fn.SimpleModal({
                btn_ok : mooPhrase.__('usn_ok'),
                btn_cancel: mooPhrase.__('usn_cancel'),
                 callback: function(){
                 note_element.remove();
                 var target_id = note_element.find('.unote-target-id').val();
                $.post(mooConfig.url.base + '/usernotess/ajax_remove_note', {target_id: target_id}, function() {
                   
                });
                
            },
                model: 'confirm',
                title: mooPhrase.__('usn_please_confirm'),
                contents: mooPhrase.__('usn_please_confirm_remove_this_note'),
                hideFooter: false, 
                closeButton: false
            }).showModal();
        });

        $('.unote_delete_app').unbind('click');
        $('.unote_delete_app').click(function(event) {
            var data = $(this).data();
            var deleteUrl = mooConfig.url.base + '/usernotess/remove_note/' + data.id;
            var note_element = $(this).closest('li.user-list-index');

            $.fn.SimpleModal({
                btn_ok : mooPhrase.__('usn_ok'),
                btn_cancel: mooPhrase.__('usn_cancel'),
                callback: function(){
                    $('#portlet-config').modal('hide');
                    window.location = deleteUrl;                    
                },
                model: 'confirm',
                title: mooPhrase.__('usn_please_confirm'),
                contents: mooPhrase.__('usn_please_confirm_remove_this_note'),
                hideFooter: false, 
                closeButton: false
            }).showModal();
        });
        
        $('.unote-edit').on('click', function () {
            
            var content = auto_grow_content = $(this).closest('li.user-list-index').find('.detail-note textarea').val();
            $('#unoteModal .unoteErrorMessage').hide();
             $('#unoteModal .modal-body #unote-content').val(content);
             $('#unoteModal .autogrow-textarea-mirror').html(content);
             $('#unoteModal').show();
             $('#unoteModal').modal("show");
             var auto_grow_content = auto_grow_content
                        .replace(/&/g, '&amp;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/\n/g, '<br />') +
                    '.<br/>.'
                ;
                console.log(auto_grow_content);
            var height = $('#unoteModal .autogrow-textarea-mirror').html(auto_grow_content).height();

            $('#unoteModal #unote-content').height(height);
            
            var note_element = $(this).closest('li.user-list-index');
            var target_id = note_element.find('.unote-target-id').val();
             $('#unoteModal .unote-target-id').val(target_id);
      
        });
        
        $('#unote-btn-save').on('click', function () {
            var content = $('#unoteModal textarea#unote-content').val();
            var target_id =  $('#unoteModal .unote-target-id').val();
            $.post(mooConfig.url.base + '/usernotess/save_note',{'content':content,'target_id':target_id},function(data){
                data = JSON.parse(data);
                if(data.result == 0){
                    $('.unoteErrorMessage').show();
                    $('.unoteErrorMessage').html(data.message);
                    
                    return false;
                }
                $('.unote-item-'+target_id +' .detail-note textarea').val(data.content);
                $('.unote-item-'+target_id +' .detail-note .note_content').html(data.content);
                $('#unoteModal').modal("hide");
//                $('#unoteModal .modal-body #unote-content').val("");
//                  $('#unoteModal .autogrow-textarea-mirror').empty();
            });
            
        });

    }

    return{
        initOnUserIndex: initOnUserIndex,
        initOnUserList: initOnUserList
    }
}));
