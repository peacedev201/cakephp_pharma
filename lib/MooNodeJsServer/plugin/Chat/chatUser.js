var chatDB = require("./chatDB.js");
var mooAuth = require("./chatAuth.js");
var chatConstants = require("./chatConstants.js");
var chatSocket = require('./chatSocket.js');
var chatNotification = require('./chatNotification.js');
var log = require("../../mooLog");
var _userStatus ={};
var _userHideMyOnlineStatus ={};
var io;
var _ = require('lodash');




var ChatUser = (function () {

    var init = function (_io, socket) {
        io = _io;
    };
    var setStatus = function(id,status){
        if(_userStatus.hasOwnProperty(id)){
            _userStatus[id].status = status;
        }else{
            _userStatus[id] = {status:status};
        }
    };
    var getStatus = function(id){
        if(!_userStatus.hasOwnProperty(id)){
            return chatConstants.USER_ONLINE;
        }
        return _userStatus[id].status ;
    };
    var setId = function (id) {
        userId = id;
    };
    var isOffline=function(id){
      
        if(!_userStatus.hasOwnProperty(id)){
            return true;
        }
        return _userStatus[id].status == chatConstants.USER_OFFLINE;
    };
    var setHideMyOnlineStatus = function(id,status){
        if(_userHideMyOnlineStatus.hasOwnProperty(id)){
            _userHideMyOnlineStatus[id] = status;
        }else{
            _userHideMyOnlineStatus[id] = status;
        }
    };
    var isHideMyOnlineStatus=function(id){
        if(!_userHideMyOnlineStatus.hasOwnProperty(id)){
            return false;
        }
        return _userHideMyOnlineStatus[id] == chatConstants.HIDE_MY_ONINE_STATUS;
    };
    var updateHideMyOnlineStatus=function(id,isNotificationOffline){
        chatDB.query(chatDB.mysql.format(chatDB.sqlString.getUserSetting, [id]), function (err, result) {
            if (err) {
                log.error("getUserSetting error", err);
            } else {
                if(result.length > 0 ){
                    var _that = module.exports;
                    _that.setHideMyOnlineStatus(id,result[0].hide_online);
                    if(isNotificationOffline && result[0].hide_online == chatConstants.HIDE_MY_ONINE_STATUS){
                        chatNotification.imOffline(id);
                        // Hacking for display all users
                        //_that.imOffline(id);
                        // End hacking
                    }
                    if(isNotificationOffline && result[0].hide_online == chatConstants.NOT_HIDE_MY_ONINE_STATUS){
                        chatNotification.imOnline(id);
                        // Hacking for display all users
                        //_that.imOnline(id);
                        // End hacking
                    }
                }
            }
        },true,id
        );
    };
    var countUsersOnline=function(){
        var count = 0;
        var _that = module.exports;
        for(var i in _userStatus){
            if (chatSocket.isUserOnline(i) && !_that.isOffline(i)) {
                count++;
            }
        }
        return count;
    };
    var getMyFriendsOnline = function (socket) {
        var _that = module.exports;
        var myFriendsIdOnline = [];

        if (socket.myFriendsId.length) {
            for (var i = 0; i < socket.myFriendsId.length; i++) {
                if (chatSocket.isUserOnline(socket.myFriendsId[i]) && !_that.isOffline(socket.myFriendsId[i]) && !_.includes(socket.myBlockersId,socket.myFriendsId[i]) && !_that.isHideMyOnlineStatus(socket.myFriendsId[i])) {
                    myFriendsIdOnline.push(socket.myFriendsId[i]);
                }

            }

        }
        // Hacking for display all users
        /*
        myFriendsIdOnline = [];
        for(var i in _userStatus){
            var id = parseInt(i); // Must convert from string to int if u dont want to get loop forever
            if ( socket.userId != id && chatSocket.isUserOnline(id) && !_that.isOffline(id)) {

                myFriendsIdOnline.push(id);
            }
        }*/
        // End hacking
        socket.emit("getMyFriendsOnlineCallBack", myFriendsIdOnline);
    };
    var getMyFriends = function (socket,ids) {
        //if(mooAuth.isLogged(socket,function(){})){
        ids = typeof ids !== 'undefined' ? ids:[];
        if(ids.length == 0){
            chatDB.query(chatDB.mysql.format(chatDB.sqlString.getMyFriends, [socket.userId]), function (err, rows) {
                if (err) {
                    log.error("getMyFriends", err);
                } else {

                    var myFriends = [];
                    if (rows.length != 0) {
                        myFriends = rows;
                        for (var i = 0; i < rows.length; i++) {
                            socket.myFriendsId.push(rows[i].id);
                        }
                    }
                    socket.emit("getMyFriendsCallBack", myFriends);
                }
            });
        }else{

            var query = chatDB.sqlString.getMyFriendsHaveIds.replace("%IN%", "'" + ids.join("','") + "'");
            chatDB.query(query, function (err, rows) {
                if (err) {
                    log.error("markMessagesIsSeenInRooms error", err);
                } else {
                    var myFriends = [];
                    if (rows.length != 0) {
                        myFriends = rows;
                        for (var i = 0; i < rows.length; i++) {
                            socket.myFriendsId.push(rows[i].id);
                        }
                    }
                    socket.emit("getMyFriendsHaveIdsCallBack", myFriends);
                }
            });
        }

        //};

    };
    var getMyGroupsConversations = function(socket){
        chatDB.query(chatDB.mysql.format(chatDB.sqlString.getMyGroups2, [socket.userId]), function (err, rows) {
            if (err) {
                log.error("getMyGroups", err);
            } else {
                socket.emit("getMyGroupsCallBack", rows);
            }
        },true,socket.userId);

    };
    var getUsers = function (socket,ids) {
     
        var query = chatDB.sqlString.users.replace("%IN%", "'" + ids.join("','") + "'");
        chatDB.query(chatDB.mysql.format(query), function (err, rows) {
            if (err) {
                log.error("getUsers error", err);
            } else {
                if(rows.length > 0){
                    socket.emit("getUsersCallback", rows);

                }else{
                    // Fixing for Infinite loop when deleting a user
                    if(_.isArray(ids)){
                        if(ids.length == 1){
                            socket.emit("getUsersCallback", [{id:ids[0],name:'Account Deleted',avatar:'',is_logged:0,is_hidden:1,gender:'Male'}]);
                        }
                    }
                }
            }
        });
    };
    var getUsersInRooms = function (socket,rIds) {

        var query = chatDB.sqlString.usersInRooms.replace("%IN%", "'" + rIds.join("','") + "'");
      
        chatDB.query(chatDB.mysql.format(query), function (err, rows) {
            if (err) {
                log.error("getUsersInRooms error", err);
            } else {
                socket.emit("getUsersByRoomIdsAtBootingCallback", rows);

            }
        });
    };
    var setOffline = function (socket) {
        socket.leave('mooUser.' + socket.userId);
        var userId = socket.userId;
        chatNotification.imOffline(userId);
        // Hacking for display all users
        //var _that = module.exports;
        //_that.imOffline(userId);
        // End hacking
        this.setStatus(userId,chatConstants.USER_OFFLINE );
     
    };
    var setOnline = function (socket) {
        socket.join('mooUser.' + socket.userId);
        socket.emit("setOnlineCallback");
        var userId = socket.userId;
        chatNotification.imOnline(userId);
        // Hacking for display all users
        //var _that = module.exports;
        //_that.imOnline(userId);
        // End hacking
        this.setStatus(userId,chatConstants.USER_ONLINE );
    };
    var startTyping = function (socket,rId){
        chatDB.query(chatDB.mysql.format(chatDB.sqlString.getMemberInARoom, [rId]), function (err, users) {
            if (err) {
                log.error("startTyping error", err);
            } else {
                if(users.length > 0 ){
                    for (var i = 0; i < users.length; i++) {
                        if (chatSocket.isUserOnline(users[i].user_id) && users[i].user_id!=socket.userId) {
                            io.to('mooUser.' + users[i].user_id).emit('startTypingCallback', {rId:rId,uId:socket.userId});
                        }
                    }
                }
            }},true,socket.userId
        );
    };
    var stopTyping = function(socket,rId){
        chatDB.query(chatDB.mysql.format(chatDB.sqlString.getMemberInARoom, [rId]), function (err, users) {
            if (err) {
                log.error("startTyping error", err);
            } else {
                if(users.length > 0 ){
                    for (var i = 0; i < users.length; i++) {
                        if (chatSocket.isUserOnline(users[i].user_id) && users[i].user_id!=socket.userId) {
                            io.to('mooUser.' + users[i].user_id).emit('stopTypingCallback', {rId:rId,uId:socket.userId});
                        }
                    }
                }
            }},
            true,socket.userId
        );
    };
    var searchFriend = function(socket,name){
        if(name == null){return}
        chatDB.query(chatDB.mysql.format(chatDB.sqlString.getMyFriendsHaveName, [socket.userId,"%"+name+"%"]), function (err, rows) {
            if (err) {
                log.error("getMyFriends", err);
            } else {
                socket.emit("searchFriendCallback", {rawFriends:rows,name:name});
            }
        });
    };
    // Hacking for display all users
    var imOnline = function (userId) {
        io.emit("serverInfoRefeshCallback");
        var _that = module.exports;
        for(var i in _userStatus){
            var id = parseInt(i); // Must convert from string to int if u dont want to get loop forever
            if ( chatSocket.isUserOnline(id) && !_that.isOffline(id)) {
                io.to('mooUser.' + id).emit('friendIsLogged', userId);
            }
        }

    };
    var imOffline = function (userId) {
        io.emit("serverInfoRefeshCallback");
        var _that = module.exports;
        for(var i in _userStatus){
            var id = parseInt(i); // Must convert from string to int if u dont want to get loop forever
            if ( chatSocket.isUserOnline(id) && !_that.isOffline(id)) {
                io.to('mooUser.' + id).emit('friendIsLogout', userId);
            }
        }
    };
    // End hacking
    return {
        init: init,
        setId: setId,
        setStatus:setStatus,
        getStatus:getStatus,
        isOffline:isOffline,
        getMyFriendsOnline:getMyFriendsOnline,
        getMyFriends:getMyFriends,
        getUsers:getUsers,
        getUsersInRooms:getUsersInRooms,
        setOffline:setOffline,
        setOnline:setOnline,
        getMyGroupsConversations:getMyGroupsConversations,
        startTyping:startTyping,
        stopTyping:stopTyping,
        countUsersOnline:countUsersOnline,
        searchFriend:searchFriend,
        setHideMyOnlineStatus:setHideMyOnlineStatus,
        isHideMyOnlineStatus:isHideMyOnlineStatus,
        updateHideMyOnlineStatus:updateHideMyOnlineStatus,
        imOnline:imOnline,
        imOffline:imOffline
    };
}());


module.exports = ChatUser;
