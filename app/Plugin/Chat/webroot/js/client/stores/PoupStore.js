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
import _ from 'lodash';


var ActionTypes = ChatConstants.ActionTypes;
var CHANGE_EVENT = 'change';
var _poup = {
    report:{isOpen: false,rId:0},
    alert:{isOpen: false,title:"",body:""},
    alertYN:{isOpen: false,title:"",body:"",noButton:"",yesButton:""},
    alertRTCSupported:{isOpen: false,title:"",body:""},
    alertVideoCallSetting:{isOpen: false,title:"",body:"",noButton:"",yesButton:""}
};
var PoupStore = assign({}, EventEmitter.prototype, {

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
        return _poup[key];
    },
    getAll:function(){
        return _poup;
    }
});
function _openReportModal(rId){
    _poup.report = {isOpen: true,rId:rId};
}
function _closeReportModal(){
    _poup.report = {isOpen: false,rId:0};
}
function _openAlertModal(data){
    _poup.alert = {isOpen: true,title:data.title,body:data.body,close_button:data.close_button};
}
function _closeAlertModal(){
    _poup.alert = {isOpen: false,title:"",body:"",close_button:true};
}
function _openAlertYesNoModal(config){
    //_poup.alertYN = {isOpen: true,title:data.title,body:data.body,noButton:data.noButton,yesButton:data.yesButton,callback:data.callback};
    var oDefault = {
        isOpen: true,
        title:"",
        body:"",
        noButton:"",
        yesButton:"",
        callback:function(){},
        callbackNo:function(){}
    }
    _poup.alertYN = _.merge(oDefault,config);
}
function _closeAlertYesNoModal(){
    _poup.alertYN = {isOpen: false,title:"",body:"",noButton:"",yesButton:""};
}
function _openRTCSupportedAlertModal(config){
    var oDefault = {
        isOpen: true,
        title:"",
        body:"",
        callbackNo:function(){}
    }
    _poup.alertRTCSupported = _.merge(oDefault,config);
}
function _closeRTCSupportedAlertModal(){
    _poup.alertRTCSupported = {isOpen: false,title:"",body:""};
}
function _openVideoCallSettingModal(config){
    var oDefault = {
        isOpen: true,
        title:"",
        body:"",
        callbackNo:function(){}
    }
    _poup.alertVideoCallSetting = _.merge(oDefault,config);
}
function _closeVideoCallSettingModal(){
    _poup.alertVideoCallSetting = {isOpen: false,title:"",body:""};
}
PoupStore.dispatchToken = ChatAppDispatcher.register(function(action) {

    switch(action.type) {
        case ActionTypes.POUP_OPEN_REPORT_MODAL:
            _openReportModal(action.rId);
            PoupStore.emitChange();
            break;
        case ActionTypes.POUP_OPEN_ALERT_MODAL:
            _openAlertModal(action.data);
            PoupStore.emitChange();
            break;
        case ActionTypes.POUP_OPEN_ALERT_YES_NO_MODAL:
            _openAlertYesNoModal(action.config);
            PoupStore.emitChange();
            break;
        case ActionTypes.POUP_CLOSE_REPORT_MODAL:
            _closeReportModal(); 
            PoupStore.emitChange();
            break;
        case ActionTypes.POUP_CLOSE_ALERT_MODAL:
            _closeAlertModal();
            PoupStore.emitChange();
            break;
        case ActionTypes.POUP_CLOSE_ALERT_YES_NO_MODAL:
            _closeAlertYesNoModal();
            PoupStore.emitChange();
            break;
        case ActionTypes.POUP_OPEN_RTC_SUPPORTED_ALERT_MODAL:
            _openRTCSupportedAlertModal(action.config);
            PoupStore.emitChange();
            break;
        case ActionTypes.POUP_CLOSE_RTC_SUPPORTED_ALERT_MODAL:
            _closeRTCSupportedAlertModal();
            PoupStore.emitChange();
            break;
        case ActionTypes.POPUP_OPEN_VIDEO_CALL_SETTING_MODAL:
            _openVideoCallSettingModal();
            PoupStore.emitChange();
            break;
        case ActionTypes.POPUP_CLOSE_VIDEO_CALL_SETTING_MODAL:
            _closeVideoCallSettingModal();
            PoupStore.emitChange();
            break;
        case ActionTypes.POUP_CLOSE_NO_CAMERA_ALERT_MODAL:
            _closeNoCameraAlertModal();
            PoupStore.emitChange();
            break;
        default:
        // do nothing
    }

});

module.exports = PoupStore;