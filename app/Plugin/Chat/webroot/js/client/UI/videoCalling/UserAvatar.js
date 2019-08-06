import React from 'react';
import {CardMedia} from 'material-ui/Card';
var __ = require('../../utils/ChatMooI18n').i18next;
import VideoCallingStore from'../../stores/VideoCallingStore';
import CHAT_CONSTANTS from '../../constants/ChatConstants';
import ChatWebAPIUtils from '../../utils/ChatWebAPIUtils';

export default class UserAvatar extends React.Component{
    constructor(props) {
        super(props);
    }
    locate = {
        contacting:__.t("contacting"),
        ringing: __.t("ringing"),
        no_answer: __.t("no_answer"),
        cant_connect_call: __.t("cant_connect_call"),
        call_ended: __.t("call_ended")
    }
    styles = {
        holder:{
            transform: 'translate(-50%, -50%)',
            left: '50%',
            top: '50%',
            position: 'absolute',
            zIndex: 3,
        },
        userAvatar : {
            borderColor: '#fff',
            borderRadius: '100%',
            borderStyle: 'solid',
            borderWidth: '3px',
            height: '192px',
            marginLeft: 'auto',
            marginRight: 'auto',
            width: '192px',
            minWidth: '0',
            display:"block"
        },
        userName : {
            color: '#fff',
            fontFamily: 'Helvetica, Arial, sans-serif',
            fontSize: '16px',
            fontStyle: 'normal',
            fontWeight: '100',
            margin: '16px auto 8px',
            textAlign : 'center',
        },
        contacting : {
            textAlign : 'center',
            color: '#fff',
            fontFamily: 'Helvetica, Arial, sans-serif',
            fontSize: '14px',
            fontStyle: 'normal',
            fontWeight: '100',
            marginBottom: '8px',
        },
    }
    styleMobiles = {
        holder:{
            transform: 'translate(-50%, -20%)',
            left: '50%',
            top: '20%',
            position: 'absolute',
            zIndex: 3,
        },
        userAvatar : {
            borderColor: '#242424',
            borderRadius: '50%',
            borderStyle: 'solid',
            borderWidth: '1px',
            height: '100px',
            marginLeft: 'auto',
            marginRight: 'auto',
            width: '100px',
            minWidth: '0',
            display:"block"
        },
        userName : {
            color: '#fff',
            fontFamily: 'Helvetica, Arial, sans-serif',
            fontSize: '20px',
            fontStyle: 'normal',
            fontWeight: '100',
            margin: '16px auto 8px',
            textAlign : 'center',
        },
    }
    render(){
        if (this.props.hasRemoteStream){
            return (null);
        }
        var callStatus = this.locate.contacting;
        switch(this.props.callStatusCode){
            case "":
                callStatus = "";
                break;
            case CHAT_CONSTANTS.CALL_STATUS.RINGING:
                callStatus = this.locate.ringing;
                break;
            case CHAT_CONSTANTS.CALL_STATUS.NOANSWER:
                callStatus = this.locate.no_answer;
                break;
            case CHAT_CONSTANTS.CALL_STATUS.RTC_UNSUPPORTED:
                callStatus = this.locate.cant_connect_call;
                break;
            case CHAT_CONSTANTS.CALL_STATUS.CALL_ENDED:
                callStatus = this.locate.call_ended;
                break;
        }

        var wrap ;
        var userInfo = this.props.userInfo;
        var userAvatar = "";
        var userName = "";
        if(userInfo != null){
            userAvatar = <CardMedia style={{position:"static"}} ><img  style={!(this.props.isPhone) ? this.styles.userAvatar : this.styleMobiles.userAvatar} src={userInfo.avatar}/></CardMedia>
            userName = <div style={!(this.props.isPhone) ? this.styles.userName : this.styleMobiles.userName }>{userInfo.name}</div>
        }
        wrap = <div style={!(this.props.isPhone) ? this.styles.holder : this.styleMobiles.holder}>
            {userAvatar}
            {userName}
            <div style={this.styles.contacting}>
                {callStatus}
            </div>
        </div>
        return <div>{wrap}</div>;
    }
}




