/* Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
import ChatConstants from '../constants/ChatConstants';
import ChatMooEmoji from "../utils/ChatMooEmoji";
import ChatMooMenu from "../utils/ChatMooMenu";
import ChatMooConfig from "../utils/ChatMooConfig";
var ChatWebAPIUtils;
var freezeAjaxSetState = false;


function _ajaxSetState(postData, callback) {
    if (freezeAjaxSetState) {
        return;
    }
    window.$.post(module.exports.getSiteUrl() + "/chats/save-user-settings",
        postData,
        function (data, status) {
            //callback();
        });
}
function _ajaxGetDataFromURL(url,callback){
    window.$.post(module.exports.getSiteUrl() + "/chats/embed",
        {link:url},
        function (data, status) {
            callback(data);
        });
}
var ChatMooUtils = {
    isAllowedSendPicture: function () {
        return ChatMooConfig.isAllowedSendPicture();
    },
    isAllowedSendFiles: function () {
        return ChatMooConfig.isAllowedSendFiles();
    },
    isAllowedEmotion: function () {
        return ChatMooConfig.isAllowedEmotion();
    },
    isAllowedChatGroup: function () {
        return ChatMooConfig.isAllowedChatGroup();
    },
    isAllowedVideoCalling: function () {
        return ChatMooConfig.isAllowedVideoCalling();
    },
    setThisOfChatWebAPIUtils: function (_that) {
        ChatWebAPIUtils = _that;
    },
    setChatSoundState: function (state) {
        ChatMooConfig.setChatSoundState(state);
    },
    getSiteUrl: function () {
        return ChatMooConfig.getSiteUrl();
    },
    getWebUrl: function () {
        return ChatMooConfig.getWebUrl();
    },
    getStorageUrl: function () {
        return ChatMooConfig.getStorageUrl();
    },
    getAvatarLinkFromDataUser: function (data, prefix) {
        var url = "";
        var avatar_prefixes = ["50_square_", "100_square_", "200_square_", "600_"];
        if(typeof prefix == "undefined" || avatar_prefixes.indexOf(prefix) == -1)
        {
            prefix = "50_square_";
        }
        if (data.avatar != "") {
            if(this.getWebUrl() == this.getStorageUrl()){
                // It is local storage
                url = this.getStorageUrl() + '/uploads/users/avatar/' + data.id + '/' + prefix + data.avatar;
            }else{
                url = this.getStorageUrl() + '/users/avatar/' + data.id + '/' + prefix + data.avatar;
            }

        } else {
            url = this.getStorageUrl();
            switch(data.gender){
                case "Female":
                    url += "/user/img/noimage/Female-user.png";
                    break;
                case "Male":
                    url += "/user/img/noimage/Male-user.png";
                    break;
                default:
                    url += "/user/img/noimage/Unknown-user.png";
            }
            //url = this.getSiteUrl() + (data.gender == "Female" ? "/user/img/noimage/Female-user.png" : "/user/img/noimage/Male-user.png");
        }
        return url;
    },
    getProfileLinkFromDataUser: function (data) {
        return this.getSiteUrl() + '/users/view/' + data.id;
    },
    getChatBlockSettingURL: function () {
        return ChatMooConfig.getChatBlockSettingURL();
    },
    getChatHistoryURL: function () {
        return ChatMooConfig.getChatHistoryURL();
    },
    getChatFullConversationURL: function () {
        return ChatMooConfig.getChatFullConversationURL();
    },
    getEmojiHtml: function () {
        return ChatMooEmoji.getEmojiHtml();
    },
    getEmojiJson: function () {
        return ChatMooEmoji.getEmojiJson();
    },
    windowWidth: function () {
        return window.jQuery(document).width();
    },
    getChatToken: function () {
        return ChatMooConfig.getChatToken();
    },
    getChatStatus: function () {
        return ChatMooConfig.getChatStatus();
    },
    getServerUrl: function () {
        return ChatMooConfig.getServerUrl();
    },
    getUploadFileLimitOnSite: function () {
        return ChatMooConfig.getUploadFileLimitOnSite();
    },
    getLanguage: function () {
        return ChatMooConfig.getLanguage();
    },
    getLanguage2Letter: function () {
        return ChatMooConfig.getLanguage2Letter();
    },
    getFriendSuggestIsChoosenInARoom: function (roomId) {
        return window.$('#chatroom-' + roomId).val();
    },
    getChatSoundState: function () {
        return ChatMooConfig.getChatSoundState();
    },
    getHideGroupState: function () {
        return ChatMooConfig.getHideGroupState();
    },
    getRoomIsOpen: function () {
        return ChatMooConfig.getRoomIsOpen();
    },
    getMoreMessageLimit: function () {
        return ChatMooConfig.getMoreMessageLimit();
    },
    getFirstTimeMessagesLimit: function () {
        return ChatMooConfig.getFirstTimeMessagesLimit();
    },
    getDataFromURL:function(url,callback){
        _ajaxGetDataFromURL(url,callback);
    },
    isTurnOffForFirstTimeUsing: function () {
        return ChatMooConfig.isTurnOffForFirstTimeUsing();
    },
    isChatSoundGlobalEnable: function () {
        return ChatMooConfig.isChatSoundGlobalEnable();
    },
    isRTL: function () {
        return ChatMooConfig.isRTL();
    },
    isApp: function () {
        return ChatMooConfig.isApp();
    },
    isIOS: function () {
        return ChatMooConfig.isIOS();
    },
    isAndroid: function () {
        return ChatMooConfig.isAndroid();
    },
    unixTime: function () {
        var time = new Date().getTime();
        return Math.floor(time / 1000);
    },
    popupImage: function (e) {
        window.jQuery(e).magnificPopup({
            type: 'image',
            gallery: {enabled: false},
            zoom: {
                enabled: true,
                opener: function (openerElement) {
                    return openerElement;
                }
            }
        }).click();
    },
    initSlimScroll: function (elementString) {
        window.require(['jquery', 'slimScroll'], function ($) {
            $(elementString).slimScroll({height: '369px'});
        });
    },

    initFriendSuggestForARoom: function (roomId, iMembers, bloodhound) {
        return;
    },
    resetFriendSuggestForARoom: function (roomId) {
        window.$('#chatroom-' + roomId).tagsinput('removeAll');
    },
    convertEmoji: function (text) {
        return ChatMooEmoji.replace(text);
    },
    isEnableChat: function () {
        return !ChatMooConfig.isDisabled();
    },
    isHideOfflineUser: function () {
        return ChatMooConfig.isHideOfflineUser();
    },
    isOpennedChatboxWhenANewMesasgeArrives: function () {
        return ChatMooConfig.isOpennedChatboxWhenANewMesasgeArrives();
    },
    isAllowedSendToNonFriend:function(){
        return ChatMooConfig.isAllowedSendToNonFriend();
    },
    isTurnOnNotification:function(){
        return ChatMooConfig.isTurnOnNotification();
    },
    turnOffChat: function () {
        freezeAjaxSetState = true;
        ChatWebAPIUtils.destoryAllRoom();
        freezeAjaxSetState = false;
        _ajaxSetState({'status': ChatConstants.USER_OFFLINE});
        ChatWebAPIUtils.sendRequestTurnOffChat();

    },
    turnOffSound: function () {
        this.setChatSoundState(ChatConstants.SOUND_DISABLE);
        _ajaxSetState({'sound': ChatConstants.SOUND_DISABLE, 'room_is_opened': ''});
    },
    turnOnChat: function () {
        _ajaxSetState({'status': ChatConstants.USER_ONLINE, 'first_time_using': ChatConstants.APP_IS_USED});

        ChatWebAPIUtils.sendRequestTurnOnChat();
    },
    turnOnSound: function () {
        this.setChatSoundState(ChatConstants.SOUND_ENABLE);
        _ajaxSetState({'sound': ChatConstants.SOUND_ENABLE});
    },
     noteRoomIsOpned: function (rooms) {
        if (rooms.isCreated.length > 0) {
            var _rooms = {isCreated: rooms.isCreated}

            for (var i = 0; i < rooms.isCreated.length; i++) {

                _rooms[rooms.isCreated[i]] = {m: rooms[rooms.isCreated[i]].minimized};
            }
                _ajaxSetState({'room_is_opened': JSON.stringify(_rooms)});
        } else {
            _ajaxSetState({'room_is_opened': ''});
        }

    },
    hideGroup: function (isHide) {
        if (isHide) {
            _ajaxSetState({'hide_group': ChatConstants.HIDE_GROUP_ENABLE});
        } else {
            _ajaxSetState({'hide_group': ChatConstants.HIDE_GROUP_DISABLE});
        }
    },
    setAppIsUsed: function () {
        if (ChatMooConfig.isFirsTimeUsing()) {
            _ajaxSetState({'first_time_using': ChatConstants.APP_IS_USED});
        }

    },
    setFirsTimeUsing: function (state) {
        ChatMooConfig.setFirsTimeUsing(state);
    },
    setChatStatus: function (state) {
        ChatMooConfig.setChatStatus(state);
    },
    setIsMobile: function (isMoblie) {
        ChatMooConfig.setIsMobile(isMoblie);
    },
    isMobile: function () {
        return ChatMooConfig.isMobile();
    },
    // Element behavior 
    hasClass: function (el, className) {
        if (el.classList)
            return el.classList.contains(className)
        else
            return !!el.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'))
    },
    addClass: function (el, className) {
        if (el.classList)
            el.classList.add(className)
        else if (!hasClass(el, className)) el.className += " " + className
    },
    removeClass: function (el, className) {
        if (el.classList)
            el.classList.remove(className)
        else if (hasClass(el, className)) {
            var reg = new RegExp('(\\s|^)' + className + '(\\s|$)')
            el.className = el.className.replace(reg, ' ')
        }
    },
    hideBodyChildNode:function(){
        var nodes = document.getElementsByTagName("BODY")[0].childNodes;
        if( nodes.length > 0){
            for(var i=0;i<nodes.length;i++){

                    if(nodes[i].tagName === "DIV"){
                        if(nodes[i].id != "appChat" && nodes[i].className.indexOf("ReactModalPortal") === -1){
                            this.addClass(nodes[i],"chat-hide");
                        }

                    }

            }
        }
    },
    showBodyChildNode:function(){
        var nodes = document.getElementsByTagName("BODY")[0].childNodes;
        if( nodes.length > 0){
            for(var i=0;i<nodes.length;i++){
             
                    if(nodes[i].tagName === "DIV"){
                        this.removeClass(nodes[i],"chat-hide");

                    }
                
            }
        }
    },
    updateConversationCounter:function(n){
        if(this.isTurnOnNotification()){
            window.require(['jquery'], function ($) {
                if (parseInt(n) > 0){
                    if($('.conversation_count').length > 0)
                    {
                        $('.conversation_count').html(n);

                    }else{
                        $('#conversationDropdown').append('<span class="conversation_count">1</span>');
                    }
                }else{
                    if($('.conversation_count')){
                        $('.conversation_count').remove();
                    }
                }
            });
        }
    },
    refeshMessagePage:function(n){
        if(this.isTurnOnNotification()){
            if(window.location.href.indexOf("home/index/tab:messages") != -1){
                window.$("#messages").click();
            }
        }
    },
    markReadOnMessagesPage:function(e){

        window.require(['jquery','mooAjax'],function($,mooAjax){
            var data = $(e).data();
            var obj = $(e);
            mooAjax.post({
                url: mooConfig.url.base + '/conversations/mark_read?access_token='+mooConfig.mooChat.access_token,
                data: {
                    id : data.id,
                    status : data.status
                }
            }, function (respsonse) {
                var json = $.parseJSON(respsonse);
                window.require(["mooChat"],function(chat){chat.markMessagesInARoomIsSeen(json.roomId);}.bind(json)).bind(json);
                var currentCounter = $('#messages .badge_counter').html();
                if (json.status === '1'){
                    obj.parents('li:first').addClass('unread');
                    obj.hide();
                    obj.prev().show();

                    // update counter
                    $('#messages .badge_counter').html(parseInt(currentCounter) + 1);

                    // update conversation count
                    if($('.conversation_count').length > 0)
                    {
                        $('.conversation_count').html(parseInt(currentCounter) + 1);

                    }else{
                        $('#conversationDropdown').append('<span class="conversation_count">1</span>');
                    }

                }else{
                    obj.parents('li:first').removeClass('unread');
                    obj.hide();
                    obj.next().show();

                    // update counter
                    $('#messages .badge_counter').html(parseInt(currentCounter) - 1);

                    // update conversation count
                    if (parseInt(currentCounter) - 1 > 0){
                        if($('.conversation_count').length > 0)
                        {
                            $('.conversation_count').html(parseInt(currentCounter) - 1);

                        }else{
                            $('#conversationDropdown').append('<span class="conversation_count">1</span>');
                        }
                    }else{
                        if($('.conversation_count')){
                            $('.conversation_count').remove();
                        }
                    }
                }
            });
        });

    }
    // End Element behavior 
};
module.exports = ChatMooUtils;
export default  module.exports;
