import React from 'react';
import _ from 'lodash';
import {Card, CardActions, CardHeader, CardMedia, CardTitle, CardText} from 'material-ui/Card';
import {GridList, GridTile} from 'material-ui/GridList';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import getMuiTheme from 'material-ui/styles/getMuiTheme';
import {List, ListItem} from 'material-ui/List';
import Avatar from 'material-ui/Avatar';
import Chip from 'material-ui/Chip';
import {pharse} from '../../utility/mooApp';
import RaisedButton from 'material-ui/RaisedButton';
import {FirstLoading} from "../utility/FirstLoading";

import ActivityItem from '../activity/Item.js';
import {isPhone} from '../../utility/MobileDetector';
import IOSAction from '../../utility/IOSAction';
import {initLoadMoreBehavior} from '../../utility/mooApp';
import ACForm from '../activity/ACForm';
import {LoadMore} from '../utility/LoadMoreContent';
import AppAction from '../../utility/AppAction';

import {VideoDetail} from "../video/VideoDetail";
import {TopicDetail} from "../topic/TopicDetail";

import SelectField from 'material-ui/SelectField';
import GroupActionTypes from '../../data/actions/GroupActionTypes';
import GroupActions from '../../data/actions/GroupActions';
import CommentActions from '../../data/actions/CommentActions';
import NavigationMoreVert from 'material-ui/svg-icons/navigation/more-vert';
import MenuItem from 'material-ui/MenuItem';
import IconButton from 'material-ui/IconButton';
import IconMenu from 'material-ui/IconMenu';
import NavigationChevronLeft from 'material-ui/svg-icons/navigation/chevron-left';

import {isIOS} from "../../utility/MobileDetector";

class GroupDetail extends React.Component {
    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
        this.state = {showMore:false,height:"105px",overflow:"hidden",
            notifyOn: ( _.has(window, "notify") && window.notify == '#on') ? true : false,
            feature: ( _.has(window, "feature") && window.feature == '#feature' ) ? true : false,
        };
    }
    componentWillMount(){
        this.props.viewGroupDetail(window.groupId);
        if(window.topicId == 0 && window.videoId == 0 ) 
        {
            this.props.getGroupActivity(window.groupId,1);
            this.props.fetchMe();
            initLoadMoreBehavior(function(){
                this.props.fetchNextGroupActivity(window.groupId);
            }.bind(this));
        }
        else {
            if(window.videoId != 0) {
                this.props.viewVideo(window.videoId);
                this.props.fetchComment(window.videoId,'Video_Video');
            }
            if(window.topicId != 0) {
                this.props.viewTopic(window.topicId);
                this.props.fetchComment(window.topicId,'Topic_Topic');
            }
        }
    }
