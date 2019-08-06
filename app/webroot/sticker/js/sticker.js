/*
 * Translation Tool
 */
(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooAlert', 'mooPhrase'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooSticker = factory();
    }
}(this, function ($, mooAlert, mooPhrase) {
    var _plugin3rd_name = 'Sticker';
    var _stickerModal = "#stickerModal";
    var _searchTypingTimer;
    var _doneTypingInterval = 350;
    var _animationLoop = 3;
    var _currentScrollPosition = 0;
    var _commentMap = {
        commentForm: {selectStickerId: 'sticker_item_comment_', buttonId: '#commentButton_', beforeCallback: 'beforePostCommentCallback', afterCallback: 'afterSubmitCommentCallbackSuccess'},
        commentReplyForm: {selectStickerId: 'sticker_item_reply_', buttonId: '#commentReplyButton_', beforeCallback: 'beforePostReplyCallback', afterCallback: 'afterSubmitReplySuccessCallback'},
        activitycommentReplyForm: {selectStickerId: 'sticker_item_activity_reply_', buttonId: '#activity_commentReplyButton_', beforeCallback: 'beforePostReplyCallback', afterCallback: 'afterSubmitReplySuccessCallback'},
        activity_comment_edit: {selectStickerId: 'sticker_activity_comment_edit_', buttonId: '#activity_comment_preview_attach_', beforeCallback: 'beforeEditCommentCallback', afterCallback: 'afterEditCommentCallback'},
        item_commentReplyForm: {selectStickerId: 'sticker_item_comment_reply_', buttonId: '#item_commentReplyButton_', beforeCallback: 'beforePostReplyCallback', afterCallback: 'afterSubmitReplySuccessCallback'},
        item_comment_edit: {selectStickerId: 'sticker_item_comment_edit_', buttonId: '#item_comment_preview_attach_', beforeCallback: 'beforeEditCommentCallback', afterCallback: 'afterEditCommentCallback'},
        activity_edit: {selectStickerId: 'sticker_activity_edit_', buttonId: '#activity_edit_', beforeCallback: 'beforeEditActivityCallback', afterCallback: 'afterEditActivityCallback'},
    };
    
    var initSticker = function(){
        //autoplay
        autoPlaySticker();
        $( document ).ajaxComplete(function( event, xhr, settings ) {
            if (settings.url.indexOf(mooConfig.url.base + "/activities/browse") !== -1 || 
                settings.url.indexOf(mooConfig.url.base + "/comments/browse") !== -1 ||
                settings.url.indexOf(mooConfig.url.base + "/photos/view") !== -1)
            {
                autoPlaySticker();
            }
        })
        
        //clear all modal content after closed
        $(_stickerModal).on('shown.bs.modal', function() {
            if(mooPhrase.__('sticker_is_android'))
            {
                Android.disableRefresh();
            }
        })
        $(_stickerModal).on('hidden.bs.modal', function () {
            if(mooPhrase.__('sticker_is_mobile') === true && _currentScrollPosition > 0){
                document.documentElement.scrollTop = document.body.scrollTop = _currentScrollPosition;
            }
            
            $(_stickerModal + ' .modal-content').empty();
            if(mooPhrase.__('sticker_is_android'))
            {
                Android.enableRefresh();
            }
        })
        
        //check comment for article or theater photo
        $('.sticker_button').each(function(){
            if($(this).find('i').closest('#commentForm').length > 0){
                $(this).find('i').data('itemId', 0);
            }
        })
        
        //click icon to show modal
        $('.stt-action, .comment_wrapper, #photoModal, #photo-content, #commentForm').off('click', '.sticker_button i');
        $('.stt-action, .comment_wrapper, #photoModal, #photo-content, #commentForm').on('click', '.sticker_button i', function(){
            if($('.sticker_button').hasClass('show-sticker')){
                showStickerModal($(this));
            }else{
                showNoAlert();
            }
        })
        
        $(_stickerModal).off('click', '.sticker_item_sticker:not(.current)');
        $(_stickerModal).on('click', '.sticker_item_sticker:not(.current)', function(){
            $('.sticker_item').removeClass('current');
            $(this).addClass('current');
            $('#sticker_images').html('');
            $('#sticker_images').spin('small');
            $.get(mooConfig.url.base + "/sticker/sticker_modal_images/" + $(this).data('id'), '', function(data){
                $('#sticker_images').spin(false);
                $('#sticker_images').html(data); 
                $('.scrollbar-inner').scrollbar({
                    height:300
                });
            });
        })
        
        $(_stickerModal).on('click', '.sticker_item_search:not(.current)', function(){
            $('.sticker_item').removeClass('current');
            $(this).addClass('current');
            $('#sticker_images').html('');
            $('#sticker_images').spin('small');
            $.get(mooConfig.url.base + "/sticker/sticker_modal_search/", function(data){
                $('#sticker_images').spin(false);
                $('#sticker_images').html(data); 
                $('.scrollbar-inner').scrollbar({
                    height:300
                });
                
            });
        })
        
        $(_stickerModal).on('click', '.sticker_item_recent:not(.current)', function(){
            $('.sticker_item').removeClass('current');
            $(this).addClass('current');
            $('#sticker_images').html('');
            $('#sticker_images').spin('small');
            $.get(mooConfig.url.base + "/sticker/sticker_modal_recent/", function(data){
                $('#sticker_images').spin(false);
                $('#sticker_images').html(data); 
                $('.scrollbar-inner').scrollbar({
                    height:300
                });
            });
        })
        
        //animation
        initStickerAnimation();
        
        //select sticker
        $(_stickerModal).off('click', '.sticker_animation');
        $(_stickerModal).on('click', '.sticker_animation', function(){
            var item_type = $('#stickerModal #sticker_item_type').val();
            var item_id = $('#stickerModal #sticker_item_id').val();
            removeSticker(item_id, item_type);
            selectSticker($(this));
        })
        
        //remove selected sticker
        $('#wallForm').on('click', '.sticker_remove_select', function(){
            removeSticker();
        })
        
        $('.comment_wrapper, #commentForm, #photoModal, #photo-content').on('click', '.sticker_remove_select', function(){
            removeSticker($(this).data('itemId'), $(this).data('itemType'));
        })
        
        //search
        $(_stickerModal).off('click', '.sticker_category');
        $(_stickerModal).on('click', '.sticker_category', function(){
            $('#sticker_search').val($(this).data('key'));
            $(".sticker_search_cancel").show();
            searchSticker();
        })

        $(_stickerModal).off('click', '.sticker_search_cancel');
        $(_stickerModal).on('click', '.sticker_search_cancel', function(){
            $('#sticker_search').val('');
            $(".sticker_search_cancel").hide();
            $('.sticker_category_container').show();
            $('#sticker_search_content').empty();
        })

        $(_stickerModal).off("input", "#sticker_search");
        $(_stickerModal).on("input", "#sticker_search", function () {
            window.clearTimeout(_searchTypingTimer);
            _searchTypingTimer = window.setTimeout(doneSearchTyping, _doneTypingInterval);
        });
        
        $(_stickerModal).off("change paste keyup", "#sticker_search");
        $(_stickerModal).on("change paste keyup", "#sticker_search", function() {
            if($(this).val().trim() != ""){
                $(".sticker_search_cancel").show();
            }
            else{
                $(".sticker_search_cancel").hide();
                $('.sticker_category_container').show();
            }
        });
        
        //remove sticker after sharing
        $('body').on('afterPostWallCallbackSuccess', function () {
            if($('#list-content li:first .sticker_animation').length > 0){
                removeSticker();
                playSticker($('#list-content li:first .sticker_animation'));
            }
            else if($('#wallForm').find('#sticker_post_feed').length > 0){
                removeSticker();
            }
        });
        
        //after post comment callback
        var existingCallback = [];
        for(var key in _commentMap){
            var afterCallback = _commentMap[key].afterCallback;
            if(existingCallback.indexOf(afterCallback) === -1){
                existingCallback.push(afterCallback);
                $('body').off(afterCallback);
                $('body').on(afterCallback, function (e, data) {
                    var item_id = data != null && typeof(data.targetId) !== "undefined" ? data.targetId : 0;
                    var item_type = data != null && typeof(data.itemType) !== "undefined" ? data.itemType : "";
                    removeSticker(item_id, item_type);
                    
                    //play added sticker
                    /*if(item_type != ""){
                        var buttonId = _commentMap[item_type].buttonId + item_id;
                        if((item = $('#theaterComments').find('li:first').find('.sticker_animation')).length > 0){
                            playSticker(item);
                        }
                        else if((item = $(buttonId).closest('ul').find('li:first').next().find('.sticker_animation')).length > 0){
                            playSticker(item);
                        }
                        else if((item = $('#comments').find('li:first').find('.sticker_animation')).length > 0){
                            playSticker(item);
                        }
                    }*/
                });
            }
        }
        
        //enable disable plugin callback
        $('body').on('enablePluginsCallback', function(e, data){
            var _plugins = data.plugins;
            if(_plugins.indexOf(_plugin3rd_name) > -1){
                enablePlugin();
            }
        });
        
        $('body').on('disablePluginsCallback', function(e, data){
            var _plugins = data.plugins;
            if(_plugins.indexOf(_plugin3rd_name) > -1){
                disabePlugin();
            }
        });
    }

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
    
    function disabePlugin(){
        $('.sticker_button').hide();
        removeSticker();
    }
    
    function enablePlugin(){
        $('.sticker_button').show();
    }
    
    function autoPlaySticker(){
        $('.sticker_animation').each(function(){
            if($(this).data('autoplayed') == 0 || typeof $(this).data('autoplayed') == 'undefined'){
                $(this).data('autoplayed', 1);
                playSticker($(this));
            }
        })
    }
    
    function selectSticker(item){
        var item_type = $('#stickerModal #sticker_item_type').val();
        var item_id = $('#stickerModal #sticker_item_id').val();
        var photo_theater = $('#stickerModal #sticker_photo_theater').val();
        var id = 'sticker_post_feed';
        if(item_type != ""){
            id = _commentMap[item_type].selectStickerId + item_id;
        }
        
        //reset background position for item which has already been hovered
        $(item).css('backgroundPosition', '0px 0px');
        var html = '<div class="sticker_select" id="' + id + '"><div class="sticker_select_animation">' + item[0].outerHTML + '<div class="sticker_remove_select" data-item-type="' + item_type + '" data-item-id="' + item_id + '">x</div></div></div>';
        if(item_type != ""){
            var buttonId = _commentMap[item_type].buttonId + item_id;
            var beforeCallback = _commentMap[item_type].beforeCallback;
            if(photo_theater == 1 && item_type == "commentForm")
            {
                if($('#photo-content').length > 0)
                {
                    $('#photo-content').find('#commentButton_0').before(html);
                }
                else
                {
                    $('#photoModal').find('#commentButton_0').before(html);
                }
            }
            else
            {
                $(buttonId).before(html);
            }
            if(item_type == "activity_edit")
            {
                $('#activity_' + item_id).find('.cancelEditActivity').click(function(){
                    removeSticker(item_id, item_type);
                });
            }
            $('body').on(beforeCallback, function (event, obj) {
                obj.emptyContent = true;
                obj.targetId = item_id;
                obj.itemType = item_type;
                obj.params = {sticker_image_id: $(item).data('id'), empty_comment: 1};
            });
        }
        else{
            $('.form-feed-holder').prepend(html);
            $('#sticker_image_id').val(item.data('id')).removeAttr('disabled');
            
            $('body').on('beforePostWall', function (event, obj) {
                obj.emptyContent = true;
            });
            $('body').trigger('startFeedPlugin3drCallback',[{plugin_name: _plugin3rd_name}]);
            
            //hide preview link for feeling
            $('.userTagging-userShareLink #preview_link').remove();
            
            //hide preview link for parse link
            $('.userTagging-userShareVideo #preview_link').remove();
            $('.userTagging-userShareVideo #userShareVideo').val('');
        
            //hide upload photo
            hideUploadPhoto();
        }
        $(_stickerModal).modal('hide');
        
        //hide photo and gif for comment
        hideOtherPluginsWhenComment(item_type, item_id);
    }
    
    function removeSticker(item_id, item_type){
        if(typeof item_id != 'undefined' && typeof item_type != 'undefined' && item_type != ''){
            var selectStickerId = _commentMap[item_type].selectStickerId + item_id;
            var beforeCallback = _commentMap[item_type].beforeCallback;
            $('#' + selectStickerId).remove();
            
            $('body').on(beforeCallback, function (event, obj) {
                obj.emptyContent = false;
                obj.targetId = '';
                obj.itemType = '';
                obj.params = {};
            });
        }
        else{
            $('#sticker_post_feed').remove();
            $('#sticker_image_id').val('').attr('disabled', 'disbled');
            
            $('body').on('beforePostWall', function (event, obj) {
                obj.emptyContent = false;
            });
            $('body').trigger('stopFeedPlugin3drCallback',[{plugin_name: _plugin3rd_name}]);
        }
        
        //show upload photo
        showUploadPhoto();
        
        //show photo and gif for comment
        showOtherPluginsWhenComment(item_type, item_id);
    }
    
    function showStickerModal(item){
        _currentScrollPosition = (document.documentElement.scrollTop || document.body.scrollTop);
        $(_stickerModal).modal({
            backdrop : 'static'
        });
        var params = {
            item_type: typeof item.data('itemType') != 'undefined' ? item.data('itemType') : '',
            item_id: typeof item.data('itemId') != 'undefined' ? item.data('itemId') : 0,
            photo_theater: typeof item.data('photoTheater') != 'undefined' ? item.data('photoTheater') : 0,
        };
        $.get(mooConfig.url.base + "/sticker/sticker_modal", params, function(data){
            $(_stickerModal + ' .modal-content').html(data); 
            initStickerSlider();
            $('.scrollbar-inner').scrollbar({
                height:300
            });
        });
    }
    
    function doneSearchTyping () {
        searchSticker();
    }
    
    function initStickerSlider(){
        $('.sticker_list').slick({
            dots: false,
            infinite: false,
            speed: 300,
            slidesToShow: 4,
            slidesToScroll: 4,
            variableWidth: true,
            prevArrow: '<i class="material-icons sticker_slick-prev">keyboard_arrow_left</i>',
            nextArrow: '<i class="material-icons sticker_slick-next">keyboard_arrow_right</i>',
            responsive: [
              {
                breakpoint: 1024,
                settings: {
                  slidesToShow: 3,
                  slidesToScroll: 3,
                  infinite: true,
                  dots: true
                }
              },
              {
                breakpoint: 600,
                settings: {
                  slidesToShow: 2,
                  slidesToScroll: 2
                }
              },
              {
                breakpoint: 480,
                settings: {
                  slidesToShow: 1,
                  slidesToScroll: 1
                }
              }
              // You can unslick at a given breakpoint now by adding:
              // settings: "unslick"
              // instead of a settings object
            ]
        });
    }
    
    function initStickerAnimation(){
        //animation
        $(document).on('mouseenter', '.sticker_animation', function (e) {
            playSticker($(this));
        })
    }
    
    function playSticker(item){
        if((parseInt(item.data('block')) == 1 && parseInt(item.data('quantity')) == 1) || item.data('playing') == 1){
            return;
        }
        var interval = parseInt(item.data('interval'))
        var backgroundPos = item.css('backgroundPosition').split(" "); 
        var backgroundSize = item.css('backgroundSize').split(" ");
        var xPos = parseFloat(backgroundPos[0]),
            yPos = parseFloat(backgroundPos[1]);
        var sizeW = parseFloat(backgroundSize[0]), 
            sizeH = parseFloat(backgroundSize[1]);
        var offset = ((sizeW / parseInt(item.attr('data-block'))) * 100).toFixed(1);
        offset = Math.ceil(offset) / 100;
        var max_quantity = parseInt(item.data('quantity')) - 1;
        var quantity = 0;
        var loop = 0;
        var stickerInterval = setInterval(function() {
            item.data('playing', 1);
            quantity += 1;
            xPos -= offset;
            if(xPos <= -sizeW){
                xPos = 0;
                yPos -= offset;
            }
            if(yPos < -sizeH || quantity > max_quantity){
                quantity = 0;
                xPos = 0;
                yPos = 0;
                loop += 1;
            }
            item.css('backgroundPosition', xPos + 'px ' + yPos + 'px');
            if(loop >= _animationLoop){
                clearInterval(stickerInterval);
                item.data('playing', 0);
            }
        }, interval);
    }
    
    function searchSticker(){
        var keyword = $('#sticker_search').val();
        if(keyword.trim() == "" || keyword.length < 2){
            return;
        }
        $('.sticker_category_container').hide();
        $('#sticker_search_content').html('');
        $('#sticker_search_content').spin('small');
        
        var params = {
            keyword: keyword
        };
        $.get(mooConfig.url.base + "/sticker/search", params, function(data){
            $('#sticker_search_content').spin(false);
            $('#sticker_search_content').html(data); 
            $('.scrollbar-inner').scrollbar({
                height:300
            });
        });
    }
    
    function initEditComment(item_type, item_id, sticker_image_id){
        hideOtherPluginsWhenComment(item_type, item_id);
        var beforeCallback = _commentMap[item_type].beforeCallback;
        $('body').on(beforeCallback, function (event, obj) {
            obj.emptyContent = true;
            obj.targetId = item_id;
            obj.itemType = item_type;
            obj.params = {sticker_image_id: sticker_image_id, empty_comment: 1};
        });
    }
    
    function showUploadPhoto()
    {
        $('#select-2').show();
        $('#wall_photo_preview').hide();
        $('#wall_photo').val('');
    }
    
    function hideUploadPhoto()
    {
        $('#select-2').hide();
        $('#wall_photo_preview').hide();
        $('#wall_photo_preview').find('span').each(function(){
            if($(this).attr('id') != 'addMoreImage')
            {
                $(this).remove();
            }
        })
        $('#wall_photo_preview').find('#addMoreImage').hide();
    }
    
    function hideOtherPluginsWhenComment(item_type, item_id){
        if(item_type == "activity_edit"){
            var parent_item = $('#activity_' + item_id);
            parent_item.find(".photo_addlist").hide();
        }
        else if(item_type == "commentForm"){
            var parent_item = $('#newComment_' + item_id);
            if(item_id == 0){
                parent_item = $('#commentForm');
            }
            parent_item.find("div[id*='comment_preview_image_']").empty();
            parent_item.find("input[id*='comment_image_']").val('');
            parent_item.find("div[id*='comment_button_attach_']").hide();
            parent_item.find(".gif_holder").hide();
        }
        else if(item_type == "commentReplyForm"){
            var parent_item = $('#newComment_reply_' + item_id);
            parent_item.find("div[id*='comment_reply_preview_image_']").empty();
            parent_item.find("input[id*='comment_reply_image_']").val('');
            parent_item.find("div[id*='comment_reply_button_attach_']").hide();
            parent_item.find(".gif_holder").hide();
        }
        else if(item_type == "activitycommentReplyForm"){
            var parent_item = $('#activitynewComment_reply_' + item_id);
            parent_item.find("div[id*='activitycomment_reply_preview_image_']").empty();
            parent_item.find("input[id*='activitycomment_reply_image_']").val('');
            parent_item.find("div[id*='activitycomment_reply_button_attach_']").hide();
            parent_item.find(".gif_holder").hide();
        }
        else if(item_type == "activity_comment_edit"){
            var parent_item = $('#comment_' + item_id);
            parent_item.find("div[id*='activity_comment_preview_attach_']").empty();
            parent_item.find("input[id*='activity_comment_attach_id_']").val('');
            parent_item.find("div[id*='activity_comment_attach_']").hide();
            parent_item.find(".gif_holder").hide();
        }
        else if(item_type == "item_commentReplyForm"){
            var parent_item = $('#item_newComment_reply_' + item_id);
            parent_item.find("div[id*='item_comment_reply_preview_image_']").empty();
            parent_item.find("input[id*='item_comment_reply_image_']").val('');
            parent_item.find("div[id*='item_comment_reply_button_attach_']").hide();
            parent_item.find(".gif_holder").hide();
        }
        else if(item_type == "item_comment_edit"){
            var parent_item = $('#item_comment_edit_' + item_id);
            parent_item.find("div[id*='item_comment_preview_attach_']").empty();
            parent_item.find("input[id*='item_comment_attach_id_']").val('');
            parent_item.find("div[id*='item_comment_attach_']").hide();
            parent_item.find(".gif_holder").hide();
        }
    }
    
    function showOtherPluginsWhenComment(item_type, item_id){
        if(item_type == "activity_edit"){
            var parent_item = $('#activity_' + item_id);
            if(parent_item.find('.sticker_activity_item').length > 0){
                parent_item.find(".photo_addlist").hide();
            }
            else{
                parent_item.find(".photo_addlist").show();
            }
        }
        else if(item_type == "commentForm"){
            var parent_item = $('#newComment_' + item_id);
            if(item_id == 0){
                parent_item = $('#commentForm');
            }
            parent_item.find("div[id*='comment_button_attach_']").show();
            parent_item.find(".gif_holder").show();
        }
        else if(item_type == "commentReplyForm"){
            var parent_item = $('#newComment_reply_' + item_id);
            parent_item.find("div[id*='comment_reply_button_attach_']").show();
            parent_item.find(".gif_holder").show();
        }
        else if(item_type == "activitycommentReplyForm"){
            var parent_item = $('#activitynewComment_reply_' + item_id);
            parent_item.find("div[id*='activitycomment_reply_button_attach_']").show();
            parent_item.find(".gif_holder").show();
        }
        else if(item_type == "activity_comment_edit"){
            var parent_item = $('#comment_' + item_id);
            parent_item.find("div[id*='activity_comment_attach_']").show();
            parent_item.find(".gif_holder").show();
        }
        else if(item_type == "item_commentReplyForm"){
            var parent_item = $('#item_newComment_reply_' + item_id);
            parent_item.find("div[id*='item_comment_reply_button_attach_']").show();
            parent_item.find(".gif_holder").show();
        }
        else if(item_type == "item_comment_edit"){
            var parent_item = $('#item_comment_edit_' + item_id);
            parent_item.find("div[id*='item_comment_attach_']").show();
            parent_item.find(".gif_holder").show();
        }
    }
    
    return{
        initSticker: initSticker,
        initEditComment: initEditComment
    };
}));
