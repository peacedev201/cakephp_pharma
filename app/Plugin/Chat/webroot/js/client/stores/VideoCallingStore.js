'use strict';

import Immutable from 'immutable';
import {ReduceStore} from 'flux/utils';

import ChatWebAPIUtils from '../utils/ChatWebAPIUtils';

import VideoCallingActionTypes from '../actions/VideoCallingActionTypes';


import AppDispatcher from '../dispatcher/ChatAppDispatcherES2015';

import _ from 'lodash';
import SimplePeer from 'simple-peer';
import CHAT_CONSTANTS from '../constants/ChatConstants';
import DetectRTC  from 'detectrtc';

var constraints = window.constraints = {
    audio: true,
    video: true
};
var myCallingStream ;
var remoteCallingStream;
var isGettingVideoFromWebcam = false;
var isErrorWhenGetVideoStream = false;
var cameraAvailable = null;
function handleSuccess(stream) {
    isGettingVideoFromWebcam = false;
    var videoTracks = stream.getVideoTracks();
    //console.log('Got stream with constraints:', constraints);
    //console.log('Using video device: ' + videoTracks[0].label);
    stream.oninactive = function() {
        //console.log('Stream inactive');
    };
    window.myCallingStream = myCallingStream = stream; // make variable available to browser console
    setTimeout(function(){
        ChatWebAPIUtils.updateCameraStream(stream);
        ChatWebAPIUtils.setMediaStream(stream);
    },100);

}

function handleError(error) {
    isGettingVideoFromWebcam = false;
    isErrorWhenGetVideoStream = true;
    window.myCallingStream = myCallingStream = false;
    setTimeout(function(){ChatWebAPIUtils.setMediaStream(false);},100);
    if (error.name === 'ConstraintNotSatisfiedError') {
        errorMsg('The resolution ' + constraints.video.width.exact + 'x' +
            constraints.video.width.exact + ' px is not supported by your device.');
    } else if (error.name === 'PermissionDeniedError') {
        errorMsg('Permissions have not been granted to use your camera and ' +
            'microphone, you need to allow the page access to your devices in ' +
            'order for the demo to work.');
    }
    errorMsg('getUserMedia error: ' + error.name, error);
}

function errorMsg(msg, error) {
    console.log("errorMsg",msg)
    isGettingVideoFromWebcam = false;
    if (typeof error !== 'undefined'){
        //console.error(error);
    }
}

class VideoCallingStore extends ReduceStore {
    constructor() {
        super(AppDispatcher);
    }
    peer = null ;
    stunTurnServer = "";
    getInitialState() {
        DetectRTC.load(function(){});
        this.currentCallStatusCode = CHAT_CONSTANTS.CALL_STATUS.CONTACTING;
        return Immutable.Map({
            peer:this.peer,
            myStream:myCallingStream,
            remoteStream:remoteCallingStream,
            currentCallStatusCode:this.currentCallStatusCode,
            userInfo: null,
            hasCamera: cameraAvailable
        });
    }
    /**
     *
     * @param initiator {boolean}
     * @param obj {object}  {
     *              rId:roomId,
     *              members:array,
     *              senderId:caller userId,
     *              senderSocketId: socket id of caller
     *            }
     * @private
     */
    _createPeer(initiator,token){
        if(typeof myCallingStream != 'undefined'){
            console.log(this.stunTurnServer);
            console.log("aaaaaaa");
            //console.log("_createPeer",myCallingStream);
            this.peer = new SimplePeer({ 
                initiator: initiator,
                stream:myCallingStream, 
                trickle: true, 
                offerConstraints: {
                    /* This is hacking incase the initator don't have camare and mic */
                    mandatory: {
                        OfferToReceiveAudio: true,
                        OfferToReceiveVideo: true
                    }
                }, 
                config: this.stunTurnServer == "" ? {} : {
                    'iceServers': JSON.parse(this.stunTurnServer)
                }
            });
            this.peer.on('error', function (err) { console.log('error', err) });
            this.peer.on('signal', function (data) {
                //console.log('SIGNAL', JSON.stringify(data));
                ChatWebAPIUtils.sendSignal({
                    receiverSocketId:token,
                    signal:data,
                });
            });
            this.peer.on('connect', function () {
                //console.log('_createPeer CONNECT')
                this.peer.send('createVideoPeer ' + Math.random())
            }.bind(this));
            
            this.peer.on('data', function (data) {
                //console.log('data: ' + data)
                try
                {
                    var sdp = JSON.parse(data).sdp;
                    if(sdp)
                    {
                        this._pc.setRemoteDescription(new RTCSessionDescription(sdp));
                    }
                }
                catch(e)
                {
                   
                }
            });
            this.peer.on('stream', function (stream) {
                //console.log('_createPeer peer.on stream: ' + stream)
                // got remote video stream, now let's show it in a video tag
                // var video = document.querySelector('video')
                //video.src = window.URL.createObjectURL(stream)
                //video.play()
                window.remoteCallingStream = remoteCallingStream = stream ;
                ChatWebAPIUtils.setMediaRemoteStream(stream);
            })
        }else{
            this._getMeidaStream();
            setTimeout(function(){this._createPeer(initiator,token)}.bind(this,initiator,token),100);
        }


    }

