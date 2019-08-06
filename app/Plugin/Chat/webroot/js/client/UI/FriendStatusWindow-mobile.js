/* Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
import React from 'react';
import ChatConstants from '../constants/ChatConstants';
import FriendStore from '../stores/FriendStore';
import RoomStore from '../stores/RoomStore';
import UserStore from '../stores/UserStore';
import GroupStore from '../stores/GroupStore';
import ViewerStore from '../stores/ViewerStore';
import MessageStore from '../stores/MessageStore';
import CounterUnseenMessageStore from '../stores/CounterUnseenMessageStore';
import ChatWebAPIUtils from '../utils/ChatWebAPIUtils';
import ChatMooUtils from '../utils/ChatMooUtils';
import ChatWindows from '../UI/ChatWindows-mobile';
var __ = require('../utils/ChatMooI18n').i18next;
import Avatar from './Avatar';

var _getRoomInfo =function(idUser,idGroup){
    var room = {id:0,minimized:ChatConstants.WINDOW_MINIMIZE};
    if(idUser != 0){
        room = RoomStore.getFromUserId(idUser);
    }
    if(idGroup != 0){
        room = RoomStore.get(idGroup);
    }
    if(room == undefined){
        return {id:0,minimized:ChatConstants.WINDOW_MINIMIZE};
    }
    return room;
};
class ChatSettings extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            isChatSoundEnable: ChatMooUtils.getChatSoundState(),
            isHideGroup: ChatMooUtils.getHideGroupState(),
            chat_online_status: ViewerStore.getOnlineStatus()
        };
        this.handleChatSound = this.handleChatSound.bind(this);
        this.handleTurnOffChat = this.handleTurnOffChat.bind(this);
        this.handleCloseAllChatsWindow = this.handleCloseAllChatsWindow.bind(this);
        this.handleHideGroups = this.handleHideGroups.bind(this);
        this.handleBlockSetting = this.handleBlockSetting.bind(this);
        this.handleChatHistory = this.handleChatHistory.bind(this);
        this.handleSelectAction = this.handleSelectAction.bind(this);
        this.handleSelectOnlineStatus = this.handleSelectOnlineStatus.bind(this);
    }
    handleChatSound() {

        if (this.state.isChatSoundEnable) {
            ChatMooUtils.turnOffSound();
        } else {
            ChatMooUtils.turnOnSound();
        }
        this.setState({isChatSoundEnable: !this.state.isChatSoundEnable});
    }
    handleTurnOffChat() {
        this.props.handleTurnOffChatIsClick();
    }
    handleCloseAllChatsWindow() {
    }
    handleHideGroups() {
        if (this.state.isHideGroup == ChatConstants.HIDE_GROUP_ENABLE) {
            this.props.handleHideGroupsIsClicked(false);
            //ChatMooUtils.hideGroup(false);
        } else {
            this.props.handleHideGroupsIsClicked(true);
            //ChatMooUtils.hideGroup(true);
        }
        this.setState({isHideGroup: !this.state.isHideGroup});

    }
    handleBlockSetting() {
        window.location.href = ChatMooUtils.getChatBlockSettingURL();
    }
    handleChatHistory() {
        window.location.href = ChatMooUtils.getChatHistoryURL();
    }
    handleSelectAction(e){

        switch(e.target.value) {
            case "chat_sounds":
                this.handleChatSound();
                break;
            case "block_settings":
                this.handleBlockSetting();
                break;
            case "chat_history":
                this.handleChatHistory();
                break;
            case "hide_groups":
                this.handleHideGroups();
                break;
            case "turn_off_chat":
                this.handleTurnOffChat();
                break;
            default:

        }
    }
    handleSelectOnlineStatus(e){
        switch(e.target.value) {
            case ChatConstants.ONLINE_STATUS.ACTIVE:
                this.setState({chat_online_status: ChatConstants.ONLINE_STATUS.ACTIVE});
                ChatWebAPIUtils.changeOnlineStatus(ChatConstants.ONLINE_STATUS.ACTIVE);
                break;
            case ChatConstants.ONLINE_STATUS.BUSY:
                this.setState({chat_online_status: ChatConstants.ONLINE_STATUS.BUSY});
                ChatWebAPIUtils.changeOnlineStatus(ChatConstants.ONLINE_STATUS.BUSY);
                break;
            case ChatConstants.ONLINE_STATUS.INVISIBLE:
                this.setState({chat_online_status: ChatConstants.ONLINE_STATUS.INVISIBLE});
                ChatWebAPIUtils.changeOnlineStatus(ChatConstants.ONLINE_STATUS.INVISIBLE);
                break;
            default:
        }
        this.props.handleSetCurrentOnlineStatus(e.target.value);
    }
    render(){

        var textChatSound = (!this.state.isChatSoundEnable) ? __.t("enable_chat_sounds") : __.t("disable_chat_sounds");
        var soundOption = (ChatMooUtils.isChatSoundGlobalEnable())?<option value="chat_sounds">{textChatSound}</option>:"";
        var textHideGroup = (this.state.isHideGroup == ChatConstants.HIDE_GROUP_ENABLE) ? __.t("show_groups") : __.t("hide_groups");
        
        if(typeof this.props.chatStatus != "undefined"){
            return <select style={{width:"40px"}} onChange={this.handleSelectOnlineStatus} value={this.props.currentStatus}>
                <option value={ChatConstants.ONLINE_STATUS.ACTIVE}>{__.t("chat_status_active")}</option>
                <option value={ChatConstants.ONLINE_STATUS.BUSY}>{__.t("chat_status_busy")}</option>
                <option value={ChatConstants.ONLINE_STATUS.INVISIBLE}>{__.t("chat_status_invisible")}</option>
            </select>;
        }
        return <select style={{width:"40px"}} onChange={this.handleSelectAction} value="">
            <option >{__.t("chose_an_action")}</option>
            {soundOption}
            <option value="block_settings">{__.t("block_settings")}</option>
            <option value="chat_history">{__.t("chat_history")}</option>
            <option value="hide_groups">{textHideGroup}</option>
            <option value="turn_off_chat">{__.t("turn_off_chat")}</option>
        </select>;
    }
};
class FriendStatusItemWindow extends React.Component {
    constructor(props) {
        super(props);
        this.handleFriendItemClick = this.handleFriendItemClick.bind(this);
    }
    handleFriendItemClick() {
        this.props.enableChatWindow(this.props.friend.id,0);
        ChatWebAPIUtils.createChatWindowForAUser(this.props.friend.id);
    }
    render(){
        var display = (this.props.friend.is_hidden == ChatConstants.ITEM_SHOW)?"block":"none";
        var user_status = (this.props.friend.is_logged == 1)?"moochat_available":"moochat_offline";
        var room = _getRoomInfo(this.props.friend.id,0);

        var unseen =(room.id != 0 && room.id != undefined)?<RoomUnseenStatusItemWindow room={room}/>:"";
        var review_message =(room.id != 0 && room.id != undefined)?<RoomReviewMessageItemWindow room={room}/>:"";
        var chat_online_status = "moochat_offline";
        if (this.props.friend.is_logged == 1 && this.props.friend.chat_online_status == ChatConstants.ONLINE_STATUS.ACTIVE) {
            chat_online_status = "moochat_available";
        }
        else if (this.props.friend.is_logged == 1 && this.props.friend.chat_online_status == ChatConstants.ONLINE_STATUS.BUSY) {
            chat_online_status = "moochat_busy";
        }
        else if (this.props.friend.is_logged != 1  || this.props.friend.chat_online_status == ChatConstants.ONLINE_STATUS.INVISIBLE) {
            chat_online_status = "moochat_offline";
        }
        
        return <li className="item" style={{display: display}}  id={'moochat_userlist_'+this.props.friend.id} onClick={this.handleFriendItemClick}>
            <a href="#app_no_tab">
                {unseen}
                <div className="left pull-left">
                    <Avatar src={this.props.friend.avatar} width="50" height="50"/>
                </div>
                <div className="right">
                    <div className="name ">{this.props.friend.name} <span className={" moochat_status moochat_userscontentdot " + chat_online_status}></span></div>
                    {review_message}
                </div>
            </a>
        </li>;
    }

};
class FriendStatusItemWindows extends React.Component {
    render(){
        var FriendStatusItemWindows = [];
        // friendlist data
        if (this.props.friends.key) {
            this.props.friends.key.map(function (key) {
                FriendStatusItemWindows.push(<FriendStatusItemWindow
                                                                     createChatWindow={this.props.createChatWindow}
                                                                     key={key}
                                                                     friend={this.props.friends[key]}
                                                                     enableChatWindow={this.props.enableChatWindow}/>);
            }.bind(this));
        }
        return (<ul className="moochat_mobile_userlist">{FriendStatusItemWindows}</ul>);
    }
};
// End Item Friends UI
class GroupItemWindow extends React.Component {
    constructor(props) {
        super(props);
        this.handleGroupItemClick = this.handleGroupItemClick.bind(this);
    }
    handleGroupItemClick() {
        this.props.enableChatWindow(0,this.props.group.id);
        ChatWebAPIUtils.createChatWindowByRoomId(this.props.group.id);
    }
    render(){
        var img1,img2,img3,img4, showImage;
        showImage="";
        img1=img2=img3=img4="";
        img1 = <Avatar className="moochat_userscontentavatarimage" src={UserStore.getAvatar(this.props.group.members[0])}/>;

        img2 = <Avatar className="moochat_userscontentavatarimage" src={UserStore.getAvatar(this.props.group.members[1])}/>;

        if(this.props.group.members.length > 2){
            img3 = <Avatar className="moochat_userscontentavatarimage" src={UserStore.getAvatar(this.props.group.members[2])}/>;

        }
        if(this.props.group.members.length > 3){
            img4 = <Avatar className="moochat_userscontentavatarimage" src={UserStore.getAvatar(this.props.group.members[3])}/>;
        }
        if(img3 == ''){
            showImage = "two_member";
        }
        else if(img3 != '' && img4 == ''){
            showImage = "three_member";
        }
        var room = _getRoomInfo(0,this.props.group.id);
        var unseen =(room.id != 0 && room.id != undefined)?<RoomUnseenStatusItemWindow room={room}/>:"";
        var review_message =(room.id != 0 && room.id != undefined)?<RoomReviewMessageItemWindow room={room}/>:"";

        return <li className="item mooGroup " id={'moochat_userlist_'+this.props.group.id} onClick={this.handleGroupItemClick}>
			{unseen}
			<div className={showImage + " moochat_userscontentavatar "}>
				{img1}{img2}{img3}{img4}
			</div>
			<div className="right">
				<div className="name">{this.props.name}</div>
				{review_message}
			</div>
        </li>;
    }

};
class GroupItemWindows extends React.Component {
    componentDidUpdate(){
        UserStore.updateMissingUser();
    }
    render(){

        var GroupItemWindows = [];

        if (this.props.groups.length > 0 && !this.props.isHideGroup) {
            for(var i=0;i<this.props.groups.length;i++){
                var name = UserStore.getNames(this.props.groups[i].members);
                GroupItemWindows.push(<GroupItemWindow key={i} group={this.props.groups[i]} name={name} enableChatWindow={this.props.enableChatWindow}/>);
            }

            return (<div className="mooGroup"><div className="mooGroup_title">{__.t("group_conversations")}</div> <ul className="moochat_mobile_userlist">{GroupItemWindows}</ul></div>);
        }else{
            return (<div></div>);
        }

    }
};
class RoomUnseenMessages extends React.Component {
    constructor(props) {
        super(props);
        this.state = {data:CounterUnseenMessageStore.getAll()};
        this._onChange = this._onChange.bind(this);
    }
    componentDidMount(){
        CounterUnseenMessageStore.addChangeListener(this._onChange);
    }
    componentWillUnmount() {
        CounterUnseenMessageStore.removeChangeListener(this._onChange);
    }
    _onChange(roomId) {
        this.setState({data:CounterUnseenMessageStore.getAll()});
    }
    render(){
        var roomsUnseenNewMessages = 0;
        if(this.props.ids.length > 0 ){
            for(var i=0;i<this.props.ids.length;i++){


                roomsUnseenNewMessages += ((CounterUnseenMessageStore.get(this.props.ids[i]) != 0) ? 1 : 0);

            }
        }

        var roomsDisplayUnseenNewMessages = (roomsUnseenNewMessages != 0) ? 'block' : 'none ';
        return <span style={{display:roomsDisplayUnseenNewMessages}} className="moochat_mobile_message">{roomsUnseenNewMessages}</span>;
    }
};
class RoomUnseenStatusItemWindow extends React.Component {
    constructor(props) {
        super(props);
        this.handleCloseUnseenWindow = this.handleCloseUnseenWindow.bind(this);
        this.hancleClickActiveUnseenWindwow = this.hancleClickActiveUnseenWindwow.bind(this);
        this.hancleClickActiveUnseenWindwow = this.hancleClickActiveUnseenWindwow.bind(this);
        this._onChange = this._onChange.bind(this);
        this.state = {newMessages:CounterUnseenMessageStore.get(this.props.room.id)};;
    }
    componentDidMount(){
        CounterUnseenMessageStore.addChangeListener(this._onChange);
    }
    componentWillUnmount() {
        CounterUnseenMessageStore.removeChangeListener(this._onChange);
    }
    handleCloseUnseenWindow(){
        ChatWebAPIUtils.destroyARoom(this.props.room.id)
    }
    _onChange(roomId) {
        if(roomId == this.props.room.id || roomId == 0){
            this.setState({newMessages:CounterUnseenMessageStore.get(this.props.room.id)});
        }
    }
    hancleClickActiveUnseenWindwow(){
        ChatWebAPIUtils.activeARoom(this.props.room.id);
    }
    render(){
        var newMessageStyle = (this.state.newMessages == 0 ) ? "hidden" : "visible";
        if (ChatMooUtils.getChatSoundState() == ChatConstants.SOUND_ENABLE && ChatMooUtils.isChatSoundGlobalEnable()) {
            RoomStore.playSound(this.props.room.id);
        }

        return <span
            id={"unseenRoom_"+this.props.room.id}
            className="moochat_new_count"
            style={{visibility: newMessageStyle}}>
            {this.state.newMessages}
        </span>;
    }
};
class RoomReviewMessageItemWindow extends React.Component {
    constructor(props) {
        super(props);
        this._onChange = this._onChange.bind(this);

        this.state = {latestMesasge:MessageStore.getLatestMesasge(this.props.room.id)};
    }
    componentDidMount(){
        MessageStore.addChangeListener(this._onChange);
    }
    componentWillUnmount() {
        MessageStore.removeChangeListener(this._onChange);
    }
    _onChange(roomId) {
        if(roomId == this.props.room.id || roomId == 0){
            this.setState({latestMesasge:MessageStore.getLatestMesasge(this.props.room.id)});
        }
    }
    render(){
        return  <div className="moochat_review_message" dangerouslySetInnerHTML={{__html: this.state.latestMesasge}}></div>

    }
};
// FriendStatus UI

export default class FriendStatusWindow extends React.Component {
    constructor(props) {
        super(props);

        var isOffline = (ChatMooUtils.getChatStatus() == ChatConstants.USER_OFFLINE) ? true : false;
        var isHideGroup = (ChatMooUtils.getHideGroupState() == ChatConstants.HIDE_GROUP_ENABLE) ? true : false;
        this.state =  {
            isOffline: isOffline,
            isHideGroup:isHideGroup,
            mooChatStatusIsClicked: false,
            friendsWindowsIsShowed:false,
            chatWindowIsShowed:false,
            searhFriendsIsShowed: false,
            idUserIsBeingUsedForCreatingARoom:0,
            idGroupIsBeingUsedForCreatingARoom:0,
            users: UserStore.getAll(),
            friends: FriendStore.getAll(),
            rooms: RoomStore.getAll(),
            groups: GroupStore.getAll(),
            chat_online_status: ViewerStore.getOnlineStatus()
        };
        this.filterTrigger = this.filterTrigger.bind(this);
        this.handleSearchFriendsIsCliked = this.handleSearchFriendsIsCliked.bind(this);
        this.handleChatSubmit = this.handleChatSubmit.bind(this);
        this.handleHideGroupsIsClicked = this.handleHideGroupsIsClicked.bind(this);
        this.handleTurnOffChatIsClick = this.handleTurnOffChatIsClick.bind(this);
        this.handleChatStatusClick = this.handleChatStatusClick.bind(this);
        this.handleCloseFriendsWindows = this.handleCloseFriendsWindows.bind(this);
        this.enableChatWindow = this.enableChatWindow.bind(this);
        this.closeChatWindow = this.closeChatWindow.bind(this);
        this._onChange = this._onChange.bind(this);
        this.handleSetCurrentOnlineStatus = this.handleSetCurrentOnlineStatus.bind(this);
    }
    componentWillReceiveProps(nextProps) {

        if(nextProps.openChatWithOneUser.hash != this.props.openChatWithOneUser.hash && nextProps.openChatWithOneUser.id != 0){

            if (!this.state.isOffline) {
                this.setState({mooChatStatusIsClicked: !this.state.mooChatStatusIsClicked , friendsWindowsIsShowed:true});
                this.enableChatWindow(nextProps.openChatWithOneUser.id,0);
            } else {
                ChatMooUtils.turnOnChat();
                this.setState({mooChatStatusIsClicked: !this.state.mooChatStatusIsClicked, friendsWindowsIsShowed:true, isOffline: false});
                this.enableChatWindow(nextProps.openChatWithOneUser.id,0);
            }
        }
        if(nextProps.openChatRoom.hash != this.props.openChatRoom.hash && nextProps.openChatRoom.id != 0){

            if (!this.state.isOffline) {
                this.setState({mooChatStatusIsClicked: !this.state.mooChatStatusIsClicked , friendsWindowsIsShowed:true});
                this.enableChatWindow(0,nextProps.openChatRoom.id);
            } else {
                ChatMooUtils.turnOnChat();
                this.setState({mooChatStatusIsClicked: !this.state.mooChatStatusIsClicked, friendsWindowsIsShowed:true, isOffline: false});
                this.enableChatWindow(0,nextProps.openChatRoom.id);
            }
        }
    }
    filterTrigger() {
        ChatWebAPIUtils.findAFriendByName(this.refs.filterInput.value);
    }
    componentDidMount(){
        //ChatMooUtils.initSlimScroll('#moochat_userscontent');
        UserStore.addChangeListener(this._onChange);
        FriendStore.addChangeListener(this._onChange);
        RoomStore.addChangeListener(this._onChange);
        GroupStore.addChangeListener(this._onChange);
    }
    componentWillUnmount () {
        UserStore.removeChangeListener(this._onChange);
        FriendStore.removeChangeListener(this._onChange);
        RoomStore.removeChangeListener(this._onChange);
        GroupStore.removeChangeListener(this._onChange);
    }
    _onChange(){
        this.setState({
            friends: FriendStore.getAll(),
            rooms: RoomStore.getAll(),
            users: UserStore.getAll(),
            groups: GroupStore.getAll()
        });
    }
    handleSearchFriendsIsCliked(){
        this.setState({searhFriendsIsShowed: !this.state.searhFriendsIsShowed});
    }
    handleChatSubmit(e){
        e.preventDefault();
    }
    handleHideGroupsIsClicked(isHide){
        ChatMooUtils.hideGroup(isHide);
        this.setState({isHideGroup:isHide});
    }
    handleTurnOffChatIsClick(){
        ChatMooUtils.turnOffChat();
        this.setState({mooChatStatusIsClicked: false, isOffline: true,friendsWindowsIsShowed:false});
    }
    handleChatStatusClick(){
        // Hide comment native keyboard show up
        if(ChatMooUtils.isIOS()) {
                window.webkit.messageHandlers.action.postMessage({'command':'hideComment', data:[]})
        }
        // Disable refesh when scroll up on android devices
        if(ChatMooUtils.isAndroid()) {
            Android.hideComment();
            Android.disableRefresh();
        }
        if (!this.state.isOffline) {
            this.setState({mooChatStatusIsClicked: !this.state.mooChatStatusIsClicked , friendsWindowsIsShowed:true});
        } else {
            ChatMooUtils.turnOnChat();
            this.setState({mooChatStatusIsClicked: !this.state.mooChatStatusIsClicked, friendsWindowsIsShowed:true, isOffline: false});
        }
    }
    handleCloseFriendsWindows(){
        // Enable android devices refesh when scroll up.
        if(ChatMooUtils.isAndroid()) {
            Android.enableRefresh();
        }
        this.setState({mooChatStatusIsClicked: !this.state.mooChatStatusIsClicked , friendsWindowsIsShowed:false});
    }
    enableChatWindow(friendId,groupId){
        var room = _getRoomInfo(friendId,groupId);
        RoomStore.setRoomMobiIsActive(room.id);
        this.setState({friendsWindowsIsShowed:false,chatWindowIsShowed:true,idUserIsBeingUsedForCreatingARoom:friendId,idGroupIsBeingUsedForCreatingARoom:groupId});
    }
    closeChatWindow(){
        var room = _getRoomInfo(this.state.idUserIsBeingUsedForCreatingARoom,this.state.idGroupIsBeingUsedForCreatingARoom);
        RoomStore.setRoomMobiIsActive(0);
        if(room.id != 0 && this.state.chatWindowIsShowed){
            RoomStore.minimizeChatWindow(room.id,true);
        }
        this.setState({friendsWindowsIsShowed:true,chatWindowIsShowed:false,idUserIsBeingUsedForCreatingARoom:0,idGroupIsBeingUsedForCreatingARoom:0});
    }
    componentDidUpdate(){
        if(ChatMooUtils.isTurnOffForFirstTimeUsing()){
            ChatMooUtils.setFirsTimeUsing(false);
            this.setState({mooChatStatusIsClicked: false, isOffline: true});
        }
    }
    handleSetCurrentOnlineStatus(status){
        this.setState({chat_online_status: status});
    }
    render() {
        // control
        // offline status
        var classUserIconStatus = !this.state.isOffline ? 'moochat_user_available2' : 'moochat_user_offline2';
       var classIconStatus = !this.state.isOffline ? 'moochat_icon_available2' : 'moochat_icon_offline2';
        // end offline status
        //    show/hide sidebar friendlist
        var mooChatStatusDisplay = this.state.mooChatStatusIsClicked ? 'none' : 'block';
        // Hacking for integration with moosocial
        if(ChatMooUtils.isTurnOnNotification()){
            //mooChatStatusDisplay = 'none';
            // This option waiting new android design
        }
        // End hacking for intrtaion with moosocial
        var friendsWindowsDisplay = this.state.friendsWindowsIsShowed ? 'block' : 'none';
        var chatWindowDisplay = this.state.chatWindowIsShowed ? 'block' : 'none';
        var searchDisplay = this.state.searhFriendsIsShowed ? 'block' : 'none';

        //    end show/hide sidebar friendlist
        if(this.state.mooChatStatusIsClicked){
            ChatMooUtils.hideBodyChildNode();
         
        }else{
            ChatMooUtils.showBodyChildNode();

        }
        // set how many friend online
        var countFriends = 0;
        if (this.state.friends.keyonline) {
            countFriends = this.state.friends.keyonline.length;
        }
        var userstabText = !this.state.isOffline ? countFriends : <div><i className="material-icons">person</i><i className="moochat_icon_offline"></i></div>;
        var mobileClassIcon = !this.state.isOffline ? "moochat_mobile_icon" : "moochat_mobile_icon offline";
        var mobileClassOnline = !this.state.isOffline ? "moochat_mobile_online" : "moochat_mobile_offline";
        // end set how many friend online

        var name = UserStore.getName(ViewerStore.get('id'));
        var avatar = <Avatar className="moochat_userscontentavatarimage" src={UserStore.getAvatar(ViewerStore.get('id'))}/>;
        var room = _getRoomInfo(this.state.idUserIsBeingUsedForCreatingARoom,this.state.idGroupIsBeingUsedForCreatingARoom);
        if(room.id != 0 && this.state.chatWindowIsShowed){
            RoomStore.minimizeChatWindow(room.id,false);
        }
        var badges = <div  id="moochat_mobile" onClick={this.handleChatStatusClick}  style={{display: mooChatStatusDisplay}}>
            <div className={mobileClassIcon}>
                <i className="material-icons">chat_bubble</i>
                <span className={mobileClassOnline}>{userstabText}</span>
                <RoomUnseenMessages ids={this.state.rooms.isCreated}/>
            </div>
        </div>;
        if(this.state.isOffline){
            badges = <div id="moochat_mobile" onClick={this.handleChatStatusClick}  style={{display: mooChatStatusDisplay}}><div className="moochat_mobile_icon offline">{__.t("offline")}</div></div>
        }
        
        var online_status_bubble = "";
        if(this.state.chat_online_status == ChatConstants.ONLINE_STATUS.ACTIVE){
            online_status_bubble = <span className="moochat_userscontentdot moochat_available"></span>;
        }
        else if(this.state.chat_online_status == ChatConstants.ONLINE_STATUS.BUSY){
            online_status_bubble = <span className="moochat_userscontentdot moochat_busy"></span>;
        }
        else if(this.state.chat_online_status == ChatConstants.ONLINE_STATUS.INVISIBLE){
            online_status_bubble = <span className="moochat_userscontentdot moochat_offline"></span>;
        }
        return (
        <div>
            {badges}

            <div className="moochat_mobile_list" style={{display:friendsWindowsDisplay}}>
                <div className="moochat_mobile_header friendChatAll">
                    <div className="moochat_username">
                        {avatar}{name}
                        {online_status_bubble}
                        <ChatSettings
                            handleSetCurrentOnlineStatus={this.handleSetCurrentOnlineStatus}
                            chatStatus="1"
                            currentStatus={this.state.chat_online_status}
                        />
                    </div>
                    <div className="closeChatMobile">
                    <i className="material-icons" onClick={this.handleCloseFriendsWindows}>clear</i>
                        </div>
                    <div className="optionChatMobile">
                    <i className="material-icons">more_vert</i>
                    <ChatSettings
                                  handleTurnOffChatIsClick={this.handleTurnOffChatIsClick}
                                  handleHideGroupsIsClicked={this.handleHideGroupsIsClicked}
                    />
                    </div>
                    <div className="searchChatMobile">
                    <i className="material-icons" onClick={this.handleSearchFriendsIsCliked}>search</i>
                        </div>
                </div>
                <div className="moochat_search" style={{display:searchDisplay}}>
                    <form onSubmit={this.handleChatSubmit}>


                        <input id="moochat_search" type="text" name="moochat_search" ref="filterInput"
                               className="moochat_search moochat_search_light textInput"
                               placeholder={__.t("type_to_find_a_user")} onChange={this.filterTrigger}/>
                    </form>
                </div>
                <div className="friend_title">{__.t("friends")}</div>
                <FriendStatusItemWindows friends={this.state.friends}
                                         createChatWindow={this.props.createChatWindow}
                                         enableChatWindow={this.enableChatWindow}
                />
                <GroupItemWindows groups={this.state.groups} isHideGroup={this.state.isHideGroup} enableChatWindow={this.enableChatWindow} />

            </div>

            <div style={{display:chatWindowDisplay}}>
                <ChatWindows
                    closeChatWindow={this.closeChatWindow}
                    friends={this.state.friends}
                    users={this.state.users}
                    idUserIsBeingUsedForCreatingARoom={this.state.idUserIsBeingUsedForCreatingARoom}
                    idGroupIsBeingUsedForCreatingARoom={this.state.idGroupIsBeingUsedForCreatingARoom}
                    enableChatWindow={this.enableChatWindow}
                />
            </div>

        </div>
        );
    }
};

