import React from 'react';
import FriendStatusWindow from'../UI/FriendStatusWindow';
import ChatWindows from'../UI/ChatWindows';
import ChatWebAPIUtils from'../utils/ChatWebAPIUtils';
import ViewerStore from'../stores/ViewerStore';
import PoupWindow from'../UI/PoupWindow';
import CHAT_CONSTANTS from'../constants/ChatConstants';
//var Perf = require('react-addons-perf');
//window.Perf = Perf;
//window.Perf.start();
export default class ChatApp extends React.Component{
    constructor(props){
        super(props);
        this._onChange = this._onChange.bind(this);
        this.handleResize = this.handleResize.bind(this);
        this.state = {owner: ViewerStore.getAll(), isGuest: ViewerStore.isGuest(),modalReportData: {isOpen:false,rId:0}};
    }
    componentDidMount(){
       
        if (ChatWebAPIUtils.boot()) {
            window.addEventListener('resize', this.handleResize);
        }
        ViewerStore.addChangeListener(this._onChange);
    }
    componentWillUnmount(){
        window.removeEventListener('resize', this.handleResize);
        ViewerStore.removeChangeListener(this._onChange);
    }
    _onChange(){
        this.setState({owner: ViewerStore.getAll(), isGuest: ViewerStore.isGuest()});
    }
    componentDidUpdate(){
        if (!this.state.isGuest) {
            ChatWebAPIUtils.sendRequestGetMyFriends();
            ChatWebAPIUtils.sendRequestGetMyGroups();
        }

    }
    handleResize(e) {
        ChatWebAPIUtils.reRenderAllRooms();
    }
    render(){

        if (this.state.isGuest) {
            return (<div id="moochat"></div>);
        } else {
            return (
                <div id="moochat">
                    <FriendStatusWindow key={1}/>
                    <ChatWindows />
                    <PoupWindow  />
                </div>
            );
        }
    }
    // Public API
    openChatWithOneUser(uId){
        ChatWebAPIUtils.createChatWindowForAUser(uId);
    }
    openChatRoom(rId){
        ChatWebAPIUtils.createChatWindowByRoomId(rId);
    }
    markMessagesInARoomIsSeen(rId){
        ChatWebAPIUtils.markMessagesInARoomIsSeen(rId);
    }
    markReadOnMessagesPage(e){
        ChatWebAPIUtils.markReadOnMessagesPage(e);
    }
    markReadAllMessages(uId){
        ChatWebAPIUtils.markReadAllMessages(uId);
    }
};
