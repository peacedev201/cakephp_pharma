import React from 'react';
import ReactDOM from 'react-dom';
import CHAT_CONSTANTS from '../constants/ChatConstants';
import FriendStore from '../stores/FriendStore';
import UserStore from '../stores/UserStore';
import RoomStore from '../stores/RoomStore';
import ViewerStore from '../stores/ViewerStore';
import MessageStore from '../stores/MessageStore';
import ChatWebAPIUtils from '../utils/ChatWebAPIUtils';
import ChatMooUtils from '../utils/ChatMooUtils';
import ReactTooltip from "react-tooltip";
var moment = require('../utils/ChatMooI18n').moment;
var __ = require('../utils/ChatMooI18n').i18next;
import validUrl from 'valid-url';
import Avatar from './Avatar';
import TextareaAutosize from 'react-textarea-autosize';
import ChatVideoCallButton from './desktop/ChatVideoCallButton';
import chatMooEmoji from "../utils/ChatMooEmoji";

var _typing = {};
var _timeout = {};
var _stopTyping = function(rId){
    if(_typing.hasOwnProperty(rId)){
        _typing[this.props.room.id] = false;
    }
    ChatWebAPIUtils.sendRequestStopTyping(rId);
}
var _clickElement = function (id) {

    var evt = new MouseEvent("click", {
        view: window,
        bubbles: true,
        cancelable: true
    });
    document.getElementById(id).dispatchEvent(evt);

};
class ChatWindowSettings extends React.Component {
    constructor(props) {
        super(props);
        this.state = {isShowChatSettings: false};
        this.handleChatSettingsClick = this.handleChatSettingsClick.bind(this);
        this.handleSeeFullConversationClicked = this.handleSeeFullConversationClicked.bind(this);
        this.handleAddFilesClicked = this.handleAddFilesClicked.bind(this);
        this.handleAddFriendsToChatClicked = this.handleAddFriendsToChatClicked.bind(this);
        this.handleDeleteConversationClicked = this.handleDeleteConversationClicked.bind(this);
        this.handleBlockMessagesClicked = this.handleBlockMessagesClicked.bind(this);
        this.handleUnblockMessagesClicked = this.handleUnblockMessagesClicked.bind(this);
        this.handleLeaveConversationClicked = this.handleLeaveConversationClicked.bind(this);
        this.handleReportAsSpamClicked = this.handleReportAsSpamClicked.bind(this);
        this.handleSelectAction = this.handleSelectAction.bind(this);
    }
    handleChatSettingsClick(e){
        this.setState({isShowChatSettings: !this.state.isShowChatSettings}); //, isScrollToBottom: false
    }
    handleSeeFullConversationClicked(e){

        window.location.href = ChatMooUtils.getChatFullConversationURL() + "/" + this.props.roomId;
    }
    handleAddFilesClicked(e){

        window._clickElement('moochat-add-files-button-' + this.props.roomId);

    }
    handleAddFriendsToChatClicked(e){

        this.props.showAddFriend(e);
    }
    handleDeleteConversationClicked(e){
        var rId = this.props.roomId;
        ChatWebAPIUtils.openAlertYesNoModal({
            title: __.t("delete_this_entire_conversation"),
            body : __.t("once_you_delete_your_copy____it_can_not_done"),
            noButton : __.t("cancel"),
            yesButton: __.t("delete_conversation"),
            callback :function () {
                ChatWebAPIUtils.sendRequestDeleteConversation(rId);
            }
        });
    }
    handleBlockMessagesClicked(e){
        var rId = this.props.roomId;
        ChatWebAPIUtils.openAlertYesNoModal({
            title    : __.t("block_messages"),
            body     : __.t("stop_getting_messages_from",{name:RoomStore.getName(rId)}) ,
            noButton : __.t("cancel"),
            yesButton: __.t("button_block_messages"),
            callback : function () {
                ChatWebAPIUtils.sendRequestBlockMessages(rId);
            }
        });
    }
    handleUnblockMessagesClicked(e){
        ChatWebAPIUtils.sendRequestUnblockMessages(this.props.roomId);
    }
    handleLeaveConversationClicked(e){
        var rId = this.props.roomId;
        var that= this;
        ChatWebAPIUtils.openAlertYesNoModal({
            title : __.t("leave_conversation"),
            body  : __.t("you_will_stop_receiving_messages") ,
            noButton : __.t("cancel"),
            yesButton: __.t("button_leave_conversation"),
            callback:function () {
                that.props.closeChatWindow();
                ChatWebAPIUtils.sendRequestLeaveConversation(rId);
            }
        });
    }
    handleReportAsSpamClicked(e){
        ChatWebAPIUtils.openReportModal(this.props.roomId);
    }
    handleVideoChatClick(e){
        ChatWebAPIUtils.openAWindowForCalling(this.props.room);
    }
    handleSelectAction(e){
        switch(e.target.value) {
            case "see_full_conversation":
                this.handleSeeFullConversationClicked(e);
                break;
            case "add_files":
                this.handleAddFilesClicked(e);
                break;
            case "add_friends_to_chat":
                this.handleAddFriendsToChatClicked(e);
                break;
            case "delete_conversation":
                this.handleDeleteConversationClicked(e);
                break;
            case "report_as_spam_or_abuse":
                this.handleReportAsSpamClicked(e);
                break;
            case "unblock_messages":
                this.handleUnblockMessagesClicked(e);
                break;
            case "block_messages":
                this.handleBlockMessagesClicked(e);
                break;
            case "menu_leave_conversation":
                this.handleLeaveConversationClicked(e);
                break;
            case "start_a_video_chat":
                this.handleVideoChatClick(e);
                break;
            default:

        }
    }
    render(){
        var displayChatGroup = (ChatMooUtils.isAllowedChatGroup())?"block":"none";
        var displaySendFiles = (ChatMooUtils.isAllowedSendFiles())?"block":"none";
        var moochat_popup_plugins_style = (this.state.isShowChatSettings) ? "block" : "none";
        var specialMenu = "";
        var videocall_button = "";
        if (typeof this.props.members != 'undefined') {
            if (this.props.members.length == 1) {
                // User menu
                if(RoomStore.isBlocked(this.props.roomId) && RoomStore.isBlocker(this.props.roomId,ViewerStore.get('id'))){
                    specialMenu = <option value="unblock_messages">{__.t("unblock_messages")}</option>;
                }else{
                    specialMenu = <option value="block_messages">{__.t("block_messages")}</option>;
                }

            } else {
                // Group menu
                specialMenu = <option value="menu_leave_conversation">{__.t("menu_leave_conversation")}</option>;
            }
        }
        if(ChatMooUtils.isAllowedVideoCalling()){
            videocall_button = <option value="start_a_video_chat">{__.t("start_a_video_chat")}</option>
        }
        return <select onChange={this.handleSelectAction} value="">
            <option >{__.t("chose_an_action")}</option>
            {videocall_button}
            <option value="see_full_conversation">{__.t("see_full_conversation")}</option>
            <option value="add_friends_to_chat">{__.t("add_friends_to_chat")}</option>
            <option value="delete_conversation">{__.t("delete_conversation")}</option>
            {specialMenu}
            <option value="report_as_spam_or_abuse">{__.t("report_as_spam_or_abuse")}</option>
        </select>;

    }
};
class ChatMooEmoji extends React.Component {
    constructor(props) {
        super(props);
        this.emojIsClicked = this.emojIsClicked.bind(this);
    }
    emojIsClicked(text){
        this.props.emojIsClicked(text);
    }
    render(){
        var emoj = [];
        var i = 0;
        ChatMooUtils.getEmojiJson().forEach(function (e) {
            i++;
            emoj.push(<span onClick={this.emojIsClicked.bind(this,e.emoji)} key={i} title={e.text}
                            className={e.class}></span>);
        }.bind(this));
        var display = (this.props.isShow) ? "block" : "none";
        var displayEmotion = (ChatMooUtils.isAllowedEmotion())?"block":"none";
        return (<div  style={{display:displayEmotion}} >
                <div className="moochat_buttonicon moochat_buttonemoticon chatwindow-emoji"
                     onClick={this.props.handleButtonEmojIsClicked}>
                    <i className="material-icons">insert_emoticon</i>
                </div>
                <div style={{display:display}} className="moochat_iconlist">{emoj}</div>
            </div>
        );
    }
};
class ChatBlocked extends React.Component {
    constructor(props) {
        super(props);
        this.handleUnBlockIsClicked = this.handleUnBlockIsClicked.bind(this);
    }
    handleUnBlockIsClicked(e){
        ChatWebAPIUtils.sendRequestUnblockMessages(this.props.room.id);
    }
    render(){
        var display = (RoomStore.isBlocked(this.props.room.id) && RoomStore.isBlocker(this.props.room.id,ViewerStore.get('id')))? "block":"none";
        return <div className="_54_- moochat_block_message" style={{display:display}} >
            <div dangerouslySetInnerHTML={{__html:__.t("messages_from_name_are_blocked",{name:RoomStore.getName(this.props.room.id)})}}></div>   <a href="#" onClick={this.handleUnBlockIsClicked}>{__.t("unblock")}</a>
        </div>;
    }
};
class ChatURLPreview extends React.Component {
    constructor(props) {
        super(props);
        this.handleRemoveLink = this.handleRemoveLink.bind(this);
    }
    handleRemoveLink(e){
        this.props.hanldeCloseLinkPreview(e);
    }
    render(){
        if(!this.props.data.show){
            return <div></div>;
        }else{

            var response = this.props.data.data;
            if(response.hasOwnProperty('result')){
                var data = response.result;

                var title = (data.hasOwnProperty("title"))?data.title:"";
                var description = (data.hasOwnProperty("description"))?data.description:"";
                var type = (data.hasOwnProperty("type"))?data.type:"";
                var img = (data.hasOwnProperty("image"))? <span style={{"backgroundImage":"url(" + data.image + ")"}} className="img_link"></span> :"";
                var code = (data.hasOwnProperty("code"))?data.code:"";
                if (title != "" || description !="" || type!="" || img != "" || code!=""){
                    return <div className="moochat_link_attach"><span  onClick={this.handleRemoveLink} className="remove_review_link"><i className="material-icons">clear</i></span> {img}<div className="linkcontent"><div style={{"fontWeight":"bold"}}>{title}</div><div className="link_description">{description}</div></div></div>;
                }else{
                    return <div></div>
                }
            }
            return <div className="moochat_iconlist"><div className="chat-spinner">
                <div className="bounce1"></div>
                <div className="bounce2"></div>
                <div className="bounce3"></div>
            </div></div>;
        }

    }
};
class ChatAddFriends extends React.Component {
    constructor(props) {
        super(props);
        this.engineBloodHoundCallback = this.engineBloodHoundCallback.bind(this);
        this.handleInputSuggestion = this.handleInputSuggestion.bind(this);
        this.handleDoneButtonIsClicked = this.handleDoneButtonIsClicked.bind(this);
        this.handleRemoveUserIsChoosen = this.handleRemoveUserIsChoosen.bind(this);
        this.handleAddUserIsChoosen = this.handleAddUserIsChoosen.bind(this);
        this.state = {
            usersIsChoosen: [],
            usersSuggestion: [],
            userSuggestionText: '',
            isFocusUserSuggestionText: false,
            runSearchEngine: false
        }
    }
    engineBloodHoundCallback(users) {
        this.setState({usersSuggestion: users});
    }
    handleInputSuggestion(e){
        var name = e.target.value;
        if(FriendStore.isCachedKeyword(name)){
            this.setState({userSuggestionText: name, runSearchEngine: true});
        }else{
            ChatWebAPIUtils.sendRequestSearchName(name,(function(){
                this.setState({userSuggestionText: name, runSearchEngine: true});
            }).bind(this));
        }
        // FriendStore.getBloodhoundEngine().search(e.target.value, this.engineBloodHoundCallback, this.engineBloodHoundCallback);
    }
    handleDoneButtonIsClicked(){
        var tmp = this.state.usersIsChoosen.slice(0);
        if(ChatMooUtils.isAllowedChatGroup()){
            this.props.handleAddFriendSubmit(tmp);
        }
        this.setState({usersIsChoosen:[],userSuggestionText: ''});
    }
    handleRemoveUserIsChoosen(i){
        var tmp = this.state.usersIsChoosen.splice(0);
        tmp.splice(i, 1);
        this.setState({"usersIsChoosen": tmp, isFocusUserSuggestionText: true});
    }
    handleAddUserIsChoosen(i){
        var tmp = this.state.usersIsChoosen.splice(0);
        tmp.push(this.state.usersSuggestion[i].id);
        this.setState({
            usersIsChoosen: tmp,
            userSuggestionText: '',
            isFocusUserSuggestionText: true,
            usersSuggestion: []
        });
    }
    componentWillReceiveProps(nextProps) {

        if(nextProps.isShow && !this.props.isShow){
            this.setState({isFocusUserSuggestionText: true});
        }
    }
    componentDidUpdate(){
        var state = {};
        if (this.state.isFocusUserSuggestionText) {  console.log('isFocusUserSuggestionText');

            //this.refs.userSuggestionText.focus();


            state.isFocusUserSuggestionText = false;
        }
        if (this.state.runSearchEngine) {
            FriendStore.getBloodhoundEngine().search(this.state.userSuggestionText, this.engineBloodHoundCallback, this.engineBloodHoundCallback);
            state.runSearchEngine = false;
        }
        if (state.hasOwnProperty('isFocusUserSuggestionText') || state.hasOwnProperty('runSearchEngine')) {
            this.setState(state);
        }
    }
    render() {
        var friends = FriendStore.getAll();
        var display = (this.props.isShow) ? "block" : "none";

        var suggestDisplay = "none";
        var widthInputSuggestion = 'auto';
        var placeholderInputSuggestion = __.t("add_friends_to_this_chat");
        var suggestItems = [];

        var itemsIsChoosen = [];
        var members = RoomStore.get(this.props.roomId).members;

        if (this.state.usersSuggestion.length > 0) {
            suggestDisplay = "block";

            for (var i = 0; i < this.state.usersSuggestion.length; i++) {
                if (members.indexOf(this.state.usersSuggestion[i].id)  == -1 && this.state.usersIsChoosen.indexOf(this.state.usersSuggestion[i].id) == -1 && suggestItems.length < 6)

                    suggestItems.push(<div key={i} className="suggestion-item tt-suggestion tt-selectable"
                                           onClick={this.handleAddUserIsChoosen.bind(this,i)}>
                        <Avatar  src={this.state.usersSuggestion[i].avatar} />
                        <span className="text">{this.state.usersSuggestion[i].name}</span>
                    </div>);
            }
        }
        if (this.state.usersIsChoosen.length > 0) {
            widthInputSuggestion = '20px';
            placeholderInputSuggestion = '';
            for (var i = 0; i < this.state.usersIsChoosen.length; i++) {
                itemsIsChoosen.push(<span key={i}
                                          className="tag label label-info">{friends[this.state.usersIsChoosen[i]].name}
                    <span data-role="remove" onClick={this.handleRemoveUserIsChoosen.bind(this,i)}></span></span>);
            }
        }
        return (
            <div className="_54_-" style={{display:display}}>

                <table className="uiGrid _51mz" cellSpacing="0" cellPadding="0">
                    <tbody>
                    <tr className="_51mx">
                        <td className="_51m- vTop _54__">
                            <div className="clearfix uiTokenizer uiInlineTokenizer">
                                <div className="tokenarea hidden_elem"></div>

                                <div className="uiTypeahead" id="js_6x">
                                    <div className="wrap"><input type="hidden" autoComplete="off"
                                                                 className="hiddenInput"/>
                                        <div className="innerWrap">
                                            <div>
                                                <div className="bootstrap-tagsinput">
                                                    {itemsIsChoosen}
                                                     <span className="twitter-typeahead" style={{position: 'relative', display: 'inline-block'}}>
            <input type="text"
                   className="tt-hint"
                   readOnly=""
                   autoComplete="off"
                   spellCheck="false"
                   tabIndex="-1"
                   dir="ltr"
                   style={{position: 'absolute', top: '0px', left: '0px', borderColor: 'transparent', boxShadow: 'none', opacity: 1, background: 'none 0% 0% / auto repeat scroll padding-box border-box rgba(0, 0, 0, 0)'}}/>
            <input
                type="text" placeholder={placeholderInputSuggestion} className="tt-input" autoComplete="off"
                spellCheck="false" dir="auto"
                style={{position: 'relative', verticalAlign: 'top', width: widthInputSuggestion, backgroundColor: 'transparent'}}
                size="24"
                onChange={this.handleInputSuggestion}
                value={this.state.userSuggestionText}
                ref="userSuggestionText"
            />
            <pre
                aria-hidden="true"
                style={{position: 'absolute', visibility: 'hidden', whiteSpace: 'pre', fontSize: '12px', fontStyle: 'normal', fontVariant: 'normal', fontWeight: 400, wordSpacing: '0px', letterSpacing: '0px', textIndent: '0px', textRendering: 'auto', textTransform: 'none'}}>t</pre>
            <div
                className="tt-menu"
                style={{position: 'absolute', top: '100%', left: '0px', zIndex: '100', display: suggestDisplay}}>
            <div className="tt-dataset tt-dataset-friends_userTagging">
                {suggestItems}
            </div>
        </div>
        </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td className="_51m- vTop _51mw">
                            <label className="doneButton uiButton uiButtonConfirm">
                                <input value={__.t("button_done")} type="submit" onClick={this.handleDoneButtonIsClicked}/>
                            </label>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        );
    }
};
class ChatMessage extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            room : RoomStore.get(this.props.data.room_id)
        };
        this.handleImageLoaded = this.handleImageLoaded.bind(this);
    }
    locate = {
        call_again:__.t("call_again"),
        call_back: __.t("call_back"),
        you_missed_a_video_chat_with_name: __.t("you_missed_a_video_chat_with_name"),
        name_missed_a_video_chat_with_you: __.t("name_missed_a_video_chat_with_you"),
    }
    imageClick(e){
        e.preventDefault();
        ChatMooUtils.popupImage(this.refs.image);
    }
    handleImageLoaded(){
        this.props.scrollToBottom();
    }
    fileClick(e){
        e.preventDefault();
        var url = ChatWebAPIUtils.getSiteUrl() + '/uploads/chat/room-' + this.props.data.room_id + '/' + this.props.data.content;
        var win = window.open(url, '_blank');
        win.focus();
    }
    render(){
        var isSelf = this.props.data.sender_id == ViewerStore.get('id');
        var chatter = (isSelf) ? "" : (UserStore.get(this.props.data.sender_id));
        var avatar = "";
        if (typeof chatter != 'undefined') {
            if (chatter.hasOwnProperty('avatar')) {
                avatar = <a className="moochat_floatL" href={chatter.url}>
                   <Avatar className="ccmsg_avatar" src={chatter.avatar} width="32" height="32"/>
                </a>;
            }
        }

        var chatboxmessagecontentClassName = isSelf ? "moochat_chatboxmessagecontent moochat_self" : "moochat_chatboxmessagecontent ";
        var spanHtmlSentNotificaiton = <span className="moochat_sentnotification"><i className="material-icons">done</i></span>;
        var msgArrowClassName = isSelf ? "selfMsgArrow" : "msgArrow";
        var content = this.props.data.content;
        var content_ex = "";
        var type = this.props.data.type;
        if (type == "file" && content.match(/\.(jpeg|jpg|gif|png)$/) != null) {
            type = "image";
        }
        switch (type) {
            case "link":
                var note_content_html = validUrl.isUri(this.props.data.note_content_html)?"<a target=\"_blank\" href="+this.props.data.note_content_html +">"+this.props.data.note_content_html+"</a>":this.props.data.note_content_html;
                content = <div className="mooText" dangerouslySetInnerHTML={{__html: note_content_html}}></div>;
                var contentParse = JSON.parse(this.props.data.content);
                if(contentParse.hasOwnProperty("dataURL")){
                    var data = contentParse.dataURL;
                    if(data.hasOwnProperty("type")){
                        var title = (data.hasOwnProperty("title"))?data.title:"";
                        var description = (data.hasOwnProperty("description"))?data.description:"";
                        var type = (data.hasOwnProperty("type"))?data.type:"";
                        var img = (data.hasOwnProperty("image"))? <span style={{"backgroundImage":"url(" + data.image + ")"}} className="img_link"></span> :"";
                        var imgURL = (data.hasOwnProperty("image"))? data.image :"";
                        var code = (data.hasOwnProperty("code"))?data.code:"";
                        var url = (data.hasOwnProperty("url"))?data.url:"";

                        switch (type) {
                            case "link":
                                content_ex = <a target="_blank" href={url} ><div className="mooText mooText_linkparse">{img}<div className="linkcontent"><div style={{"fontWeight":"bold"}}>{title}</div><div className="link_description">{description}</div></div></div></a>;
                                break;
                            case "video":
                                code = code.replace(/(width=")\d+("\W+height=")\d+/, '$1190$2100');
                                content_ex = <div className="mooText mooText_videoparse"><div className="linkvideoContent" dangerouslySetInnerHTML={{__html: code}}></div><div className="videoParseDetail"><div style={{"fontWeight":"bold"}}>{title}</div><div className="link_description">{description}</div></div></div>;
                                break;
                            case "photo":
                                content_ex =
                                    <div className="mooText_linkPhotoparse"><a ref="image" onLoad={this.handleImageLoaded} onClick={this.imageClick} className="imagemessage mediamessage"  href={imgURL}>
                                        <img className="file_image" type="image" src={imgURL} style={{maxHeight:'70px'}}/>
                                    </a></div>;
                                break;
                            default:
                        }
                    }
                }

                break;
            case "image":
                var image = ChatWebAPIUtils.getSiteUrl() + '/uploads/chat/room-' + this.props.data.room_id + '/' + content;
                content =
                    <a ref="image" onLoad={this.handleImageLoaded} onClick={this.imageClick} className="imagemessage mediamessage"  href={image}>
                        <img className="file_image" type="image" src={image} style={{maxHeight:'70px'}}/>
                    </a>;
                break;
            case "file":
                var file = ChatWebAPIUtils.getSiteUrl() + '/uploads/chat/room-' + this.props.data.room_id + '/' + content;
                content = <a ref="file" onClick={this.fileClick}  href={image}
                             style={{cursor:'pointer'}}  >
                    <i className="moochat_icon_more moochat_icon_more_file"></i>
                    <div style={{maxWidth:"140px",fontWeight:'bold',display:"inline-block"}}>{content}</div>
                </a>;
                break;
            case "system":
                avatar = "";
                var system = JSON.parse(this.props.data.content);
                var user;
                switch (system.action) {
                    case "left_the_conversation":
                        content =
                            <div className="mooChat_miniNoticed"><i className="material-icons">remove_circle_outline</i>
                                { __.t("name_left_the_conversation",{"name":chatter.name})}
                            </div>;

                        break;
                    case "added":
                        var userB = "";
                        for (var i = 0; i < system.usersId.length; i++) {

                            userB += ((i == 0) ? " " : ", ") + UserStore.getName(system.usersId[i]);


                        }
                        content = <div className="mooChat_miniNoticed"><i className="material-icons">person_add</i>
                            { __.t("userA_added_userB",{"userA":((isSelf) ? __.t("you") : chatter.name),"userB":userB})}
                        </div>;
                    case CHAT_CONSTANTS.SYSTEM_MESSAGE_ACTION.MISS_VIDEO_CALL:
                        var miss_call_content = "";
                        var call_again_title = "";
                        var className_wrapper = "mooChat_missCall";
                        if(system.caller_id == this.state.room.members[0])
                        {
                            miss_call_content = this.locate.you_missed_a_video_chat_with_name.replace("name", UserStore.getName(system.caller_id));
                            call_again_title = this.locate.call_back;
                            className_wrapper = className_wrapper + " missCall_warning";
                            
                            //show avatar
                            var chatter = UserStore.get(system.caller_id);
                            avatar = <a className="moochat_floatL" href={chatter.url}>
                                <Avatar className="ccmsg_avatar" src={chatter.avatar} width="32" height="32"/>
                            </a>;
                            chatboxmessagecontentClassName = "moochat_chatboxmessagecontent";
                        }
                        else
                        {
                            miss_call_content = this.locate.name_missed_a_video_chat_with_you.replace("name", UserStore.getName(this.props.data.sender_id));
                            call_again_title = this.locate.call_again;
                            chatboxmessagecontentClassName = "moochat_chatboxmessagecontent moochat_self";
                        }
                        
                        var unixTime = parseInt(this.props.data.time + '000');
                        var time = moment(unixTime).startOf('min').fromNow();
                        content = 
                            <div className={className_wrapper}>
                                <div className="missCall_content">{miss_call_content}</div>
                                <div className="missCall_datetime">{time}</div>
                                <div className="missCall_button">
                                    <ChatVideoCallButton room={this.state.room} show_name="1" show_name_title={call_again_title} />
                                </div>
                            </div>;
                    default:
                }

                break;
            default:
                chatboxmessagecontentClassName += (this.props.data.note_one_emoj_only == 1) ? " only_emoji" : "";
                if(chatMooEmoji.isOneEmojiOnly(this.props.data.content)){
                    chatboxmessagecontentClassName += " only_emoji";
                }
                content = <div className="mooText" dangerouslySetInnerHTML={{__html: this.props.data.note_content_html}}></div>;
        }
        var unixTime = parseInt(this.props.data.time + '000');
        var time = moment(unixTime).startOf('min').fromNow();
        return (
            <div className="moochat_chatboxmessage" id={'moochat_message_' + this.props.data.id}>
                {avatar}
                <div className={chatboxmessagecontentClassName} data-tip={time}>
                    {content}{content_ex}
                    <div>
                    {spanHtmlSentNotificaiton}<span className="moochat_time"> {time}</span>
                        </div>
                </div>

            </div>
        );
    }
};
class ChatTypingStatus extends React.Component {
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
                            <Avatar className="ccmsg_avatar" title={chatter.name} src={chatter.avatar} width="32" height="32"/>
                        </a>);
                    }

                }
                return <div className="moochat_chatboxmessage chatTyping_content">{avatars}
                    <div className="chatTyping_text">
                    <div className="_5pd7"></div>
                    <div className="_5pd7"></div>
                    <div className="_5pd7"></div>
                    </div>
                </div>;
            }else{
                return <div>

                </div>;
            }
        }else{
            return <div >

            </div>;
        }

    }
};

