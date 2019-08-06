var chatDB = require("../chatDB");
var log = require("../../../mooLog");
module.exports = function (socket,page) {
    if(typeof page === 'undefined'){page = 0}
    chatDB.query(chatDB.mysql.format(chatDB.sqlString.getLatestMessageIds, [socket.userId, page]), function (err, rooms) {
        if (err) {
            log.error("getLatestMessageIds error", err);
        } else {

            if (rooms.length != 0) {
                var ids = []
                for (var i = 0; i < rooms.length; i++) {
                    if (rooms[i].latest_mesasge_id > 0){
                        ids.push(rooms[i].latest_mesasge_id)
                    }
                }
                var query = chatDB.sqlString.getMessageByIds.replace("%IN%", "'" + ids.join("','") + "'");
                chatDB.query(query, function (err, messages) {
                    if (err) {
                        log.error("getLatestMessageIds error", err);
                    } else {
                        var query = chatDB.sqlString.getMessagesStatusByIds.replace("%IN%", "'" + ids.join("','") + "'");
                        chatDB.query(chatDB.mysql.format(query,[socket.userId]),function(err,status){
                            if (err) {
                                log.error("getMessagesStatusByIds error", err);
                            } else {
                                socket.emit("getLatestMessagesCallback", {rooms: rooms , messages:messages,status:status});
                            }
                        })

                    }
                });


            }

        }
    });
};