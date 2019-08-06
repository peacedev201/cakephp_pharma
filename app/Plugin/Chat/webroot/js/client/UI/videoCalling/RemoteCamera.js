import React from 'react';
import _ from 'lodash';
import ChatWebAPIUtils from '../../utils/ChatWebAPIUtils';
import ActionVisibility from 'material-ui/svg-icons/action/visibility';
import ActionVisibilityOff from 'material-ui/svg-icons/action/visibility-off';




export default class RemoteCamera extends React.Component{
    constructor(props) {
        super(props);
    }
    componentDidUpdate(prevProps, prevState) {
        if (this.props.hasRemoteStream){
            this.video.srcObject = this.props.videoCallings.get("remoteStream");
        }
    }
    styles = {
        mainStyle:{
            backgroundRepeat:"no-repeat",
            backgroundSize:"cover",
            display: 'block',
            width:'100%',
            height: '100%',
            position: 'absolute',
            zIndex : 1,
        },
        pfBg:{
            background: 'black none repeat scroll 0 0',
            bottom: 0,
            left: 0,
            opacity: '0.8',
            position: 'absolute',
            right: 0,
            top: 0,
        },
    }
    render(){
        var mainStyle = _.clone(this.styles.mainStyle);

        if (!this.props.hasRemoteStream){
            var userInfo = this.props.userInfo;
            var userAvatar = "";
            if(userInfo != null)
            {
                userAvatar = userInfo.avatar;
            }
            _.assign(mainStyle,{
                backgroundImage:"url('" + (userAvatar) + "')",
                WebkitFilter: 'blur(90px)',
                filter: 'blur(90px)',
            });
        }
        return <video ref={(input) => { this.video = input; }} style={mainStyle} autoPlay={true} playsInline={true}>
            <div style={this.styles.pfBg}></div>
        </video>;
    }
}




