import React from 'react';
import ChatWebAPIUtils from'../../utils/ChatWebAPIUtils';

import darkBaseTheme from 'material-ui/styles/baseThemes/darkBaseTheme';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import getMuiTheme from 'material-ui/styles/getMuiTheme';

import MobileDetect  from 'mobile-detect';

import MyCamera from './MyCamera';
import RemoteCamera from './RemoteCamera';
import UserAvatar from './UserAvatar';
import Actions from './Actions';
import VideoCallingStore from'../../stores/VideoCallingStore';
import CHAT_CONSTANTS from '../../constants/ChatConstants';
var __ = require('../../utils/ChatMooI18n').i18next;
import PoupWindow from '../PoupWindow';
import DetectRTC  from 'detectrtc';
/**
 * A simple example of `BottomNavigation`, with three labels and icons
 * provided. The selected `BottomNavigationItem` is determined by application
 * state (for instance, by the URL).
 */
var currentTheme =  getMuiTheme(darkBaseTheme);
var allowCameraPermission = false;
var promptCameraPermission = true;


class VideoCallingLayout extends React.Component {
     constructor(props) {
        super(props);
    }
    locale = {
        rtc_supported_user_isnt_using_browser: __.t("rtc_supported_user_isnt_using_browser"),
        cant_connect_call: __.t("cant_connect_call"),
        camera_permission_warning_title: __.t("camera_permission_warning_title"),
        camera_permission_warning_content: __.t("camera_permission_warning_content"),
        user_does_not_have_video_calling_permission: __.t("user_does_not_have_video_calling_permission"),
        no_camera_warning_message: __.t("no_camera_warning_message")
    }
    styles = {
        cameraPermissionWarning:{
            position: 'absolute',
            top: '10px',
            right: '5px',
            zIndex: 3,
            width: '300px',
            backgroundColor: '#FFFFFF',
            padding: '7px'
        },
        cameraPermissionWarningArrow:{
            width: '0',
            height: '0',
            borderLeft: '5px solid transparent',
            borderRight: '5px solid transparent',
            borderBottom: '5px solid #FFFFFF',
            top: '-5px',
            right: '5px',
            position: 'absolute'
        },
        cameraPermissionWarningLeft:{
            position: 'absolute',
            top: '10px',
            left: '30px',
            right:'0px',
            zIndex: 3,
            width: '300px',
            backgroundColor: '#FFFFFF',
            padding: '7px'
        },
        cameraPermissionWarningArrowLeft:{
            width: '0',
            height: '0',
            borderLeft: '5px solid transparent',
            borderRight: '5px solid transparent',
            borderBottom: '5px solid #FFFFFF',
            top: '-5px',
            left: '5px',
            right:'0px',
            position: 'absolute'
        }
    }
    componentWillMount(){
    }

