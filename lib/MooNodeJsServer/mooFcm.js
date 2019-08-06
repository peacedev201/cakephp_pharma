var NodeCache = require( "node-cache" );
var request = require('request');
var mooDB = require("./mooDB");
var cache = require("./mooCache");
var chatSocket = require('./mooSocket');
var _io;
var fcmTokenList = new NodeCache();

var mooFcm = (function () {
    var setIO = function (io) {
        _io = io;
    };
    
    var cacheTokenList = function(user_id, token){
        if(typeof fcmTokenList.get(user_id) != "undefined"){
            var arr = fcmTokenList.get(user_id);
            if(arr.indexOf(token) == -1){
                arr.push(token);
                fcmTokenList.set(user_id, arr);
            }
        }
        else{
            fcmTokenList.set(user_id, [token]);
        }
    };
    
    var clearCacheTokenList = function(user_id, token){
        if(typeof fcmTokenList.get(user_id) == "undefined"){
            return
        }
        var arr = fcmTokenList.get(user_id);
        var index = arr.indexOf(token);
        if (index > -1) {
            arr.splice(index, 1);
        }
        fcmTokenList.set(user_id, arr);
    };
    
    var clearAllCacheTokenList = function(){
        fcmTokenList = [];
    };
    
    var send = function (sender_id, user_id, room_id, message) {
        if(sender_id == user_id){
            return;
        }
        if(chatSocket.isUserOnline(user_id)){
            return;
        }
        if(typeof fcmTokenList.get(user_id) == "undefined" || fcmTokenList.get(user_id).length == 0){
            return;
        }
        var config = cache.myCache.get( "getConfig", true );
        if(config.chat_fcm_server_api_key.trim() == ""){
            return;
        }
        var registration_ids = fcmTokenList.get(user_id);
        var serverKey = config.chat_fcm_server_api_key.trim(); //put your server key here
        //var fcm = new FcmNode(serverKey);

        mooDB.query(mooDB.mysql.format(mooDB.sqlString.getUserInfo, [sender_id]), function (err, rows) {
            if (err) {
                log.error("fcm getUserInfo err", err);
            } else {
                if (rows.length > 0) {
                    var sender = rows[0];
                    var fcm_data = { //this may vary according to the message type (single recipient, multicast, topic, et cetera)
                        registration_ids: registration_ids, 
                        /*notification: {
                            title: user_id, 
                            body: message,
                            icon: "https://cdn4.iconfinder.com/data/icons/new-google-logo-2015/400/new-google-favicon-512.png"
                        },*/
                        data: {
                            title: sender.name, 
                            body: message,
                            sender_id: sender_id,
                            room_id: room_id,
                            avatar: sender.avatar,
                            gender: sender.gender
                        }
                    };

                    var req = {
                        uri: 'https://fcm.googleapis.com/fcm/send',
                        method: 'POST',
                        headers: {
                            'Authorization': 'key= ' + serverKey,
                            'Content-Type': 'application/json'
                        },
                        body:JSON.stringify(fcm_data)
                    };
                    request.post(req, function callback(err, httpResponse, body) {
                        if (err) {
                            console.error('fcm failed:', err);
                        }
                        else{
                            //console.log('successful!  Server responded with:', body);
                        }
                    });
                }
            }
        });
    };
    
    var saveToken = function (user_id, token, client_type) {
        mooDB.query(mooDB.mysql.format(mooDB.sqlString.getFcmByToken, [token]), function (err, rows) {
            if (err) {
                _io.emit('error',err);
            } else {
                if (rows.length == 0) {
                    var record = {
                        user_id: user_id,
                        token: token,
                        client_type: client_type
                    };
                    mooDB.query(mooDB.mysql.format(mooDB.sqlString.saveFcmToken, record), function (err, result) {
                        if (err) {
                            log.error("_querySaveFcmToken err", err);
                        } else {

                        }
                    });
                }
            }
        });
    };
    
    var removeToken = function (user_id, token) {
        mooDB.query(mooDB.mysql.format(mooDB.sqlString.removeFcmToken, [user_id, token]), function (err, rows) {
            if (err) {
                log.error("_queryRemoveToken err", err);
            } else {
                
            }
        });
    };
    
    return {
        setIO: setIO,
        send: send,
        saveToken: saveToken,
        removeToken: removeToken,
        cacheTokenList: cacheTokenList,
        clearCacheTokenList: clearCacheTokenList,
        clearAllCacheTokenList: clearAllCacheTokenList
    };
}());
module.exports = mooFcm;