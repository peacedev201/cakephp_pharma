/* Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
import ChatAppDispatcher from '../dispatcher/ChatAppDispatcher';
import ChatConstants from '../constants/ChatConstants';
var EventEmitter = require('events').EventEmitter;
import assign from 'object-assign';
import ChatWebAPIUtils from '../utils/ChatWebAPIUtils';


var ActionTypes = ChatConstants.ActionTypes;
var CHANGE_EVENT = 'change';
var _user = {id:0, chat_online_status:""}; // 0 means Guest

function _userIsLoggedCallback(uId, chat_online_status){
    if (uId != 0){
        _user.id = uId;
        _user.chat_online_status = chat_online_status;
        ViewerStore.emitChange();
        ChatWebAPIUtils.sendRequestForGetRoomHasUnreadMessage();
    }
}
function _iChangeOnlineStatus(chat_online_status){
    ViewerStore.setOnlineStatus(chat_online_status);
    ViewerStore.emitChange();
}
var ViewerStore = assign({}, EventEmitter.prototype, {

    emitChange: function() {
        this.emit(CHANGE_EVENT);
    },

    /**
     * @param {function} callback
     */
    addChangeListener: function(callback) {
        this.on(CHANGE_EVENT, callback);
    },

    removeChangeListener: function(callback) {
        this.removeListener(CHANGE_EVENT, callback);
    },

    get: function(key) {
        return _user[key];
    },
    getAll:function(){
        return _user;
    },
    isGuest:function(){
        return (_user.id == 0)?true:false;
    },
    getOnlineStatus: function(){
        return _user.chat_online_status;
    },
    setOnlineStatus: function(status){
        _user.chat_online_status = status;
    }
});

ViewerStore.dispatchToken = ChatAppDispatcher.register(function(action) {

    switch(action.type) {
        case ActionTypes.USER_IS_LOGGED_CALLBACK:
            _userIsLoggedCallback(action.uId, action.chat_online_status);
            break;
        case ActionTypes.USER_I_CHANGE_ONLINE_STATUS_CALLBACK:
            _iChangeOnlineStatus(action.chat_online_status);
            break;
        default:
        // do nothing
    }

});

module.exports = ViewerStore;
export default module.exports;