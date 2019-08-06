import ChatWebAPIUtils from '../ChatWebAPIUtils';
import UserStore from '../../stores/UserStore';
import VideoCallingActions from '../../actions/VideoCallingActions';
import SimplePeer from 'simple-peer';
import _ from "lodash";
var peer ;
var __ = require('../../utils/ChatMooI18n').i18next;
var windowCalling = {};
var windowCallingByRoom = {};
var videoCallTimeout = "";
var getMediaStreamCounter = 0;
var receivingSignal = {}
import ChatMooConfig from "../../utils/ChatMooConfig";
import DetectRTC  from 'detectrtc';

/**
 * Create init peer
 * @param obj {object} {rId:roomId,members:array}
 * @param wId windowCalling Id
 */
function createPeerWhenHaveACalling(obj,wId){
    if (typeof  windowCalling[wId].ChatWebAPIUtils === "undefined"){
        setTimeout(function(){createPeerWhenHaveACalling(obj,wId)},200);
    }else{
        windowCalling[wId].ChatWebAPIUtils.createVideoPeer(obj);
    }
}

/**
 * This function will create a new peer base on 'simple-peer' then send the SIGN to another
 * to make a connection between 2 browsers
 * @param obj {object} {rId:roomId,members:array,senderId:caller userId,senderSocketId: socket id of caller}
 */
export function createVideoPeer(token) {
    VideoCallingActions.createInitiatorPeer(token);
}

/**
 * This function will get singal data type offer or anwser from peer then uses it for creating connect
 * @param obj {object} {signal: peer's signal data , senderSocketId : peer's socket id}
 */
export function receiveSignal(obj){
    receivingSignal[obj.rId] = 1;
    VideoCallingActions.receiveSignal(obj);
}
export function getVideoCallingTokenCallback(token){
    ChatWebAPIUtils.stunTurnServer();
    ChatWebAPIUtils.sendRequestVideoCalling(token);
    /*var w = window,
            d = document,
            e = d.documentElement,
            g = d.getElementsByTagName('body')[0],
            x = w.innerWidth || e.clientWidth || g.clientWidth,
            y = w.innerHeight|| e.clientHeight|| g.clientHeight;*/
    //window.open(ChatWebAPIUtils.getSiteUrl() + '/chat/videoCalling/?token='+token + '&receiver_id=' + room.members[0], room.rId, "height="+(y*0.87)+",width="+(x*0.87));
}


