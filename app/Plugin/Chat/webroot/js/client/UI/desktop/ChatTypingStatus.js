import React from 'react';
import Avatar from './../Avatar';
import UserStore from '../../stores/UserStore';

export default  class ChatTypingStatus extends React.Component {
    render(){
        if(this.props.room.hasOwnProperty("isTyping")){
            if(this.props.room.isTyping.length>0){
                var avatars = [];
                for (var i=0;i<this.props.room.isTyping.length;i++){
                    // For debug
                    //this.props.room.isTyping = [1,2,3,4,5];
                    var chatter =  (UserStore.get(this.props.room.isTyping[i]));
                    if (chatter != null){
                        avatars.push(<a key={i} className="moochat_floatL" href="">
                            <Avatar className="ccmsg_avatar" src={chatter.avatar} title={chatter.name}/>
                        </a>);
                    }

                }
                return <div className="chatTyping_content">{avatars}
                    <div className="chatTyping_text">
                        <div className="_5pd7"></div>
                        <div className="_5pd7"></div>
                        <div className="_5pd7"></div>
                    </div>
                </div>;
                //var key = (this.props.room.isTyping.length == 1)?"name_is_typing":"names_are_typing";
                //return <div >{__.t(key,{name:UserStore.getNames(this.props.room.isTyping)})}</div>;
            }else{
                return (null);
            }
        }else{
            return (null);
        }

    }
};