import React from 'react';

import {redA700} from 'material-ui/styles/colors';
import Paper from 'material-ui/Paper';
import IconButton from 'material-ui/IconButton';
import AVCam  from 'material-ui/svg-icons/av/videocam';
import AVCamOff  from 'material-ui/svg-icons/av/videocam-off';
import AVMic  from 'material-ui/svg-icons/av/mic';
import AVMicOff  from 'material-ui/svg-icons/av/mic-off';
import NOPhone  from 'material-ui/svg-icons/notification/phone-missed';
import ACSettings  from 'material-ui/svg-icons/action/settings';
import NAFullScreen  from 'material-ui/svg-icons/navigation/fullscreen';
import NAFullScreenExit  from 'material-ui/svg-icons/navigation/fullscreen-exit';
import ImageSwitchCamera  from 'material-ui/svg-icons/image/switch-camera';

var __ = require('../../utils/ChatMooI18n').i18next;

export default  class Action  extends React.Component {
    state = {
        video: true,
        mic  : true,
        fullscreen:false,
        myAvatar:true,
    };
    translate = {
        enable_your_video:__.t("enable_your_video"),
        disable_your_video:__.t("disable_your_video"),
        mute_your_mic:__.t("mute_your_mic"),
        unmute_your_mic:__.t("unmute_your_mic"),
        end_call:__.t("end_call"),
        settings:__.t("settings"),
        rotateCamera:__.t("rotate_camera"),
        enter_full_screen:__.t("enter_full_screen"),
        exit_full_screen:__.t("exit_full_screen"),
    }
    style = {
        bottom: '35px',
        left: '50%',
        marginLeft: '-200px',
        position: 'absolute',
        textAlign: 'center',
        width: '400px',
        zIndex: '5'
    };
    styleMobile = {
        bottom: '15px',
        left: '0',
        marginLeft: '0',
        position: 'absolute',
        textAlign: 'center',
        width: '100%',
        zIndex: '5',
        backgroundColor:"none",
        boxShadow: "none",
    };
    styles = {
        smallIcon: {
            width: 36,
            height: 36,
        },
        smallIconDisabled: {
            width: 36,
            height: 36,
            color :this.props.currentTheme.palette.disabledColor,
        },
        smallIconRed: {
            width: 36,
            height: 36,
            color :redA700,
        },
        small: {
            width: 72,
            height: 72,
            padding: 16,
        }
    };
    styleMobiles = {
        smallIcon: {
            width: 36,
            height: 36,
        },
        smallIconDisabled: {
            width: 36,
            height: 36,
            color :this.props.currentTheme.palette.disabledColor,
        },
        smallIconRed: {
            width: 36,
            height: 36,
            color :redA700,
        },
        small: {
            width: 72,
            height: 72,
            padding: 16,
            borderRadius: "50%",
            background:"rgba(0, 0, 0, 0.4) none repeat scroll 0 0",
        },
        iconLeft: {
            position: "absolute",
            left:15,
        },
        iconRight: {
            position: "absolute",
            right:15,
        }
    };
    getIconStyle =(type)=>{
        var enable = true;
        switch(type){
            case "video":
                enable = this.state.video ;
                break;
            case "mic":
                enable = this.state.mic ;
                break;
            default:
        }

        return (enable ? this.styles.smallIcon:this.styles.smallIconDisabled)
    }
    getTooltipText=(type)=>{
        switch(type){
            case "video":
                return (this.state.video ? this.translate.enable_your_video:this.translate.disable_your_video);
            case "mic":
                return (this.state.mic ? this.translate.mute_your_mic:this.translate.unmute_your_mic);
            case "end":
                return  this.translate.end_call;
            case "settings":
                return this.translate.settings;
            case "rotateCamera":
                return this.translate.rotateCamera;
            case "fullscreen":
                return (this.state.fullscreen ? this.translate.exit_full_screen:this.translate.enter_full_screen);
            default:
                return (null);
        }

    }
    getIconTag=(type)=>{
        switch(type){
            case "video":
                return (this.state.video ? <AVCam/>:<AVCamOff/>);
            case "mic":
                return (this.state.mic ? <AVMic/>:<AVMicOff/>);
            case "fullscreen":
                return (this.state.fullscreen ? <NAFullScreen/>:<NAFullScreenExit/>);
            default:
        }
        return (null);
    }
    onCam =()=>{
        ChatWebAPIUtils.setMyCameraStreamEnable(!this.state.video);
        this.setState({video:!this.state.video})
    }
    onMic =()=>{
        ChatWebAPIUtils.setMyMicStreamEnable(!this.state.mic);
        this.setState({mic:!this.state.mic})
    }
    onEnd =()=>{
        ChatWebAPIUtils.sendRequestEndVideoCalling(this.props.room_id);
        window.close();
    }
    onSettings =()=>{
        ChatWebAPIUtils.openVideoCallSettingModal();
    }
    onFullScreen =()=>{
        this.setState({fullscreen:!this.state.fullscreen});
        if ((document.fullScreenElement && document.fullScreenElement !== null) ||
            (!document.mozFullScreen && !document.webkitIsFullScreen)) {
            if (document.documentElement.requestFullScreen) {
                document.documentElement.requestFullScreen();
            } else if (document.documentElement.mozRequestFullScreen) {
                document.documentElement.mozRequestFullScreen();
            } else if (document.documentElement.webkitRequestFullScreen) {
                document.documentElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
            }
        } else {
            if (document.cancelFullScreen) {
                document.cancelFullScreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitCancelFullScreen) {
                document.webkitCancelFullScreen();
            }
        }
    }
    roateCamera =()=>{
        console.log("roateCamera click");
    }

