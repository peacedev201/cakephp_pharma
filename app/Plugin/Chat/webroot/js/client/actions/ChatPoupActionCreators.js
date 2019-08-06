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
    openReportModal: function(rId) {
        ChatAppDispatcher.dispatch({
            type: ActionTypes.POUP_OPEN_REPORT_MODAL,
            rId: rId
        });
    },
    closeReportModal: function(rId) {
        ChatAppDispatcher.dispatch({
            type: ActionTypes.POUP_CLOSE_REPORT_MODAL
        });
    },
    openAlertModal:function(title,body,close_button){
        ChatAppDispatcher.dispatch({
            type: ActionTypes.POUP_OPEN_ALERT_MODAL,
            data: {title:title,body:body,close_button:close_button}
        });
    },
    closeAlertModal: function() {
        ChatAppDispatcher.dispatch({
            type: ActionTypes.POUP_CLOSE_ALERT_MODAL
        });
    },
    openAlertYesNoModal:function(config){
        ChatAppDispatcher.dispatch({
            type: ActionTypes.POUP_OPEN_ALERT_YES_NO_MODAL,
            config:config
        });
    },
    closeAlertYesNoModal: function() {
        ChatAppDispatcher.dispatch({
            type: ActionTypes.POUP_CLOSE_ALERT_YES_NO_MODAL
        });
    },
    openRTCSupportedAlertModal:function(config){
        
        ChatAppDispatcher.dispatch({
            type: ActionTypes.POUP_OPEN_RTC_SUPPORTED_ALERT_MODAL,
            config: config
        });
    },
    closeRTCSupportedAlertModal: function() {
        ChatAppDispatcher.dispatch({
            type: ActionTypes.POUP_CLOSE_RTC_SUPPORTED_ALERT_MODAL
        });
    },
    openVideoCallSettingModal:function(){
        ChatAppDispatcher.dispatch({
            type: ActionTypes.POPUP_OPEN_VIDEO_CALL_SETTING_MODAL,
        });
    },
    closeVideoCallSettingModal:function(){
        ChatAppDispatcher.dispatch({
            type: ActionTypes.POPUP_CLOSE_VIDEO_CALL_SETTING_MODAL,
        });
    }
};
