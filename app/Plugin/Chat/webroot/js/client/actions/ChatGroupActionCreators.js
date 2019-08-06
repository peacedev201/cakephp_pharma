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
     * @param {array} rawGroups
     */
    receiveAll: function(rawGroups) {
        ChatAppDispatcher.dispatch({
            type: ActionTypes.RECEIVE_RAW_GROUPS,
            rawGroups: rawGroups
        });
    }

};

export default module.exports;