var _query ={
    checkTokenIsExists : "SELECT user_id FROM prefix_chat_tokens WHERE token= ?",
    getMyStatCached:"SELECT * FROM prefix_chat_cached_query_user_stats WHERE user_id = ?",
    setMyStatCached:"UPDATE prefix_chat_cached_query_user_stats SET new_friend=0,new_block=0,new_profile=0 WHERE user_id = ?",
    getMyFriends : "SELECT User.id,User.name,User.avatar,0 AS is_logged,1 AS is_hidden ,gender FROM prefix_users AS User LEFT JOIN prefix_roles AS Role ON (User.role_id = Role.id) INNER JOIN prefix_friends AS Friend ON (User.id = Friend.friend_id) WHERE Friend.user_id = ? AND active = '1' limit 100",
    getMyBlockers : "SELECT object_id FROM prefix_user_blocks WHERE user_id = ?",
    getMyFriendsHaveIds:"SELECT User.id,User.name,User.avatar,0 AS is_logged,1 AS is_hidden ,gender FROM prefix_users AS User  WHERE User.id IN (%IN%) AND active = '1'",
    getAllMyFriendId : "SELECT User.id FROM prefix_users AS User  LEFT JOIN prefix_friends AS Friend ON (User.id = Friend.friend_id) WHERE Friend.user_id = ? AND active = '1'",
    getMyFriendsHaveName : "SELECT User.id,User.name,User.avatar,0 AS is_logged,1 AS is_hidden ,gender FROM prefix_users AS User LEFT JOIN prefix_roles AS Role ON (User.role_id = Role.id) INNER JOIN prefix_friends AS Friend ON (User.id = Friend.friend_id) WHERE Friend.user_id = ? AND active = '1' AND User.name LIKE ? ",
    getMyGroups : "SELECT room_id,members FROM (SELECT count(user_id) as number ,GROUP_CONCAT(CONVERT(user_id, CHAR(10))) AS members , room_id FROM prefix_chat_rooms_members WHERE room_id in (SELECT room_id FROM prefix_chat_rooms_members WHERE user_id = ?) GROUP BY room_id) AS TMP WHERE number > 2",
    getMyGroups2: "SELECT TMP.room_id,TMP.members FROM (SELECT count(user_id) as number ,GROUP_CONCAT(CONVERT(user_id, CHAR(10))) AS members , room_id FROM prefix_chat_rooms_members WHERE room_id in (SELECT room_id FROM prefix_chat_rooms_members WHERE user_id = ?) GROUP BY room_id) AS TMP LEFT JOIN prefix_chat_rooms AS p ON TMP.room_id = p.id WHERE  p.is_group=1",
    users:"SELECT id,name,avatar,0 AS is_logged,1 AS is_hidden,gender FROM prefix_users  WHERE id IN (%IN%)",
    usersInRooms:"SELECT id,name,avatar,0 AS is_logged,1 AS is_hidden,gender FROM prefix_users  WHERE id IN (SELECT user_id FROM prefix_chat_rooms_members WHERE room_id  IN (%IN%))",
    rooms:"SELECT id,code FROM prefix_chat_rooms  WHERE id IN (%IN%)",
    checkRomByCode:"SELECT id,first_blocked,second_blocked,is_group,has_joined FROM prefix_chat_rooms WHERE code= ?",
    updateCodeRoom:"UPDATE prefix_chat_rooms SET code = ? WHERE id = ? ",
    updateCodeRoomAndHasJoined:"UPDATE prefix_chat_rooms SET code = ? , has_joined=? WHERE id = ? ",
    getMemberInARoom:"SELECT user_id FROM prefix_chat_rooms_members WHERE room_id = ? ",
    checkUserIsMemberInARoom:"SELECT user_id FROM prefix_chat_rooms_members WHERE room_id = ? and user_id = ? ",
    updateFirstBlockInARoom:"UPDATE prefix_chat_rooms SET first_blocked = ? WHERE id = ? ",
    secondFirstBlockInARoom:"UPDATE prefix_chat_rooms SET second_blocked = ? WHERE id = ? ",
    getRoomInfo:"SELECT * FROM prefix_chat_rooms WHERE id = ? ",
    getUnreadMesageInARoom:"SELECT COUNT(s.id) as newMessages,s.message_id FROM prefix_chat_status_messages as s LEFT JOIN prefix_chat_messages as m ON s.message_id = m.id WHERE s.room_id=? AND s.user_id=? AND s.unseen=1 AND m.sender_id != ? ORDER BY s.message_id",
    clearReadMesages:"DELETE FROM prefix_chat_status_messages WHERE unseen=0 ",
    countTotalStatusMessages:"SELECT COUNT(*) AS count FROM prefix_chat_status_messages",
    leaveARoom:"DELETE FROM prefix_chat_rooms_members WHERE room_id = ? AND user_id = ?",
    createRom:"INSERT INTO prefix_chat_rooms SET ?",
    joinRom:"INSERT INTO prefix_chat_rooms_members SET ?",
    getRoomHasUnReadMessage:"SELECT DISTINCT(room_id) FROM prefix_chat_status_messages WHERE user_id = ? AND unseen=1 ",
    setMyFriendMesasge:"INSERT INTO prefix_chat_messages SET ?",
    countTotalMessage:"SELECT COUNT(*) AS count FROM prefix_chat_messages",
    getMessage:"SELECT id,content,note_content_html,sender_id,room_id,type,UNIX_TIMESTAMP(created) AS created  FROM prefix_chat_messages ORDER BY id DESC LIMIT ? ",
    getMessageByIds:"SELECT id,content,note_content_html,note_one_emoj_only,sender_id,room_id,type,UNIX_TIMESTAMP(created) AS created  FROM prefix_chat_messages  WHERE id IN (%IN%) ",
    getLatestMessageIds:"SELECT * FROM prefix_chat_rooms AS A WHERE id in ( SELECT room_id FROM prefix_chat_rooms_members WHERE user_id = ? ) ORDER BY A.latest_mesasge_id DESC LIMIT ?,100",
    getMessagesStatusByIds:"SELECT id,message_id,room_id,unseen FROM prefix_chat_status_messages  WHERE message_id IN (%IN%) and user_id = ? and `delete` = 0",
    setMesasgeStatus:"INSERT INTO prefix_chat_status_messages SET ?",
    setMesasgeStatusIsSeen:"UPDATE prefix_chat_status_messages SET unseen = 0 WHERE id IN (%IN%) AND user_id = ?",
    setMesasgeStatusIsSeen2:"UPDATE prefix_chat_status_messages SET unseen = 0 WHERE room_id IN (%IN%) AND user_id = ?",
    getMyGroupMesasge:"SELECT * FROM (SELECT m.id,m.room_id,m.type, m.content,m.note_content_html,m.note_one_emoj_only, UNIX_TIMESTAMP(m.created) AS time ,m.sender_id , IFNULL(u.unseen,0) as unseen,IFNULL(u.user_id,0) as receiver_id , IFNULL(u.id,0) as unread_id FROM prefix_chat_messages as m LEFT JOIN  prefix_chat_status_messages  as u ON ( m.id =u.message_id and u.user_id = ?) WHERE m.room_id = ? AND u.delete = 0 GROUP BY id ORDER BY time DESC  LIMIT ? ) TMP ORDER BY id ",
    getMyFriendMesasge:"SELECT * FROM (SELECT m.id,m.room_id,m.type, m.content,m.note_content_html,m.note_one_emoj_only, UNIX_TIMESTAMP(m.created) AS time ,m.sender_id , IFNULL(u.unseen,0) as unseen,IFNULL(u.user_id,0) as receiver_id , IFNULL(u.id,0) as unread_id FROM prefix_chat_messages as m LEFT JOIN  prefix_chat_status_messages  as u ON ( m.id =u.message_id and u.user_id = ?) WHERE m.room_id = ? AND u.delete = 0  GROUP BY id ORDER BY time DESC  LIMIT ? ) TMP ORDER BY id ",
    getMyFriendMesasgeToId:"SELECT * FROM (SELECT m.id,m.room_id,m.type, m.content,m.note_content_html,m.note_one_emoj_only, UNIX_TIMESTAMP(m.created) AS time ,m.sender_id , IFNULL(u.unseen,0) as unseen,IFNULL(u.user_id,0) as receiver_id , IFNULL(u.id,0) as unread_id FROM prefix_chat_messages as m LEFT JOIN  prefix_chat_status_messages  as u ON ( m.id =u.message_id and u.user_id = ?) WHERE m.room_id = ? AND u.delete = 0 AND  m.id >= ? GROUP BY id ORDER BY time DESC   ) TMP ORDER BY id ",
    //getMoreMyFriendMesasge:"SELECT * FROM (SELECT m.id,m.room_id,m.type, m.content,m.note_content_html,m.note_one_emoj_only, UNIX_TIMESTAMP(m.created) AS time ,m.sender_id , IFNULL(u.unseen,0) as unseen,IFNULL(u.user_id,0) as receiver_id , IFNULL(u.id,0) as unread_id FROM prefix_chat_messages as m LEFT JOIN  prefix_chat_status_messages  as u ON ( m.id =u.message_id and u.user_id = ?) WHERE m.room_id = ? AND u.delete = 0 AND  m.id < ? GROUP BY id ORDER BY time DESC  LIMIT ? ) TMP ORDER BY id ",
    getMoreMyFriendMesasge:"SELECT m.id,m.room_id,m.type, m.content,m.note_content_html,m.note_one_emoj_only, UNIX_TIMESTAMP(m.created) AS time ,m.sender_id , IFNULL(u.unseen,0) as unseen,IFNULL(u.user_id,0) as receiver_id , IFNULL(u.id,0) as unread_id FROM prefix_chat_messages as m LEFT JOIN  prefix_chat_status_messages  as u ON ( m.id =u.message_id and u.user_id = ?) WHERE m.room_id = ? AND u.delete = 0 AND  m.id < ?  ORDER BY id DESC  LIMIT ? ",
    deleteRoomMesasge:"UPDATE prefix_chat_status_messages SET `delete` = 1 WHERE room_id= ? AND user_id= ?",
    reportRoomMesasge:"INSERT INTO prefix_chat_reports SET ?",
    checkReportRoomMesasgeIsExists:"SELECT id FROM prefix_chat_reports WHERE room_id = ? AND by_user = ?",
    //updateNotificationForMember:"UPDATE prefix_users SET chat_count = (SELECT COUNT(*) FROM (SELECT room_id FROM prefix_chat_status_messages WHERE user_id = ? and unseen=1 group by room_id) AS A) WHERE id=?" // Get ER_LOCK_DEADLOCK: Deadlock found when trying to get lock; try restarting transaction
    countNewChatMessage:"SELECT COUNT(*) AS count FROM (SELECT room_id FROM prefix_chat_status_messages WHERE user_id = ? and unseen=1 group by room_id) AS A",
    updateNotificationForMemeber:"UPDATE prefix_users SET chat_count = ? WHERE id=?",
    updateLatestMessageIdForARoom:"UPDATE prefix_chat_rooms SET latest_mesasge_id = ? WHERE id=?",
    updateRoomStatus:"UPDATE prefix_chat_users_settings SET room_is_opened = ? WHERE user_id=?",
    createRoomStatus:"INSERT INTO prefix_chat_users_settings SET ?",
    getHideOnlineSetting:"SELECT hide_online FROM prefix_users WHERE id=?",
    getRoles:"SELECT id,params FROM prefix_roles",
    getUserRoles:"SELECT role_id FROM prefix_users WHERE id=?",
    getUserSetting:"SELECT * FROM prefix_chat_users_settings WHERE user_id=?",
    getChatBlockedRoomId:"SELECT GROUP_CONCAT(id) as ids FROM prefix_chat_rooms WHERE first_blocked=? OR second_blocked=?",
    getMemberInARoomExcept:"SELECT * FROM prefix_chat_rooms_members WHERE room_id IN (%IN%) AND user_id !=?",
    getMyRom:"SELECT GROUP_CONCAT(room_id) as ids FROM prefix_chat_rooms_members WHERE user_id = ?",
    getRomBySearchContent:"SELECT room_id, count(*) as count_message FROM prefix_chat_messages WHERE room_id IN (%IN%) AND type='text' AND content LIKE ? GROUP BY room_id",
    searchMessageByRoom: "SELECT id,content,note_content_html,sender_id,room_id,type,UNIX_TIMESTAMP(created) AS created  FROM prefix_chat_messages WHERE type='text' AND room_id = ? AND content LIKE ?  ORDER BY id DESC LIMIT ?,20",
    getMessageByRoomBetweenId: "(SELECT id,content,note_content_html,sender_id,room_id,type,UNIX_TIMESTAMP(created) AS created  FROM prefix_chat_messages WHERE room_id = ? AND id <= ?  ORDER BY id LIMIT 5) UNION (SELECT id,content,note_content_html,sender_id,room_id,type,UNIX_TIMESTAMP(created) AS created  FROM prefix_chat_messages WHERE room_id = ? AND id > ?  ORDER BY id LIMIT 5)"
}
           