    render() {
        var fullscreen , setting , RotateCamera ;
        RotateCamera = fullscreen = setting = <div></div>;
        if(!this.props.isPhone) {
            fullscreen = <IconButton
                    iconStyle={this.styles.smallIcon}
                    style={this.styles.small}
                    onClick={this.onFullScreen}
                    tooltipPosition="top-center"
                    tooltip={this.getTooltipText("fullscreen")}
                >
                    {this.getIconTag("fullscreen")}
                </IconButton>;
             
            setting = <IconButton
                    iconStyle={this.styles.smallIcon}
                    style={this.styles.small}
                    onClick={this.onSettings}
                    tooltipPosition="top-center"
                    tooltip={this.getTooltipText("settings")}
                >
                    <ACSettings />
                </IconButton>; 
        }
        else {
            RotateCamera = <div style={{position:"absolute",right:"10px",top:"10px",zIndex:"5",borderRadius:"50%"}}><IconButton
                    iconStyle={this.styles.smallIcon}
                    style={this.styles.small}
                    onClick={this.roateCamera}
                    tooltipPosition="bottom-center"
                    tooltip={this.getTooltipText("rotateCamera")}
                >
                    <ImageSwitchCamera />
                    </IconButton></div>; 
        }
        
        return (
            <div>
            <Paper zDepth={1} style={!(this.props.isPhone) ? this.style : this.styleMobile } >
                <IconButton
                    iconStyle={this.getIconStyle("video")}
                    style={!(this.props.isPhone) ? this.styles.small : Object.assign({},this.styleMobiles.small,this.styleMobiles.iconLeft)}
                    onClick={this.onCam}
                    tooltipPosition="top-center"
                    tooltip={!this.props.isPhone ? this.getTooltipText("video") : ""}
                >
                    {this.getIconTag("video")}
                </IconButton>

                <IconButton
                    iconStyle={this.getIconStyle("mic")}
                    style={!(this.props.isPhone) ? this.styles.small : Object.assign({},this.styleMobiles.small)}
                    onClick={this.onMic}
                    tooltipPosition="top-center"
                    tooltip={!this.props.isPhone ? this.getTooltipText("mic") : ""}
                >
                    {this.getIconTag("mic")}
                </IconButton>

                <IconButton
                    iconStyle={this.styles.smallIconRed}
                    style={!(this.props.isPhone) ? this.styles.small : Object.assign({},this.styleMobiles.small,this.styleMobiles.iconRight)}
                    onClick={this.onEnd}
                    tooltipPosition="top-center"
                    tooltip={!this.props.isPhone ? this.getTooltipText("end") : ""}
                >
                    <NOPhone />
                </IconButton>

                {setting}

                {fullscreen}

            </Paper>
            </div>
        );
    }
}




