/* Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery','mooBehavior','mooFriendinviterTabContent','mooGlobal','mooAlert','mooAjax','mooPhrase','mooButton','mooFileUploader'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooFriendinviter = factory();
    }
}(this, function ($,mooBehavior,mooFriendinviterTabContent,mooGlobal,mooAlert,mooAjax,mooPhrase,mooButton,mooFileUploader) {
    
    var initOnGetcontacts = function (provide_id,totalContacts,total_allow_select) {
        
            $('#send_invite').click(function(){
                inviteFriends(provide_id);
            });
        
            $('#checkallBox').click(function(){
                        $('.check_item').prop('checked', false);
                        if (totalContacts > total_allow_select){
                            var counter1 = total_allow_select;
                        }else{
                            var counter1 = total_allow_select;
                        }
                        if ($(this).is(':checked')){
                            if (totalContacts > total_allow_select)
                                $('#count_contacts').html(total_allow_select);
                            else
                                $('#count_contacts').html(totalContacts);
                        }else{
                            $('#count_contacts').html(0);
                        }
                       
                        for (id = 1; id <= counter1; id++)
                        {
                                $('#check_' + id).prop('checked', $(this).is(':checked'));
                                if ($('#row_' + id))
                                {
                                    if ($(this).is(':checked'))
                                    {
                                        $('#row_' + id).className = 'thTableSelectRow';
                                    }
                                    else
                                    {
                                        if (id % 2 == 1)
                                        {
                                            $('#row_' + id).className = 'thTableOddRow';
                                        }
                                        else
                                        {
                                            $('#row_' + id).className = 'thTableEvenRow';
                                        }
                                    }
                                }
                        }
            });
        
            if(document.getElementById('contactimporter_page_list')){
                var contactimporter_pages = new mooFriendinviterTabContent.ddtabcontent("contactimporter_page_list");
                contactimporter_pages.setpersist(false);
                contactimporter_pages.setselectedClassTarget("link");
                contactimporter_pages.init(0);
            }
                    

            $('.check_item').click(function(){                                     
                        var element_id = $(this).attr('rel');
                        var check_element = $('#check_' + element_id);
                        
                        var count_contacts = $('#count_contacts').html();
                        
                        if (!$(this).is(':checkbox'))
                            check_element.prop('checked',!check_element.is(':checked'));
                        
                        if(check_element.is(':checked') && count_contacts >= total_allow_select){
                            check_element.prop('checked',false);
                            mooPhrase.__('You have exceeded the maximum number of people you can invite');
                        }
                        
                            if (check_element.is(':checked'))
                            {
                                $('#row_' + element_id).className = 'thTableSelectRow';
                            }
                            else
                            {
                                if (element_id % 2 == 1)
                                {
                                    $('#row_' + element_id).className = 'thTableOddRow';
                                }
                                else
                                {
                                    $('#row_' + element_id).className = 'thTableEvenRow';
                                }
                            }
                            var select_id = $('input:checkbox.check_item:checked').length;;
                            
                            $('#count_contacts').html(select_id);

        });
                  
       $('#skip_action').click(function () {
            var url = mooConfig.url.base + '/friend_inviters?app_no_tab=1';
            window.location.href = url;
        });
        
        $('.respondRequest').unbind('click');
        $('.respondRequest').on('click', function(){
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
                $('#respond').unbind('click');
                $('#respond').siblings('ul').remove();
                if(data.status == 1){
                    $('#addFriend_' + data.user_id).remove();
                }else{
                     $('#respond').html(mooPhrase.__('Add'));
                     $('#respond').attr('data-action','add');
                     $('#respond').click(function () {
                            addFriend('add',data.id, $('#respond').attr('id') );
                     });
                }
            });
        });
        
        
        var inviteFriends = function (socialtype) {

            if ($('#id_nonsite_success_mess'))
                $('#id_nonsite_success_mess').hide();

            var nonsitemembers = new Array();
            var checked = false;
            var total_checked = 0;
            var total_contacts = $('#nonsitetotal_contacts').val();
            // console.log(total_contacts);
            for (var i = 1; i <= total_contacts; i++)
            {
                if ($('#check_' + i).is(":checked")) {
                    total_checked++;
                    checked = true;
                    //  console.log(i);
                    //SPLIT NAME AND EMAIL:     
                    nonsitemembers [i] = $('#check_' + i).val();
                    if (socialtype == 'twitter' && total_contacts > 20) {
                        $('#user_' + $('#check_' + i).val()).hide();
                        $('#check_' + i).removeAttr('checked');
                        count_twit_request_sent++;
                    }
                }
            }

            if (checked) {

                var sModal =  $.fn.SimpleModal({
                        btn_ok: mooPhrase.__('btn_ok'),
                        btn_cancel: mooPhrase.__('btn_cancel'),
                        callback: function(){ 
                        },
                        title: '',
                        contents: "<div style='height:30px;'><center><b>" + mooPhrase.__('sending_request') + "</b><br /><div class='invite_loading'></div></center></div>",
                        model: 'modal', 
                        hideFooter: true, 
                        closeButton: false        
                    }).showModal();
            
                $('.simple-modal-header').hide(); 
                $('.simple-modal-footer').hide();
                var postData = {
                    'nonsitemembers': nonsitemembers,
                    'socialtype': socialtype,
                    'custom_message': $('#custom_message').length ? $('#custom_message').val() : ''
                };

                var url = mooConfig.url.base + '/friend_inviters/invitetosite';
                mooAjax.post({
                    url : url,
                    data: postData
                }, function(data){
                        $('#show_sitefriend').hide();
                        var json = $.parseJSON(data);
                       if ( json.result == 1 ){
                           window.location.href = mooConfig.url.base + '/friend_inviters/invited?app_no_tab=1';                        
                        } else {
                           $('#show_nonsitefriends').html(data);
                        }
                });
            } else {
                 $.fn.SimpleModal({
                    btn_ok : mooPhrase.__('btn_ok'),
                    btn_cancel: mooPhrase.__('cancel'),
                    model: 'modal',
                    title: mooPhrase.__('warning'),
                    contents: mooPhrase.__('please_select_at_least_one_friend_to_invite')
                }).showModal();
            }
        }  
        
        $('.friendinviter_friend').unbind('click');
        $('.friendinviter_friend').click(function () {
             var data = $(this).data();
             addFriend(data.action, data.user_id, $(this).attr('id'));           
         });
               
    }
    
    var addFriend = function (action, user_id, obj_id) {
            $('#' + obj_id).unbind('click');
            switch(action){
                case 'add':
                    $.post(mooConfig.url.base + "/friends/ajax_sendRequest", {user_id: user_id, message: ''}, function(response){
                        $('#' + obj_id).html(mooPhrase.__('Cancel request'));
                        $('#' + obj_id).attr('data-action','cancel');
                        $('#' + obj_id).click(function () {
                            addFriend('cancel',user_id, obj_id);
                        });
                    });
                    break;
                case 'cancel':
                     $.post(mooConfig.url.base + "/friend_inviters/cancelfriend", {user_id: user_id}, function(response){
                        $('#' + obj_id).html(mooPhrase.__('Add'));
                        $('#' + obj_id).attr('data-action','add');
                        $('#' + obj_id).click(function () {
                            addFriend('add',user_id, obj_id);
                        });
                    });
                    break;
            }
    }

    var initOnIndex = function () {
        /*
        $('#facebook_share').click(function() {
            faceWindow($(this).attr('href'));
            return false;    
        });
      
        $('#twitter_tweet').click(function() {
            tweetWindow($(this).attr('href'));
            return false;    
        });
        */
       
        var tweetWindow = function(url) {
            var text = "Join to our social network";
            window.open( "http://twitter.com/share?text=" + encodeURIComponent(text) +  "&url=" + 
            encodeURIComponent(url) + "&count=none/", "tweet", "'_blank'" ) 
        }

        var faceWindow = function(url) {
            window.open( "http://www.facebook.com/sharer.php?u=" + encodeURIComponent(url),"facebook", "'_blank'" ) 
        }
        
                   
          $('#invite_btn').click(function () {
                    $('#invite_btn').spin('small');
                    mooButton.disableButton('invite_btn');
                    $(".error-message").hide();
                    mooAjax.post({
                        url: mooConfig.url.base + '/friend_inviters/ajax_invite',
                        data: $("#invite_form").serialize()
                    }, function (data) {
                        mooButton.enableButton('invite_btn');
                        $('#invite_btn').spin(false);
                        var json = $.parseJSON(data);
                        if (json.result == 1)
                        {
                            $("#to").val('');
                            $("#message").val('');
                            $(".error-message").hide();
                            mooAlert.alert(mooPhrase.__('your_invitation_has_been_sent'));
                        }
                        else
                        {
                            $(".error-message").show();
                            $(".error-message").html(json.message);
                        }
                    });

                    return false;

            });
        
        document.getElementById("share_button").addEventListener("click", function() {
            copyToClipboard(document.getElementById("share_url"));
        });
        
        var copyToClipboard = function (elem) {
                // create hidden text element, if it doesn't already exist
            if (navigator.userAgent.match(/ipad|ipod|iphone/i)) {
                  elem.contentEditable = true;
                  elem.readOnly = true;
            }
            var targetId = "_hiddenCopyText_";
            var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
            var origSelectionStart, origSelectionEnd;
            if (isInput) {
                // can just use the original source element for the selection and copy
                target = elem;
                origSelectionStart = elem.selectionStart;
                origSelectionEnd = elem.selectionEnd;
            } else {
                // must use a temporary form element for the selection and copy
                target = document.getElementById(targetId);
                if (!target) {
                    var target = document.createElement("textarea");
                    target.style.position = "absolute";
                    target.style.left = "-9999px";
                    target.style.top = "0";
                    target.id = targetId;
                    document.body.appendChild(target);
                }
                target.textContent = elem.textContent;
            }
            // select the content
            var currentFocus = document.activeElement;
            target.focus();
            target.setSelectionRange(0, target.value.length);

            // copy the selection          
            var succeed;
            try {
                  succeed = document.execCommand("copy");
            } catch(e) {
                succeed = false;
            }
            // restore original focus
            if (currentFocus && typeof currentFocus.focus === "function") {
                currentFocus.focus();
            }

            if (isInput) {
                // restore prior selection
                elem.setSelectionRange(origSelectionStart, origSelectionEnd);
            } else {
                // clear temporary content
                target.textContent = "";
            }
            return succeed;
        }
        
         var errorHandler = function(event, id, fileName, reason) {
            console.log("id: " + id + ", fileName: " + fileName + ", reason: " + reason);
        };
        
        var uploader = new mooFileUploader.fineUploader({
            element: $('#attachments_upload')[0],
            multiple: false,
            autoUpload: true,
            text: {
                uploadButton: '<div class="upload-section"><i class="material-icons">receipt</i>' + mooPhrase.__('drag_or_click_here_to_upload_file') + '</div>'
            },
            validation: {
                allowedExtensions: ['csv','txt'],
                sizeLimit: 134217728
            },
            request: {
                endpoint: mooConfig.url.base + "/friend_inviters/uploads"
            },
            callbacks: {
                onError: errorHandler,
                onComplete: function(id, fileName, response) {
                   $('#filename').val(response.filename);
                }
            }
        });
        
        $('#getCSV').click(function(){
            if($('#filename').val() != ''){
                $('#frm_getcsv').submit();
            }
        });
    }
                    
    return {
        initOnGetcontacts : initOnGetcontacts,
        initOnIndex : initOnIndex
    }

}));