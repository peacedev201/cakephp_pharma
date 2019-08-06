import React from 'react';

import ChatWebAPIUtils from '../../utils/ChatWebAPIUtils';
import ChatMooUtils from '../../utils/ChatMooUtils';
import UserStore from '../../stores/UserStore';
import ViewerStore from '../../stores/ViewerStore';
var moment = require('../../utils/ChatMooI18n').moment;
var __ = require('../../utils/ChatMooI18n').i18next;
import Avatar from './../Avatar';
import validUrl from 'valid-url';
import ChatVideoCallButton from './ChatVideoCallButton';
import RoomStore from '../../stores/RoomStore';
import CHAT_CONSTANTS from '../../constants/ChatConstants';
import ChatMooEmoji from "../../utils/ChatMooEmoji";

export default  class ChatMessage extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            room : RoomStore.get(this.props.data.room_id)
        };
    }
    locate = {
        call_again:__.t("call_again"),
        call_back: __.t("call_back"),
        you_missed_a_video_chat_with_name: __.t("you_missed_a_video_chat_with_name"),
        name_missed_a_video_chat_with_you: __.t("name_missed_a_video_chat_with_you"),
    }
    imageClick = (e) =>{
        e.preventDefault();
        ChatMooUtils.popupImage(this.refs.image);
    }
    handleImageLoaded = () =>{
        this.props.scrollToBottom();
    }
    fileClick = (e) =>{
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
                    <Avatar className="ccmsg_avatar" src={chatter.avatar} dataTip={chatter.name} dataPlace="left"/>
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
                        break;
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
                                <Avatar className="ccmsg_avatar" src={chatter.avatar} dataTip={chatter.name} dataPlace="left"/>
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
                        break;
                    default:
                }

                break;
            default:
                chatboxmessagecontentClassName += (this.props.data.note_one_emoj_only == 1) ? " only_emoji" : "";
                if(ChatMooEmoji.isOneEmojiOnly(this.props.data.content)){
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