(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD
        define(['jquery', 'mooPhrase', 'mooGlobal', 'slimScroll'], factory);
    } else if (typeof exports === 'object') {
        // Node, CommonJS-like
        module.exports = factory(require('jquery'));
    } else {
        // Browser globals (root is window)
        root.mooPostFeeling = factory(root.jQuery);
    }
}(this, function ($, mooPhrase, mooGlobal, slimScroll) {
    var _plugin3rd_name = 'Feeling';

    var _disable_this_plugin = false;
    var _flag_disable_by_plugin = false;

    var _listFeedPlugin3drEnable = [];

    var _flag_disable_link_review_by_plugin = false;

    var _element_wallForm = $('#wallForm');
    var _element_wall_photo = $('#wall_photo');
    var _element_userShareLink = $('#userShareLink');
    var _element_userShareVideo = $('#userShareVideo');
    var _element_uploadVideo = $('#video_destination');

    var _feeling_data = [
        {
            id: 1,
            image: 'feeling/img/emoji-1.png',
            text: 'Category 1',
            items: [
                {
                    id: 1,
                    image: 'feeling/img/emoji-2.png',
                    text: 'Item 1.1',
                    type: 'icon',
                    link: ''
                },
                {
                    id: 2,
                    image: 'feeling/img/emoji-3.png',
                    text: 'Item 1.2',
                    type: 'link',
                    link: 'http://youtube.com'
                }
            ]
        }
    ];
    var _feeling_data_category_index = 0;
    var _feeling_data_item_index = 0;

    var feeling_lvl_1 = {
        id: '',
        image: '',
        text: ''
    };
    var feeling_lvl_2 = {
        id: '',
        image: '',
        text: ''
    };

    var _element_feeling;

    var element_feeling_button;
    var element_feeling_table_select;

    var element_feeling_lvl_1_search;
    var element_feeling_lvl_1_id;

    var element_feeling_lvl_2_search;
    var element_feeling_lvl_2_id;

    var element_feeling_lvl_1_text;
    var element_feeling_lvl_2_text;

    var element_feeling_lvl_1_items;
    var element_feeling_lvl_2_items;

    var element_feeling_lvl_1_overview;
    var element_feeling_lvl_2_overview;

    var element_feeling_remove;

    var element_feeling_result;
    var element_feeling_result_overview;

    var initElements = function () {
        _element_feeling = $('<div class="felling-wall">' +
            '<div class="clearfix">' +
            '<a id="FeelingButton" class="feeling-button" href="#"><i class="material-icons">insert_emoticon</i> '+ mooPhrase.__('feeling_text_button_wall') +'</a>' +
            '<div id="feelingResult">&#8212;&nbsp;<span class="feeling-icon"></span>&nbsp;<span class="feeling-lvl1"></span>&nbsp;<a href="#"><span class="feeling-lvl2"></span></a></div>' +
            '</div>' +
            '<div id="feelingResultOverview" class="feeling-result-overview"></div>' +
            '<table id="feelingTableSelect" class="feeling-table-select">' +
            '<tbody>' +
            '<tr class="">' +
            '<td class="feeling-lvl-1">' +
            '<span id="feelingLvl1Text"></span>' +
            '<div id="feelingLvl1Overview" class="feeling-overview"></div>' +
            '<div id="feelingLvl1Items" class="feeling-popup"><ul class="feelingSlimScroll"></ul></div>' +
            '<input id="feelingLvl1" type="text" placeholder="'+ mooPhrase.__('feeling_text_choose_feeling') +'">' +
            '<input id="feelingLvl1ID" type="hidden" name="feelingCategoryId">' +
            '</td>' +
            '<td class="feeling-lvl-2">' +
            '<span id="feelingLvl2Text"></span>' +
            '<div id="feelingLvl2Overview" class="feeling-overview"></div>' +
            '<div id="feelingLvl2Items" class="feeling-popup"><ul class="feelingSlimScroll"></ul></div>' +
            '<input id="feelingLvl2" class="empty" type="text" placeholder="'+ mooPhrase.__('feeling_text_choose_item') +'">' +
            '<input id="feelingLvl2ID" type="hidden" name="feelingId">' +
            '<a id="feelingRemove" class="feeling-remove"><i class="material-icons">cancel</i></a>' +
            '</td>' +
            '</tr>' +
            '</tbody>' +
            '</table>' +
            '</div>'
        );

        element_feeling_button = _element_feeling.find('#FeelingButton');
        element_feeling_table_select = _element_feeling.find('#feelingTableSelect');

        element_feeling_lvl_1_search = _element_feeling.find('#feelingLvl1');
        element_feeling_lvl_1_id = _element_feeling.find('#feelingLvl1ID');

        element_feeling_lvl_2_search = _element_feeling.find('#feelingLvl2');
        element_feeling_lvl_2_id = _element_feeling.find('#feelingLvl2ID');

        element_feeling_lvl_1_text = _element_feeling.find('#feelingLvl1Text');
        element_feeling_lvl_2_text = _element_feeling.find('#feelingLvl2Text');

        element_feeling_lvl_1_items = _element_feeling.find('#feelingLvl1Items');
        element_feeling_lvl_2_items = _element_feeling.find('#feelingLvl2Items');

        element_feeling_lvl_1_overview = _element_feeling.find('#feelingLvl1Overview');
        element_feeling_lvl_2_overview = _element_feeling.find('#feelingLvl2Overview');

        element_feeling_remove = _element_feeling.find('#feelingRemove');

        element_feeling_result = _element_feeling.find('#feelingResult');
        element_feeling_result_overview = _element_feeling.find('#feelingResultOverview');

    };

    var removeFeeling = function () {
        disableLvl1();
        disableLvl2();
        resetFilterPopupLvl1();
        resetFilterPopupLvl2();
        //element_feeling_table_select.hide();
        finishChoose();
    };
    var chooseLvl1 = function (id, text, image) {

        if(id !== ''){
            feeling_lvl_1.id = id;
            feeling_lvl_1.image = image;
            feeling_lvl_1.text = text;

            element_feeling_lvl_1_id.val(id);
            element_feeling_lvl_1_search.val(text);
            element_feeling_lvl_1_text.html(text);
            element_feeling_lvl_1_search.parent().addClass('choose');
            activeLvl2();
        }else {
            feeling_lvl_1.id = '';
            feeling_lvl_1.image = '';
            feeling_lvl_1.text = '';

            element_feeling_lvl_1_id.val('');
            element_feeling_lvl_1_search.val('');
            element_feeling_lvl_1_text.html('');
            element_feeling_lvl_1_search.parent().removeClass('choose');
        }
        closePopupLvl1();
    };
    var disableLvl1 = function () {
        feeling_lvl_1.id = '';
        feeling_lvl_1.image = '';
        feeling_lvl_1.text = '';

        element_feeling_lvl_1_search.val('');
        element_feeling_lvl_1_id.val('');
        element_feeling_lvl_1_text.html('');
        element_feeling_lvl_1_search.parent().removeClass('choose');
    };
    var preChooseLvl1 = function () {
        feeling_lvl_1.id = '';
        feeling_lvl_1.image = '';
        feeling_lvl_1.text = '';

        //element_feeling_lvl_1_search.val('');
        element_feeling_lvl_1_id.val('');
        element_feeling_lvl_1_text.html('');
        element_feeling_lvl_1_search.parent().removeClass('choose');
    };
    var closePopupLvl1 = function () {
        element_feeling_lvl_1_items.hide();
        element_feeling_lvl_1_overview.hide();
        if(element_feeling_lvl_1_id.val() === ''){
            disableLvl1();
            resetFilterPopupLvl1();
            finishChoose();
        }
    };
    var filterPopupLvl1 = function () {
        filter = element_feeling_lvl_1_search.val().toUpperCase();
        element_feeling_lvl_1_items.find('li').each(function (index) {
            a = $(this).find('a');
            if (a.html().toUpperCase().indexOf(filter) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    };
    var resetFilterPopupLvl1 = function () {
        element_feeling_lvl_1_items.find('li').show();
    };

    var activeLvl2 = function () {
        ul = element_feeling_lvl_2_items.find('ul');
        ul.empty();
        items_data = _feeling_data[_feeling_data_category_index].items;
        //console.log('items_data', items_data);
        for (i = 0; i < items_data.length; i++){
            li = '<li><span class="feeling-icon" style="background-image: url('+ items_data[i].image +')"></span><a href="'+ items_data[i].link +'" data-type="'+ items_data[i].type +'" data-idx="'+ i +'" data-id="'+ items_data[i].id +'" data-img="'+ items_data[i].image +'" data-text="'+ items_data[i].text +'">'+ items_data[i].text +'</a></li>';
            ul.append(li);
        }

        element_feeling_lvl_2_items.find('a').click(function (e) {
            e.preventDefault();
            _feeling_data_item_index = $(this).data('idx');
            chooseLvl2($(this).data('id'), $(this).data('text'), $(this).data('img'), $(this).data('type'), $(this).attr('href'));
        });

        element_feeling_lvl_2_search.parent().addClass('active');
        element_feeling_lvl_2_search.focus();
    };
    var disableLvl2 = function () {
        element_feeling_lvl_2_search.parent().removeClass('active');

        feeling_lvl_2.id = '';
        feeling_lvl_2.image = '';
        feeling_lvl_2.text = '';

        element_feeling_lvl_2_search.val('');
        element_feeling_lvl_2_id.val('');
        element_feeling_lvl_2_text.html('');
        element_feeling_lvl_2_search.parent().removeClass('choose');
    };
    var preChooseLvl2 = function () {
        feeling_lvl_2.id = '';
        feeling_lvl_2.image = '';
        feeling_lvl_2.text = '';

        //element_feeling_lvl_1_search.val('');
        element_feeling_lvl_2_id.val('');
        element_feeling_lvl_2_text.html('');
        element_feeling_lvl_2_search.parent().removeClass('choose');
    };
    var chooseLvl2 = function (id, text, image, type, link) {
        if(id !== ''){
            feeling_lvl_2.id = id;
            feeling_lvl_2.image = image;
            feeling_lvl_2.text = text;

            element_feeling_lvl_2_id.val(id);
            element_feeling_lvl_2_search.val(text);
            element_feeling_lvl_2_text.html(text);
            element_feeling_lvl_2_search.parent().addClass('choose');
            //activeLvl2();

            if(type === 'link'){
                //console.log('chooseLvl2 addLinkInFeed: ',link);
                doValidateFeelingReviewLinkOnWall();
                if(!_flag_disable_link_review_by_plugin){
                    addLinkInFeed(link);
                }
            }
        }else {
            feeling_lvl_2.id = '';
            feeling_lvl_2.image = '';
            feeling_lvl_2.text = '';

            element_feeling_lvl_2_id.val('');
            element_feeling_lvl_2_search.val('');
            element_feeling_lvl_2_text.html('');
            element_feeling_lvl_2_search.parent().removeClass('choose');
        }
        closePopupLvl2();
    };
    var closePopupLvl2 = function () {
        element_feeling_lvl_2_items.hide();
        element_feeling_lvl_2_overview.hide();
        if(element_feeling_lvl_2_id.val() === ''){
            removeFeeling();
        }else{
            finishChoose();
        }
    };
    var finishChoose = function () {
        // console.log('feeling_lvl_1: ', feeling_lvl_1);
        // console.log('feeling_lvl_2: ', feeling_lvl_2);
        if( element_feeling_lvl_1_id.val() !== '' && element_feeling_lvl_2_id.val() !== '' ){
            element_feeling_result.find('.feeling-icon').css('background-image', 'url('+feeling_lvl_2.image+')');
            element_feeling_result.find('.feeling-lvl1').html(feeling_lvl_1.text);
            element_feeling_result.find('.feeling-lvl2').html(feeling_lvl_2.text);
            element_feeling_result.show();
            element_feeling_table_select.hide();
            $('body').trigger('startFeedPlugin3drCallback',[{plugin_name: _plugin3rd_name}]);
        }else {
            element_feeling_result.hide();
            element_feeling_result.find('.feeling-icon').css('background-image', '');
            element_feeling_result.find('.feeling-lvl1').html('');
            element_feeling_result.find('.feeling-lvl2').html('');
            element_feeling_table_select.show();
            $('body').trigger('stopFeedPlugin3drCallback',[{plugin_name: _plugin3rd_name}]);
        }
    };
    var filterPopupLvl2 = function () {
        filter = element_feeling_lvl_2_search.val().toUpperCase();
        element_feeling_lvl_2_items.find('li').each(function (index) {
            a = $(this).find('a');
            //console.log('(filter): ', filter);
            //console.log('a.html().toUpperCase().indexOf(filter): ', a.html().toUpperCase().indexOf(filter));
            if (a.html().toUpperCase().indexOf(filter) > -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    };
    var resetFilterPopupLvl2 = function () {
        element_feeling_lvl_2_items.find('li').show();
        /*element_feeling_lvl_1_items.find('li').each(function (index) {
         $(this).show();
         });*/
    };
    var closeResultOverview = function () {
        element_feeling_result_overview.hide();
        element_feeling_table_select.removeClass('edit').show();
    };
    var renderHtmlCategory = function () {
        ul = element_feeling_lvl_1_items.find('ul');
        for (i = 0; i < _feeling_data.length; i++){
            li = '<li class="feeling-arrow"><span class="feeling-icon" style="background-image: url('+ _feeling_data[i].image +')"></span><a href="#" data-idx="'+ i +'" data-id="'+ _feeling_data[i].id +'" data-img="'+ _feeling_data[i].image +'" data-text="'+ _feeling_data[i].text +'">'+ _feeling_data[i].text +'</a></li>';
            ul.append(li);
        }

        element_feeling_lvl_1_items.find('a').click(function (e) {
            e.preventDefault();
            _feeling_data_category_index = $(this).data('idx');
            chooseLvl1($(this).data('id'), $(this).data('text'), $(this).data('img'));
        });
    };

    var openFeeling = function (index) {
        element_feeling_table_select.show();
        element_feeling_lvl_1_search.focus();
    };
    var closeFeeling = function () {
        element_feeling_table_select.hide();
    };

    var requestSent = false;
    function getUrlFromText(text) {
        //console.log('getUrlFromText',text);
        result = text.match(/\b([\d\w\.\/\+\-\?\:]*)((ht|f)tp(s|)\:\/\/|[\d\d\d|\d\d]\.[\d\d\d|\d\d]\.|www\.|\.tv|\.ac|\.com|\.edu|\.gov|\.int|\.mil|\.net|\.org|\.biz|\.info|\.name|\.pro|\.museum|\.co)([\d\w\.\/\%\+\-\=\&amp;\?\:\\\&quot;\'\,\|\~\;]*)\b/gi);
        if (result)
        {
            return result[0];
        }
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

    var addLinkInFeed = function(iContent){
        var content = $('#message').val();
        iContent = getUrlFromText(iContent);
        //if (array_delete_links.hasOwnProperty(iContent))
        //    return;

        if (iContent && (substr(iContent, 0, 7) == 'http://' || substr(iContent, 0, 8) == 'https://' || (substr(iContent, 0, 4) == 'www.')))
        {
            var checkHttps = strpos(iContent,'https://',0);
            var checkHttp = strpos(iContent,'http://',0);
            if(checkHttps === 0  || checkHttp === 0){
                //check video link
                var checkV1 = strpos(iContent,'youtube.com',0);
                var checkV2 = strpos(iContent,'youtu.be',0);
                var checkV3 = strpos(iContent,'vimeo.com',0);
                if(!checkV1 && !checkV2 && !checkV3){
                    $('.userTagging-userShareLink').removeClass('hidden');
                    $('.userTagging-userShareVideo').addClass('hidden');
                    $('#userShareVideo').val('');
                    $('#userShareLink').val(iContent);
                    getLinkPreview('userShareLink', iContent, true, content);
                }else{
                    $('.userTagging-userShareVideo').removeClass('hidden');
                    $('.userTagging-userShareLink').addClass('hidden');
                    $('#userShareLink').val('');
                    $('#userShareVideo').val(iContent);
                    getLinkPreview('userShareVideo', iContent, true, content);
                }
            }
        }
    };
    var getLinkPreview = function(el, content, paste, oldContent){
        var element = $('.userTagging-'+ el);

        //if (!array_save_links.hasOwnProperty(content))
        //{
        element.spin('tiny');
        //}
        setTimeout(function(){ //break the callstack to let the event finish
            if(!requestSent) {
                requestSent = true;
                var fbURL=mooConfig.url.base + "/activities/ajax_preview_link";
                $.post(fbURL, {content:content}, function(resp){
                    element.spin(false);
                    doPreviewLink(element,content, paste, oldContent,resp);
                });
            }
        },0);
    };
    var doPreviewLink = function(element,content, paste, oldContent, resp) {
        $('#preview_link').remove();
        var obj = jQuery.parseJSON(resp);


        if(!jQuery.isEmptyObject(obj) && typeof obj.title !== "undefined" &&  obj.title !== "404 Not Found" &&  obj.title !== "403 Forbidden"){
            var data = '<div class="activity_item" id="preview_link">';
            if(typeof obj.image !== "undefined" && obj.image != ''){
                data += '<div class="activity_left"><a class="removePreviewlink removeImage" href="javascript:void(0)"><i class="icon-delete material-icons">clear</i></a>';
                if(obj.image.indexOf('http') != -1){
                    data += '<img src="' + obj.image + '" class="img_wrapper2">';
                }else{
                    data += '<img src="' + mooConfig.url.base + '/uploads/links/' + obj.image + '" class="img_wrapper2">';
                }
                data += '<input type="hidden" name="data[share_image]" id="userShareLink" value="1">';
                data += '</div>';
            }
            if(obj.image != ''){
                data += '<div class="activity_right">';
            }else{
                data += '<div>';
            }
            data += '<a class="removePreviewlink removeContent" href="javascript:void(0)"><i class="icon-delete material-icons">clear</i></a>';
            data += '<a class="attachment_edit_link feed_title" href="' + obj.url + '" target="_blank" rel="nofollow">';
            data += '<strong>' + obj.title + '</strong>';
            data += '</a>';
            if (typeof obj.description !== "undefined" && obj.description != ''){
                data += '<div class="attachment_body_description">';
                data += '<a class="attachment_edit_link comment_message feed_detail_text">' + obj.description + '</a>';
                data += '</div>';
            }
            data += '<input type="hidden" name="data[share_text]" id="userShareLink" value="1">';
            data += '</div></div>';

            element.append(data);
            /*if(paste){
             $('.textoverlay').text(oldContent);
             $('.autogrow-textarea-mirror').text(oldContent);
             }*/
            removePreviewlink();
            $('body').trigger('afterPreviewLinkWallCallback',[]);
        }

        requestSent = false;
    };
    var removePreviewlink = function(){
        $('.removeImage').unbind('click');
        $('.removeImage').on('click', function(){
            $(this).parent().remove();
            $('#shareImage').val('0');
        });
        $('.removeContent').unbind('click');
        $('.removeContent').on('click', function(){
            destroyPreviewlink();
        });
    };
    var destroyPreviewlink = function() {
        $('#preview_link').remove();
        $('#userShareLink').val('');
        $('#userShareVideo').val('');
        $('#shareImage').val('');
        $('body').trigger('afterDestroyPreviewLinkWallCallback',[]);
    };
    var substr = function(sString, iStart, iLength) {
        if(iStart < 0)
        {
            iStart += sString.length;
        }

        if(iLength == undefined)
        {
            iLength = sString.length;
        }
        else if(iLength < 0)
        {
            iLength += sString.length;
        }
        else
        {
            iLength += iStart;
        }

        if(iLength < iStart)
        {
            iLength = iStart;
        }

        return sString.substring(iStart, iLength);
    };

    var strpos = function(haystack, needle, offset) {
        var i = (haystack+'').indexOf(needle, (offset || 0));
        return i === -1 ? false : i;
    };

    var _checkMorePluginCallBack = function () {
        if ( (_listFeedPlugin3drEnable.length == 1) && (_listFeedPlugin3drEnable.indexOf(_plugin3rd_name) > -1) ){
            //chi 1 minh feeling dang active
            _flag_disable_link_review_by_plugin = false;
        }else if( (_listFeedPlugin3drEnable.length > 1) && (_listFeedPlugin3drEnable.indexOf(_plugin3rd_name) > -1) ){
            //feeling va plugin khac dang active
            _flag_disable_link_review_by_plugin = true;
        }else if( (_listFeedPlugin3drEnable.length > 0) && (_listFeedPlugin3drEnable.indexOf(_plugin3rd_name) == -1) ){
            //feeling ko active , plugin khac active
            _flag_disable_link_review_by_plugin = true;
        }else if(_listFeedPlugin3drEnable.length == 0){
            _flag_disable_link_review_by_plugin = false;
        }
    };

    var doValidateFeelingReviewLinkOnWall = function () {

        _checkMorePluginCallBack();

        if(!_flag_disable_link_review_by_plugin){
            if (_element_userShareLink.length > 0) {
                userShareLink = _element_userShareLink.val();
            } else {
                userShareLink = '';
            }
            if (_element_userShareVideo.length > 0) {
                userShareVideo = _element_userShareVideo.val();
            } else {
                userShareVideo = '';
            }
            if (_element_wall_photo.length > 0) {
                wall_photo = _element_wall_photo.val();
            } else {
                wall_photo = '';
            }
            if (_element_uploadVideo.length > 0) {
                userUploadVideo = _element_uploadVideo.val();
            } else {
                userUploadVideo = '';
            }

            var flag = false;
            if (userShareLink !== '') {
                flag = true;
            }
            if (userShareVideo !== '') {
                flag = true;
            }
            if (wall_photo !== '') {
                flag = true;
            }
            if (userUploadVideo !== '') {
                flag = true;
            }
            _flag_disable_link_review_by_plugin = flag;
        }
    };

    var parseAjaxLink = function(url) {
        if(mooConfig.isApp) {
            return mooGlobal.appBindTokenLanguage(url);
        }
        return url;
    };

    var doValidateFeelingOnWall = function () {

        //checkMorePluginCallBack();

        if(!_flag_disable_by_plugin) {
            var userShareLink = '';
            var userShareVideo = '';
            var wall_photo = '';
            var flag = false;

            if (_element_userShareLink.length > 0) {
                userShareLink = _element_userShareLink.val();
            } else {
                userShareLink = '';
            }
            if (_element_userShareVideo.length > 0) {
                userShareVideo = _element_userShareVideo.val();
            } else {
                userShareVideo = '';
            }
            if (_element_wall_photo.length > 0) {
                wall_photo = _element_wall_photo.val();
            } else {
                wall_photo = '';
            }
            if (_element_uploadVideo.length > 0) {
                userUploadVideo = _element_uploadVideo.val();
            } else {
                userUploadVideo = '';
            }

            if (userShareLink !== '') {
                flag = true;
            }
            if (userShareVideo !== '') {
                flag = true;
            }
            if (wall_photo !== '') {
                flag = true;
            }
            if (userUploadVideo !== '') {
                flag = true;
            }

            if (flag === true) {
                disableFeeling();
            } else {
                enableFeeling();
            }
        }else{
            disableFeeling();
        }
    };

    var renderFeelingHtml = function () {
        _element_feeling.insertBefore(_element_wallForm.find('#wall_photo_preview').parent());

        element_feeling_button.click(function (e) {
            e.preventDefault();
            if(_element_wallForm.hasClass('show-feeling')){
                openFeeling();
                if(element_feeling_lvl_1_id.val() !== '' && element_feeling_lvl_2_id.val() !== ''){
                    element_feeling_table_select.addClass('edit').show();
                    element_feeling_result_overview.show();
                }
            }else{
                showNoAlert();
            }
        });
        element_feeling_remove.click(function (e) {
            e.preventDefault();
            closeResultOverview();
            removeFeeling();
            closeFeeling();
        });
        element_feeling_lvl_1_search.focusin(function(){
            element_feeling_lvl_1_items.show();
            element_feeling_lvl_1_overview.show();
        });
        element_feeling_lvl_1_search.keyup(function(){
            if($(this).val() !== ''){
                filterPopupLvl1();
            }else {
                resetFilterPopupLvl1();
            }
        });
        element_feeling_lvl_1_overview.click(function (e) {
            e.preventDefault();
            closePopupLvl1();
            closeFeeling();
        });
        element_feeling_lvl_1_text.click(function (e) {
            //removeFeeling();
            closeResultOverview();
            disableLvl2();
            preChooseLvl1();
            filterPopupLvl1();
            element_feeling_lvl_1_search.focus();
        });
        //
        ///// lvl 2
        //
        element_feeling_lvl_2_search.keyup(function(e){
            if(e.keyCode === 8 && $(this).val() === '' && $(this).hasClass('empty')){
                removeFeeling();
                element_feeling_lvl_1_search.focus();
            }else{
                //console.log('element_feeling_lvl_2_search.keyup: ', $(this).val());
                if($(this).val() !== ''){
                    $(this).removeClass('empty');
                    filterPopupLvl2();
                }else {
                    $(this).addClass('empty');
                    resetFilterPopupLvl2();
                }
            }
        });
        element_feeling_lvl_2_search.focusin(function(){
            element_feeling_lvl_2_items.show();
            element_feeling_lvl_2_overview.show();
        });
        element_feeling_lvl_2_overview.click(function (e) {
            e.preventDefault();
            closePopupLvl2();
            closeFeeling();
        });
        element_feeling_lvl_2_text.click(function (e) {
            //removeFeeling();
            closeResultOverview();
            preChooseLvl2();
            filterPopupLvl2();
            element_feeling_lvl_2_search.focus();
        });
        element_feeling_result.find('a').click(function (e) {
            e.preventDefault();
            element_feeling_table_select.addClass('edit').show();
            element_feeling_result_overview.show();
            // element_feeling_lvl_2_text.trigger('click');
        });
        element_feeling_result_overview.click(function (e) {
            e.preventDefault();
            closeResultOverview();
            element_feeling_table_select.hide();
        });

        $('body').on('afterPostWallCallbackSuccess', function(e, data){
            removeFeeling();
            element_feeling_table_select.hide();
            element_feeling_result.hide();
        });

        $('.feelingSlimScroll').slimScroll({ height: '300px' });

        /*$('body').on('enablePlugin3drPostWallCallback', function(e, data){
            //enableBackgroundColor();
            var index = _list_more_plugin_disabled.indexOf(data.plugin_name);
            if( index != -1 ){
                _list_more_plugin_disabled.splice(index, 1);
            }
            doValidateFeelingOnWall();
        });
        $('body').on('disablePlugin3drPostWallCallback', function(e, data){
            if( _list_more_plugin_disabled.indexOf(data.plugin_name) == -1 && data.plugin_name != _plugin3rd_name ){
                _list_more_plugin_disabled.push(data.plugin_name);
            }
            doValidateFeelingOnWall();
        });*/

        //$('body').on('afterDestroyPreviewLinkWallCallback', function(e, data){
            //doValidateBackgroundOnWall();
        //});

        $('body').on('enablePluginsCallback', function(e, data){
            var _plugins = data.plugins;

            if(_plugins.indexOf(_plugin3rd_name) > -1){
                _flag_disable_by_plugin = false;
                doValidateFeelingOnWall();
            }
        });
        
        $('body').on('disablePluginsCallback', function(e, data){
            var _plugins = data.plugins;

            if(_plugins.indexOf(_plugin3rd_name) > -1){
                _flag_disable_by_plugin = true;
                doValidateFeelingOnWall();
            }
        });

        $('body').on('listFeedPlugin3drEnableCallback', function(e, data){
            _listFeedPlugin3drEnable = data.plugins;
        });
    };

    var disableFeeling = function () {
        removeFeeling();
        closeFeeling();
        _element_feeling.hide();
    };

    var enableFeeling = function () {
        _element_feeling.show();
    };

    var getFeedConfig = function () {
        if(typeof mooConfig.FeelingFeedConfig != "undefined" && !$.isEmptyObject(mooConfig.FeelingFeedConfig)){
            _plugin3rd_config = mooConfig.FeelingFeedConfig;
        }
    };

    var getListPlugin3drDisable = function () {
        return _plugin3rd_config[_plugin3rd_name];
    };

    return{
        init: function (url) {
            getFeedConfig();
            if(_element_wallForm.length > 0) {
                initElements();
                $.ajax({
                    url: parseAjaxLink(url),
                    dataType: 'json',
                    data: '',
                    cache: false,
                    beforeSend : function(){},
                    success: function(data) {
                        //console.log(data);
                        _feeling_data = data;
                        renderFeelingHtml();
                        renderHtmlCategory();
                    }
                });
            }
        }
    }
}));
