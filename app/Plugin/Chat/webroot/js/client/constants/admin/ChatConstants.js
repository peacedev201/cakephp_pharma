/* Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

var chatContants = {
    SERVER_IS_ONLINE:1,
    SERVER_IS_OFFLINE:0,
    SERVER_IS_BEING_CHECKED:2,
    ActionTypes:{
        SETUP_SERVER_STATUS:'SETUP_SERVER_STATUS',
        SETUP_SERVER_INFO:'SETUP_SERVER_INFO',
        RECEIVE_RAW_MONITOR_MESSAGES:'RECEIVE_RAW_MONITOR_MESSAGES',
        RECEIVE_RAW_USERS:'RECEIVE_RAW_USERS',
        RECEIVE_RAW_ROOMS:'RECEIVE_RAW_ROOMS'
    },
    ERROR:{
        NO_ERROR:0
    },
    SYSTEM_MESSAGE_ACTION:{
        MISS_VIDEO_CALL: "miss_video_call"
    }
};

module.exports = chatContants;
