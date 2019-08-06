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
     * @param {int} roomId
     */
    setMessagesIsSeen: function(roomId) {
        ChatAppDispatcher.dispatch({
            type: ActionTypes.SET_ROOM_MESSAGES_IS_SEEN,
            roomId: roomId
        });
    },

    markReadAllMessagesCallback: function(){
        ChatAppDispatcher.dispatch({
            type: ActionTypes.MARK_READ_ALL_MESSAGES
        });
    }
};
