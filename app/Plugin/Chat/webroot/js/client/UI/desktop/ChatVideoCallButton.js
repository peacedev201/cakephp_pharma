import React from 'react';
import ChatWebAPIUtils from '../../utils/ChatWebAPIUtils';
import ChatMooUtils from '../../utils/ChatMooUtils';
import RoomStore from '../../stores/RoomStore';
var __ = require('../../utils/ChatMooI18n').i18next;

export default class ChatVideoCallButton extends React.Component {
    constructor(props) {
        super(props);
    }

    handleVideoChatClick = (e) => {
        e.stopPropagation();
        ChatWebAPIUtils.openAWindowForCalling(this.props.room);

    }
    render(){
        if(ChatMooUtils.isAllowedVideoCalling())
        {
            var content = <i className="material-icons">videocam</i>
            if(this.props.show_name)
            {
                content = <a>{this.props.show_name_title}</a>
            }
            return (RoomStore.isGroup(this.props.room.id) ? null: <div className="moochat_icon_more_2  moochat_icon_more_2_addfriend protip"
                         data-tip={__.t("start_a_video_chat_with",{name:RoomStore.getName(this.props.room.id)})}
                         data-place="top"
                         onClick={this.handleVideoChatClick}>
                    {content}
                </div>
            );
        }
        return "";
    }
}