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

import AppAction from '../../utility/AppAction';
import IOSAction from '../../utility/IOSAction';
import {isIOS} from "../../utility/MobileDetector";

export class VideoDetail extends React.Component {
    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
        this.handleEditDetail = this.handleEditDetail.bind(this);
    }
    handleClick(url) {
        window.location.href = url;
    }
    handleEditDetail() {
        AppAction.hideComment();
        var url = mooConfig.url.full + mooConfig.url.base + '/videos/create/' + window.videoId;
        AppAction.openEditNotNewTab({"link":url});
    }
    render() {
        var content,block,contentVideo,videoView,title,description,media,date,by,category,groupInfo,canEdit,canDelete,menu;
        menu = canDelete = canEdit = groupInfo = by = date = media = description = title = videoView = content = <div></div>;
        
        if( this.props.videos.get('isOpen') == true  ) { 
            
            if( this.props.videos.get('isShowMessage') == true  ) {
                var mText = this.props.videos.get('message');
                videoView = <div style={{fontFamily:"Roboto, sans-serif",color:"#b94a48",backgroundColor: "#f2dede",border: "1px solid #eed3d7",fontSize:"14px",borderRadius:"3px",padding:"15px",marginbottom: "10px"}}>{mText.message}</div>
            }
            else {
                var video = this.props.videos.get('videos');
                if(_.has(video,"videoType")){
                    switch (video.videoType){ 
                        case 'youtube':
                            media =  <div className="video-detail" >
                            <iframe style={{}} width={'100%'} height={'100%'} src={'https://www.youtube.com/embed/' + _.get(video,"videoSourceId")+'?wmode=opaque'} frameBorder={0} allowFullScreen></iframe>
                            </div>;
                            break;
                        case 'vimeo':
                            media =  <div className="video-detail" >
                                <iframe width={'100%'} height={'auto'} src={'https://player.vimeo.com/video/' + _.get(video,"videoSourceId")+'?wmode=opaque'} frameBorder={0} allowFullScreen></iframe>
                            </div>;
                            break;
                    }
                }

                if(_.has(video,"pcUpload") && _.get(video,"pcUpload") == true ){
                        if (isIOS()) {
                                media = <div className="video-detail" >
                                    <video width="100%" controls
                                        poster={_.get(video,"thumbnail.850")}>
                                        <source src={video.videoSource} type='video/mp4'/>
                                    </video>
                                </div>;
                        } else {
                                media = <div className="video-detail" onClick={() => Android.openUrl(_.get(video, "videoSource"))}>
                                    <div style={{backgroundImage:"url('" + _.get(video, "thumbnail.850") + "')",backgroundRepeat:"no-repeat",paddingBottom:"56.25%",display:"block",backgroundSize:"cover",backgroundPosition:"center center"}}></div>
                                </div>;
                        }
                }
                
                if (_.has(window, "canEdit")) { 
                    canEdit = <MenuItem onClick={() => this.handleEditDetail()} primaryText={pharse("edit")} />;
                } 
                if (_.has(window, "canDelete")) { 
                    canDelete = <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/videos/delete/' + window.videoId )} primaryText={pharse("delete")} />;
                } 
                menu = <IconMenu
                        style={{display:"block",position:"absolute",right:"5px",top:"18px"}}
                        iconButtonElement={<IconButton style={{padding:"0px",height:"auto",width:"100%",display:"block"}}  ><NavigationMoreVert ></NavigationMoreVert></IconButton>}
                        anchorOrigin={{horizontal: 'right', vertical: 'top'}}
                        targetOrigin={{horizontal: 'right', vertical: 'top'}}
                    >
                        {canEdit}
                        {canDelete}
                        <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/reports/ajax_create/Video_Video/' + window.videoId )} primaryText={pharse("report")} />
                </IconMenu>;
                if (_.has(video, "title")) {
                    title = <div style={{position:"relative"}}>
                        <div style={{padding:"20px 30px 10px 10px",color:"#000",fontSize:'16px',fontWeight:"bold",lineHeight:"20px"}} dangerouslySetInnerHTML={{__html: video.title}}></div>
                        {menu}
                       </div>
                }
                if (_.has(window, "videoCategory")) {
                    category = <div style={{display:"inline-block",margin:"5px 0 5px 5px"}} >{pharse('in')}<div style={{margin:"0 0 0 5px",display:"inline-block",color:"#000",fontSize:'12px',fontWeight:"600"}} >{window.videoCategory}</div></div>;
                }
                if (_.has(video, "publishedTranslated")) {
                    by = <div style={{display:"inline-block",margin:"5px"}} >{pharse('by')}<div onClick={() => this.handleClick(_.get(video,'userUrl',''))} style={{margin:"0 5px",display:"inline-block",color:"#247BBA",fontSize:'12px',fontWeight:"600"}} >{video.userName}</div></div>;
                    date = <div style={{padding:"10px 10px",fontSize:"12px",color:"#999"}}>{video.publishedTranslated}{category}{by}</div>;
                }
                if (_.has(video, "groupName") && _.get(video, "groupName") != '' ) {
                    groupInfo = <div style={{padding:"10px 10px",fontSize:"12px",color:"#999"}}><div style={{float:"left",margin:"0 10px 0 0"}} >{pharse('inGroup')} : </div> <div style={{color:"#000",fontSize:'12px',fontWeight:"600"}} >{video.groupName}</div></div>;
                }
                if (_.has(video, "description")) {
                    description = <div className="post-body" style={{padding:"10px 10px 10px ",fontSize:"15px",lineHeight:"1.5",wordBreak:"break-word"}} dangerouslySetInnerHTML={{__html: video.description}}></div>
                }

                videoView = <div style={{margin:"-8px"}} >
                    <Card style={{boxShadow:"none"}} >{media}{title}{groupInfo}{date}{description}</Card>
                    <DetailActionItem {...video} likes={this.props.likes} reactions={this.props.reactions} object={'Video_Video'} />
                    <CommentItem {...this.props} likes={this.props.likes} reactions={this.props.reactions} />
                </div>;
            }
        }
        
        return <MuiThemeProvider >{videoView}</MuiThemeProvider>;
        
    }
}

