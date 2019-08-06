var mooSocket = require('./mooSocket');
var mooDB = require("./mooDB");
var request = require('request');
var _io;
var chatNotification = (function () {
    var setIO = function (io) {
        _io = io;
    };
    var imOnline = function (userId) {
        _io.emit("serverInfoRefeshCallback");
        mooDB.query(mooDB.mysql.format(mooDB.sqlString.getMyFriends, [userId]), function (err, rows) {
            if (err) {
                _io.emit('error',err);
            } else {

                if (rows.length != 0) {
                    for (var i = 0; i < rows.length; i++) {

                        if (mooSocket.isUserOnline(rows[i].id)) {
                            //socket.broadcast.to('mooUser.'+rows[i].id).emit('friendIsLogged', socket.userId);
                            _io.to('mooUser.' + rows[i].id).emit('friendIsLogged', userId);
                        }

                    }
                }
            }
        });
    };
    var imOffline = function (userId) {
        _io.emit("serverInfoRefeshCallback");
        mooDB.query(mooDB.mysql.format(mooDB.sqlString.getMyFriends, [userId]), function (err, rows) {
            if (err) {
                _io.emit('error',err);
            } else {

                if (rows.length != 0) {
                    for (var i = 0; i < rows.length; i++) {
                        if (mooSocket.isUserOnline(rows[i].id)) {
                            //console.log("friendIsLogout ",new Date().toISOString().slice(0, 19).replace('T', ' '));
                            _io.to('mooUser.' + rows[i].id).emit('friendIsLogout', userId);
                        }

                    }
                }
            }
        });
    };
    var imLogged = function (socket) {
        mooDB.query(mooDB.mysql.format(mooDB.sqlString.getUserInfo, [socket.userId]), function (err, rows) {
            if (err) {
                _io.emit('error',err);
            } else {
                socket.emit("userIsLogged", socket.userId, rows[0].chat_online_status);
            }
        });
    };
    
    var stunTurnServer = function(socket, token){
        //stun turn server
        mooDB.query(mooDB.mysql.format(mooDB.sqlString.getStunTurnServer), function (err, rows) {
            if (err) {
                _io.emit('error',err);
            } else {
                var data = rows[0].value_actual.replace(/'/g, '"').trim();
                if(data != ""){
                    data = JSON.parse(data);
                    if(typeof data.api != "undefined" && typeof data.api.url != "undefined" && typeof data.api.sid != "undefined" && typeof data.api.token != "undefined" && data.api.url.indexOf("twilio.com") !== -1){
                        parseIceServersTwilio(socket, data, token);
                    }
                    if(typeof data.api != "undefined" && typeof data.api.url != "undefined" && typeof data.api.sid != "undefined" && typeof data.api.token != "undefined" && data.api.url.indexOf("xirsys.net") !== -1){
                        parseIceServersXirsys(socket, data, token);
                    }
                    else if(typeof data.iceServers != "undefined"){
                        stunTurnServerCallback(socket.userId, JSON.stringify(data.iceServers), token);
                    }
                }
                else{
                    stunTurnServerCallback(socket.userId, "", token);
                }
            }
        });
    }
    
    function parseIceServersTwilio(socket, data, token){
        var req = {
            uri: data.api.url,
            headers: {
                'Authorization': "Basic " + new Buffer(data.api.sid + ":" + data.api.token).toString("base64"),
                'Content-Type': 'application/json'
            }
        };
        request.post(req, function callback(err, httpResponse, body) {
            if (err) {
                console.error('Twilio failed:', err);
                stunTurnServerCallback(socket.userId, "", token);
            }
            else{
                try {
                    var result = JSON.parse(body);
                    if(typeof result.ice_servers != "undefined"){
                        stunTurnServerCallback(socket.userId, JSON.stringify(result.ice_servers), token);
                    }
                    else{
                        console.error(result.detail);
                        stunTurnServerCallback(socket.userId, "", token);
                    }
                }
                catch (err) {
                    console.error("Twilio parse json failed");
                    stunTurnServerCallback(socket.userId, "", token);
                }
            }
        });
    }
    
    function parseIceServersXirsys(socket, data, token){
        var req = {
            uri: data.api.url,
            headers: {
                'Authorization': "Basic " + new Buffer(data.api.sid + ":" + data.api.token).toString("base64"),
                'Content-Type': 'application/json'
            }
        };
        request.put(req, function callback(err, httpResponse, body) {
            if (err) {
                console.error('Xirsys failed:', err);
                stunTurnServerCallback(socket.userId, "", token);
            }
            else{
                try {
                    var result = JSON.parse(body);
                    if(result.s == "ok"){
                        stunTurnServerCallback(socket.userId, JSON.stringify(result.v.iceServers), token);
                    }
                    else{
                        stunTurnServerCallback(socket.userId, "", token);
                    }
                }
                catch (err) {
                    console.error(err);
                    stunTurnServerCallback(socket.userId, "", token);
                }
            }
        });
    }
    
    function stunTurnServerCallback(userId, data, token){
        _io.to('mooUser.' + userId).emit('stunTurnServerCallback', data, token);
    }
    
    var iChangeOnlineStatus = function (userId) {
        mooDB.query(mooDB.mysql.format(mooDB.sqlString.getUserInfo, [userId]), function (err, rows) {
            if (err) {
                _io.emit('error',err);
            } else {
                var user = rows[0];
                _io.to('mooUser.' + user.id).emit('iChangeOnlineStatusCallback', user.chat_online_status);
                mooDB.query(mooDB.mysql.format(mooDB.sqlString.getMyFriends, [userId]), function (err, rows) {
                    if (err) {
                        _io.emit('error',err);
                    } else {

                        if (rows.length != 0) {
                            for (var i = 0; i < rows.length; i++) {
                                if (mooSocket.isUserOnline(rows[i].id)) {
                                    _io.to('mooUser.' + rows[i].id).emit('friendChangeOnlineStatusCallback', user);
                                }

                            }
                        }
                    }
                });
            }
        });
    }
    
    return {
        setIO: setIO,
        imOnline: imOnline,
        imOffline: imOffline,
        imLogged: imLogged,
        iChangeOnlineStatus: iChangeOnlineStatus,
        stunTurnServer: stunTurnServer
    };
}());
module.exports = chatNotification;