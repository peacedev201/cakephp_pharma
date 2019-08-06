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

var __ = require('../utils/ChatMooI18n').i18next;
import validUrl from 'valid-url';



import ChatBlocked from './desktop/ChatBlocked';
import ChatURLPreview from './desktop/ChatURLPreview';
import ChatAddFriends from './desktop/ChatAddFriends';
import ChatMessage from './desktop/ChatMessage';
import ChatTypingStatus from './desktop/ChatTypingStatus';
import ChatSendPictureButton from './desktop/ChatSendPictureButton';
import ChatMooEmojiButton from './desktop/ChatMooEmojiButton';
import ChatSendFileButton from './desktop/ChatSendFileButton';
import ChatWindowSettingsButton from './desktop/ChatWindowSettingsButton';
import ChatAddFriendButton from './desktop/ChatAddFriendButton';
import ChatVideoCallButton from './desktop/ChatVideoCallButton';
import TextareaAutosize from 'react-textarea-autosize';
var _typing = {};
var _timeout = {};
var _stopTyping = function(rId){
    if(_typing.hasOwnProperty(rId)){
        _typing[this.props.room.id] = false;
    }
    ChatWebAPIUtils.sendRequestStopTyping(rId);
}



class ChatWindow extends React.Component {
    constructor(props) {
        super(props);
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
            isMessageLoading:MessageStore.isMessageLoading(this.props.room.id),
            room : RoomStore.get(this.props.room.id)
        };
    }
    componentDidMount(){
        MessageStore.addChangeListener(this._onChange);

        this._makeTextareaFocused();
        this._loadMessages();
        ReactTooltip.rebuild();
        this.boxchat_content_height = window.getComputedStyle(document.getElementsByClassName('moochat_tabcontenttext')[0]).getPropertyValue("height");
        this.boxchat_message_height = window.getComputedStyle(document.getElementsByClassName('moochat_textarea')[0]).getPropertyValue("height");
        this.boxchat_content_height = parseFloat(this.boxchat_content_height);
        this.boxchat_message_height = parseFloat(this.boxchat_message_height);
        // Autobot for test multi user chat to one room

        //var roomId = this.props.room.id;
        //if(this.props.room.id == 2 ) {
        /*
            if (ViewerStore.get('id') != 1) {
                setInterval(function () {
                    var text = "";
                    var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

                    for (var i = 0; i < 15; i++)
                        text += possible.charAt(Math.floor(Math.random() * possible.length));

                    ChatWebAPIUtils.sendRequestTextMessage(text, roomId, "text");
                }, 100);

            }
          */
        //}

    }
    componentWillUnmount() {
        MessageStore.removeChangeListener(this._onChange);
    }
    _onChange = (roomId) => {
        if(this._isNeedToUpdateState(roomId)){
            this.setState({
                messages: MessageStore.get(this.props.room.id),
                isScrollToBottom: MessageStore.isScrollToBottom(this.props.room.id),
                isMessageLoading:MessageStore.isMessageLoading(this.props.room.id),
                room : RoomStore.get(this.props.room.id)
            });
        }
    }

    componentDidUpdate(){
        this._scrollToBottom();
        this._suggestForAddingFriends();
        this._makeTextareaFocused();
        this._loadMessages();
        ReactTooltip.rebuild();
    }
    _scrollToBottom = () => {
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
    handleChatChange = (e) => {
        if (e.target.value != "\n") {
            this.setState({chatMessage: e.target.value});
        }
        this.changeBoxInputMessageHeight(e);
    }

    handleButtonEmojIsClicked = (e) => {
        this.setState({emojIsShow: !this.state.emojIsShow});
    }
    handleEmojIsClicked = (emoji) => {
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

    handleChatKeyPress = (e) => {

        if (e.which == 13 && !e.shiftKey) {
            var message = e.target.value;
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

                }

                this.setState({chatMessage: '',dataURLPreview:{show:false,data:{}}});

        }else{
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
        }
    }
    handleCloseWindow = (e) => {
        e.stopPropagation();
        ReactTooltip.hide();
        ChatWebAPIUtils.destroyARoom(this.props.room.id);
    }
    hanldeCloseLinkPreview = (e) => {
        this.setState({dataURLPreview:{show:false,data:{}}});
    }
    handleTabTitleWindowClick = () => {
        ChatWebAPIUtils.minimizeARoom(this.props.room.id);
    }
    handleAddFriendClick = (e) =>{
        if (typeof e != 'undefined') {
            e.stopPropagation();
        }
        if(!RoomStore.isBlocked(this.props.room.id)){
            this.setState({isShowAddFriend: !this.state.isShowAddFriend, isScrollToBottom: false});
        }


    }
    handleChatOnPaste = (e) => {
        // common browser -> e.originalEvent.clipboardData
        // uncommon browser -> window.clipboardData
        var clipboardData = e.clipboardData || e.originalEvent.clipboardData || window.clipboardData;
        var pastedData = clipboardData.getData('text');
        var that=this;

        if (validUrl.isUri(pastedData)){
            that.setState({dataURLPreview:{show:true,data:{}}});
           ChatMooUtils.getDataFromURL(pastedData,function(data){
               that.setState({dataURLPreview:{show:true,data:data}})
           });
        }
    }
    showAddFriend = (e) => {
        if (typeof e != 'undefined') {
            e.stopPropagation();
        }
        
        if(!RoomStore.isBlocked(this.props.room.id)){
            this.setState({isShowAddFriend: true, isScrollToBottom: false});
        }


    }
    handleAddFriendSubmit = (users) => {

        //var val = ChatMooUtils.getFriendSuggestIsChoosenInARoom(this.props.room.id);
        if (users.length > 0) {
         
            var members = RoomStore.get(this.props.room.id).members;
       
            //if (members.length == 1) {
            if(!RoomStore.isGroup(this.props.room.id)){
                // create new
                users = users.concat(members);
                ChatWebAPIUtils.createChatGroupWindowForUsers(users);
            } else {
                // add more   
                ChatWebAPIUtils.addUsersToARoom(users, this.props.room.id);
            }

        }
        this.setState({isShowAddFriend: !this.state.isShowAddFriend, isScrollToBottom: false});


    }
    handleChatNameIsClicked = (e) => {
        e.stopPropagation();
        window.location.href = ChatMooUtils.getChatFullConversationURL() + "/" + this.props.room.id;

    }
    handleChatSettingsClick = (e) => {
        e.stopPropagation();
        this.setState({isShowChatSettings: !this.state.isShowChatSettings, isScrollToBottom: false});

    }
    handleMouseWheel = (event, d) => {
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
    handleChatScrolling = (e) => {
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
    _isOpenedAtMaximum =() => {
        return this.props.room.minimized == CHAT_CONSTANTS.WINDOW_MAXIMIZE;
    }
    _isNeedToLoadMessages =() => {
        if(this.state.room.hasOwnProperty("messagesIsLoaded")){
            if(this.state.room.messagesIsLoaded == CHAT_CONSTANTS.WINDOW_MESSAGES_IS_UNLOADED){
                return true;
            }
        }
        return false;
    }
    _isFocused = () => {
       return this.state.room.isFocused == CHAT_CONSTANTS.IS_FOCUSED_CHAT_WINDOW;
    }
    _isFocusing = () => {
        return this.state.isFocusChatTexarea;
    }
    _isNeedToUpdateState = (roomId) =>{
        return roomId == this.props.room.id
    }

    _makeTextareaFocused(){
        if(this._isFocusing()
            || (this._isOpenedAtMaximum() && this._isFocused())
        ){
            ReactDOM.findDOMNode(this.refs.mooChatTextarea).focus();
            this.setState({isFocusChatTexarea: false});
            RoomStore.freeFlagIsFocused(this.props.room.id);
        }
    }
    _loadMessages(){
        if(this._isOpenedAtMaximum() && this._isNeedToLoadMessages()){
            RoomStore.markMessagesIsLoaded(this.props.room.id);
            ChatWebAPIUtils.sendRequestGetRoomMessages(this.props.room.id);
        }
    }
    changeBoxInputMessageHeight(room_id, height, number_of_lines){
        var box_content = document.getElementById('moochat_tabcontenttext_' + room_id);
        var line_height = 14;
        var max_line = 7;
        var content_height = this.boxchat_content_height;
        var message_height = this.boxchat_message_height;
        
        /*if(e.target.value == "\n" || e.target.value == "")
        {
            number_of_lines = 1;
        }*/
        if(number_of_lines == 1)
        {
            line_height = 0;
        }
        if(number_of_lines <= max_line)
        {
            var offset_height = (line_height * (number_of_lines - 1));
            //e.target.style.height = message_height + offset_height + 'px';
            box_content.style.height = content_height - offset_height + 'px';
        }
    }
    render(){
        if (RoomStore.hasNewMessage() && RoomStore.getRoomIdHasNewMessage() == this.props.room.id && this._isOpenedAtMaximum()) {
            RoomStore.freeFlagHasNewMessage();
        }

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
        //var moochatStatusStyle = (RoomStore.isActivated(this.props.room.id)) ? "moochat_available" : "moochat_offline";
        var chat_online_status = RoomStore.getOnlineStatus(this.props.room.id);
        var moochatStatusStyle = "";
        if (chat_online_status == CHAT_CONSTANTS.ONLINE_STATUS.ACTIVE) {
            moochatStatusStyle = "moochat_available";
        }
        if (chat_online_status == CHAT_CONSTANTS.ONLINE_STATUS.BUSY) {
            moochatStatusStyle = "moochat_busy";
        }
        if (chat_online_status == CHAT_CONSTANTS.ONLINE_STATUS.INVISIBLE) {
            moochatStatusStyle = "moochat_offline";
        }
        
        // End Room Title

        // Room behavior
        var mooChatWindowClassName = (this.props.room.minimized == CHAT_CONSTANTS.WINDOW_MINIMIZE) ? "moochat_tabpopup moochat_tabopen_bottom" : "moochat_tabpopup moochat_tabopen moochat_tabopen_bottom"; // Default is opend
        // End Room behavior

        var messagesHeight = (this.state.isShowAddFriend) ? "336px" : "336px";
        var moochat_popup_plugins_style = (this.state.isShowChatSettings) ? "block" : "none";

        if (typeof this.props.room.id == 'undefined') {
            return (null);
        }
        var moochat_room_loading  =  (this.state.isMessageLoading) ? "block" : "none";
        
        var mooChatWindowCSS = '';
        if(ChatMooUtils.isRTL()){
            mooChatWindowCSS = {display: 'none', right: this.props.left+'px'};
        }
        else {
            mooChatWindowCSS = {display: 'none', left: this.props.left+'px'};
        }

        return (
            <div key={this.props.room.id} id={"moochat_user_popup_" + this.props.room.id}
                 className={mooChatWindowClassName}
                 style={mooChatWindowCSS}>
                <div className="moochat_tabtitle" onClick={this.handleTabTitleWindowClick}>
                    <div className={"moochat_userscontentdot moochat_floatL "+moochatStatusStyle}></div>
                    <span className="moochat_typing" style={{display: 'none'}}></span>
                    <div className="moochat_name" data-tip={roomTitle} data-place="top" onClick={this.handleChatNameIsClicked}>{roomTitle}</div>
                    <div className="moochat_closebox moochat_floatR moochat_tooltip" onClick={this.handleCloseWindow}
                         data-tip={__.t("close_tab")} data-place="top">
                        <i className="material-icons">clear</i>
                    </div>
                    <ChatAddFriendButton handleAddFriendClick={this.handleAddFriendClick}/>
                    <ChatVideoCallButton room={this.props.room} />
                    <ChatWindowSettingsButton roomId={this.props.room.id} showAddFriend={this.showAddFriend}
                                        members={this.props.room.members}/>
                </div>
                <div className="moochat_tabsubtitle moochat_tabcontent">
                    <ChatAddFriends isShow={this.state.isShowAddFriend} roomId={this.props.room.id}
                                    handleAddFriendSubmit={this.handleAddFriendSubmit}/>
                    <ChatBlocked room ={this.props.room}/>

                    <div onWheel={this.handleMouseWheel}
                         onScroll={this.handleChatScrolling}
                         className="moochat_tabcontenttext"
                         id={"moochat_tabcontenttext_"+ this.props.room.id}
                         ref="messageList" style={{height:messagesHeight}}>
                        <div className="chat-spinner" style={{display:moochat_room_loading}}>
                            <div className="bounce1"></div>
                            <div className="bounce2"></div>
                            <div className="bounce3"></div>
                        </div>
                        
                        {messages}
                        <ChatTypingStatus room={this.props.room}/>
                    </div>

                    <div className="moochat_tabcontentinput">
                        <form className="mooChatForm"  ref="mooChatForm">
                            <TextareaAutosize
                                id={"mooChatTextarea_"+ this.props.room.id}
                                ref="mooChatTextarea"
                                className="moochat_textarea placeholder"
                                placeholder={__.t("type_a_message")}
                                onChange={this.handleChatChange}
                                onKeyPress={this.handleChatKeyPress}
                                value={this.state.chatMessage}
                                style={{height: '16px', 'overflowY': 'auto'}}
                                onPaste={this.handleChatOnPaste}
                                room_id={this.props.room.id}
                                maxRows={7}
                                onHeightChange={(height, instance) => this.changeBoxInputMessageHeight(this.props.room.id, height, instance.rowCount)}
                            />
                        </form>

                        <ChatSendPictureButton room={this.props.room}/>
                        <ChatMooEmojiButton
                            emojIsClicked={this.handleEmojIsClicked}
                            isShow={this.state.emojIsShow}
                            handleButtonEmojIsClicked={this.handleButtonEmojIsClicked}/>
                        <ChatSendFileButton room={this.props.room}/>
                        <ChatURLPreview data={this.state.dataURLPreview} hanldeCloseLinkPreview={this.hanldeCloseLinkPreview} />
                        <div className="moochat_buttonicon moochat_buttoncamera moochat_floatL"></div>

                    </div>
                    <div style={{clear:'both'}}></div>
                </div>
            </div>
        )
    }
};
export default class ChatWindows extends React.Component {
    constructor(props) {
        super(props);
        this.state = {users: UserStore.getAll(), friends: FriendStore.getAll(), rooms: RoomStore.getAll()};
    }
    componentDidMount(){
        UserStore.addChangeListener(this._onChange);
        FriendStore.addChangeListener(this._onChange);
        RoomStore.addChangeListener(this._onChange);
    }
    componentWillUnmount() {
        UserStore.removeChangeListener(this._onChange);
        FriendStore.removeChangeListener(this._onChange);
       RoomStore.removeChangeListener(this._onChange);

    }


    _onChange = () => {
        this.setState({users: UserStore.getAll(), friends: FriendStore.getAll(), rooms: RoomStore.getAll()});
    }
    componentDidUpdate(){
        UserStore.updateMissingUser();
    }
    render(){
        var windows = [];
        var left_base = 232;  // 243
        var left_base_minized = 163;
        var left_room = ChatMooUtils.windowWidth() - 467;
        if (this.state.rooms.isCreated) {

            for (var i = 0; i < this.state.rooms.isCreated.length; i++) {



                //if (left_room > 150){
                windows.push(<ChatWindow
                    key={this.state.rooms.isCreated[i]}
                    room={this.state.rooms[this.state.rooms.isCreated[i]]}
                    left={left_room}
                    friends={this.state.friends}
                    users={this.state.users}
                    doModalReport={this.props.doModalReport}
                />);
                if (this.state.rooms[this.state.rooms.isCreated[i]].minimized == 0) {

                    left_room = left_room - left_base;
                } else {
                    left_room = left_room - left_base_minized;
                }
                //}
            }
        }
        return <div>{windows}<ReactTooltip key={99999} place="right" effect="solid"/></div>;
    }
};
