var chatDB = require("./chatDB");

var mooSocket = require('../../mooSocket');

var log = require("../../mooLog");
var cache = require("../../mooCache");
var _io;
var _salt = "moo";
var _ = require('lodash');

var jwt = require('jsonwebtoken');
var mooDB = require("../../mooDB");

var ChatVideo = (function () {

    var init = function (io,salt) {
        _io = io;
        _salt = salt;
    };
    var calling = function (socket,token) {
        var decoded = jwt.verify(token, _salt);
        var roomId  = _.get(decoded,"rId",0);
        var members = _.get(decoded,"members",[]);
		var videoCallEnabled = _.get(decoded,"videoCallEnabled",true);
        socket.videoCalling = {rId:roomId,members:members};
        if( members.length > 0){
            _.forEach(members, function(uId) {
                _io.to('mooUser.' + uId).emit('videoCallingCallback',{
                    rId:parseInt(roomId),
                    members:members,
                    senderId:socket.userId,
                    senderSocketId:socket.id,
					videoCallEnabled: videoCallEnabled
                });
            });
        }

    };
    var token = function(socket,obj){
        var token = jwt.sign(obj, _salt);
        socket.emit("getVideoCallingTokenCallback", token);
    };
    /**
     *
     * @param socket
     * @param obj {
     *                  receiverId: socket id of caller,
     *                  signal: data of signal for creating connect peer-to-peer
     *            }
     */
    var sendSignal = function (socket,obj) {
        //console.log("sendSignal",obj);
        if(_.has(obj,'receiverSocketId')){
            _.assignIn(obj,{senderSocketId:socket.id});
            socket.to(_.get(obj,'receiverSocketId')).emit("receiveSignal",obj);
        }
    }
	
    var ringingVideoCall = function (socket, obj) {
        _io.to('mooUser.' + obj.senderId).emit('ringingVideoCallCallback', obj);
    }
    
    var cancelVideoCall = function (socket,obj) {
        _io.to('mooUser.' + obj.senderId).emit('cancelVideoCallCallback', obj);
    }
	
    var endVideoCalling = function(socket, rId){
        var roomsId =  _.get(socket,"roomsId");
        if(typeof roomsId == "undefined"){
            return;
        }
        var members =  _.get(roomsId, rId);
        if(typeof members == "undefined" || members.length == 0){
            return;
        }

        _.forEach(members, function(uId) {
            _io.to('mooUser.' + uId).emit('endVideoCallingCallback',{
                rId:rId,
                senderId:socket.userId,
                senderSocketId:socket.id
            });
        });
    }
    
    var getVideoUserInfo = function(socket, uId){
        var query = mooDB.sqlString.getVideoUserInfo.replace("?", uId);
        mooDB.query(mooDB.mysql.format(query), function (err, rows) {
            if (err) {
                log.error("getVideoUserInfo error", err);
            } else {
                var user = rows[0];
                var roles = user['roles'].split(",");
                user['allow_video_call'] = false;
                if(roles && roles.indexOf('chat_allow_chat') >= 0 && roles.indexOf('chat_allow_video_calling') >= 0){
                    user['allow_video_call'] = true;
                }
                socket.emit("getVideoUserInfoCallback", user);

            }
        });
    }
    
    var closeVideoCallDialog = function(socket, obj){
        _io.to('mooUser.' + obj.senderId).emit('closeVideoCallDialogCallback', obj);
    }
    
    var cancelVideoCallRTCSupported = function(socket, obj){
        _io.to('mooUser.' + obj.senderId).emit('cancelVideoCallRTCSupportedCallback', obj);
    }

    var callingPickup = function(socket){
        //close all incoming calling popups for all windows if user answers
        _io.to('mooUser.' + socket.userId).emit('callingPickupCallback',{});
    }
    return {
        init: init,
        calling:calling,
        token:token,
        sendSignal:sendSignal,
        ringingVideoCall:ringingVideoCall,
        cancelVideoCall:cancelVideoCall,
        endVideoCalling:endVideoCalling,
        getVideoUserInfo: getVideoUserInfo,
        closeVideoCallDialog: closeVideoCallDialog,
        cancelVideoCallRTCSupported: cancelVideoCallRTCSupported,
        callingPickup:callingPickup
    };
}());


module.exports = ChatVideo;