//    componentDidUpdate(){
//        if(this.props.comments.get("isScrollToBottom")){
//            window.scrollTo(0,document.body.scrollHeight);
//            setTimeout(function () {
//                CommentActions.stopScrollToBottom();
//            }, 1);
//        }
//    }
    componentDidUpdate(prevProps, prevState) {
        if(this.props.comments.get("isScrollToBottom")){
            window.scrollTo(0,document.body.scrollHeight);
            setTimeout(function () {
                CommentActions.stopScrollToBottom();
            }, 1);
        }
        setTimeout(function () {
            if(prevProps.activites.get('shouldCheck') == true) {
                prevProps.removeActivityByRefesh();
            }
        }, 1);
    }
    handleShareFeed(share_type){
        console.log("openShareFeed",this.props.groups.get('groups'));
        AppAction.openShareFeed(this.props.groups.get('groups'), share_type);
    }
    handleShowMore() {
        if(!this.state.showMore) {
            this.setState({
                showMore: !this.state.showMore,
                height:"auto",
                overflow:"visible"
            });
        }
        else {
            this.setState({
                showMore: !this.state.showMore,
                height:"105px",
                overflow:"hidden"
            });
        }
    }
    handleClick(url) {
            window.location.href = url;
    }
    handleJoin() {
        GroupActions.joinGroup(window.groupId);
    }
    handleLeaveGroup() {
        GroupActions.leaveGroup(window.groupId);
    }
    handleNotify(type) {
        if(type == '#on') {
            GroupActions.turnOnNotify(window.groupId);
        }
        else {
            GroupActions.turnOffNotify(window.groupId);
        }
        this.setState({
                notifyOn: !this.state.notifyOn,
            });
    }
    handleFeature(type) {
        if(type == '#feature') {
            GroupActions.featureGroup(window.groupId);
        }
        else {
            GroupActions.unFeatureGroup(window.groupId);
        }
        this.setState({
                feature: !this.state.feature,
            });
    }
    handleEditDetail() {
        AppAction.hideComment();
		var url = mooConfig.url.full + mooConfig.url.base + '/groups/create/' + window.groupId ;
        AppAction.openEditNotNewTab({"link":url});
    }


    render() {
        var canDelete ,groupMenu , message,mText,notify,memberList,adminList ,unFeature ,feature, turnOn ,turnOff,canLeave,canEdit,content,group,title,description,media,groupView,privacy,date,category,location,address,userName,attend,maybe,notAttend,awaiting,feed,rsvp,menu,canShare,canInvite;
        canDelete = groupMenu = message = notify = memberList = adminList = unFeature = feature = turnOn = turnOff = canLeave = canEdit = canInvite = canShare = menu = rsvp = feed = awaiting = notAttend = maybe = attend = userName = address = location = category = date = groupView  = media = description = title  = content = <div></div>;
        
        var mainStyle = {borderBottom:"1px solid #dfdfdf",padding:"10px 0"};
        var labelStyle = {display:"block",float:"left",width:"100px",fontSize:"13px",paddingLeft:"10px",fontFamily:"Roboto, sans-serif"};
        var textStyle = {fontSize:"13px",overflow:"hidden",fontFamily:"Roboto, sans-serif"};
        var headStyle = {fontSize:"15px",overflow:"hidden",fontFamily:"Roboto, sans-serif",margin:"10px 10px 0",fontWeight:"bold"};
        var showMore = {height:"105px",overflow:"hidden"};
        var showLess = {height:"auto",overflow:"visible"};
        
        if( this.props.groups.get('isOpen') == true  ) { 
            group = this.props.groups.get('groups');
            if( this.props.groups.get('isShowMessage') == true  ) {
                if( this.props.groups.get('isReload') == true  ) {
                    window.location.reload();
                }
                if(group.privacy == 3) {
                    mText = this.props.groups.get('message');
                    message = <div style={{color:"#468847",backgroundColor: "#dff0d8",border: "1px solid #d6e9c6",fontSize:"14px",borderRadius:"3px",padding:"15px",marginbottom: "10px"}}>{mText.message}</div>
                }
            }
            
            
            if (_.has(window, "canInvite")) {
                canInvite = <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/group/groups/ajax_invite/' + window.groupId )} primaryText={pharse("inviteFriend")} />
            }
            if (_.has(window, "canEdit")) {
                canEdit = <MenuItem onClick={() => this.handleEditDetail()} primaryText={pharse("edit")} />
            }
            if (_.has(window, "canDelete")) {
                canDelete = <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/groups/do_delete/' + window.groupId )} primaryText={pharse("delete")} />
            }
            if (_.has(window, "canLeave")) {
                canLeave = <MenuItem onClick={() => this.handleLeaveGroup()} primaryText={pharse("leave")} />
            }
            if (_.has(window, "notify")) {
                if (this.state.notifyOn) {
                    notify = <MenuItem onClick={() => this.handleNotify('#on')} primaryText={pharse("turnOn")} />
                }
                else {
                    notify = <MenuItem onClick={() => this.handleNotify('#off')} primaryText={pharse("turnOff")} />
                }
            }
            if (_.has(window, "feature")) {
                if (this.state.feature) {
                    feature = <MenuItem onClick={() => this.handleFeature('#feature')} primaryText={pharse("feature")} />
                }
                else {
                    feature = <MenuItem onClick={() => this.handleFeature('#unfeature')} primaryText={pharse("unFeature")} />
                }
            }

            if (_.has(window, "canShare")) {
                
            canShare = <MenuItem
                    innerDivStyle = {{padding:"0px 0px 0px 52px"}}
                    primaryText={pharse("shareGroup")}
                    leftIcon={<NavigationChevronLeft></NavigationChevronLeft>}
                    menuItems={[
                      <MenuItem primaryText={pharse('myWall')} onClick={() => this.handleShareFeed('#me')}/>,
                      <MenuItem primaryText={pharse('friendWall')} onClick={() => this.handleShareFeed('#friend')}/>,
                      <MenuItem primaryText={pharse('groupWall')} onClick={() => this.handleShareFeed('#group')}/>,
                      <MenuItem primaryText={pharse('email')} onClick={() => this.handleShareFeed('#email')}/>,
                    ]}
                  />
            }
            if (_.has(window, "canView") && window.canView ) {
                menu = <IconMenu
                        style={{display:"block",position:"absolute",right:"5px",top:"18px"}}
                        iconButtonElement={<IconButton style={{padding:"0px",height:"auto",width:"100%",display:"block"}}  ><NavigationMoreVert color="#fff" ></NavigationMoreVert></IconButton>}
                        anchorOrigin={{horizontal: 'right', vertical: 'top'}}
                        targetOrigin={{horizontal: 'right', vertical: 'top'}}
                    >
                        {canInvite}
                        {canEdit}
                        {canDelete}
                        {canLeave}
                        {canShare}
                        {notify}
                        {feature}
                        <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/reports/ajax_create/group_group/' + window.groupId )} primaryText={pharse("reportGroup")} />
                </IconMenu>;
            }
            else {
                menu = <IconMenu
                    style={{float:"right",position:"relative",right:"5px",top:"-20px"}}
                    iconButtonElement={<IconButton style={{padding:"0px",height:"auto",width:"100%",display:"block"}}  ><NavigationMoreVert color="#000" ></NavigationMoreVert></IconButton>}
                    anchorOrigin={{horizontal: 'right', vertical: 'top'}}
                    targetOrigin={{horizontal: 'right', vertical: 'top'}}
                >
                    {canInvite}
                    {canEdit}
                    {canDelete}
                    {canLeave}
                    {canShare}
                    {notify}
                    {feature}
                        <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/reports/ajax_create/group_group/' + window.groupId )} primaryText={pharse("reportGroup")} />
                </IconMenu>;
            }
            if (_.has(group, "name")) {
            title = <div style={{position:"relative"}}>
                    <div style={{padding:"20px 30px 10px 10px",color:"#fff",fontSize:'20px',fontWeight:"bold",lineHeight:"20px"}} dangerouslySetInnerHTML={{__html: group.name}}></div>
                    {menu}
                </div>
            }
            if (_.has(group, "thumbnail.850")) {  
                media = <CardMedia style={{position:"relative"}}>
                    <div style={{backgroundImage:"url('"+_.get(group,"thumbnail.850")+"')",backgroundRepeat:"no-repeat",paddingBottom:"56.25%",display:"block",backgroundSize:"cover",backgroundPosition:"center center"}}></div>
                    <div className="boxGradient"></div>
                    <div style={{position:"absolute",bottom:"20px",left:"0",zIndex:"100"}} >{title}</div>
                </CardMedia>;
            }
            
            if (_.has(group, "privacy")) {
                
                if(group.privacy == 1) {
                   privacy = <div style={mainStyle}><div style={labelStyle}>{pharse("type")}:</div><div style={textStyle}>{pharse("public")}</div></div>;
                }
                if(group.privacy == 2) {
                   privacy = <div style={mainStyle}><div style={labelStyle}>{pharse("type")}:</div><div style={textStyle}>{pharse("private")}</div></div>;
                }
                if(group.privacy == 3) {
                   privacy = <div style={mainStyle}><div style={labelStyle}>{pharse("type")}:</div><div style={textStyle}>{pharse("restricted")}</div></div>;
                }
            }
            
            if (_.has(group, "categoryName")) {
                category = <div style={mainStyle}><div style={labelStyle}>{pharse("category")}:</div><div style={textStyle}>{group.categoryName}</div></div>;
            }
            if (_.has(group, "description")) {
                description = <div style={{}}><div style={headStyle}>{pharse("des")}:</div>
                        <div className="post-body" style={{padding:"10px 10px 10px ",fontSize:"15px",lineHeight:"1.5"}} dangerouslySetInnerHTML={{__html: group.description}}></div></div>
            }
            if (_.has(window, "adminList")) {
                var user = window.adminList;
                var arrayUser = [];
                var titleTmp = pharse('groupAdmin');
                //var titleTmp = pharse('groupAdmin').replace('%s', Object.keys(user).length) ;
                Object.keys(user).forEach(function(key) {
                    arrayUser.push(<Chip onClick={() => this.handleClick(user[key].url)} labelStyle={{overflow: "hidden",textOverflow: "ellipsis",maxWidth: "200px"}} key={user[key].name+"-1"} style={{backgroundColor:"#e9e9eb",display:"inline-flex",margin:"4px"}} >
                                <Avatar src={user[key].avatar} />
                                {user[key].name}
                              </Chip>
                      );
                },this);
                
                adminList = <div style={{borderTop:"1px solid #dfdfdf"}}><div style={headStyle}>{titleTmp}</div>
                    <div style={{padding:"10px 10px 10px ",fontSize:"13px",lineHeight:"1.5"}}>{arrayUser}</div></div>
            }
            if (_.has(window, "memberList")) {
                var user = window.memberList;
                var arrayUser = [];
                var titleTmp = pharse('memberList');
                //var titleTmp = pharse('memberList').replace('%s', Object.keys(user).length);
                Object.keys(user).forEach(function(key) {
                    arrayUser.push(<Chip onClick={() => this.handleClick(user[key].url)} labelStyle={{overflow: "hidden",textOverflow: "ellipsis",maxWidth: "200px"}} key={user[key].name+"-1"} style={{backgroundColor:"#e9e9eb",display:"inline-flex",margin:"4px"}} >
                                <Avatar src={user[key].avatar} />
                                {user[key].name}
                              </Chip>
                      );
                },this);
                
                memberList = <div style={{borderTop:"1px solid #dfdfdf"}}><div style={headStyle}>{titleTmp}</div>
                    <div style={{padding:"10px 10px 10px ",fontSize:"13px",lineHeight:"1.5"}}>{arrayUser}</div></div>
            }

            if (_.has(window, "visibleJoin")) {
                rsvp = <div onClick={() => this.handleJoin()} style={{padding:"10px 0 10px 10px",textAlign:"left",display:"inline-block"}} ><RaisedButton buttonStyle={{height:"auto"}} labelStyle={{textTransform:"uppercase"}} label={pharse('join')} /></div>;
            }
            if (_.has(window, "visibleRequest")) {
                var totalrequest = parseInt(group.joinRequest);
                if(totalrequest > 0) {
                    rsvp =  <div onClick={() => this.handleClick(mooConfig.url.base + '/group/groups/ajax_requests/' + window.groupId )} style={{display:"inline-block",padding:"10px 0 10px 10px",textAlign:"left"}} ><RaisedButton buttonStyle={{height:"auto"}} labelStyle={{textTransform:"uppercase"}} label={ (totalrequest > 1) ? pharse('requests').replace('%s', totalrequest) : pharse('request').replace('%s', totalrequest) } /></div>;
                }
            }
            
            if(window.topicId == 0 && window.videoId == 0 ) {
                if (_.has(window, "canView") && window.canView ) {
                    groupMenu = <GroupMenu {...this.props} ></GroupMenu>;
                    feed = <GroupFeed {...this.props} isPhone={isPhone()} ></GroupFeed>;
                    groupView = <div style={{margin:"-8px"}} >
                                <Card style={{boxShadow:"none"}} >{message}{media}{groupMenu}{rsvp}</Card>
                                <div style={{background:"#fff",paddingBottom:"10px"}}>
                                    <div style={{height:this.state.height,overflow:this.state.overflow}} >
                                        <Card style={{boxShadow:"none"}} >{privacy}{category}{description}{adminList}{memberList}</Card>
                                    </div>
                                    <div onClick={() => this.handleShowMore()} style={{margin:"10px auto",textAlign:"center"}} ><RaisedButton buttonStyle={{height:"auto"}} labelStyle={{textTransform:"uppercase"}} label={this.state.showMore ? pharse('showLess'): pharse('showMore')  } /></div>
                                </div>
                                {feed}
                            </div>;
                }
                else {
                    groupView = <div style={{margin:"-8px"}} >
                                <div style={{background:"#fff",paddingTop:"30px"}}>
                                    <Card style={{boxShadow:"none"}} >{menu}{rsvp}{privacy}{category}</Card>
                                </div>
                            </div>;
                }
            }
            if(window.videoId != 0 ) {
                groupView = <VideoDetail {...this.props} ></VideoDetail>
            }
            if(window.topicId != 0 ) {
                groupView = <TopicDetail {...this.props} ></TopicDetail>
            }

        
        }
        else {
            content = <FirstLoading></FirstLoading>;
        }
        
        return <MuiThemeProvider ><div>{content}{groupView}</div></MuiThemeProvider>;

    }
}

