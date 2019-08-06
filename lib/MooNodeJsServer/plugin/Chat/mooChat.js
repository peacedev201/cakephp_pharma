//var mooEmitter = require('../../../../../../lib/MooNodeJsServer/mooEmitter.js');
var mooEmitter = require('../../mooEmitter');
var message  = require("./Message/chatMessage");
var video    = require("./chatVideo");
var chatInfo = require('./chatInfo');
var chatConfig = require('./chatConfig');
var file = require('./chatFile')
var mooUser = require('../../mooUser')
var mooChat = (function () {

    var init = function(){
        mooEmitter.on('mooConfigSuccess',function(host, login, password, database, prefix, sourceSql, mysqlPort,io,salt,app){
            require("./chatDB").config(host, login, password, database, prefix, sourceSql, mysqlPort);
            message.init(io);
            video.init(io,salt);
            file.init(app);
        });

        mooEmitter.on('io_connection',function(io,socket){
            // Chat config
            socket.on('getChatConfig',function(){
                chatConfig.get(this);
            });
            socket.on('refeshChatConfigOnly',function(){
                chatConfig.refeshConfig(this);
            });
            socket.on('refeshChatRoleOnly',function(){
                chatConfig.refeshRoles(this);
            });
            socket.on('refeshChatUserSettingOnly',function(){
                chatConfig.refeshUserSettings(this);
            });
            socket.on('refeshChatConfigAll',function(){
                chatConfig.refeshAll(this);
            });
            // Server info
            socket.on('getServerInfo',function(){
                chatInfo.getServerInfo(this);
            });
            socket.on('getMonitorMessages',function(limit){
                chatInfo.getMonitorMessages(this,limit);
            });
            socket.on('getRooms',function(ids){
                chatInfo.getRooms(this,ids);
            });
            // End server info
            // Message init
            socket.on("saveRoomStatus", function (data) {
                message.saveRoomStatus(this,data.rooms);
            });
            socket.on("createChatWindowByUser", function (data) {
                message.createRoom(this,data.friendIds,data.isAllowedSendToNonFriend);
            });
            socket.on("createChatWindowByRoomId", function (data) {
                message.createChatWindowByRoomId(this,data);
            });
            socket.on("refeshStatusChatWindowByRoomId", function (rId) {
                message.refeshStatusARoom(this,rId);
            });
            socket.on("sendTextMessage", function (data) {
                message.setTextMessage(this,data);
            });
            socket.on("getRoomMessages", function (data) {
                message.getRoomMessages(this,data.roomId,data.limit,data.firstIdNewMessage);
            });
            socket.on("getRoomMessagesMore", function (data) {
                message.getRoomMessagesMore(this,data.rId,data.mIdStart,data.limit);
            });
            socket.on("markMessagesIsSeenInRooms", function (data) {
                message.markMessagesIsSeenInRooms(this,data.messageIdsUnSeen,data.roomIsSeen);
            });
	    socket.on("markReadAllMessages", function (uId) {
                message.markReadAllMessages(this,uId);
            });
            socket.on("getRoomHasUnreadMessage", function () {
                message.getRoomHasUnreadMessage(this);
            });
            socket.on("deleteConversation", function (rId) {
                message.deleteConversation(this,rId);
            });
            socket.on("reportMessageSpam", function (data) {
                message.reportMessageSpam(this,data);
            });
            socket.on("leaveConversation", function (rId) {
                message.leaveConversation(this,rId);
            });
            socket.on("addUsersToARoom", function (friendIds, roomId) {
                message.addUsersToARoom(this,friendIds, roomId);
            });
            socket.on("blockMessages", function (rId) {
                message.blockMessages(this, rId);
            });
            socket.on("unblockMessages", function (rId) {
                message.unblockMessages(this,rId);
            });
            socket.on("getLatestMessages", function (page) {
                if(typeof page === 'undefined'){page = 0}
                message.getLatestMessages(this,page);
            });
            socket.on("getUserMe", function () {
                mooUser.getUserMe(this);
            });
            socket.on("getBlockedUserList", function () {
                message.getBlockedUserList(this);
            });
            socket.on("searchMessage", function (text) {
                message.searchMessage(this,text);
            });
            socket.on("searchMessageByRoom", function (room_id,text,page) {
                message.searchMessageByRoom(this,room_id,text,page);
            });
            socket.on("getMessageByRoomBetweenId", function (room_id,message_id) {
                message.getMessageByRoomBetweenId(this,room_id,message_id);
            });
            // End message init
            // Video call init
            socket.on("videoCalling", function (token) {
                video.calling(this,token);
            });
            socket.on("endVideoCalling",function(rId){
                video.endVideoCalling(this, rId)
            });
            socket.on("getVideoCallingToken",function(obj){
                video.token(this,obj);
            });
            socket.on("sendSignal",function(obj){
                video.sendSignal(this,obj)
            });
            socket.on("ringingVideoCall", function (obj) {
                video.ringingVideoCall(this, obj);
            });
            socket.on("cancelVideoCall", function (obj) {
                video.cancelVideoCall(this,obj);
            });
            socket.on("getVideoUserInfo", function (uId) {
                video.getVideoUserInfo(this,uId);
            });
            socket.on("closeVideoCallDialog", function (obj) {
                video.closeVideoCallDialog(this, obj);
            });
            socket.on("cancelVideoCallRTCSupported", function (obj) {
                video.cancelVideoCallRTCSupported(this,obj);
            });
            socket.on("callingPickup", function () {
                video.callingPickup(this);
            });
            // End video call init
        });
    };


    return {
        init:init
    };
}());
module.exports =  mooChat ;