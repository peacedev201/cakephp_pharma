import React from 'react';
import _ from 'lodash';
import {Card, CardActions, CardHeader, CardMedia, CardTitle, CardText} from 'material-ui/Card';
import {GridList, GridTile} from 'material-ui/GridList';
import {List, ListItem} from 'material-ui/List';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import {pharse} from '../../utility/mooApp';
import {CommentItem} from '../utility/CommentItem.js';
import {DetailActionItem} from '../utility/DetailActionItem';
import CommentActionTypes from '../../data/actions/CommentActionTypes';
import {FirstLoading} from "../utility/FirstLoading";

import NavigationMoreVert from 'material-ui/svg-icons/navigation/more-vert';
import MenuItem from 'material-ui/MenuItem';
import IconMenu from 'material-ui/IconMenu';
import IconButton from 'material-ui/IconButton';

import {isIOS} from "../../utility/MobileDetector";
import IOSAction from '../../utility/IOSAction';
import TopicActions from '../../data/actions/TopicActions';
import AppAction from '../../utility/AppAction';

export class TopicDetail extends React.Component {
    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
        //this.handleEditDetail = this.handleEditDetail.bind(this);
        this.handlePin = this.handlePin.bind(this);
        this.handleLock = this.handleLock.bind(this);
        this.state = {
            pin: ( _.has(window, "pin") && window.pin == '#pin' ) ? true : false,
        };
    }
    handlePin(type) {
        if(type == '#pin') {
            TopicActions.pinTopic(window.topicId);
        }
        else {
            TopicActions.unPinTopic(window.topicId);
        }
        this.setState({
                pin: !this.state.pin,
            });
    }
    handleLock(type) {
        if(type == '#lock') {
            TopicActions.lockTopic(window.topicId);
        }
        else {
            TopicActions.unLockTopic(window.topicId);
        }
    }
    handleClick(url) {
        AppAction.hideComment();
        window.location.href = url;
    }
    handleEditDetail() { 
        AppAction.hideComment();
		var url = mooConfig.url.full + mooConfig.url.base + '/topics/create/' + window.topicId;
        AppAction.openEditNotNewTab({"link":url});
    }
    render() {
        var content,block,contentTopic,topicView,title,description,media,date,by,allowAction,category,groupInfo,canEdit,canDelete,menu,pin,message,canLock,canUnlock;
        canUnlock = canLock = message = pin = menu = canDelete = canEdit = groupInfo = allowAction = by = date = media = description = title = topicView = content = <div></div>;
        
        if( this.props.topics.get('isOpen') == true  ) { 
            
            if( this.props.topics.get('isShowErrorMessage') == true  ) {
                var mText = this.props.topics.get('message');
                topicView = <div style={{fontFamily:"Roboto, sans-serif",color:"#b94a48",backgroundColor: "#f2dede",border: "1px solid #eed3d7",fontSize:"14px",borderRadius:"3px",padding:"15px",marginbottom: "10px"}}>{mText.message}</div>
            }
            else {
                if( this.props.topics.get('isShowMessage') == true  ) {
                    if( this.props.topics.get('isReload') == true  ) {
                        window.location.reload();
                    }
                    var mText = this.props.topics.get('message');
                    message = <div style={{color:"#468847",backgroundColor: "#dff0d8",border: "1px solid #d6e9c6",fontSize:"14px",borderRadius:"3px",padding:"15px",marginbottom: "10px"}}>{mText.message}</div>
                }
                var topic = this.props.topics.get('topics');
                if (_.has(topic, "thumbnail.850")) {  
                    media = <CardMedia>
                            <div style={{backgroundImage:"url('"+_.get(topic,"thumbnail.850")+"')",backgroundRepeat:"no-repeat",paddingBottom:"56.25%",display:"block",backgroundSize:"cover",backgroundPosition:"center center"}}></div>
                        </CardMedia>;
                }
                if (_.has(window, "pin")) {
                    if (this.state.pin) {
                        pin = <MenuItem onClick={() => this.handlePin('#pin')} primaryText={pharse("pin")} />;
                    } 
                    else { 
                        pin = <MenuItem onClick={() => this.handlePin('#unpin')} primaryText={pharse("unPin")} />;
                    } 
                }
                if (_.has(window, "canLock")) { 
                    canLock = <MenuItem onClick={() => this.handleLock('#lock')} primaryText={pharse("lock")} />;
                } 
                if (_.has(window, "canUnlock")) { 
                    canUnlock = <MenuItem onClick={() => this.handleLock('#unlock')} primaryText={pharse("unLock")} />;
                } 
                if (_.has(window, "canEdit")) { 
                    canEdit = <MenuItem onClick={() => this.handleEditDetail()} primaryText={pharse("edit")} />;
                } 
                if (_.has(window, "canDelete")) { 
                    canDelete = <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/topics/do_delete/' + window.topicId )} primaryText={pharse("delete")} />;
                } 
                menu = <IconMenu
                        style={{display:"block",position:"absolute",right:"5px",top:"18px"}}
                        iconButtonElement={<IconButton style={{padding:"0px",height:"auto",width:"100%",display:"block"}}  ><NavigationMoreVert ></NavigationMoreVert></IconButton>}
                        anchorOrigin={{horizontal: 'right', vertical: 'top'}}
                        targetOrigin={{horizontal: 'right', vertical: 'top'}}
                    >   
                        {pin}
                        {canLock}
                        {canUnlock}
                        {canEdit}
                        {canDelete}
                        <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/reports/ajax_create/Topic_Topic/' + window.topicId )} primaryText={pharse("report")} />
                </IconMenu>;
                if (_.has(topic, "title")) {
                     title = <div style={{position:"relative"}}>
                        <div style={{padding:"20px 30px 10px 10px",color:"#000",fontSize:'16px',fontWeight:"bold",lineHeight:"20px"}} dangerouslySetInnerHTML={{__html: topic.title}}></div>
                        {menu}
                       </div>
                }
                if (_.has(window, "topicCategory")) {
                    category = <div style={{display:"inline-block",margin:"5px 0 5px 5px"}} >{pharse('in')}<div style={{margin:"0 0 0 5px",display:"inline-block",color:"#000",fontSize:'12px',fontWeight:"600"}} >{window.topicCategory}</div></div>;
                }
                if (_.has(topic, "publishedTranslated")) {
                    by = <div style={{display:"inline-block",margin:"5px"}} >{pharse('by')}<div onClick={() => this.handleClick(_.get(topic,'userUrl',''))}  style={{margin:"0 5px",display:"inline-block",color:"#247BBA",fontSize:'12px',fontWeight:"600"}} >{topic.userName}</div></div>;
                    date = <div style={{padding:"10px 10px",fontSize:"12px",color:"#999"}}>{topic.publishedTranslated}{category}{by}</div>;
                }
                if (_.has(topic, "groupName") && _.get(topic, "groupName") != '' ) {
                    groupInfo = <div style={{padding:"10px 10px",fontSize:"12px",color:"#999"}}><div style={{float:"left",margin:"0 10px 0 0"}} >{pharse('inGroup')} : </div> <div style={{color:"#000",fontSize:'12px',fontWeight:"600"}} >{topic.groupName}</div></div>;
                }
                if (_.has(topic, "body")) {
                    description = <div className="post-body" style={{padding:"10px 10px 10px ",fontSize:"15px",lineHeight:"1.5"}} dangerouslySetInnerHTML={{__html: topic.body}}></div>
                }
                if(_.get(topic,"allowAction") == true ) {
                    allowAction = <DetailActionItem {...topic} likes={this.props.likes} reactions={this.props.reactions} object={'Topic_Topic'} />
                }

                topicView = <div style={{margin:"-8px"}} >
                    <Card style={{boxShadow:"none"}} >{message}{media}{title}{groupInfo}{date}{description}</Card>
                    {allowAction}
                    <CommentItem {...this.props} likes={this.props.likes} reactions={this.props.reactions} />
                </div>;
            }
        }
        else {
             content = <FirstLoading></FirstLoading>;
        }
        
        return <MuiThemeProvider ><div>{content}{topicView}</div></MuiThemeProvider>;
        
    }
}
