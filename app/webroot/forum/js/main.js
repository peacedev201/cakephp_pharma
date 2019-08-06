(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooBehavior', 'mooPhrase', 'mooAjax', 'mooGlobal', 'mooTooltip', 'mooAlert', 'mooButton', 'mooFileUploader', 'tagsinput'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooForum = factory();
    }
}(this, function ($, mooBehavior, mooPhrase, mooAjax, mooGlobal, mooTooltip, mooAlert, mooButton, mooFileUploader) {

    var initOnTopicListing = function(){
        initSearchGlobal();

        $('#btn_index_search_topic').click(function (e) {
            e.preventDefault();
            var keyword = $('#search_keyword').val();
            if(keyword != '') {
                window.location = $('#form_index_search_topic').attr('action') + '/keyword:' + keyword;
            }
        });
    }

    var initSearchGlobal = function () {
        $('#btn_search_topic').click(function (e) {
            e.preventDefault();
            var keyword = $('#keyword').val();
            if(keyword != '') {
                var type = '';
                if (keyword[0] == '#') {
                    type = '/hashtag';
                    keyword = keyword.substring(1);
                }
                keyword = encodeURIComponent(encodeURIComponent(keyword));
                window.location = mooConfig.url.base + '/forums/topic/search/' + keyword + type;
            }
        })
    }

    var initOnViewTopic = function(){
        $('.btn_subscribe').unbind('click');
        $('.btn_subscribe').click(function () {
            var data = $(this).data();
            $.post(mooConfig.url.base + "/forums/topic/subscribe/"+data.id, function (data) {
                data = $.parseJSON(data);
                if(data.result == '1'){
                    $('.btn_subscribe').html('<i class="material-icons">done</i>'+mooPhrase.__('subscribed'));
                    $('.btn_subscribe').addClass('active');
                }else{
                    $('.btn_subscribe').html('<i class="material-icons">rss_feed</i>'+mooPhrase.__('subscribe'));
                    $('.btn_subscribe').removeClass('active');
                }
                $('.btn_subscribe').spin(false);
                mooButton.enableButton('btn_subscribe');
            });
        });

        $('.btn_favorite').unbind('click');
        $('.btn_favorite').click(function () {
            var data = $(this).data();
            $.post(mooConfig.url.base + "/forums/topic/favorite/"+data.id, function (data) {
                data = $.parseJSON(data);
                if(data.result == '1'){
                    $('.btn_favorite').addClass('active');
                    $('.btn_favorite .material-icons').html('star');
                }else{
                    $('.btn_favorite').removeClass('active');
                    $('.btn_favorite .material-icons').html('star_border');
                }
                $('.btn_favorite').spin(false);
                mooButton.enableButton('btn_favorite');
            });
        });

        $('.lock-topic').unbind('click');
        $('.lock-topic').click(function () {
            var data = $(this).data();
            $.post(mooConfig.url.base + "/forums/topic/lock/"+data.id, function (data) {
                data = $.parseJSON(data);
                if(data.result == '1'){
                    $('.lock-topic').html(mooPhrase.__('lock'));
                }else if(data.result == '2'){
                    $('.lock-topic').html(mooPhrase.__('open'));
                }
                $('.lock-topic').spin(false);
                mooButton.enableButton('lock-topic');
            });
        });

        $('#btn_search_reply').click(function (e) {
            e.preventDefault();
            window.location = $('#form_search_reply').attr('action') + '/keyword:' + $('#keyword').val();
        });

        deleteTopic();
        unPinTopic();
    }

    var deleteTopic = function(){
        $('.delete-topic').unbind('click');
        $('.delete-topic').click(function(){

            var data = $(this).data();
            var deleteUrl = mooConfig.url.base + '/forums/topic/delete/' + data.id;
            if(typeof data.topic != 'undefined'){
                deleteUrl += '/'+data.topic;
            }

            if (mooConfig.isApp)
            {
                deleteUrl = deleteUrl+ '?app_no_tab=1';
            }
            mooAlert.confirm(mooPhrase.__('are_you_sure_you_want_to_remove_this_entry'), deleteUrl);
        });
    }

    var unPinTopic = function(){
        $('.unpin-forum-topic').unbind('click');
        $('.unpin-forum-topic').click(function(){

            var data = $(this).data();
            var unpinUrl = mooConfig.url.base + '/forum/forum_pins/unpin/' + data.id;
            if(typeof data.topic != 'undefined'){
                unpinUrl += '/'+data.topic;
            }

            if (mooConfig.isApp)
            {
                unpinUrl = unpinUrl+ '?app_no_tab=1';
            }
            mooAlert.confirm(mooPhrase.__('are_you_sure_you_want_to_unpin_this_topic'), unpinUrl);
        });
    }

    var initAjaxInvite = function(){

        var friends_userTagging = new Bloodhound({
            datumTokenizer:function(d){
                return Bloodhound.tokenizers.whitespace(d.name);
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            prefetch: {
                url: mooConfig.url.base + '/users/friends.json',
                cache: false,
                filter: function(list) {

                    return $.map(list.data, function(obj) {
                        return obj;
                    });
                }
            },

            identify: function(obj) { return obj.id; },
        });

        friends_userTagging.initialize();


        $('#friends').tagsinput({
            freeInput: false,
            itemValue: 'id',
            itemText: 'name',
            typeaheadjs: {
                name: 'friends_userTagging',
                displayKey: 'name',
                highlight: true,
                limit:10,
                source: friends_userTagging.ttAdapter(),
                templates:{
                    notFound:[
                        '<div class="empty-message">',
                        mooPhrase.__('no_results'),
                        '</div>'
                    ].join(' '),
                    suggestion: function(data){
                        if($('#friends').val() != '')
                        {
                            var ids = $('#friends').val().split(',');
                            if(ids.indexOf(data.id) != -1 )
                            {
                                return '<div class="empty-message" style="display:none">'+mooPhrase.__('no_results')+'</div>';
                            }
                        }
                        return [
                            '<div class="suggestion-item">',
                            '<img alt src="'+data.avatar+'"/>',
                            '<span class="text">'+data.name+'</span>',
                            '</div>',
                        ].join('')
                    }
                }
            }
        });
        $('#sendButton').unbind('click');
        $('#sendButton').click(function(){
            $('#sendButton').spin('small');
            mooButton.disableButton('sendButton');
            $(".error-message").hide();

            mooAjax.post({
                url : mooConfig.url.base + '/forums/topic/ajax_sendInvite',
                data: $("#sendInvite").serialize()
            }, function(data){
                mooButton.enableButton('sendButton');
                $('#sendButton').spin(false);
                var json = $.parseJSON(data);
                if ( json.result == 1 )
                {
                    $('#simple-modal-body').html(json.msg);
                }
                else
                {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });

            return false;

        });

        $('#invite_type_topic').change(function(){
            $('#invite_friend').hide();
            $('#invite_email').hide();
            if ($('#invite_type_topic').val() == '1')
            {
                $('#invite_friend').show();
            }
            else
            {
                $('#invite_email').show();
            }
        });
    }

    var initListReply = function(){
        $('.thank-topic').unbind('click');
        $('.thank-topic').click(function(){
            var data = $(this).data();
            $.post(mooConfig.url.base + "/forums/topic/ajax_thank/"+data.id + '/' + data.parent, function (result) {
                var res = $.parseJSON(result);
                $('#topic_thank_' + data.id).html( parseInt(res.thank_count) );
                $('.btn-thank-' + data.id).toggleClass('active');
            });
        });

        $('.topic-get-link').unbind('click');
        $('.topic-get-link').click(function(){
            var data = $(this).data();
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(data.href).select();
            document.execCommand("copy");
            $temp.remove();
            mooAlert.alert(mooPhrase.__('url_has_been_copied_to_clipboard'));
        });
    }

    var initOnViewForum = function(){
        $('#btn_forum_search_topic').click(function (e) {
            e.preventDefault();
            var keyword = $('#keyword').val();
            var type = '/keyword';
            if(keyword[0] == '#'){
                type = '/hashtag';
                keyword = keyword.substring(1);
            }
            window.location = $('#form_forum_search_topic').attr('action') + type + ':' + keyword;
        });

        $('.btn_subscribe').unbind('click');
        $('.btn_subscribe').click(function (){
            var data = $(this).data();
            $.post(mooConfig.url.base + "/forums/subscribe/"+data.id, function (data) {
                data = $.parseJSON(data);
                if(data.result == '1'){
                    $('.btn_subscribe').html('<i class="material-icons">done</i>'+mooPhrase.__('subscribed'));
                    $('.btn_subscribe').addClass('active');
                }else{
                    $('.btn_subscribe').html('<i class="material-icons">rss_feed</i>'+mooPhrase.__('subscribe'));
                    $('.btn_subscribe').removeClass('active');
                }
                $('.btn_subscribe').spin(false);
                mooButton.enableButton('btn_subscribe');
            });
        });

        $('#btn_forum_locked').unbind('click');
        $('#btn_forum_locked').click(function (e){
            e.preventDefault();
            $.fn.SimpleModal({
                btn_ok: mooPhrase.__('btn_ok'),
                callback: function(){

                },
                title: mooPhrase.__('message'),
                contents: mooPhrase.__('this_forum_is_marked_as_locked_for_new_topic'),
                model: 'modal',
                hideFooter: false,
                closeButton: false
            }).showModal();
        });
    }

    function removeArr(array, element) {
        const index = array.indexOf(element);

        if (index !== -1) {
            array.splice(index, 1);
        }
        return array;
    }

    function initPinTopic(price)
    {
    	$('#pinTopicForm').submit(function(e){
    		if ($('#time').val() == '')
    		{
    			e.preventDefault();
                $(".error-message").show();
                $(".error-message").html(mooPhrase.__('day_required'));
    		}else if(!$.isNumeric($('#time').val())){
                e.preventDefault();
                $(".error-message").show();
                $(".error-message").html(mooPhrase.__('day_numeric'));
            }else{
                $(".error-message").hide();
            }
    	});
    	$('#time').on('keyup change',function(){
    		var time = parseInt($('#time').val());
    		total = price * time;
    		$('#pin_total').html(total);
    	})
    }

    var initOnReport = function(){

        $('#forum_report_btn').unbind('click');
        $('#forum_report_btn').click(function(){
            mooButton.disableButton('forum_report_btn');
            $('#forum_report_btn').spin('small');

            mooAjax.post({
                url : mooConfig.url.base + "/forum/forum_reports/ajax_save",
                data : $("#forum_report_form").serialize()
            }, function(data){
                mooButton.enableButton('forum_report_btn');
                $('#forum_report_btn').spin(false);

                var json = $.parseJSON(data);

                if ( json.result == '1' )
                {
                    $(".error-message").hide();
                    mooAlert.alert(json.message);
                    $('#portlet-config').modal('hide');
                    $('#themeModal').modal('hide');
                }else if(json.result == '2'){
                    window.location = json.redirect;
                }
                else
                {
                    $(".error-message").show();
                    $(".error-message").html(json.message);
                }
            });

        });
        return false;
    }


    return{
        initOnTopicListing : initOnTopicListing,
        initOnViewTopic : initOnViewTopic,
        initAjaxInvite : initAjaxInvite,
        initListReply : initListReply,
        initOnViewForum : initOnViewForum,
        initPinTopic : initPinTopic,
        initSearchGlobal : initSearchGlobal,
        initOnReport : initOnReport,
    }
}));