class ChatWindow extends React.Component{
    componentDidMount(){
        MessageStore.addChangeListener(this._onChange);
        var room = RoomStore.get(this.props.room.id);
        if(this.props.room.minimized == CHAT_CONSTANTS.WINDOW_MAXIMIZE){


            if(room.hasOwnProperty("messagesIsLoaded")){

                if(room.messagesIsLoaded == CHAT_CONSTANTS.WINDOW_MESSAGES_IS_UNLOADED){
                    RoomStore.markMessagesIsLoaded(room.id);
                    ChatWebAPIUtils.sendRequestGetRoomMessages(room.id);
                }
            }
        }

        var el = ReactDOM.findDOMNode(this.refs.mooChatTextarea);
        if(el){
          // el.focus();
        }


    }
    componentWillUnmount() {
        MessageStore.removeChangeListener(this._onChange);
    }
    _onChange(roomId){
        if(roomId == this.props.room.id){
            this.setState({messages: MessageStore.get(this.props.room.id), isScrollToBottom: MessageStore.isScrollToBottom(this.props.room.id),isMessageLoading:MessageStore.isMessageLoading(this.props.room.id)});
        }
    }
    constructor(props) {
        super(props);
        this._scrollToBottom = this._scrollToBottom.bind(this);
        this._suggestForAddingFriends = this._suggestForAddingFriends.bind(this);
        this.handleChatChange = this.handleChatChange.bind(this);
        this.handleSendPicture = this.handleSendPicture.bind(this);
        this.handleButtonEmojIsClicked = this.handleButtonEmojIsClicked.bind(this);
        this.handleEmojIsClicked = this.handleEmojIsClicked.bind(this);
        this.handleSendFile = this.handleSendFile.bind(this);
        this.handleChatKeyPress = this.handleChatKeyPress.bind(this);
        this.handleSendChatIsClicked = this.handleSendChatIsClicked.bind(this);
        this.handleCloseWindow = this.handleCloseWindow.bind(this);
        this.hanldeCloseLinkPreview = this.hanldeCloseLinkPreview.bind(this);
        this.handleTabTitleWindowClick = this.handleTabTitleWindowClick.bind(this);
        this.handleAddFriendClick = this.handleAddFriendClick.bind(this);
        this.handleChatOnPaste = this.handleChatOnPaste.bind(this);
        this.showAddFriend = this.showAddFriend.bind(this);
        this.handleAddFriendSubmit = this.handleAddFriendSubmit.bind(this);
        this.handleChatNameIsClicked = this.handleChatNameIsClicked.bind(this);
        this.handleChatSettingsClick = this.handleChatSettingsClick.bind(this);
        this.handleMouseWheel = this.handleMouseWheel.bind(this);
        this.handleChatScrolling = this.handleChatScrolling.bind(this);
        this.handleBackFriendsList = this.handleBackFriendsList.bind(this);


        this._onChange = this._onChange.bind(this);
        this.state = {
            user_status: 'moochat_offline',
            chatMessage: '',
            messages: MessageStore.get(this.props.room.id),
            isShowAddFriend: false,
            isShowChatSettings: false,
            isScrollToBottom: true,
            isFocusChatTexarea: false,
            emojIsShow: false,
            dataURLPreview:{show:false,data:{}},
            isMessageLoading:MessageStore.isMessageLoading(this.props.room.id)
        };
    }
    componentDidUpdate(){
        this._scrollToBottom();
        this._suggestForAddingFriends();
        if (this.state.isFocusChatTexarea) {


            //this.refs.mooChatTextarea.focus();

            this.setState({isFocusChatTexarea: false});
        }

        if(this.props.room.minimized == CHAT_CONSTANTS.WINDOW_MAXIMIZE){
            var room = RoomStore.get(this.props.room.id);

            if(room.hasOwnProperty("messagesIsLoaded")){

                if(room.messagesIsLoaded == CHAT_CONSTANTS.WINDOW_MESSAGES_IS_UNLOADED){
                    RoomStore.markMessagesIsLoaded(room.id);
                    ChatWebAPIUtils.sendRequestGetRoomMessages(room.id);
                }
            }

            if(room.isFocused == CHAT_CONSTANTS.IS_FOCUSED_CHAT_WINDOW){
                RoomStore.freeFlagIsFocused(room.id);
                //this.refs.mooChatTextarea.focus();

            }
        }

    }
    _scrollToBottom() {
        if (this.state.isScrollToBottom) {
            var ul = ReactDOM.findDOMNode(this.refs.messageList);
            if (typeof ul != 'undefined') {
                if (ul != null) {
                    ul.scrollTop = ul.scrollHeight;
                }

            }

        }else{
            var i = MessageStore.getScrollToIfNeed(this.props.room.id);
            if(i > 0){
                MessageStore.freeScrollToIfNeed(this.props.room.id);
                var ul = ReactDOM.findDOMNode(this.refs.messageList);
                if (typeof ul != 'undefined') {
                    if (ul != null) {
                        ul.scrollTop = document.getElementById('moochat_message_'+i).offsetTop;
                    }

                }

            }
        }

    }
    _suggestForAddingFriends(){

        if (this.state.isShowAddFriend) {
            ChatMooUtils.initFriendSuggestForARoom(this.props.room.id, RoomStore.get(this.props.room.id).members, FriendStore.getBloodhoundEngine());
        }
    }
    handleChatChange(e){
        if (e.target.value != "\n") {
            this.setState({chatMessage: e.target.value});
        }
    }
    handleSendPicture(e){
        if(!ChatMooUtils.isAllowedSendPicture()){
            return;
        }


        var files = e.target.files;
        var data = new FormData();
        var error = 0;
        var mesasges = "";
        for (var i = 0; i < files.length; i++) {
            var file = files[i];

            if (!file.type.match('image.*')) {
                mesasges = __.t("Images only");
                error = 1;
            } else if (file.size > ChatMooUtils.getUploadFileLimitOnSite()) {
                mesasges = __.t("file_is_too_large") + ChatMooUtils.getUploadFileLimitOnSite();
                error = 1;
            } else {
                data.append('image', file, file.name);
                data.append('roomId', this.props.room.id);
            }
        }
        if (!error) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', ChatWebAPIUtils.getSiteUrl() + '/chats/send-picture', true);
            xhr.send(data);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var json = JSON.parse(xhr.responseText);
                    if (json.hasOwnProperty('error_code')) {
                        if (json.error_code == 0) {
                            ChatWebAPIUtils.sendRequestTextMessage(json.result.filename, this.props.room.id, "image");
                        } else {
                            ChatWebAPIUtils.openAlertModal(__.t("warning"),xhr.responseText);
                        }
                    }

                    console.log(json);
                } else {
                    console.log(" Error in upload, try again.");
                }
            }.bind(this);
        }else{
            ChatWebAPIUtils.openAlertModal(__.t("warning"),mesasges);
        }
    }
    handleButtonEmojIsClicked(e){
        this.setState({emojIsShow: !this.state.emojIsShow});
    }
    handleEmojIsClicked(emoji){
        if(!ChatMooUtils.isAllowedEmotion()){
            return;
        }
        var message = this.state.chatMessage + emoji;
        this.setState({
            chatMessage: message,
            "emojIsShow": false,
            isFocusChatTexarea: true
        });
    }
    handleSendFile(e){
        if(!ChatMooUtils.isAllowedSendFiles()){
            return;
        }
        var files = e.target.files;
        var data = new FormData();
        var error = 0;
        for (var i = 0; i < files.length; i++) {
            var file = files[i];

            if (file.size > ChatMooUtils.getUploadFileLimitOnSite()) {
                mesasges = __.t("file_is_too_large") + ChatMooUtils.getUploadFileLimitOnSite();
                error = 1;
            } else {
                data.append('file', file, file.name);
                data.append('roomId', this.props.room.id);
            }


        }
        if (!error) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', ChatWebAPIUtils.getSiteUrl() + '/chats/send-files', true);
            xhr.send(data);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var json = JSON.parse(xhr.responseText);
                    if (json.hasOwnProperty('error_code')) {
                        if (json.error_code == 0) {
                            ChatWebAPIUtils.sendRequestTextMessage(json.result.filename, this.props.room.id, "file");
                        } else {
                            try {
                                var error = JSON.parse(xhr.responseText);
                                if(error.hasOwnProperty("error_code") && error.hasOwnProperty('result')){
                                    if(error.error_code == 1){
                                        ChatWebAPIUtils.openAlertModal(__.t("warning"),error.result.error);
                                    }
                                }
                            } catch (e) {
                                ChatWebAPIUtils.openAlertModal(__.t("warning"),xhr.responseText);
                            }

                        }
                    }

                    console.log(json);
                } else {
                    console.log(" Error in upload, try again.");
                }
            }.bind(this);
        }else{
            ChatWebAPIUtils.openAlertModal(__.t("warning"),mesasges);
        }
    }
    handleChatKeyPress(e){

            var rId = this.props.room.id;
            if(!RoomStore.isBlocked(rId) ){
                if(!_typing.hasOwnProperty(this.props.room.id)){
                    _typing[this.props.room.id] = true;
                    ChatWebAPIUtils.sendRequestStartTyping(this.props.room.id);
                    _timeout[this.props.room.id] = setTimeout(_stopTyping.bind(this,this.props.room.id), 500);
                }else{
                    if( _typing[this.props.room.id] === false){
                        _typing[this.props.room.id] = true;
                        ChatWebAPIUtils.sendRequestStartTyping(this.props.room.id);
                        _timeout[this.props.room.id] = setTimeout(_stopTyping.bind(this,this.props.room.id), 500);
                    }else{
                        if(_timeout.hasOwnProperty(this.props.room.id)){
                            clearTimeout(_timeout[this.props.room.id]);
                        }
                        _timeout[this.props.room.id] = setTimeout(_stopTyping.bind(this,this.props.room.id), 500);
                    }
                }
            }
        //}

    }
    handleSendChatIsClicked(e){
        var message = this.state.chatMessage;
        if (!message) {
            return false;
        }
        //ChatWebAPIUtils.sendRequestTextMessage(message, this.props.room.id, "text");
        //this.setState({chatMessage: ''});
        var rId = this.props.room.id;
        if(RoomStore.isBlocked(rId) ){
            if(RoomStore.isBlocker(this.props.room.id,ViewerStore.get('id'))){
                ChatWebAPIUtils.openAlertYesNoModal({
                    title:__.t("unblock_messgages"),
                    body:__.t("you_and_name_will_be_able_to_send",{name:RoomStore.getName(rId)}),
                    noButton:__.t("cancel"),
                    yesButton:__.t("button_unblock_messgages"),
                    callback:function () {
                        ChatWebAPIUtils.sendRequestUnblockMessages(rId);
                    }
                });
            }else{
                ChatWebAPIUtils.openAlertModal(__.t("warning"),__.t("this_person_not_receving_messgaes_from_you"));
            }

        }else{
            var tmp = document.createElement("DIV");
            tmp.innerHTML = message.trim();
            message = tmp.textContent || tmp.innerText || "";
            if(message != ""){
                if(this.state.dataURLPreview.show){
                    var response = this.state.dataURLPreview.data ;
                    if(response.hasOwnProperty('result')){
                        ChatWebAPIUtils.sendRequestTextMessage({message:message,dataURL:response.result}, this.props.room.id, "link");
                    }else{
                        ChatWebAPIUtils.sendRequestTextMessage(message, this.props.room.id, "text");
                    }

                }else{
                    ChatWebAPIUtils.sendRequestTextMessage(message, this.props.room.id, "text");
                }
            }

            this.setState({chatMessage:'',dataURLPreview:{show:false,data:{}}});
        }
    }
    handleCloseWindow(e){
        e.stopPropagation();
        ReactTooltip.hide();
        ChatWebAPIUtils.destroyARoom(this.props.room.id);
    }
    hanldeCloseLinkPreview(e){
        this.setState({dataURLPreview:{show:false,data:{}}});
    }
    handleTabTitleWindowClick(){
        ChatWebAPIUtils.minimizeARoom(this.props.room.id);
    }
    handleAddFriendClick(e){
        if (typeof e != 'undefined') {
            e.stopPropagation();
        }
        if(!RoomStore.isBlocked(this.props.room.id)){
            this.setState({isShowAddFriend: !this.state.isShowAddFriend, isScrollToBottom: false});
        }


    }
    handleChatOnPaste(e){
        // common browser -> e.originalEvent.clipboardData
        // uncommon browser -> window.clipboardData
        var clipboardData = e.clipboardData || e.originalEvent.clipboardData || window.clipboardData;
        var pastedData = clipboardData.getData('text/plain');
        var that=this;
        if (validUrl.isUri(pastedData)){
            that.setState({dataURLPreview:{show:true,data:{}}})
            ChatMooUtils.getDataFromURL(pastedData,function(data){
                that.setState({dataURLPreview:{show:true,data:data}})
            });
        }
    }
    showAddFriend(e){
        if (typeof e != 'undefined') {
            e.stopPropagation();
        }

        if(!RoomStore.isBlocked(this.props.room.id)){
            this.setState({isShowAddFriend: true, isScrollToBottom: false});
        }


    }
    handleAddFriendSubmit(users){

        //var val = ChatMooUtils.getFriendSuggestIsChoosenInARoom(this.props.room.id);
        if (users.length > 0) {

            var members = RoomStore.get(this.props.room.id).members;

            //if (members.length == 1) {
            if(!RoomStore.isGroup(this.props.room.id)){
                // create new
                users = users.concat(members);
                var that = this;
                ChatWebAPIUtils.createChatGroupWindowForUsers(users,function(data){
                    that.props.enableChatWindow(0,data.roomId);
                });
            } else {
                // add more   
                ChatWebAPIUtils.addUsersToARoom(users, this.props.room.id);
            }

        }
        this.setState({isShowAddFriend: !this.state.isShowAddFriend, isScrollToBottom: false});


    }
    handleChatNameIsClicked(e){
        e.stopPropagation();
        window.location.href = ChatMooUtils.getChatFullConversationURL() + "/" + this.props.room.id;

    }
    handleChatSettingsClick(e){
        e.stopPropagation();
        this.setState({isShowChatSettings: !this.state.isShowChatSettings, isScrollToBottom: false});

    }
    handleMouseWheel(event, d){
        var e = event || window.event;
        var deltaX = e.deltaX * -30 ||
            e.wheelDeltaX / 4 ||
            0;
        var deltaY = e.deltaY * -30 ||
            e.wheelDeltaY / 4 ||
            (typeof e.wheelDeltaY == 'undefined' &&
            e.wheelDelta / 4) ||
            e.detail * -10 ||
            0;
        e.currentTarget.scrollTop -= deltaY;
        if (e.preventDefault) e.preventDefault();
        if (e.stopPropagation) e.stopPropagation();
        //e.cancelBubble = true;
        //e.returnValue = false;

        return false;
    }
    handleChatScrolling(e){
        var ul = ReactDOM.findDOMNode(this.refs.messageList);
        if (typeof ul != 'undefined') {
            if (ul != null) {
                var rId = this.props.room.id;

                if(ul.scrollTop == 0  && MessageStore.isAllowedLoadMoreMessages(rId)){

                    var mIdStart = MessageStore.getStartMesageId(rId);
                    if(mIdStart != 0){
                        //this.setState({isScrollToBottom: false});
                        //RoomStore.markMessagesIsLoading(rId);
                        MessageStore.setMessageLoading(rId);
                        setTimeout(function(){ ChatWebAPIUtils.sendRequestGetRoomMessagesMore(rId,mIdStart,ChatMooUtils.getMoreMessageLimit()); }, 200);
                        //ChatWebAPIUtils.sendRequestGetRoomMessagesMore(rId,mIdStart,10);
                    }

                }
            }

        }

    }
    handleBackFriendsList(){
        this.props.closeChatWindow();
    }
    render(){

        if (RoomStore.hasNewMessage() && RoomStore.getRoomIdHasNewMessage() == this.props.room.id && this.props.room.minimized == CHAT_CONSTANTS.WINDOW_MAXIMIZE) {
            RoomStore.freeFlagHasNewMessage();
        }
        var displaySendPicutre = (ChatMooUtils.isAllowedSendPicture())?"block":"none";

        var displaySendFiles = (ChatMooUtils.isAllowedSendFiles())?"block":"none";
        var displayChatGroup = (ChatMooUtils.isAllowedChatGroup())?"block":"none";

        // Messages
        var messages = [];
            for (var key in this.state.messages) {
                if (this.state.messages.hasOwnProperty(key)) {
                    messages.push(<ChatMessage  scrollToBottom={this._scrollToBottom} data={this.state.messages[key]} key={key} users={this.props.users}
                                               friends={this.props.friends}/>);
                }
            }


        // End Messages

        // Room Title

        var roomTitle = RoomStore.getName(this.props.room.id);
        //var moochatStatusStyle = (RoomStore.isActivated(this.props.room.id)) ? __.t("online"):__.t("offline");
        var chat_online_status = RoomStore.getOnlineStatus(this.props.room.id);
        var moochatStatusStyle = "";
        if (chat_online_status == CHAT_CONSTANTS.ONLINE_STATUS.ACTIVE) {
            moochatStatusStyle = __.t("chat_status_active");
        }
        if (chat_online_status == CHAT_CONSTANTS.ONLINE_STATUS.BUSY) {
            moochatStatusStyle = __.t("chat_status_busy");
        }
        if (chat_online_status == CHAT_CONSTANTS.ONLINE_STATUS.INVISIBLE) {
            moochatStatusStyle = __.t("offline");
        }

        // End Room Title

        // Room behavior
        var mooChatWindowClassName = (this.props.room.minimized == CHAT_CONSTANTS.WINDOW_MINIMIZE) ? "moochat_tabpopup moochat_tabopen_bottom" : "moochat_tabpopup moochat_tabopen moochat_tabopen_bottom"; // Default is opend
        // End Room behavior

        var messagesHeight = (this.state.isShowAddFriend) ? "336px" : "336px";
        var moochat_popup_plugins_style = (this.state.isShowChatSettings) ? "block" : "none";

        if (typeof this.props.room.id == 'undefined') {
            return <div></div>;
        }
        var moochat_allow_send_file = (ChatMooUtils.isAllowedSendFiles()) ? '' : 'not_send_file';
        var moochat_allow_emoj = (ChatMooUtils.isAllowedEmotion()) ? '' : 'not_send_emoj';
        var moochat_room_loading  =  (this.state.isMessageLoading) ? "block" : "none";
        return <div className="moochat_popup" onScroll={this.handleChatScrolling} ref="messageList" id={"moochat_user_popup_" + this.props.room.id}>
            <div className="moochat_mobile_header moochat_user_detail">
                <i className="moochat_back material-icons" onClick={this.handleBackFriendsList}>keyboard_backspace</i>
                <div className="moochat_username">
                    {roomTitle}
                    <span className={"moochat_status"}>{moochatStatusStyle}</span>
                </div>
                <div className="moochat_video_button">
                    <ChatVideoCallButton room={this.props.room} />
                </div>
                <div className="moochat_option">
                    <i className="material-icons">more_vert</i>
                </div>
                <ChatWindowSettings room={this.props.room}
                                    roomId={this.props.room.id}
                                    showAddFriend={this.showAddFriend}
                                    members={this.props.room.members}
                                    closeChatWindow = {this.props.closeChatWindow}
                />

                <ChatAddFriends isShow={this.state.isShowAddFriend} roomId={this.props.room.id}
                                handleAddFriendSubmit={this.handleAddFriendSubmit}/>
                <ChatBlocked room ={this.props.room}/>
            </div>
            <div className="moochat_main_message">
                <div className="moochat_chatboxmessage chat-spinner" style={{display:moochat_room_loading}}>
                    <div className="bounce1"></div>
                    <div className="bounce2"></div>
                    <div className="bounce3"></div>
                </div>
                {messages}
                <ChatTypingStatus room={this.props.room}/>
            </div>
            <div className={moochat_allow_emoj + " moochat_tabcontentinput " + moochat_allow_send_file}>

          <span className="moochat_form_icon moochat_camera">
              <i className="material-icons">camera_alt</i>
              <form action="/ajax/mercury/upload.php" className="_vzk"  method="post"
                    encType="multipart/form-data">
                                    <input id={"moochat-add-files-button-"+this.props.room.id}
                                           onChange={this.handleSendFile} type="file" className="_n _2__f _5f0v"  title={__.t("add_files")}
                                           name="attachment[]" multiple="" accept="*" />
                                </form>
          </span>
          <span className="moochat_form_icon moochat_emoji">
              <i className="material-icons">mood</i>
              <ChatMooEmoji
                  emojIsClicked={this.handleEmojIsClicked}
                  isShow={this.state.emojIsShow}
                  handleButtonEmojIsClicked={this.handleButtonEmojIsClicked}/>
              <ChatURLPreview data={this.state.dataURLPreview} hanldeCloseLinkPreview={this.hanldeCloseLinkPreview} />
          </span>
                <form className="mooChatForm"  ref="mooChatForm">
                            <TextareaAutosize
                                id="mooChatTextarea"
                                ref="mooChatTextarea"
                                className="moochat_textarea placeholder"
                                placeholder={__.t("type_a_message")}
                                onChange={this.handleChatChange}
                                onKeyPress={this.handleChatKeyPress}
                                value={this.state.chatMessage}
                                onPaste={this.handleChatOnPaste}
                                maxRows={7}
                            />
                </form>
          <span className="moochat_form_icon moochat_send">
              <i className="material-icons" onClick={this.handleSendChatIsClicked}>send</i>
          </span>
            </div>
        </div>;

    }
};

export default class ChatWindows extends React.Component{
    componentDidUpdate(){
        UserStore.updateMissingUser();
    }
    getRoomInfo(idUser,idGroup){
        var room = {id:0,minimized:CHAT_CONSTANTS.WINDOW_MINIMIZE};
        if(idUser != 0){
            room =  RoomStore.getFromUserId(idUser);
        }
        if(idGroup != 0){
            room = RoomStore.get(idGroup);
        }
        if(room == undefined){
            return {id:0,minimized:CHAT_CONSTANTS.WINDOW_MINIMIZE};
        }
        return room;
    }
    render(){

        var room = this.getRoomInfo(this.props.idUserIsBeingUsedForCreatingARoom,this.props.idGroupIsBeingUsedForCreatingARoom);

        if(room.id == 0){

            return <div></div>;
        }
        return <div>
            <ChatWindow
                room={room}
                friends={this.props.friends}
                users={this.props.users}
                closeChatWindow={this.props.closeChatWindow}
                doModalReport={this.props.doModalReport}
                enableChatWindow={this.props.enableChatWindow}
            />
        </div>;

    }
};
