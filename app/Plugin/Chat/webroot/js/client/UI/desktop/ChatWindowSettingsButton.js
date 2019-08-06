import React from 'react';
import ChatWebAPIUtils from '../../utils/ChatWebAPIUtils';
import ChatMooUtils from '../../utils/ChatMooUtils';
import RoomStore from '../../stores/RoomStore';
import ViewerStore from '../../stores/ViewerStore';
var __ = require('../../utils/ChatMooI18n').i18next;

var _clickElement = function (id) {
    var evt = new MouseEvent("click", {
        view: window,
        bubbles: true,
        cancelable: true
    });
    document.getElementById(id).dispatchEvent(evt);
};

export default class ChatWindowSettingsButton extends React.Component {
    constructor(props) {
        super(props);
        this.state = {isShowChatSettings: false };
    }
    handleChatSettingsClick = (e) =>{
        e.stopPropagation();
        this.setState({isShowChatSettings: !this.state.isShowChatSettings}); //, isScrollToBottom: false
    }
    handleSeeFullConversationClicked = (e) =>{
        this.handleChatSettingsClick(e);
        window.location.href = ChatMooUtils.getChatFullConversationURL() + "/" + this.props.roomId;
    }
    handleAddFilesClicked = (e) => {
        this.handleChatSettingsClick(e);
        _clickElement('moochat-add-files-button-' + this.props.roomId);

    }
    handleAddFriendsToChatClicked = (e) =>{
        this.handleChatSettingsClick(e);
        this.props.showAddFriend(e);
    }
    handleDeleteConversationClicked = (e) =>{
        this.handleChatSettingsClick(e);

        //if (confirm("Delete This Entire Conversation?")) {
        //    ChatWebAPIUtils.sendRequestDeleteConversation(this.props.roomId);
        //}
        var rId = this.props.roomId;
        ChatWebAPIUtils.openAlertYesNoModal({
            title:__.t("delete_this_entire_conversation"),
            body:__.t("once_you_delete_your_copy____it_can_not_done") ,
            noButton:__.t("cancel"),
            yesButton:__.t("delete_conversation"),
            callback:function () {
                ChatWebAPIUtils.sendRequestDeleteConversation(rId);
            }
        });
    }
    handleBlockMessagesClicked = (e) =>{
        this.handleChatSettingsClick(e);
        var rId = this.props.roomId;
        ChatWebAPIUtils.openAlertYesNoModal({
            title:__.t("block_messages"),
            body:__.t("stop_getting_messages_from",{name:RoomStore.getName(rId)}) ,
            noButton:__.t("cancel"),
            yesButton:__.t("button_block_messages"),
            callback:function () {
                ChatWebAPIUtils.sendRequestBlockMessages(rId);
            }
        });
    }
    handleUnblockMessagesClicked = (e) =>{
        this.handleChatSettingsClick(e);
        ChatWebAPIUtils.sendRequestUnblockMessages(this.props.roomId);
    }
    handleLeaveConversationClicked = (e) =>{
        this.handleChatSettingsClick(e);
        var rId = this.props.roomId;
        ChatWebAPIUtils.openAlertYesNoModal({
            title:__.t("leave_conversation"),
            body:__.t("you_will_stop_receiving_messages") ,
            noButton:__.t("cancel"),
            yesButton:__.t("button_leave_conversation"),
            callback:function () {
                ChatWebAPIUtils.sendRequestLeaveConversation(rId);
            }
        });
    }
    handleReportAsSpamClicked = (e) =>{
        this.handleChatSettingsClick(e);
        ChatWebAPIUtils.openReportModal(this.props.roomId);
    }

