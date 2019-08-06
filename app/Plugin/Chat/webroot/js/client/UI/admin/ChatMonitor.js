import React from 'react';

import ChatWebAPIUtils from '../../utils/admin/ChatWebAPIUtils';
import GeneralStore from '../../stores/admin/GeneralStore';
import MonitorStore from '../../stores/admin/MonitorStore';
import UserStore from '../../stores/admin/UserStore';
import RoomStore from '../../stores/admin/RoomStore';
import CHAT_CONSTANTS from '../../constants/admin/ChatConstants';
var __ = require('../../utils/ChatMooI18n').i18next;

class ChatMonitorMessages extends React.Component {
    locate = {
        call_again:__.t("call_again"),
        call_back: __.t("call_back"),
        you_missed_a_video_chat_with_name: __.t("you_missed_a_video_chat_with_name"),
        name_missed_a_video_chat_with_you: __.t("name_missed_a_video_chat_with_you"),
    }
    render(){
            var items = [];
            var uIds  = [];
            var tmp;

            if (this.props.data.length > 0){
                for(var i=0;i<this.props.data.length;i++){
                    uIds = RoomStore.getMembers(this.props.data[i].room_id);
                    tmp = [];
                    if(uIds.length > 0){
                        for(var j=0;j<uIds.length;j++){
                            if(uIds[j] != this.props.data[i].sender_id){
                                tmp.push(uIds[j]);
                            }
                        }
                    }
                    var content = this.props.data[i].note_content_html;
                    var type = this.props.data[i].type;
                    switch (type) {
                        case "image":
                        case "file":
                            var file = ChatWebAPIUtils.getSiteUrl() + '/uploads/chat/room-' + this.props.data[i].room_id + '/' + content;
                            content = <div>{UserStore.getName(this.props.data[i].sender_id)} send a <a target="_blank" href={file}>file</a></div>;
                            break;
                        case "system":
                            var system = JSON.parse(content);
                            switch (system.action) {
                                case "left_the_conversation":
                                    content = <div>{UserStore.getName(this.props.data[i].sender_id)} left the conversation. </div>;
                                    break;
                                case "added":
                                    var userB = "";
                                    for (var jj = 0; jj < system.usersId.length; jj++) {

                                        userB += ((jj == 0) ? " " : ", ") + UserStore.getName(system.usersId[jj]);


                                    }
                                    content = <div>{UserStore.getName(this.props.data[i].sender_id)} added {userB} </div>;
                                    break;
                                case CHAT_CONSTANTS.SYSTEM_MESSAGE_ACTION.MISS_VIDEO_CALL:
                                    var item = this.props.data[i];
                                    var system = JSON.parse(item.content);
                                    var miss_call_content = "";
                                    if(system.caller_id == item.sender_id)
                                    {
                                        miss_call_content = this.locate.you_missed_a_video_chat_with_name.replace("name", UserStore.getName(system.caller_id));
                                    }
                                    else
                                    {
                                        miss_call_content = this.locate.name_missed_a_video_chat_with_you.replace("name", UserStore.getName(item.sender_id));
                                    }

                                    content = <div>{miss_call_content}</div>;
                            }
                            break;
                        default:
                    }
                    if(typeof content.key != "undefined")
                    {
                        content = <div className="col-md-8">{content}</div>
                    }
                    else
                    {
                        content = <div className="col-md-8" dangerouslySetInnerHTML={{__html: content}}></div>
                    }
                    items.push(<li className="list-group-item" key={i} style={{borderBottom:"1px solid #ccc",paddingTop:"7px"}}>
                        <div className="col-md-2" style={{borderRight:"1px dotted #333"}}><span>{UserStore.getName(this.props.data[i].sender_id)}</span> wrote to <span>{UserStore.getNames(tmp)}</span></div>
                        {content}
                        <div className="col-md-2">{this.props.data[i].created}</div>
                    </li>);
                }
            }
            return <ul className="list-group monitorChat">{items}</ul>;
        
    }
};
export default class ChatMonitor extends React.Component {
    constructor(props) {
        super(props);
        this.state = {general: GeneralStore.getAll(),monitor:MonitorStore.getAll(),users:UserStore.getAll(),rooms:RoomStore.getAll()};
        this._onChange = this._onChange.bind(this);
    }
    componentDidMount(){
        ChatWebAPIUtils.initMonitorSocket();
        GeneralStore.addChangeListener(this._onChange);
        MonitorStore.addChangeListener(this._onChange);
        UserStore.addChangeListener(this._onChange);
        RoomStore.addChangeListener(this._onChange);
    }
    componentWillUnmount(){
        GeneralStore.removeChangeListener(this._onChange);
        MonitorStore.removeChangeListener(this._onChange);
        UserStore.removeChangeListener(this._onChange);
        RoomStore.removeChangeListener(this._onChange);
    }
    _onChange(){
        this.setState({general: GeneralStore.getAll(),monitor:MonitorStore.getAll(),users:UserStore.getAll(),rooms:RoomStore.getAll()});
    }
    componentDidUpdate(){
        UserStore.updateMissingUser();
        RoomStore.updateMissingEntries();
    }
    render(){
        if(GeneralStore.isServerBeingChecked()){
            return <div className="note note-info">Checking</div>
        }

        if(GeneralStore.isServerOffline()){
            return <div className="note note-info">
               <p>MooChat is not working on your site, your chat server URL might be incorrect or your chat server is down</p>
                <p>You can go to <a href="./chat_settings">Settings</a>  to make sure that your chat server URL is correct or <a href="./chat_error">Error</a> to see the cause of problem which makes your server down.</p>
            </div>
        }

        if(GeneralStore.isServerOnline()){
            return <div className="note note-info" >
                <p>See what users are typing in real-time on your site</p>
                <p>USERS ONLINE : {this.state.general.info.moo_users_chatting}</p>
                <ChatMonitorMessages data={this.state.monitor} />
            </div>
        }

        return <div>Empty</div>;

    }
};
