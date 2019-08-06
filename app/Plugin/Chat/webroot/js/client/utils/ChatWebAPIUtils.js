/* Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

import ChatFriendActionCreators from '../actions/ChatFriendActionCreators';
import ChatGroupActionCreators from '../actions/ChatGroupActionCreators';
import ChatRoomActionCreators from '../actions/ChatRoomActionCreators';
import ChatMessageActionCreators from '../actions/ChatMessageActionCreators';
import ChatUserActionCreators from '../actions/ChatUserActionCreators';
import ChatPoupActionCreators from '../actions/ChatPoupActionCreators';
import ChatCounterUnseenMesasgeActionCreators from '../actions/ChatCounterUnseenMesasgeActionCreators';
// Mobi
import ChatStateMobileActionCreators from '../actions/ChatStateMobileActionCreators';
// End Mobi
import ChatMooUtils from '../utils/ChatMooUtils';
import CHAT_CONSTANTS from '../constants/ChatConstants';
import * as SConnect from './socket/SConnect';
import * as SUser  from './socket/SUser';
import * as SFriend from './socket/SFriend';
import * as SRoom from './socket/SRoom';
import * as SMessages from './socket/SMessages';
import * as SVideo from './socket/SVideo';
var __ = require('../utils/ChatMooI18n').i18next;
var _mySocket = null;
var _makeSureEndcallOneTime = false;

var _createChatGroupWindowForUsersCallback = function(){};

function createChatWindowByUserCallback(data) {
    if(ChatMooUtils.isMobile()){
        _createChatGroupWindowForUsersCallback(data);
        _createChatGroupWindowForUsersCallback=function(data){};
    }
    ChatRoomActionCreators.createForAUserByUserCallback(data);

}
function disconnect(rooms) {
    console.log('Server disconnect');
    window.onfocus = function(){
        module.exports.boot();
        window.onfocus=function(){}
    }
}


var searchFriendBackground = {on:false,callback:function(){}};
function searchFriendCallback(data){
    if(!searchFriendBackground.on){
        ChatFriendActionCreators.searchFriendCallback(data);
    }else{
        ChatFriendActionCreators.addByKeyword(data);
        searchFriendBackground.callback();
        searchFriendBackground.on = false;

    }

}

module.exports =  {
    getSocket:function(){
        return _mySocket;
    },
    isEnableChat:function() {
        return ChatMooUtils.isEnableChat();
    },
    getChatToken:function() {
        return ChatMooUtils.getChatToken();
    },
    getChatStatus:function() {
        return ChatMooUtils.getChatStatus();
    },
    getServerUrl:function() {
        return ChatMooUtils.getServerUrl();
    },
    getSiteUrl:function() {
        return ChatMooUtils.getSiteUrl();
    },
    getAvatarLinkFromDataUser:function(data, prefix){
        return ChatMooUtils.getAvatarLinkFromDataUser(data, prefix);
    },
    getProfileLinkFromDataUser:function(data){
        return ChatMooUtils.getProfileLinkFromDataUser(data);
    },
    isHideOfflineUser:function() {
        return ChatMooUtils.isHideOfflineUser();
    },
    isOpennedChatboxWhenANewMesasgeArrives:function() {
        return ChatMooUtils.isOpennedChatboxWhenANewMesasgeArrives();
    },
    boot:function(isMoblie){
        isMoblie = typeof isMoblie !== 'undefined' ? isMoblie:false;
     
        ChatMooUtils.setIsMobile(isMoblie);
        if (this.isEnableChat()) {

            this.initSocket(function(){
                
                if(ChatMooUtils.isTurnOffForFirstTimeUsing()){
                    ChatMooUtils.turnOffChat();
                }else{
                        var roomsIsOpened = ChatMooUtils.getRoomIsOpen();
                        if (roomsIsOpened.isCreated.length > 0){
                            if(ChatMooUtils.getChatStatus() == CHAT_CONSTANTS.USER_OFFLINE){

                            }else{
                                module.exports.sendReqpuestForUpdatingUserByRoomIdsAtBooting(roomsIsOpened.isCreated);
                            }

                        }
                }

            });
            return true;
        }
        return false;
    },
    initSocket:function(callback) {
        ChatMooUtils.setThisOfChatWebAPIUtils(this);
        _mySocket = require('socket.io-client')(this.getServerUrl(), {
            query: "chat_token=" + this.getChatToken() + "&chat_status=" + this.getChatStatus(),
            reconnection: true,
            reconnectionDelay: 1000,
            reconnectionAttempts: 10
        });
        _mySocket.on("connect",callback);
        //_mySocket.on("connect_error", failedConnect);
        _mySocket.on("reconnect_failed", SConnect.failedConnect);
        _mySocket.on("disconnect", disconnect);
        _mySocket.on("userIsLogged", SUser.isLoggedCallback);
        _mySocket.on("getMyFriendsCallBack", SFriend.getMyFriendsCallBack);
        _mySocket.on("getMyFriendsHaveIdsCallBack", SFriend.getMyFriendsHaveIdsCallBack);
        _mySocket.on("getMyFriendsOnlineCallBack", SFriend.getMyFriendsOnlineCallBack);
        _mySocket.on("friendIsLogged", SFriend.friendIsLogged);
        _mySocket.on("friendIsLogout", SFriend.friendIsLogout);
        _mySocket.on("createChatWindowByUserCallback", createChatWindowByUserCallback);
        _mySocket.on("createChatWindowBySystemCallback", SRoom.createChatWindowBySystemCallback);
        _mySocket.on("getRoomMessagesCallback", SRoom.getRoomMessagesCallback);
        _mySocket.on("getRoomMessagesMoreCallback", SRoom.getRoomMessagesMoreCallback);
        _mySocket.on("markMessagesIsSeenInRoomsCallback", SRoom.markMessagesIsSeenInRoomsCallback);
        _mySocket.on("markReadAllMessagesCallback", SRoom.markReadAllMessagesCallback);
        _mySocket.on("newMessage", SMessages.newMessage);
        _mySocket.on("getUsersCallback", SUser.getUsersCallback);
        _mySocket.on("getUsersByRoomIdsAtBootingCallback", SUser.getUsersByRoomIdsAtBootingCallback);
        _mySocket.on("getRoomHasUnreadMessageCallback", SRoom.getRoomHasUnreadMessageCallback);
        _mySocket.on("setOnlineCallback", SRoom.setOnlineCallback);
        _mySocket.on("getMyGroupsCallBack", SRoom.getMyGroupsCallBack);
        _mySocket.on("deleteConversationCallback", SRoom.deleteConversationCallback);
        _mySocket.on("reportMessageSpamCallback", SRoom.reportMessageSpamCallback);
        _mySocket.on("leaveConversationCallback", SRoom.leaveConversationCallback);
        _mySocket.on("addUsersToARoomCallback", SRoom.addUsersToARoomCallback);
        _mySocket.on("blockMessagesCallback", SRoom.blockMessagesCallback);
        _mySocket.on("unblockMessagesCallback", SRoom.unblockMessagesCallback);
        _mySocket.on("refeshStatusChatWindowByRoomIdCallback", SRoom.refreshStatusChatWindowByRoomIdCallback);
        _mySocket.on("startTypingCallback", SRoom.startTypingCallback);
        _mySocket.on("stopTypingCallback", SRoom.stopTypingCallback);
        _mySocket.on("searchFriendCallback", searchFriendCallback);
        _mySocket.on("videoCallingCallback", SVideo.videoCallingCallback);
        _mySocket.on("getVideoCallingTokenCallback", SVideo.getVideoCallingTokenCallback);
        _mySocket.on("receiveSignal", SVideo.receiveSignal);
        _mySocket.on("ringingVideoCallCallback", SVideo.ringingVideoCallCallback);
        _mySocket.on("cancelVideoCallCallback", SVideo.cancelVideoCallCallback);
        _mySocket.on("endVideoCallingCallback", SVideo.endVideoCallingCallback);
        _mySocket.on("getVideoUserInfoCallback", SVideo.getVideoUserInfoCallback);
        _mySocket.on("closeVideoCallDialogCallback", SVideo.closeVideoCallDialogCallback);
        _mySocket.on("cancelVideoCallRTCSupportedCallback", SVideo.cancelVideoCallRTCSupportedCallback);
        _mySocket.on("callingPickupCallback", SVideo.callingPickupCallback);
        _mySocket.on("friendChangeOnlineStatusCallback", SFriend.friendChangeOnlineStatusCallback);
        _mySocket.on("iChangeOnlineStatusCallback", SUser.iChangeOnlineStatus);
        _mySocket.on("stunTurnServerCallback", SVideo.stunTurnServerCallback);
    },
    createChatWindowForAUser:function(uId) {
        ChatRoomActionCreators.createForAUser(uId);
    },
    createChatWindowByRoomId:function(rId) {
        ChatRoomActionCreators.createByRoomId(rId);
    },
    createChatGroupWindowForUsers:function(uIds,callback) {
        if(ChatMooUtils.isMobile()){
            if(typeof callback === undefined){
                _createChatGroupWindowForUsersCallback=function(data){};
            }else{
                _createChatGroupWindowForUsersCallback = callback;
            }
        }

        _mySocket.emit("createChatWindowByUser", {friendIds:uIds, isAllowedSendToNonFriend:ChatMooUtils.isAllowedSendToNonFriend()});
    },
    addUsersToARoom:function(friendIds, roomId) {
        _mySocket.emit("addUsersToARoom", friendIds, roomId);
    },
    sendRequestCreateChatWindowByUser:function(uId) {
        _mySocket.emit("createChatWindowByUser", {friendIds:uId,isAllowedSendToNonFriend:ChatMooUtils.isAllowedSendToNonFriend()});
    },
    sendRequestRefeshStatusARoomByRoomId:function(rId){
        _mySocket.emit("refeshStatusChatWindowByRoomId", rId);
    },
    sendRequestGetRoomMessages: function(roomId,firstIdNewMessage){
        firstIdNewMessage = typeof firstIdNewMessage !== 'undefined' ? firstIdNewMessage : 0; firstIdNewMessage=0;
        _mySocket.emit("getRoomMessages", {roomId:roomId,limit:ChatMooUtils.getFirstTimeMessagesLimit(),firstIdNewMessage:firstIdNewMessage});
    },
    sendRequestGetRoomMessagesMore: function(rId,mIdStart,limit){

        _mySocket.emit("getRoomMessagesMore", {rId:rId,mIdStart:mIdStart,limit:limit});
    },
    sendRequestTextMessage: function(text, roomId, type){
        //text = ChatMooUtils.convertEmoji(text);
        _mySocket.emit("sendTextMessage", {
            text: text,
            roomId: roomId,
            type: type,
            timestamps: ChatMooUtils.unixTime()
        });
    },
    sendRequestSaveRoomStatus:function(rooms){
        _mySocket.emit("saveRoomStatus", {rooms:rooms});
    },
    sendRequestCreateChatWindowByRoomId: function(roomId,isMinimized,isFocused,isSaveRoomStatus){
        var minimized = (typeof isMinimized != 'undefined')? isMinimized:CHAT_CONSTANTS.WINDOW_MAXIMIZE ;
        var focus =  (typeof isFocused != 'undefined')?isFocused:CHAT_CONSTANTS.IS_FOCUSED_CHAT_WINDOW;
        var isSaveRoomStatus =  (typeof isSaveRoomStatus != 'undefined')?isSaveRoomStatus:false;
        if(ChatMooUtils.isMobile()){
            isMinimized = CHAT_CONSTANTS.WINDOW_MINIMIZE;
        }
        _mySocket.emit("createChatWindowByRoomId", {roomId:roomId,minimized:minimized,isFocused:focus,isSaveRoomStatus:isSaveRoomStatus});
    },
    sendRequestMarkMessagesIsSeenInRooms: function(messageIdsUnSeen,roomIsSeen){
        _mySocket.emit("markMessagesIsSeenInRooms", {messageIdsUnSeen:messageIdsUnSeen,roomIsSeen:roomIsSeen});
    },
    sendRequestForGetRoomHasUnreadMessage: function(){
        if(ChatMooUtils.getChatStatus() != CHAT_CONSTANTS.USER_OFFLINE){
            _mySocket.emit("getRoomHasUnreadMessage");
        }

    },
    sendRequestForUpdatingUsers: function(users){
        _mySocket.emit("getUsers", users);
    },
    sendReqpuestForUpdatingUserByRoomIdsAtBooting:function(rIds){
        _mySocket.emit("getUsersByRoomIdsAtBooting", rIds);
    },
    sendRequestGetMyFriends: function (ids) {
        ids = typeof ids !== 'undefined' ? ids:[];
        _mySocket.emit("getMyFriends",ids);
    },
    sendRequestTurnOffChat: function () {
        ChatMooUtils.setChatStatus(CHAT_CONSTANTS.USER_OFFLINE);
        _mySocket.emit("setOffline");
    },
    sendRequestTurnOnChat: function () {
        ChatMooUtils.setChatStatus(CHAT_CONSTANTS.USER_ONLINE);
        _mySocket.emit("setOnline");
    },
    sendRequestGetMyGroups: function () {
        _mySocket.emit("getMyGroups");
    },
    sendRequestDeleteConversation: function (rId) {
        ChatMessageActionCreators.deleteAllMesages(rId);
        _mySocket.emit("deleteConversation", rId);
    },
    sendRequestReportMesasgeSpam: function (data) {
        _mySocket.emit("reportMessageSpam", data);
    },
    sendRequestLeaveConversation: function (rId) {
        _mySocket.emit("leaveConversation", rId);
    },
    sendRequestBlockMessages:function(rId){
        _mySocket.emit("blockMessages", rId);
    },
    sendRequestUnblockMessages:function(rId){
        _mySocket.emit("unblockMessages", rId);
    },
    sendRequestStartTyping:function(rId){
        _mySocket.emit("startTyping", rId);
    },
    sendRequestStopTyping:function(rId){
        _mySocket.emit("stopTyping", rId);
    },
    sendRequestSearchName:function(name,callback){
        if(typeof callback !== 'undefined'){
            searchFriendBackground.on = true;
            searchFriendBackground.callback = callback;
        }
        _mySocket.emit("searchFriend", name);
    },
    sendRequestMissVideoCall: function(roomId, caller_id){
        var message = {
            action: CHAT_CONSTANTS.SYSTEM_MESSAGE_ACTION.MISS_VIDEO_CALL,
            caller_id: caller_id
        };
        message = JSON.stringify(message);
        this.sendRequestTextMessage(message, roomId, CHAT_CONSTANTS.CHAT_MESSAGE_TYPE.TYPE_SYSTEM);
    },
    /* Room Behavior */
    refeshARoom: function(roomId){
        ChatRoomActionCreators.refeshARoom(roomId);
    },
    activeARoom: function(roomId){
        ChatRoomActionCreators.activeARoom(roomId);
    },
    destroyARoom: function(roomId){
        ChatRoomActionCreators.destroyARoom(roomId);
    },
    destoryAllRoom: function(){
        ChatRoomActionCreators.destroyAllRoom();
    },
    minimizeARoom: function(roomId){
        ChatRoomActionCreators.minimizeARoom(roomId, true);
    },
    maximizeARoom: function(roomId, isMinimized){
        ChatRoomActionCreators.minimizeARoom(roomId, false);
    },
    caculateNewMessagesForAllRoom: function(){
        ChatRoomActionCreators.caculateNewMessages();
    },
    reRenderAllRooms: function(){
        ChatRoomActionCreators.reRenderAllRooms();
    },
    markMessagesIsLoadedForARoom:function(roomId){
       ChatRoomActionCreators.markMessagesIsLoaded(roomId);
    },
    markMessagesInARoomIsSeen:function(roomId){
        ChatCounterUnseenMesasgeActionCreators.setMessagesIsSeen(roomId);
    },
    markReadAllMessages:function(uId){
        _mySocket.emit("markReadAllMessages", uId);
    },
    /* End Room Behavior */
    findAFriendByName: function(name){
        ChatFriendActionCreators.filter(name);
    },
    /* POPUP behavior*/
    openReportModal: function(rId){
        ChatPoupActionCreators.openReportModal(rId);
    },
    closeReportModal: function(){
        ChatPoupActionCreators.closeReportModal();
    },
    openAlertModal: function(title,body,close_button){
        ChatPoupActionCreators.openAlertModal(title,body,close_button);
    },
    closeAlertModal: function(){
        ChatPoupActionCreators.closeAlertModal();
    },
    openRTCSupportedAlertModal: function(tconfig){
        ChatPoupActionCreators.openRTCSupportedAlertModal(tconfig);
    },
    closeRTCSupportedAlertModal: function(){
        ChatPoupActionCreators.closeRTCSupportedAlertModal();
    },
    /**
     * Open a dialog supports yes/no callback function
     * @param {object} config - it includes propeties below
     *      title : dialog's title
     *      body  : dialogs's message
     *      noButton: label of no button
     *      yesButton : label of yes button
     *      callback  : the callback function when yes button is clicked
     *      callbackNo: the callback function when no button is clicked
     */
    openAlertYesNoModal: function(config){
        // title,body,noButton,yesButton,callback
        ChatPoupActionCreators.openAlertYesNoModal(config);
    },
    closeAlertYesNoModal:function(){
        ChatPoupActionCreators.closeAlertYesNoModal();
    },
    /* END POPUP behavior*/
    /* Mobi */
    showMobiIconStatus:function(){
        ChatStateMobileActionCreators.showIconStatus();
    },
    showMobiFriendsWindow:function(){
        ChatStateMobileActionCreators.showFriendsWindow();
    },
    showMobiChatWindow:function(){
        ChatStateMobileActionCreators.showChatWindow();
    },
    updateConversationCounter:function(n){
        ChatMooUtils.updateConversationCounter(n);
    },
    /* End Mobi */
    /* Core conversations integration */
    markReadOnMessagesPage:function(e){
        ChatMooUtils.markReadOnMessagesPage(e);
    },
    /* End core conversations integration */

    /* Video Calling */
    /**
     * Send request to register a video calling token used for creating peer-to-peer connect
     * @param obj {object} {rId:roomId,members:array}
     * @param wId : Id of object window is opened
     */
    sendRequestGetVideoCallingToken:function(obj){
        _mySocket.emit("getVideoCallingToken", obj);
    },
    /**
     * It used for getVideoCallingTokenCallback
     * @param videoToken
     */
    sendRequestVideoCalling:function(videoToken){
        _mySocket.emit("videoCalling", videoToken);
    },
    /**
     * End a video call
     */
    sendRequestEndVideoCalling:function(rId){
    	if (!_makeSureEndcallOneTime)
    	{
    		_makeSureEndcallOneTime = true;
    		_mySocket.emit("endVideoCalling", rId);
    	}
    },
    /**
     * make a calling
     * @param  {object} room - It must include {id,members}
     */
    openAWindowForCalling(room){
        SVideo.openAWindowForCalling(room);
    },
    /**
     * This function will create a new peer base on 'simple-peer' then send the SIGN to another
     * to make a connection between 2 browsers
     * @param obj {rId:roomId,members:array}
     */
    createVideoPeer(token){
        SVideo.createVideoPeer(token);
    },
    /**
     * This function is used to send the signal data
     * @param obj {object} {
     *                  receiverId: socket id of caller,
     *                  signal: data of signal for creating connect peer-to-peer
     *            }
     */
    sendSignal(obj){
        _mySocket.emit("sendSignal", obj);
    },
    /**
     * Get media stream (video and audio) for streaming between two browsers using peer-to-peer connect type
     */
    getMediaStream(obj){
        SVideo.getMediaStream(obj);
    },
    saveVideoCallSetting(obj){
        SVideo.saveVideoCallSetting(obj);
    },
    updateCameraStream(stream){
        SVideo.updateCameraStream(stream);
    },
    /**
     *      This function is callback for getMediaStream to prevent
     *  the  Uncaught Error: Dispatch.dispatch(...): Cannot dispatch in the middle of a dispatch.
     *  when try to dispatch set_media_stream in get_media_stream handle
     * @param stream this is stream object return from
     */
    setMediaStream(stream){
        SVideo.setMediaStream(stream);
    },
    /**
     *  Set media stream for remote  camera in connecting peer-to-peer between 2 browsers
     * @param stream
     */
    setMediaRemoteStream(stream){
        SVideo.setMediaRemoteStream(stream);
    },
    /**
     * Turn on/off camera for streaming
     * @param is - boolean true/false = on/off camera
     */
    setMyCameraStreamEnable(is){
        SVideo.setMyCameraStreamEnable(is);
    },
    /**
     * Turn on/off mic for streaming
     * * @param is - boolean true/false = on/off mic
     */
    toogleMyMicStream(){
        SVideo.toogleMyMicStream();
    },
    setMyMicStreamEnable(is){
        SVideo.setMyMicStreamEnable(is);
    },
    /* End Video Calling */

    ringingVideoCall:function(obj){
        _mySocket.emit("ringingVideoCall", obj);
    },
    
    cancelVideoCall(obj){
        _mySocket.emit("cancelVideoCall", obj);
    },

    callingPickup:function(){
        _mySocket.emit("callingPickup");
    },
    getUserInfo(uId){
        _mySocket.emit("getVideoUserInfo", uId);
    },
    
    closeVideoCallDialog(obj){
        _mySocket.emit("closeVideoCallDialog", obj);
    },
    
    cancelVideoCallRTCSupported(obj){
        _mySocket.emit("cancelVideoCallRTCSupported", obj);
    },
    
    openVideoCallSettingModal(){
        ChatPoupActionCreators.openVideoCallSettingModal();
    },
    
    closeVideoCallSettingModal(){
        ChatPoupActionCreators.closeVideoCallSettingModal();
    },
    
    changeOnlineStatus(status){
        _mySocket.emit("changeUserOnlineStatus", status);
    },
    stunTurnServer(token){
        _mySocket.emit("stunTurnServer", token);
    }
};

export default module.exports;
