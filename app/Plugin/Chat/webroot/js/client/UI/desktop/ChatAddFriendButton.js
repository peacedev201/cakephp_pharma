import React from 'react';
import ChatMooUtils from '../../utils/ChatMooUtils';
var __ = require('../../utils/ChatMooI18n').i18next;

export default class ChatAddFriendButton extends React.Component {
    constructor(props) {
        super(props);
    }

    render(){
        var displayChatGroup = (ChatMooUtils.isAllowedChatGroup())?"block":"none";
        return (<div className="moochat_icon_more_2  moochat_icon_more_2_addfriend protip" style={{display:displayChatGroup}}
                     data-tip={__.t("add_more_friends_to_chat")}
                     data-place="top"
                     onClick={this.props.handleAddFriendClick}>
                <i className="material-icons">group_add</i>
            </div>
        );
    }
}