_query["getConfig"] = "SELECT name,value_actual,value_default,type_id FROM prefix_settings WHERE name IN (" +
    "'chat_chat_server_url'," +
    "'storage_current_type'," +
    "'StorageAwsObjectMap'," +
    "'storage_amazon_use_cname'," +
    "'storage_amazon_url_cname'," +
    "'storage_cloudfront_enable'," +
    "'storage_cloudfront_cdn_mapping'," +
    "'chat_disable'," +
    "'chat_hide_offline_users_in_who_online'," +
    "'chat_open_chatbox_when_a_new_message_arrives'," +
    "'chat_beep_on_arrival_of_all_messages'," +
    "'chat_hidden_in_mobile'," +
    "'chat_load_bar_in_offline_mode_for_all_first_time_users'," +
    "'chat_number_of_messages_fetched_when_load_earlier_messeges'," +
    "'number_of_messages_fetched_window_first_time'," +
    "'send_message_to_non_friend'," +
    "'chat_turn_on_notification'," +
    "'chat_waiting_video_call_time_out'," +
    "'chat_fcm_server_api_key'," +
    "'chat_fcm_sender_id'," +
    "'chat_hidden_in_mobile')";

function mooSQL(prefix){
    
    var tmpQuery = "";
    for(var key in _query) {
        tmpQuery = _query[key].toString();
        _query[key] =  tmpQuery.replace(/prefix_/g,prefix);

    }
    return _query;
}

module.exports = mooSQL;


