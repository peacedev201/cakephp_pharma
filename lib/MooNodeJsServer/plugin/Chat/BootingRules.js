var chatDB = require("./chatDB.js");
var log = require("../../mooLog");
var BootingRules = (function () {

    var initDataSource = function(chatDB){
        chatDB = chatDB ;
    };
    var clearTableChatUserIsConnecting = function(){
        chatDB.query('TRUNCATE TABLE '+chatDB.prefix+'chat_user_is_connecting ', function(err,rows){
            if(err){
                //io.emit('error');
                log.error('ERROR : TRUNCATE TABLE ',err);
            } else {

            }
        });
    };
    // Fixed deadlock for chat status more than 500k records
    var fixedDeadlockOnChatStatusMessagesTable = function(){
        chatDB.query(chatDB.sqlString.countTotalStatusMessages, function (err, result) {
            if (result[0].count > 50000 ){
                chatDB.query(chatDB.sqlString.clearReadMesages, function (err, rows) {
                    if (err) {
                        log.error("fixedDeadlockOnChatStatusMessagesTable error", err);
                    }
                });
            }
            if (err) {
                log.error("fixedDeadlockOnChatStatusMessagesTable error", err);
            }
        });
    }

    var run = function(){
        setTimeout(fixedDeadlockOnChatStatusMessagesTable, 3000); // Hacking for booting


    };
    return {
        initDataSource : initDataSource,
        run:run
        //clearTableChatUserIsConnecting : clearTableChatUserIsConnecting
    };
}());


module.exports =  BootingRules ;