class GroupMenu extends React.Component {
    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
    }
    componentWillMount(){
    }
    handleClick(url) {
        if(url) {
            window.location.href = url;
        }
    }

    render() {
        var content,titleTmp,title,details,members,photos,videos,topics,menuMore ;
        menuMore = videos = topics = photos = members = details = title = titleTmp = content = <div></div>;
        
        var menuBlock = {
            fontSize:"14px",display:"block",fontFamily:"Roboto, sans-serif",background:"#fff",height:"48px",position:"relative",flex: "1"
        };
        var css_titleTmp = {
            textAlign: "center",height:"48px",lineHeight:"48px"
        };
        var css_titleTmp_text = {
            display:"inline-block",verticalAlign:"baseline"
        };
        var css_label_style = {
            fontSize:"16px",color:"#000",padding:"0",textTransform:"none",textAlign:"left",display: "block"
        };

                    
        titleTmp = <div  style={css_titleTmp}>
                        <div style={css_titleTmp_text} >{pharse("Mdetail")}</div>
                    </div>;
        title = <RaisedButton fullWidth={true} label={titleTmp} style={{boxShadow:"none"}} overlayStyle={{height:"auto",boxShadow:"none"}} buttonStyle={{height:"auto",boxShadow:"none"}} labelStyle={css_label_style} />

        details = <div onClick={() => window.location.reload()} style={menuBlock}>{title}</div>
                    
        titleTmp = <div  style={css_titleTmp}>
                        <div style={css_titleTmp_text} >{pharse('Mmember')}</div>
                    </div>;
        title = <RaisedButton fullWidth={true} label={titleTmp} style={{boxShadow:"none"}} overlayStyle={{height:"auto",boxShadow:"none"}} buttonStyle={{height:"auto",boxShadow:"none"}} labelStyle={css_label_style} />

        members = <div onClick={() => this.handleClick(mooConfig.url.base + '/groups/members/' + window.groupId )} style={menuBlock}>{title}</div>
        
        titleTmp = <div  style={css_titleTmp}>
                        <div style={css_titleTmp_text} >{pharse('Mphoto')}</div>
                    </div>;
        title = <RaisedButton fullWidth={true} label={titleTmp} style={{boxShadow:"none"}} overlayStyle={{height:"auto",boxShadow:"none"}} buttonStyle={{height:"auto",boxShadow:"none"}} labelStyle={css_label_style} />

        photos = <div onClick={() => this.handleClick(mooConfig.url.base + '/photos/ajax_browse/group_group/' + window.groupId )} style={menuBlock}>{title}</div>
        

        videos = <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/videos/browse/group/' + window.groupId )}  primaryText={pharse('Mvideo')} style={menuBlock}/>
        topics = <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/topics/browse/group/' + window.groupId )}  primaryText={pharse('Mtopic')} style={menuBlock}/>

        menuMore = <div style={{display:"block",height: "48px",textAlign: "center", flex: "1"}}>
                        <IconMenu
                        style={{}}
                        iconButtonElement={<IconButton style={{width:"25px",height: "48px", padding: "0"}}  ><NavigationMoreVert ></NavigationMoreVert></IconButton>}
                                            anchorOrigin={{horizontal: 'right', vertical: 'top'}}
                                            targetOrigin={{horizontal: 'right', vertical: 'top'}}
                                            >
                                              {videos}{topics}
                                        </IconMenu>
                                    </div>

        return <div style={{display: "flex",margin:"0 0 10px", background: "#fff", borderTop: "1px solid #dadde1", borderBottom: "1px solid #dadde1"}}>{details}{members}{photos}{menuMore}</div>;
        }
}


