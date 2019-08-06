import React from 'react';
//import _ from 'lodash';
import ChatWebAPIUtils from '../../utils/ChatWebAPIUtils';
import ActionVisibility from 'material-ui/svg-icons/action/visibility';
import ActionVisibilityOff from 'material-ui/svg-icons/action/visibility-off';




export default class MyCamera extends React.Component{
    constructor(props) {
        super(props);
    }

    state = {
        video:true,
    }
    componentDidMount(){
        this.bindVideoToStream();
    }
    componentDidUpdate(){
        this.bindVideoToStream();
    }
    bindVideoToStream =()=>{

        if(this.state.video){
            var obj = this.props.videoCallings.get("myStream");
            if( typeof obj != 'undefined' ){ // bug with lodash _.isEmpty
                if( obj !== null && typeof obj === 'object'){
                    this.video.srcObject = obj;
                }
            }else{
                setTimeout(function(){
                    ChatWebAPIUtils.getMediaStream();
                },100);
            }
        }
    }
    toggleMyAvatar =()=>{
        this.setState({video:!this.state.video})
    }
    styles = {
        myCamera:{
            bottom: '35px',
            height: '144px',
            overflow: 'hidden',
            position: 'absolute',
            right: '35px',
            textAlign: 'right',
            transition: 'bottom .3s',
            width: '256px',
            zIndex: 4,
            boxShadow: '0 1px 1px rgba(255,255,255,.2), 0 2px 4px rgba(0,0,0,.2)',
            borderRadius : '4px',   
            background:'#000',
        },
        myCameraOff:{
            bottom: '35px',
            height: '144px',
            overflow: 'hidden',
            position: 'absolute',
            right: '35px',
            textAlign: 'right',
            transition: 'bottom .3s',
            width: '256px',
            zIndex: 4,
        },
        myIcon:{
            zIndex: 5,
            position: 'absolute',
            boxShadow: '0 1px 1px rgba(255,255,255,.2), 0 2px 4px rgba(0,0,0,.2)',
            borderRadius : '4px',
            right:'15px',
            bottom:'15px',
            cursor:'pointer'
        },
        myVideo:{
            transform: 'translate(-50%, -50%)',
            maxWidth: '50px',
            minWidth: 0,
            height: '50px',
            width: '50px',
            left: '50%',
            top: '50%',
            transition: 'height .3s, width .3s, border-radius .3s, margin .3s',
            position: 'absolute',
            zIndex: 5,
            boxShadow: '0 0 0 200px black',
            borderRadius : '25px',   
        },
        iconColorFFF:{
            cursor:'pointer',
            color:"#fff"
        },
        iconColor000:{
            cursor:'pointer',
            color:"#000"
        }
    }
    render(){
        var wrap , video , icon ;
        
        video = <video style={{position:"static",width:"100%",height:"100%"}} ref={(input) => { this.video = input; }} autoPlay={true} playsInline={true} ></video>

        if(this.state.video) {
            icon  = <div style={this.styles.myIcon} ><ActionVisibility onClick={this.toggleMyAvatar} style={this.styles.iconColorFFF} /></div>;
            wrap = <div style={this.styles.myCamera}>{video}{icon}</div>;
        }
        else {
            icon  = <div style={this.styles.myIcon} ><ActionVisibilityOff onClick={this.toggleMyAvatar} style={this.styles.iconColor000} /></div>;
            wrap = <div style={this.styles.myCameraOff}>{icon}</div>;
        }
        return <div>{wrap}</div>;
    }
}




