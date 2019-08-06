/* Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */

import ChatAppDispatcher from '../dispatcher/ChatAppDispatcher';
import ChatConstants from '../constants/ChatConstants';

var ActionTypes = ChatConstants.ActionTypes;

module.exports = {
    /**
     * @param {array} rawFriends
     */
    userIsLoggedCallback: function(uId, chat_online_status) {
        ChatAppDispatcher.dispatch({
            type: ActionTypes.USER_IS_LOGGED_CALLBACK,
            uId: uId,
            chat_online_status: chat_online_status
        });
    },

    iChangeOnlineStatus: function(chat_online_status) {
        ChatAppDispatcher.dispatch({
            type: ActionTypes.USER_I_CHANGE_ONLINE_STATUS_CALLBACK,
            chat_online_status: chat_online_status
        });
    },
};

export default module.exports;