class GroupFeed extends React.Component {
     constructor(props) {
        super(props);
    }

    render() {
        var postForm = <div></div>;
            if (_.has(window, "canPostFeed")) {
               postForm = <ACForm target_id={window.groupId} type={"Group_Group"} {...this.props} />;
            }
           if(this.props.activites.get('records').count() > 0 ){
                var items = [];
                var isPhone = this.props.isPhone;

                var isFetching = this.props.activites.get('isFetching') ? true : false ;  
                var process = <LoadMore isFetching={isFetching} ></LoadMore>;

                this.props.activites.get('records').forEach(function(item){
                    items.push(<ActivityItem {...item} key={item.id} isPhone={isPhone} likes={this.props.likes} reactions={this.props.reactions} singleComment={true} page="groups.view" />);
                    items.push(<div key={'a'+item.id} style={{height:'7px'}}/>);
                }.bind(this));

                return <MuiThemeProvider >
                    <div style={{padding:"8px 2px"}}>
                        <div style={{paddingBottom:"0"}} >{postForm}<div style={{height:'7px'}}/>{items}{process}</div>
                   </div>
                </MuiThemeProvider>;
            }else {
                return <MuiThemeProvider >
                        <div style={{padding:"8px 2px"}}>
                            {postForm}
                            <div style={{marginTop:"10px",background:"#fff",padding:"10px",textAlign:"center",fontFamily:"Roboto, sans-serif"}} >{pharse('notFound')}</div>
                        </div>
                        </MuiThemeProvider>;
            }  
        
    }
}



function GroupViewContent(props) {
    return <GroupDetail {...props} />
}

export default GroupViewContent;