    /**
     * This function will get singal data type offer or anwser from peer then uses it for creating connect
     * @param obj {object} {signal: peer's signal data , senderSocketId : peer's socket id}
     * @private
     */
    _receiveSignal(obj){
        if(typeof myCallingStream != 'undefined' ){
            if ( _.isEmpty(this.peer)){
                console.log(this.stunTurnServer);
                console.log("bbbbbbbb");
                //console.log("_receiveSignal create SimplePeer",myCallingStream)
                this.peer = new SimplePeer({ 
                    initiator: false, 
                    trickle: true,
                    stream:myCallingStream /*,answerConstraints: {
                    mandatory: {
                        OfferToReceiveAudio: false,
                        OfferToReceiveVideo: false
                    }}*/ , 
                    config: this.stunTurnServer == "" ? {} : {
                        'iceServers': JSON.parse(this.stunTurnServer)
                    }
                });
                this.peer.on('signal', function (data) {
                    //console.log('SIGNAL', JSON.stringify(data) ,myCallingStream);
                    ChatWebAPIUtils.sendSignal({
                        receiverSocketId:_.get(obj,"senderSocketId"),
                        signal:data,
                    });
                });
                this.peer.on('connect', function () {
                    //console.log('_receiveSignal CONNECT')
                    this.peer.send('send form _receiveSignal' + Math.random())
                }.bind(this));

                this.peer.on('data', function (data) {
                    //console.log('data: ' + data)
                    try
                    {
                        var sdp = JSON.parse(data).sdp;
                        if(sdp)
                        {
                            this._pc.setRemoteDescription(new RTCSessionDescription(sdp));
                        }
                    }
                    catch(e)
                    {

                    }
                });
                this.peer.on('stream', function (stream) {
                    //console.log('peer.on stream: ' + stream)
                    // got remote video stream, now let's show it in a video tag
                    // var video = document.querySelector('video')
                    //video.src = window.URL.createObjectURL(stream)
                    //video.play()
                    window.remoteCallingStream = remoteCallingStream = stream ;
                    ChatWebAPIUtils.setMediaRemoteStream(stream);
                })
            }
            this.peer.signal(_.get(obj,"signal"));
        }else{
            this._getMeidaStream();
            setTimeout(function(){this._receiveSignal(obj)}.bind(this,obj),100);

        }

    }

    /**
     * This function will get stream which is used for streamming peer-to-peer video calling action
     * @private
     */
    _getMeidaStream(obj){
        if ( !isGettingVideoFromWebcam && _.isEmpty(myCallingStream)){
            if(typeof obj != "undefined")
            {
                constraints.video = {deviceId: obj.videoSource ? {exact: obj.videoSource} : undefined};
            }
            isGettingVideoFromWebcam = true;
            navigator.mediaDevices.getUserMedia(constraints).
            then(handleSuccess).catch(handleError);
        }

    }
	
    _ringingVideoCall(obj){
        this.currentCallStatusCode = CHAT_CONSTANTS.CALL_STATUS.RINGING;
    }
    
    _cancelVideoCall(obj){
        this.currentCallStatusCode = CHAT_CONSTANTS.CALL_STATUS.NOANSWER;
    }
    
    _cancelVideoCallRTC(obj){
        this.currentCallStatusCode = CHAT_CONSTANTS.CALL_STATUS.RTC_UNSUPPORTED;
    }
    
    _endVideoCall(obj){
        this.currentCallStatusCode = CHAT_CONSTANTS.CALL_STATUS.CALL_ENDED;
        setTimeout(function(){ChatWebAPIUtils.setMediaStream(false);},100);
    }
    
