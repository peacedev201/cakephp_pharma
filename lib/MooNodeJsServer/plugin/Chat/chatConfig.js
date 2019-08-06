var chatDB = require("./chatDB.js");
var log = require("../../mooLog");
var _ = require("lodash");
var cache = require("../../mooCache");
function _detectConfig(socket,rows){
    try{
        config = cache.myCache.get( "getConfig", true );
    } catch( err ){
        var config = {}
        if (rows.length != 0) {
            for (var i = 0; i < rows.length; i++) {
                var v = rows[i];
                if (!_.isEmpty(v.value_actual)){
                    config[v.name] = v.value_actual;
                }else{
                    config[v.name] = v.value_default;
                }

                if(v.type_id == 'checkbox' || v.type_id=='radio'){
                    eval("var param = "+config[v.name]+";");
                    if (param.length != 0) {
                        for (var j = 0; j < param.length; j++) {
                            if(param[j].select == '1'){
                                config[v.name] = param[j].value;
                            }
                        }
                    }
                }
            }
        }
        cache.set("getConfig",config);
    }
    chatDB.query(chatDB.sqlString.getRoles, function (err, result) {
        if (err) {
            log.error("getRoles err", err);
        } else {
            _detectRoles(socket,result,config)
        }
    },true);
}
function _detectRoles(socket,rows,config){
    try{
        roles = cache.myCache.get( "getRoles1", true );
    } catch( err ){
        var roles = {}
        if (rows.length != 0) {
            for (var i = 0; i < rows.length; i++) {
                roles[rows[i].id] = rows[i].params.split(',');
            }
        }
        cache.set("getRoles",roles);
    }

    chatDB.query(chatDB.mysql.format(chatDB.sqlString.getUserRoles,[socket.userId]), function (err, result) {
        if (err) {
            log.error("getUserRoles err", err);
        } else {
            _detectPermission(socket,result,roles,config)
        }
    },true);
}
function _detectPermission(socket,rows,roles,config){
    try{
        permissions = cache.myCache.get( "getPermission"+socket.userId, true );
    } catch( err ){
        if (rows.length != 0) {
            var role = roles[rows[0].role_id];
            var isAllowedChat =  _.includes(role,'chat_allow_chat');
            var isAllowedSendPicture = _.includes(role,'chat_allow_send_picture');
            var isAllowedSendFiles = _.includes(role,'chat_allow_send_files');
            var isAllowedEmotion = _.includes(role,'chat_allow_user_emotion');
            var isAllowedChatGroup = _.includes(role,'chat_allow_chat_group');
            var isAllowedVideoCalling = _.includes(role,'chat_allow_video_calling');
            var permissions = {
                'isAllowedChat':isAllowedChat,
                'isAllowedSendPicture':isAllowedChat && isAllowedSendPicture,
                'isAllowedSendFiles':isAllowedChat && isAllowedSendFiles,
                'isAllowedEmotion':isAllowedChat && isAllowedEmotion,
                'isAllowedChatGroup':isAllowedChat && isAllowedChatGroup,
                'isAllowedVideoCalling':isAllowedChat && isAllowedVideoCalling,
            }

        }
        cache.set("getPermission"+socket.userId,permissions);
    }
    _detectUserSettings(socket,permissions,config)
}
function _detectUserSettings(socket,permission,config){
    chatDB.query(chatDB.mysql.format(chatDB.sqlString.getUserSetting,[socket.userId]), function (err, rows) {
        if (err) {
            log.error("getUserSetting err", err);
        } else {
            /*
            try{
                userSetting = cache.myCache.get( "getUserSetting", true );
            } catch( err ){
                var userSetting = {}
                if (rows.length != 0) {
                    userSetting = _.omit(rows[0], ['id', 'user_id']);
                }
                cache.set("getUserSetting",userSetting);
            }*/
            if (rows.length != 0) {
                var settings = _.omit(rows[0], ['id', 'user_id']);
                config["permissions"] = permission;
                config["settings"] = settings;
                socket.emit("getConfigCallback",config);
            }

        }
    });
}
module.exports =(function () {
    var get = function(socket){
        chatDB.query(chatDB.sqlString.getConfig, function (err, result) {
            if (err) {
                log.error("getConfig err", err);
            } else {
                _detectConfig(socket,result);
                /*
                var moo_total_messages = result[0].count;
                socket.emit('getServerInfoCallback',{
                    os_type:  os.type(),
                    os_platform:os.platform(),
                    os_arch:os.arch(),
                    os_cpu :os.cpus(),
                    //os_homedir:os.homedir(),
                    os_loadavg:os.loadavg(),
                    os_release:os.release(),
                    os_tmpdir:os.tmpdir(),
                    os_hostname:os.hostname(),
                    os_total_memory:os.totalmem()/1000000,
                    os_free_memory : os.freemem()/1000000,
                    moo_users_chatting:user.countUsersOnline(),
                    moo_total_messages:moo_total_messages,
                });*/
            }
        },true);

    };
    var refeshConfig = function(socket){
        cache.myCache.del("getConfig");
        cache.myCache.del(cache.findQueryCache(chatDB.sqlString.getConfig))
    }
    var refeshRoles = function(socket){
        cache.myCache.del("getRoles");
        cache.myCache.del("getPermission"+socket.userId);
        cache.myCache.del(cache.findQueryCache(chatDB.sqlString.getRoles))
        cache.myCache.del(cache.findQueryCache(chatDB.mysql.format(chatDB.sqlString.getUserRoles,[socket.userId])))
    }
    var refeshUserSettings = function(socket){
        cache.myCache.del("getUserSetting");
        cache.myCache.del(cache.findQueryCache(chatDB.mysql.format(chatDB.sqlString.getUserSetting,[socket.userId])))
    }
    var refeshAll= function(socket){
        refeshConfig(socket);
        refeshRoles(socket);
        refeshUserSettings(socket);
    }
    return {
        get:get,
        refeshConfig:refeshConfig,
        refeshRoles:refeshRoles,
        refeshUserSettings:refeshUserSettings,
        refeshAll:refeshAll
    }
}());