function uuidv4() {
    return 'xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

/**
 * This function for hacking to make sure the video bunlde js is loaded then send request for creating
 * video token
 * @param {object} {rId:roomId,members:array}
 * @param wId
 */
function sendRequestGetVideoCallingToken(obj,wId){
    /*if (typeof  windowCalling[wId].ChatWebAPIUtils === "undefined"){
        setTimeout(function(){
            sendRequestGetVideoCallingToken(obj,wId);
        },200);
    }else{
        windowCalling[wId].ChatWebAPIUtils.sendRequestGetVideoCallingToken(obj);
    }*/
    //ChatWebAPIUtils.sendRequestGetVideoCallingToken(obj);
}

/**
 * It will open new window for creating a video call for a room
 * Noted that :
 *     1. This function is being used by parent window for opening
 *     new child popup window for make a video calling .
 *     2. Avoid browser popup blockers by beeing invoked from javascript
 *     that is not invoked by direct user action
 * @param room Room object - It must include {id,members}
 */
export function openAWindowForCalling(room){
    if(!DetectRTC.isWebRTCSupported)
    {
        ChatWebAPIUtils.openRTCSupportedAlertModal({
            title:__.t("rtc_supported_alert_switch_browsers")
        });
    }
    else
    {
        receivingSignal[room.id] = 0;
        var w = window,
            d = document,
            e = d.documentElement,
            g = d.getElementsByTagName('body')[0],
            x = w.innerWidth || e.clientWidth || g.clientWidth,
            y = w.innerHeight|| e.clientHeight|| g.clientHeight;
        var wId = uuidv4();
        windowCallingByRoom[room.id] = windowCalling[wId] = window.open(ChatWebAPIUtils.getSiteUrl() + '/chat/videoCalling/?peer_id='+wId + '&receiver_id=' + room.members[0] + '&room_id=' + room.id + '&members=' + room.members.join(','), room.id, "height="+(y*0.87)+",width="+(x*0.87));
        //sendRequestGetVideoCallingToken({rId:room.id,members:room.members},wId);
    }
}
/**
 * The callback function for videoCalling event which is called from another user
 * via openAWindowForCalling function .
 * Noted that :
 *      This function is invoked in parent window and open new child window in case
 *      user accepts answer video calling
 * @param {object} {rId:roomId,members:array,callerId:caller userId,callerSocketId: socket id of caller}
 */
export function videoCallingCallback(obj){
    //receivingSignal[obj.rId] = 0;
    
    //destroy timeout
    clearTimeout(videoCallTimeout);
            
    //check ringing
    ChatWebAPIUtils.ringingVideoCall(obj);
    
    if(!DetectRTC.isWebRTCSupported)
    {
        ChatWebAPIUtils.openRTCSupportedAlertModal({
            title:__.t("incoming_video_call"),
            body:__.t("user_is_calling",{name:UserStore.getName(_.get(obj,"senderId",0))}),
            callbackNo:function(){
                ChatWebAPIUtils.cancelVideoCallRTCSupported(obj);
            }
        });
    }
    else
    {
        //waiting video call time out
        videoCallTimeout = setTimeout(function(){
            ChatWebAPIUtils.sendRequestMissVideoCall(obj.rId, obj.senderId);
            if(document.getElementById('cancelButton'))
            {
                document.getElementById('cancelButton').click();
                ChatWebAPIUtils.closeVideoCallDialog(obj);
            }
        }, ChatMooConfig.waitingVideoCallTimeout() * 1000);

        ChatWebAPIUtils.openAlertYesNoModal({
            title:__.t("incoming_video_call"),
            body:__.t("user_is_calling",{name:UserStore.getName(_.get(obj,"senderId",0))}),
            noButton:__.t("decline"),
            yesButton:__.t("answer"),
            noButtonId: "cancelButton",
            callback:function () {
                //destroy timeout
                clearTimeout(videoCallTimeout);

                var w = window,
                    d = document,
                    e = d.documentElement,
                    g = d.getElementsByTagName('body')[0],
                    x = w.innerWidth || e.clientWidth || g.clientWidth,
                    y = w.innerHeight|| e.clientHeight|| g.clientHeight;
                var wId = uuidv4();
                windowCalling[wId] = window.open(ChatWebAPIUtils.getSiteUrl() + '/chat/videoCalling/?token=' + obj.senderSocketId + '&caller_id=' + obj.senderId + '&room_id=' + obj.rId, obj.id, "height="+(y*0.87)+",width="+(x*0.87));
                //createPeerWhenHaveACalling(obj,wId);
            },
            callbackNo:function(){
                ChatWebAPIUtils.cancelVideoCall(obj);
            }
        });
    }
}

/**
 * Get media stream (video and audio) for streaming
 * between two browsers using peer-to-peer connect type
 */
export function getMediaStream(obj){
    // Fix bug uncaught error dispatch with another event
    if (getMediaStreamCounter == 0) {
        VideoCallingActions.getMediaStream(obj);
    }
    getMediaStreamCounter ++;
}

/**
 *
 * @param stream - it is the stream object callback from function navigator.mediaDevices.getUserMedia
 */
export function setMediaStream(stream){
    VideoCallingActions.setMediaStream(stream);
}
/**
 * Set media stream for remote  camera in connecting peer-to-peer between 2 browsers
 * @param stream -
 */
export function setMediaRemoteStream(stream){
    VideoCallingActions.setMediaRemoteStream(stream);
}
/**
 * Turn on/off camera for streaming
 */
export function setMyCameraStreamEnable(is){
    VideoCallingActions.setMyCameraStreamEnable(is);
}
/**
 * Turn on/off mic for streaming
 */
export function toogleMyMicStream(){
    VideoCallingActions.setMediaRemoteStream(stream);
}

export function ringingVideoCallCallback(obj){
    VideoCallingActions.ringingVideoCall(obj);
}

export function cancelVideoCallCallback(obj){
    VideoCallingActions.cancelVideoCall(obj);
}

export function cancelVideoCallRTCSupportedCallback(obj){
    VideoCallingActions.cancelVideoCallRTCSupported(obj);
}

export function endVideoCallingCallback(obj){
    VideoCallingActions.endVideoCall(obj);
}

export function setMyMicStreamEnable(is){
    VideoCallingActions.setMyMicStreamEnable(is);
}

export function getUserInfo(){
    VideoCallingActions.getUserInfo();
}
export function getVideoUserInfoCallback(obj){
    VideoCallingActions.getUserInfo(obj);
}
export function closeVideoCallDialogCallback(obj){
    windowCallingByRoom[obj.rId].close();
}
export function saveVideoCallSetting(obj){
    VideoCallingActions.getMediaStream(obj);
}
export function updateCameraStream(stream){
    VideoCallingActions.updateCameraStream(stream);
}
export function callingPickupCallback(){
    clearTimeout(videoCallTimeout);
    ChatWebAPIUtils.closeAlertYesNoModal();
}
export function stunTurnServerCallback(value, token){
    VideoCallingActions.stunTurnServerCallback(value);
    if(typeof token != 'undefined' && token != null){
        ChatWebAPIUtils.createVideoPeer(token);
    }
}