    render(){
        var displayChatGroup = (ChatMooUtils.isAllowedChatGroup())?"block":"none";
        var displaySendFiles = (ChatMooUtils.isAllowedSendFiles())?"block":"none";
        var moochat_popup_plugins_style = (this.state.isShowChatSettings) ? "block" : "none";
        var specialMenu = "";
        if (typeof this.props.members != 'undefined') {
            if (this.props.members.length == 1) {
                // User menu
                if(RoomStore.isBlocked(this.props.roomId) && RoomStore.isBlocker(this.props.roomId,ViewerStore.get('id'))){
                    specialMenu = <div className="moochat_plugins_dropdownlist">
                        <div className="moochat_pluginsicon moochat_chathistory moochat_floatL">
                            <i className="material-icons">info</i>
                        </div>
                        <div className="moochat_plugins_name moochat_floatL" onClick={this.handleUnblockMessagesClicked}>{__.t("unblock_messages")}</div>
                    </div>;
                }else{
                    specialMenu = <div className="moochat_plugins_dropdownlist">
                        <div className="moochat_pluginsicon moochat_chathistory moochat_floatL">
                            <i className="material-icons">info</i>
                        </div>
                        <div className="moochat_plugins_name moochat_floatL" onClick={this.handleBlockMessagesClicked}>{__.t("block_messages")}</div>
                    </div>;
                }

            } else {
                // Group menu
                specialMenu = <div className="moochat_plugins_dropdownlist">
                    <div className="moochat_pluginsicon moochat_chathistory moochat_floatL">
                        <i className="material-icons">info</i>
                    </div>
                    <div className="moochat_plugins_name moochat_floatL" onClick={this.handleLeaveConversationClicked}>{__.t("menu_leave_conversation")}</div>
                </div>;
            }
        }

        return <div className="moochat_plugins_dropdown moochat_floatR">
            <div className="moochat_plugins_dropdown_icon moochat_tooltip"
                 onClick={this.handleChatSettingsClick} data-tip={__.t("options")} data-place="top" >
                <i className="material-icons">arrow_drop_down</i>
            </div>
            <div className="moochat_popup_plugins" style={{display:moochat_popup_plugins_style }}>

                <div className="moochat_plugins moochat_floatR" style={{overflow: 'hidden'}}>
                    <div className="moochat_plugins_dropdownlist">
                        <div className="moochat_pluginsicon moochat_clearconversation moochat_floatL">
                            <i className="material-icons">view_headline</i>
                        </div>
                        <div className="moochat_plugins_name moochat_floatL"
                             onClick={this.handleSeeFullConversationClicked}>{__.t("see_full_conversation")}</div>
                    </div>
                    <div className="moochat_plugins_dropdownlist" style={{display: displaySendFiles}}>
                        <div className="moochat_pluginsicon moochat_audiochat moochat_floatL">
                            <i className="material-icons">attachment</i>
                        </div>
                        <div className="moochat_plugins_name moochat_floatL" onClick={this.handleAddFilesClicked}>{__.t("add_files")}</div>
                    </div>
                    <div className="moochat_plugins_dropdownlist" style={{display:displayChatGroup}}>
                        <div className="moochat_pluginsicon moochat_block moochat_floatL">
                            <i className="material-icons">group_add</i>
                        </div>
                        <div className="moochat_plugins_name moochat_floatL"
                             onClick={this.handleAddFriendsToChatClicked}>{__.t("add_friends_to_chat")}</div>
                    </div>
                    <div className="moochat_plugins_dropdownlist">
                        <div className="moochat_pluginsicon moochat_broadcast moochat_floatL">
                            <i className="material-icons">delete</i>
                        </div>
                        <div className="moochat_plugins_name moochat_floatL"
                             onClick={this.handleDeleteConversationClicked}>{__.t("delete_conversation")}</div>
                    </div>
                    {specialMenu}

                    <div className="moochat_plugins_dropdownlist">
                        <div className="moochat_pluginsicon moochat_chathistory moochat_floatL">
                            <i className="material-icons">flag</i>
                        </div>
                        <div className="moochat_plugins_name moochat_floatL" onClick={this.handleReportAsSpamClicked}>{__.t("report_as_spam_or_abuse")} </div>
                    </div>

                </div>

            </div>
        </div>;
    }
};