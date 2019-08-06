'use strict';

import VideoCallingActionTypes from './VideoCallingActionTypes';
import AppDispatcher from '../dispatcher/ChatAppDispatcherES2015';

const Actions = {
    /**
     * Create initiator Peer , the signal data will have type is offer
     * @param obj {rId:roomId,members:array,senderId:caller userId,senderSocketId: socket id of caller}
     */
    createInitiatorPeer(token){
        AppDispatcher.dispatch({
            type:VideoCallingActionTypes.CREATE_INITIATOR_PEER,
            token:token
        });
    },
    /**
     * This function will get singal data type offer or anwser from peer then uses it for creating connect
     * @param obj {object} {signal: peer's signal data , senderSocketId : peer's socket id}
     */
    receiveSignal(obj){
        AppDispatcher.dispatch({
            type:VideoCallingActionTypes.RECEIVE_SIGNAL_FROM_PEER,
            obj:obj
        });
    },
    getMediaStream(obj){
            AppDispatcher.dispatch({
                type:VideoCallingActionTypes.GET_MEDIA_STREAM,
                obj:obj
            });
    },
    /**
     *
     * @param stream - it is the stream object callback from function navigator.mediaDevices.getUserMedia
     */
    setMediaStream(stream){
        AppDispatcher.dispatch({
            type:VideoCallingActionTypes.SET_MEDIA_STREAM,
            stream:stream
        });
    },
    /**
     *
     * @param stream - it is the stream object callback from function navigator.mediaDevices.getUserMedia from remote browser
     */
    setMediaRemoteStream(stream){
        AppDispatcher.dispatch({
            type:VideoCallingActionTypes.SET_MEDIA_REMOTE_STREAM,
            stream:stream
        });
    },
    /**
     *  Turn on/off camera for streaming
     */
    setMyCameraStreamEnable(is){
        AppDispatcher.dispatch({
            type:VideoCallingActionTypes.SET_MY_CAMERA_ENABLE,
            is:is
        });
    },
    /**
     * Turn on/off mic for streaming
     */
    setMyMicStreamEnable(is){
        AppDispatcher.dispatch({
            type:VideoCallingActionTypes.SET_MY_MIC_ENABLE,
            is:is
        });
    },
    
    ringingVideoCall(obj){
        AppDispatcher.dispatch({
            type:VideoCallingActionTypes.CALL_STATUS_RINGING_VIDEO_CALL,
            obj:obj
        });
    },
    
    cancelVideoCall(obj){
        AppDispatcher.dispatch({
            type:VideoCallingActionTypes.CALL_STATUS_CANCEL_VIDEO_CALL,
            obj:obj
        });
    },
    
    cancelVideoCallRTCSupported(obj){
        AppDispatcher.dispatch({
            type:VideoCallingActionTypes.CALL_STATUS_CANCEL_VIDEO_CALL_RTC,
            obj:obj
        });
    },
    
    endVideoCall(obj){
        AppDispatcher.dispatch({
            type:VideoCallingActionTypes.CALL_STATUS_END_VIDEO_CALL,
            obj:obj
        });
    },
    
    getUserInfo(user_info){
        AppDispatcher.dispatch({
            type:VideoCallingActionTypes.GET_USER_INFO,
            user_info: user_info
        });
    },
    
    updateCameraStream(stream){
        AppDispatcher.dispatch({
            type:VideoCallingActionTypes.UPDATE_CAMERA_STREAM,
            stream:stream
        });
    },
    
    stunTurnServerCallback(value){
        AppDispatcher.dispatch({
            type:VideoCallingActionTypes.STUN_TURN_SERVER,
            value:value
        });
    },
};
export default Actions;