import React from 'react';
import ChatWebAPIUtils from '../../utils/ChatWebAPIUtils';
import RoomStore from '../../stores/RoomStore';
import ViewerStore from '../../stores/ViewerStore';
var __ = require('../../utils/ChatMooI18n').i18next;


export default class ChatBlocked extends React.Component {
    constructor(props) {
        super(props);
    }
    handleUnBlockIsClicked = (e) =>{
        ChatWebAPIUtils.sendRequestUnblockMessages(this.props.room.id);
    }
    render(){
        var display = (RoomStore.isBlocked(this.props.room.id) && RoomStore.isBlocker(this.props.room.id,ViewerStore.get('id')))? "block":"none";
        return <div className="_54_-" style={{display:display}}>
            <div dangerouslySetInnerHTML={{__html:__.t("messages_from_name_are_blocked",{name:RoomStore.getName(this.props.room.id)})}}></div>  <a href="#" onClick={this.handleUnBlockIsClicked}>{__.t("unblock")}</a>
        </div>;
    }
};