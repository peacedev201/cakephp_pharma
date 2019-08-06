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
    getRoomMessagesCallback: function(data) {
        ChatAppDispatcher.dispatch({
            type: ActionTypes.GET_MESSAGE_FOR_A_ROOM_CALLBACK,
            data: data
        });
    },
    /**
     * @param {array} rawFriends
     */
    newMessage: function(data) {
        ChatAppDispatcher.dispatch({
            type: ActionTypes.GET_NEW_MESSAGE_FOR_A_ROOM_CALLBACK,
            message: data
        });
    },
    getRoomMessagesMoreCallback:function(data){
        ChatAppDispatcher.dispatch({
            type: ActionTypes.GET_MESSAGE_MORE_FOR_A_ROOM_CALLBACK,
            data: data
        });
    },
    deleteAllMesages:function(rId){
        ChatAppDispatcher.dispatch({
            type: ActionTypes.DELETE_ALL_MESSAGE_MORE_FOR_A_ROOM,
            rId: rId
        });
    }
};
export default module.exports ;