    componentDidUpdate(prevProps, prevState) {
        
    }
    componentDidMount(){
        var user_id = this.props.user_id;
        var receiver_id = this.props.receiver_id;
        var caller_id = this.props.caller_id;
        var room_id = this.props.room_id;
        var members = this.props.members;
        var token = this.props.token;
        var requestCallingParams = {rId:room_id,members:members.split(',')};
            
        //end call if close window
        window.addEventListener("beforeunload", function (event) {
            ChatWebAPIUtils.sendRequestEndVideoCalling(room_id);
            window.close();
        })
        
        if (ChatWebAPIUtils.boot()) {
            DetectRTC.load(function() {
                
                if(DetectRTC.isWebsiteHasWebcamPermissions){
                    promptCameraPermission = false;
                    if(receiver_id > 0){
                        ChatWebAPIUtils.sendRequestGetVideoCallingToken(requestCallingParams);
                    }
                    else{
                        ChatWebAPIUtils.stunTurnServer(token);
                    }
                    ChatWebAPIUtils.callingPickup();
                    ChatWebAPIUtils.getUserInfo(user_id);
                }
                else{
                    navigator.getUserMedia({ audio: true, video: true},function (stream) {
                        allowCameraPermission = true;
                        promptCameraPermission = false;
                        if(receiver_id > 0){
                            ChatWebAPIUtils.sendRequestGetVideoCallingToken(requestCallingParams);
                        }
                        else{
                            ChatWebAPIUtils.stunTurnServer(token);
                        }
                        ChatWebAPIUtils.getUserInfo(user_id);
                        ChatWebAPIUtils.callingPickup();
                   }, function(){
                        allowCameraPermission = false;
                        promptCameraPermission = false;
                        ChatWebAPIUtils.getUserInfo(user_id);
                        ChatWebAPIUtils.sendRequestEndVideoCalling(room_id);
                   });
                }
            });
            
        }
    }
    hasRemoteStream() {
        var obj = this.props.videoCallings.get("remoteStream");
        return ( obj !== null && typeof obj === 'object') // bug with lodash _.isEmpty
    }
    getCallStatusCode() {
        return this.props.videoCallings.get("currentCallStatusCode");
    }
    getuserInfo() {
        return this.props.videoCallings.get("userInfo");
    }
    hasCamera(){
        return this.props.videoCallings.get("hasCamera");
    }
    render() {
        var md = new MobileDetect(window.navigator.userAgent);
        var userInfo = this.getuserInfo();
        var hasRemoteStream = this.hasRemoteStream();
        var callStatusCode = this.getCallStatusCode();
        var hasCamera = this.hasCamera();
        var myCamera = <div></div>;
        var isPhone = true;
        if(!md.os()) {
            myCamera = <MyCamera {...this.props}  />;
            isPhone = false;
        }
        
        if(promptCameraPermission){
            return "";
        }
        else if(hasCamera === true && !DetectRTC.isWebsiteHasWebcamPermissions && !allowCameraPermission){
            callStatusCode = "";
            var stylePermission = this.styles.cameraPermissionWarning;
            var stylePermissionArrow = this.styles.cameraPermissionWarningArrow
            if(DetectRTC.browser.name == "Firefox"){
                stylePermission = this.styles.cameraPermissionWarningLeft;
                stylePermissionArrow = this.styles.cameraPermissionWarningArrowLeft;
            }
            var cameraPermissionWarning = <div style={stylePermission}>
                <b>{this.locale.camera_permission_warning_title}</b>
                <div>{this.locale.camera_permission_warning_content}</div>
                <div style={stylePermissionArrow}></div>
            </div>;
            return <div>
                <MuiThemeProvider muiTheme={currentTheme}>
                    <div>
                        {cameraPermissionWarning}
                        <UserAvatar {...this.props} hasRemoteStream={hasRemoteStream} callStatusCode={callStatusCode} userInfo={userInfo} isPhone={isPhone}/>
                    </div>
                </MuiThemeProvider>
                <PoupWindow  />
            </div>;
        }
        else if(userInfo && !userInfo.allow_video_call){
            setTimeout(function(){
                window.close();
            }, CHAT_CONSTANTS.NO_CAMERA_CLOSE_POPUP_TIMEOUT);
            var warning_content = this.locale.user_does_not_have_video_calling_permission.replace('%s', userInfo.name);
            ChatWebAPIUtils.openAlertModal(this.locale.cant_connect_call, warning_content, false);
            callStatusCode = "";
            
            return <div>
                <MuiThemeProvider muiTheme={currentTheme}>
                    <div>
                        <UserAvatar {...this.props} hasRemoteStream={hasRemoteStream} callStatusCode={callStatusCode} userInfo={userInfo} isPhone={isPhone}/>
                    </div>
                </MuiThemeProvider>
                <PoupWindow  />
            </div>;
        }
        else if(hasCamera === true){
            var callActionBar = <Actions currentTheme={currentTheme} isPhone={isPhone} {...this.props} />;
            if(callStatusCode == CHAT_CONSTANTS.CALL_STATUS.NOANSWER || 
               callStatusCode == CHAT_CONSTANTS.CALL_STATUS.RTC_UNSUPPORTED ||
               callStatusCode == CHAT_CONSTANTS.CALL_STATUS.CALL_ENDED)
            {
                myCamera = "";
                callActionBar = "";
                hasRemoteStream = false;
            }
            if(callStatusCode == CHAT_CONSTANTS.CALL_STATUS.RTC_UNSUPPORTED){
                ChatWebAPIUtils.openAlertModal(this.locale.cant_connect_call, this.locale.rtc_supported_user_isnt_using_browser.replace("%s", userInfo.name));
            }
            return <div>
            <MuiThemeProvider muiTheme={currentTheme}>
                <div>
                    <RemoteCamera {...this.props} hasRemoteStream={hasRemoteStream} userInfo={userInfo}/>
                    <UserAvatar {...this.props} hasRemoteStream={hasRemoteStream} callStatusCode={callStatusCode} userInfo={userInfo} isPhone={isPhone}/>
                    {myCamera}
                    {callActionBar}
                </div>

            </MuiThemeProvider>
            <PoupWindow  />
            </div>;
        }
        else if(hasCamera === false){
            setTimeout(function(){
                window.close();
            }, CHAT_CONSTANTS.NO_CAMERA_CLOSE_POPUP_TIMEOUT);
            ChatWebAPIUtils.openAlertModal(this.locale.cant_connect_call, this.locale.no_camera_warning_message, false);
            callStatusCode = "";
            return <div>
                <MuiThemeProvider muiTheme={currentTheme}>
                    <div>
                        <UserAvatar {...this.props} hasRemoteStream={hasRemoteStream} callStatusCode={callStatusCode} userInfo={userInfo} isPhone={isPhone}/>
                    </div>
                </MuiThemeProvider>
                <PoupWindow  />
            </div>;
        }
        return "";
    }
}
function VideoCallingView(props) {
    return <VideoCallingLayout {...props} />
}

export default VideoCallingView;