    /*onDispatcherAction(payload) {

        var action = payload.action;

        if (ActionTypes.SEND_REQUEST === action.type) {
            this.stateStuff.loading = true;
            this.emitChange();
        }

        if (ActionTypes.RECEIVE_RESPONSE === action.type) {
            this.stateStuff.loading = false;
            this.stateStuff.objectFoo = action.data.objectFoo;
            this.stateStuff.arrayOfBaz = action.data.arrayOfBaz;
            this.emitChange();
        }
    }*/

    /**
     * Turn on/off my camera when streaming
     * @param state - boolean
     * @private
     */
    _changeStateMyCamera(state){
        if ( typeof myCallingStream != 'undefined'){
            state ? myCallingStream.getVideoTracks()[0].enabled=true:myCallingStream.getVideoTracks()[0].enabled=false
        }

    }
    
    _updateCameraStream(stream){
        //window.myCallingStream = myCallingStream = stream; // make variable available to browser console
        //setTimeout(function(){ChatWebAPIUtils.setMediaStream(stream);},100);
        if(this.peer != null)
        {
            var temp_peer = this.peer;
            this.peer._pc.removeStream(myCallingStream);
            this.peer._pc.addStream(stream);
            this.peer._pc.createOffer()
            .then(function (offer) {
                temp_peer._pc.setLocalDescription(offer);
            })
            .then(function () {
                temp_peer.send(JSON.stringify({ "sdp": temp_peer._pc.localDescription }));
            });
        }
    }

    /**
     * Turn on/off my mic when streaming
     * @param state - boolean
     * @private
     */
    _changeStateMyMic(state){
        if ( typeof myCallingStream != 'undefined'){
            state ? myCallingStream.getAudioTracks()[0].enabled=true:myCallingStream.getAudioTracks()[0].enabled=false
        }
    }
    
    _stunTurnServerCallback(value){
        console.log(value);
        this.stunTurnServer = value;
    }
    reduce(state, action) {

        switch (action.type) {
            case VideoCallingActionTypes.CREATE_INITIATOR_PEER:
                this._createPeer(true,action.token);
                return state;
            case VideoCallingActionTypes.RECEIVE_SIGNAL_FROM_PEER:
                this._receiveSignal(action.obj);
                return state;
            case VideoCallingActionTypes.GET_MEDIA_STREAM:
                this._getMeidaStream(action.obj);
                return state;
            case VideoCallingActionTypes.SET_MEDIA_STREAM:
                state = state.set("myStream",action.stream);
                return state;
            case VideoCallingActionTypes.SET_MEDIA_REMOTE_STREAM:
                state = state.set("remoteStream",action.stream);
                return state;
            case VideoCallingActionTypes.SET_MY_CAMERA_ENABLE:
                this._changeStateMyCamera(action.is);
                return state;
            case VideoCallingActionTypes.SET_MY_MIC_ENABLE:
                this._changeStateMyMic(action.is)
                return state;
            case VideoCallingActionTypes.CALL_STATUS_RINGING_VIDEO_CALL:
                this._ringingVideoCall(action.obj)
                state = state.set("currentCallStatusCode", this.currentCallStatusCode);
                return state;
            case VideoCallingActionTypes.CALL_STATUS_CANCEL_VIDEO_CALL:
                this._cancelVideoCall(action.obj)
                state = state.set("currentCallStatusCode", this.currentCallStatusCode);
                return state;
            case VideoCallingActionTypes.CALL_STATUS_CANCEL_VIDEO_CALL_RTC:
                this._cancelVideoCallRTC(action.obj)
                state = state.set("currentCallStatusCode", this.currentCallStatusCode);
                return state;
            case VideoCallingActionTypes.CALL_STATUS_END_VIDEO_CALL:
                this._endVideoCall(action.obj)
                state = state.set("currentCallStatusCode", this.currentCallStatusCode);
                return state;
            case VideoCallingActionTypes.GET_USER_INFO:
                //this.userInfo = action.user_info;
                var user_info = action.user_info;
                if(user_info != null){
                    user_info.avatar = ChatWebAPIUtils.getAvatarLinkFromDataUser(user_info, "200_square_");
                }
                state = state.set("userInfo", action.user_info);
                
                //check has camera
                if(DetectRTC.videoInputDevices.length > 0){
                    cameraAvailable = true;
                }
                else{
                    cameraAvailable = false;
                }
                state = state.set("hasCamera", cameraAvailable);
                return state;
            case VideoCallingActionTypes.UPDATE_CAMERA_STREAM:
                this._updateCameraStream(action.stream)
                return state;
            case VideoCallingActionTypes.STUN_TURN_SERVER:
                this._stunTurnServerCallback(action.value)
                return state;
            default:
                return state;
        }
    }
}

export default new VideoCallingStore();