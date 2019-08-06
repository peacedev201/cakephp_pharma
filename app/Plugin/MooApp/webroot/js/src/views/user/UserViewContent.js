import React from 'react';
import _ from 'lodash';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import getMuiTheme from 'material-ui/styles/getMuiTheme';
import {CardMedia} from 'material-ui/Card';
import {GridList, GridTile} from 'material-ui/GridList';

import Avatar from 'material-ui/Avatar';
import {pharse} from '../../utility/mooApp';
import RaisedButton from 'material-ui/RaisedButton';
import UserActions from '../../data/actions/UserActions';


import IconMenu from 'material-ui/IconMenu';
import MenuItem from 'material-ui/MenuItem';

import {FirstLoading} from "../utility/FirstLoading";

import IconButton from 'material-ui/IconButton';
import EditorBorderColor from 'material-ui/svg-icons/editor/border-color';
import CommunicationMessage from 'material-ui/svg-icons/communication/message';
import CommunicationRssFeed from 'material-ui/svg-icons/communication/rss-feed';
import SocialGroupAdd from 'material-ui/svg-icons/social/group-add';
import SocialPersonAdd from 'material-ui/svg-icons/social/person-add';
import NavigationCheck from 'material-ui/svg-icons/navigation/check';
import NavigationMoreVert from 'material-ui/svg-icons/navigation/more-vert';

import ContentClear from 'material-ui/svg-icons/content/clear';

import SocialPersonOutline from 'material-ui/svg-icons/social/person-outline';
import ImageCollections from 'material-ui/svg-icons/image/collections';

import ActivityItem from '../activity/Item';
import {isPhone} from '../../utility/MobileDetector';

import {initLoadMoreBehavior} from '../../utility/mooApp';
import ACForm from '../activity/ACForm';
import {LoadMore} from '../utility/LoadMoreContent';
import AppAction from '../../utility/AppAction';

import LinearProgress from 'material-ui/LinearProgress';
const iconNotActive = getMuiTheme({
    svgIcon: {
        color: "#90949c"
    }
});
class UserView extends React.Component {
    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
    }
    componentWillMount(){
        this.props.viewUserById(window.userId);
        this.props.fetchMe();
        if(window.access == 1) {
            
            if(window.activityId == 0) {
                this.props.getUserActivity(window.userId,1);
                initLoadMoreBehavior(function(){
                        this.props.fetchNextUserActivities(window.userId);
                    }.bind(this));
            }
            else {
                this.props.fetchSingleActivity(window.activityId);
            }
            
            
            // Hacking for auto hide ios header
            var lastScrollTop = 0;
            var isSendCommandHideNavigationBar = false;
            var isSendCommandShowNavigationBar = true;

            // element should be replaced with the actual target element on which you have applied scroll, use window in case of no target element.
            window.addEventListener("scroll", function(){ // or window.addEventListener("scroll"....
                var st = window.pageYOffset || document.documentElement.scrollTop; // Credits: "https://github.com/qeremy/so/blob/master/so.dom.js#L426"
                if ((window.innerHeight + window.pageYOffset ) >= document.body.offsetHeight  ) {
                    return false;
                }
                //var body = document.getElementsByTagName("body")[0];
                //if(body.style.overflow == "visible") {
                    if (st > lastScrollTop && st > 0  ){
                        AppAction.hideNavigationBar()
                    }
                    if(st < lastScrollTop){
                        AppAction.showNavigationBar()
                    }
                    lastScrollTop = st;
                //}
            }, false);
        }
        
    }
    componentDidUpdate(prevProps, prevState) {
        setTimeout(function () {
            if(prevProps.activites.get('shouldCheck') == true) {
                prevProps.removeActivityByRefesh();
                prevProps.fetchMe();
            }
        }, 1);
    }
    handleClick(url) {
        window.location.href = url;
    }

    render() {
        var content , cover ,profile,avatar,name,button,info,menu,feed ,profileNamePlugin ,profileCompletion ;
        profileCompletion = profileNamePlugin = feed = menu = info = button = name = avatar = profile = cover = content = <div></div>;
        if( this.props.users.get('isOpen') == true  ) { 
            var user = this.props.users.get('users');
            //var block = this.props.users.get('users').forEach(function(obj) {
                if (_.has(user, "cover")) {  
                    cover = <div style={{backgroundImage :"url(" + _.get(user,"cover") + ")",height:"165px",backgroundSize:"cover",display:"Block",backgroundRepeat:"no-repeat",backgroundPosition:"center center"}} ></div>
                }
                if (_.has(user, "avatar.200_square")) {  
                    if (_.has(window,"isOnline")) {  
                        avatar = <div style={{position:"relative",display:"block",margin:"-85px auto 15px",width:"150px"}}> 
                                <Avatar style={{padding:"5px",background:"#fafafa",borderRadius:"5px"}} size={150} src={_.get(user,'avatar.200_square','')} />
                                <span className="online-stt"></span>
                                    </div>
                    }
                    else {
                        avatar = <Avatar style={{position:"relative",padding:"5px",background:"#fafafa",borderRadius:"5px",display:"block",margin:"-85px auto 15px"}} size={150} src={_.get(user,'avatar.200_square','')} />;
                    }
                }
                if (_.has(user, "name")) {  
                    name = <div style={{fontFamily:"Roboto, sans-serif",textAlign:"center",display:"block",margin:"10px auto",padding:"0 10px 0 ",color:"#000",fontSize:'16px',fontWeight:"bold",lineHeight:"20px"}} dangerouslySetInnerHTML={{__html: user.name}}></div>
                }
                if (_.has(window, "profileNamePlugin")) {  
                    profileNamePlugin = <div style={{fontFamily:"Roboto, sans-serif",textAlign:"center",display:"block",margin:"0 auto",padding:"0 10px 0 ",color:"#000",fontSize:'16px',fontWeight:"bold",lineHeight:"20px"}} dangerouslySetInnerHTML={{__html: window.profileNamePlugin}}></div>
                }
                if (_.has(window, "profileCompletion")) {  
                    profileCompletion = <UserProfileCompletion {...this.props} ></UserProfileCompletion>;
                }
                button = this.props.users.get(user.id);

                if(user.canView == true) {
                    info  = <UserProfileInfo {...user} ></UserProfileInfo>;
                    menu = <UserProfileMenu {...this.props} ></UserProfileMenu>;
                    feed = <UserFeed {...this.props} isPhone={isPhone()} ></UserFeed>;
                }
                else {
                    info  = <div style={{fontFamily:"Roboto, sans-serif",textAlign:"center",display:"block",margin:"0 auto",padding:"20px ",color:"#000"}}>{user.name} {pharse('notAllow')}</div>;
                }
           
                profile = <div>
                        <div style={{background:"#fff",margin:"-8px -8px 10px"}} >
                            {cover}{avatar}{name}{profileNamePlugin}
                            <UserProfileAction button={button} {...user} ></UserProfileAction>
                            {info}
                        </div>
                        {menu}
                        {profileCompletion}
                        {feed}
                        
                        
                    </div>
            //}, this);
        }
        else {
            content = <FirstLoading></FirstLoading>;
        }
        
        return <MuiThemeProvider ><div>{content}{profile}</div></MuiThemeProvider>;

    }
}
class UserProfileAction extends React.Component {
    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.handleRespond = this.handleRespond.bind(this);
        this.handleActionFollow = this.handleActionFollow.bind(this);
        this.state = {follow:this.props.follow};
    }
    componentWillMount(){
    }
    handleActionFollow() {
      var id = window.userId;
      if(this.state.follow) {
         UserActions.unFollowUser(id);
      }
      else {
         UserActions.followUser(id);
      }
      this.setState({ follow:!this.state.follow });
    }

    handleClick(url) {
        window.location.href = url;
    }
    handleRespond(id,flag) {
        UserActions.respondRequest(id,flag);
    }
    handleChange(id,action) {
        switch (action) {
            case 1 : 
                UserActions.addFriend(id);
                break;
            case 4 : 
                UserActions.cancelRequest(id);
                break;
            case 2 : 
                UserActions.removeFriend(id);
                break;
        }
    }

    render() {
        var content, editProfile,sendMessage,requestSent,respond,addFriend,follow,moreAction,feature,unFeature,editUser,unFriend,block,unBlock,url ;
        unBlock = block = unFriend = editUser = unFeature = feature = moreAction = follow = addFriend = respond = requestSent = sendMessage = editProfile = content = <div></div>;
        
        if(window.uid == this.props.id) {
            
            url =  mooConfig.url.base + '/users/profile';
            editProfile = <MuiThemeProvider muiTheme={iconNotActive}>
                <span onClick={() => this.handleClick(url)}  style={{height: "60px", padding: "10px 0", textAlign: "center", flex: "1"}}>
                    <IconButton style={{height: "35px", padding: "0"}} ><EditorBorderColor ></EditorBorderColor></IconButton>
                        <span style={{fontSize:"11px",display:"block",fontFamily:"Roboto, sans-serif",textAlign:"center",marginTop:"5px"}} >{pharse('editProfile')}</span>
                </span>
            </MuiThemeProvider>;
        }
        if(window.uid != this.props.id) {
            url =  mooConfig.url.base + '/conversations/ajax_send/' + window.userId;
            sendMessage = <MuiThemeProvider muiTheme={iconNotActive}>
                <span onClick={() => this.handleClick(url)} style={{height: "60px", padding: "10px 0", textAlign: "center", flex: "1"}}>
                    <IconButton style={{height: "35px", padding: "0"}} ><CommunicationMessage ></CommunicationMessage></IconButton>
                    <span style={{fontSize:"11px",display:"block",fontFamily:"Roboto, sans-serif",textAlign:"center",marginTop:"5px"}} >{pharse('message')}</span>
                </span>
            </MuiThemeProvider>;
        }
        if (this.props.button.userCase == 4 ) {
                requestSent = <MuiThemeProvider muiTheme={iconNotActive}>
                    <span onClick={() => this.handleChange(_.get(this.props,'id',0),this.props.button.userCase)}  style={{height: "60px", padding: "10px 0", textAlign: "center", flex: "1"}}>
                        <IconButton style={{height: "35px", padding: "0"}} ><ContentClear ></ContentClear></IconButton>
                            <span style={{fontSize:"11px",display:"block",fontFamily:"Roboto, sans-serif",textAlign:"center",marginTop:"5px"}} >{pharse('cancelRequest')}</span>
                    </span>
                </MuiThemeProvider>;
            }
            if (this.props.button.userCase == 3 ) {
                respond = <MuiThemeProvider muiTheme={iconNotActive}>
                
                
                <span onClick={() => this.handleChange(_.get(this.props,'id',0),this.props.button.userCase)} style={{height: "60px", padding: "10px 0", textAlign: "center", flex: "1"}}>
                                
                                    <IconMenu
                                        style={{}}
                                        iconButtonElement={<IconButton style={{height: "35px", padding: "0"}}  ><SocialGroupAdd ></SocialGroupAdd></IconButton>}
                                        anchorOrigin={{horizontal: 'right', vertical: 'top'}}
                                        targetOrigin={{horizontal: 'right', vertical: 'top'}}
                                    >
                                        <MenuItem onClick={() => this.handleRespond(_.get(this.props,'id',0),1)} primaryText={pharse('accept')} />
                                        <MenuItem onClick={() => this.handleRespond(_.get(this.props,'id',0),0)} primaryText={pharse('delete')} />
                                </IconMenu>
                                    <span style={{fontSize:"11px",display:"block",fontFamily:"Roboto, sans-serif",textAlign:"center",marginTop:"5px"}} >{pharse('respond')}</span>
                            </span>
                </MuiThemeProvider>;
            }
            if (this.props.button.userCase == 1 ) {
                addFriend = <MuiThemeProvider muiTheme={iconNotActive}>
                    <span onClick={() => this.handleChange(_.get(this.props,'id',0),this.props.button.userCase)}  style={{height: "60px", padding: "10px 0", textAlign: "center", flex: "1"}}>
                        <IconButton style={{height: "35px", padding: "0"}} ><SocialPersonAdd ></SocialPersonAdd></IconButton>
                            <span style={{fontSize:"11px",display:"block",fontFamily:"Roboto, sans-serif",textAlign:"center",marginTop:"5px"}} >{pharse('add')}</span>
                    </span>
                </MuiThemeProvider>;
            }
            if (this.props.button.userCase == 2 ) {
                addFriend = <MuiThemeProvider muiTheme={iconNotActive}>
                    <span onClick={() => this.handleChange(_.get(this.props,'id',0),this.props.button.userCase)}  style={{height: "60px", padding: "10px 0", textAlign: "center", flex: "1"}}>
                        <IconButton style={{height: "35px", padding: "0"}} ><SocialPersonOutline ></SocialPersonOutline></IconButton>
                            <span style={{fontSize:"11px",display:"block",fontFamily:"Roboto, sans-serif",textAlign:"center",marginTop:"5px"}} >{pharse('unFriend')}</span>
                    </span>
                </MuiThemeProvider>;
            }
            if (this.props.enableFollow == true) {
                if(this.state.follow == true) {
                    follow = <MuiThemeProvider muiTheme={iconNotActive}>
                        <span onClick={() => this.handleActionFollow()}  style={{height: "60px", padding: "10px 0", textAlign: "center", flex: "1"}}>
                            <IconButton style={{height: "35px", padding: "0"}} ><NavigationCheck ></NavigationCheck></IconButton>
                                <span style={{fontSize:"11px",display:"block",fontFamily:"Roboto, sans-serif",textAlign:"center",marginTop:"5px"}} >{pharse('following')}</span>
                        </span>
                    </MuiThemeProvider>;
                }
                else {
                    follow = <MuiThemeProvider muiTheme={iconNotActive}>
                        <span onClick={() => this.handleActionFollow()} style={{height: "60px", padding: "10px 0", textAlign: "center", flex: "1"}}>
                            <IconButton style={{height: "35px", padding: "0"}} ><CommunicationRssFeed ></CommunicationRssFeed></IconButton>
                                <span style={{fontSize:"11px",display:"block",fontFamily:"Roboto, sans-serif",textAlign:"center",marginTop:"5px"}} >{pharse('follow')}</span>
                        </span>
                    </MuiThemeProvider>;
                }
            }
        
        
        
        if (_.has(window, "feature")) {
            //feature = <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/admin/users/feature/' + window.userId )} primaryText={pharse('feature')} />
        }
        if (_.has(window, "unFeature")) {
            //unFeature = <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/admin/users/unfeature/' + window.userId )} primaryText={pharse('unFeature')}  />
        }
        if (_.has(window, "block")) {
            block = <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/user_blocks/ajax_add/' + window.userId )}  primaryText={pharse('block')}  />
        }
        if (_.has(window, "unBlock")) {
            unBlock = <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/user_blocks/ajax_remove/' + window.userId )} primaryText={pharse('unBlock')}  />
        }
        
        moreAction = <MuiThemeProvider muiTheme={iconNotActive}>
            <span style={{height: "60px", padding: "10px 0", textAlign: "center", flex: "1"}}>
                <IconMenu
                style={{}}
                iconButtonElement={<IconButton style={{height: "35px", padding: "0"}}  ><NavigationMoreVert ></NavigationMoreVert></IconButton>}
                                    anchorOrigin={{horizontal: 'right', vertical: 'top'}}
                                    targetOrigin={{horizontal: 'right', vertical: 'top'}}
                                    >
                                        {feature}
                                        {unFeature}
                                        {block}
                                        {unBlock}
                                        <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/reports/ajax_create/user/' + window.userId )} primaryText={pharse("reportUser")} />
                                </IconMenu>
                                    <span style={{fontSize:"11px",display:"block",fontFamily:"Roboto, sans-serif",textAlign:"center",marginTop:"5px"}} >{pharse('more')}</span>
                            </span>
        </MuiThemeProvider>;
        
        
        content = <div style={{display: "flex", margin: "10px 0", background: "#fff", borderTop: "1px solid #dadde1", borderBottom: "1px solid #dadde1"}}>
            {editProfile}{sendMessage}{requestSent}{respond}{addFriend}{follow}{moreAction}</div>;
                    return <div>{content}</div>;

    }
}
class UserProfileInfo extends React.Component {
    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
    }
    componentWillMount(){
    }
    handleClick(url) {
        window.location.href = url + '?access_token='+mooConfig.access_token;
    }

    render() {
        var content,gender,birthday ;
        birthday = gender = content = <div></div>;
        var fields =[];
        var infoStyle = {
            fontSize:"14px",display:"block",paddingBottom:"10px",fontFamily:"Roboto, sans-serif",paddingLeft:"16px"
        };
        
        if (_.has(this.props, "gender")) {  
                gender = <div style={infoStyle} >{pharse('gender')} : <div style={{display:"inline-block"}} dangerouslySetInnerHTML={{__html: this.props.gender}}></div></div>
            }
        if (_.has(this.props, "birthdayFormat")) {  
                birthday = <div style={infoStyle}>{pharse('born')} : <div style={{display:"inline-block"}} dangerouslySetInnerHTML={{__html: this.props.birthdayFormat}}></div></div>
            }
        if (_.has(window, "profileFields")) {  
            
            var profileField = window.profileFields;
            Object.keys(profileField).forEach(function(obj) {
                    fields.push(<div key={obj} style={infoStyle}>{_.get(profileField[obj], "name")} : <div style={{display:"inline-block"}} dangerouslySetInnerHTML={{__html: _.get(profileField[obj], "value")}}></div></div>);
                },this);
            }
            
        var moreInfo = [];
        if (_.has(window, "moreProfileInfo")) {
            var moreProfileInfo = window.moreProfileInfo;
            Object.keys(moreProfileInfo).forEach(function(obj) {
                moreInfo.push(<div key={obj} style={infoStyle} dangerouslySetInnerHTML={{__html: _.get(moreProfileInfo[obj], "content")}}></div>);
            }, this)
        }
        
           
        return <div style={{marginBottom:"10px"}}>{gender}{birthday}{fields}{moreInfo}</div>;

    }
}
class UserProfileMenu extends React.Component {
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
        var viewAllAlbum,content,info,title,blog,titleTmp,topic,video,group,event,blocked,friend,albumContent ,media, title,url,photoContent,album,menuMore,images,cellHeight,col1,col2,col3,col4 ;
        viewAllAlbum = images = menuMore = album = photoContent = media = title = albumContent = friend = blocked = group = event = video = topic = titleTmp = blog = title = info = content = <div></div>;
        
        var menuBlock = {
            fontSize:"14px",display:"block",fontFamily:"Roboto, sans-serif",background:"#fff",height:"48px",position:"relative",flex: "1"
        };
        var menuUser = window.menu;
        var arrayMenu = [];
        Object.keys(menuUser).forEach(function(key) {
            //console.log(key, menuUser[key].text);
            switch(key) {
                case "album":
                    
                        //if(Object.keys(albumArray).length > 4 ) {
                            var url1 = mooConfig.url.base + '/photos/profile_user_photo/' + window.userId + '?access_token='+mooConfig.access_token;
                            viewAllAlbum = <div onClick={() => this.handleClick(url1)} style={{color:"#247BBA",position:"absolute",right:"10px",top:"0"}} >{pharse('viewAll')}</div>
                        //}
                        title = <div  style={{padding: "0 10px", textAlign: "left",height:"40px",lineHeight:"40px",position:"relative"}}>
                            <IconButton style={{verticalAlign:"sub",width:"25px",height:"normal", padding: "0"}} ><ImageCollections color="#2196F3" viewBox="0 -9 32 32"></ImageCollections></IconButton>
                            <span style={{display:"inline-block",verticalAlign:"baseline"}} >{menuUser[key].text}</span>
                             {viewAllAlbum}
                        </div>;
                    
                    if (window.albums) { 
                        var albumArray = window.albums;    
                        switch (Object.keys(albumArray).length){
                            case 1 :
                            case 2 :
                                var images = [];
                                var block = Object.keys(albumArray).forEach(function(obj) {
                                var url = '';
                                    if (_.has(albumArray[obj], "Album.coverFull")) {  
                                        media = <img src={_.get(albumArray[obj], "Album.coverFull")}/>;
                                    }
                                    if (_.has(albumArray[obj], "Album.moo_url")) {
                                        url = _.get(albumArray[obj], "Album.moo_href") ;
                                    }
                                    images.push(<GridTile onClick={() => this.handleClick(url)} key={_.get(albumArray[obj], "Album.id")} cols={1} rows={1} >{media}</GridTile>);
                                },this);
                                photoContent = <GridList cols={2} >{images}</GridList>
                            break;
                            
                            case 3 :
                                var cnt = 1;
                                var block = Object.keys(albumArray).forEach(function(obj) {
                                    var url = '';
                                    if (_.has(albumArray[obj], "Album.coverFull")) {  
                                        media = <img src={_.get(albumArray[obj], "Album.coverFull")}/>;
                                    }
                                    if (_.has(albumArray[obj], "Album.moo_url")) {
                                        url = _.get(albumArray[obj], "Album.moo_href") ;
                                    }
                                    if(cnt == 1) {
                                        col1 = <GridTile onClick={() => this.handleClick(url)} key={window.userId+'-1'} cols={2} rows={2} >{media}</GridTile>
                                    }
                                    if(cnt == 2) {
                                        col2 = <GridTile onClick={() => this.handleClick(url)} key={window.userId+'-2.1'} cols={1} rows={1} >{media}</GridTile>
                                    }
                                    if(cnt == 3) {
                                        col3 = <GridTile onClick={() => this.handleClick(url)} key={window.userId+'-2.2'} cols={1} rows={1} >{media}</GridTile>
                                    }
                                    cnt++;                                    
                                },this);
                                cellHeight = 100;
                                photoContent = <GridList  cols={3} cellHeight={cellHeight}>
                                            {col1}
                                        <GridTile key={window.userId+'-2'} cols={1} rows={2} >
                                            <GridList  cols={1} cellHeight={cellHeight}>
                                                 {col2}{col3}
                                            </GridList>
                                        </GridTile>
                                    </GridList>;
                            break;
                            
                            default :
                                var cnt = 1;
                                var block = Object.keys(albumArray).forEach(function(obj) {
                                    var url = '';
                                    if (_.has(albumArray[obj], "Album.coverFull")) {  
                                        media = <img src={_.get(albumArray[obj], "Album.coverFull")}/>;
                                    }
                                    if (_.has(albumArray[obj], "Album.moo_url")) {
                                        url = _.get(albumArray[obj], "Album.moo_href") ;
                                    }
                                    if(cnt == 1) {
                                        col1 = <GridTile onClick={() => this.handleClick(url)} key={window.userId+'-1'} cols={3} rows={3} >{media}</GridTile>
                                    }
                                    if(cnt == 2) {
                                        col2 = <GridTile onClick={() => this.handleClick(url)} key={window.userId+'-2.1'} cols={1} rows={1} >{media}</GridTile>
                                    }
                                    if(cnt == 3) {
                                        col3 = <GridTile onClick={() => this.handleClick(url)} key={window.userId+'-2.2'} cols={1} rows={1} >{media}</GridTile>
                                    }
                                    if(cnt == 4) {
                                        col4 = <GridTile onClick={() => this.handleClick(url)} key={window.userId+'-2.3'} cols={1} rows={1} >{media}</GridTile>
                                    }
                                    cnt++;                                    
                                },this);
                                cellHeight = 66;
                                photoContent = <GridList  cols={4} cellHeight={cellHeight}>
                                        {col1}
                                    <GridTile key={window.userId+'-2'} cols={1} rows={3} >
                                        <GridList  cols={1} cellHeight={cellHeight}>
                                            {col2}{col3}{col4}
                                        </GridList>
                                    </GridTile>
                                </GridList>;
                            break;
                        }
                    }
                    albumContent = <div>{photoContent}</div>;
                    album = <div style={{fontSize:"14px",display:"block",fontFamily:"Roboto, sans-serif",background:"#fff",margin:"10px -6px",position:"relative"}}>{title}{albumContent}</div>
                       break;
                case "info":
                    
                    titleTmp = <div  style={{textAlign: "center",height:"48px",lineHeight:"48px"}}>
                        <div style={{display:"inline-block",verticalAlign:"baseline"}} >{menuUser[key].text}</div>
                    </div>;
                    title = <RaisedButton fullWidth={true} label={titleTmp} style={{boxShadow:"none"}} overlayStyle={{height:"auto",boxShadow:"none"}} buttonStyle={{height:"auto",boxShadow:"none"}} labelStyle={{fontSize:"16px",color:"#000",padding:"0",textTransform:"none",textAlign:"left",display: "block"}} />

                    info = <div onClick={() => this.handleClick(menuUser[key].url)} style={menuBlock}>{title}</div>
                    break;
                case "friend":
                    titleTmp = <div  style={{textAlign: "center",height:"48px",lineHeight:"48px"}}>
                        <div style={{display:"inline-block",verticalAlign:"baseline"}} >{menuUser[key].text}</div>
                    </div>;
                    title = <RaisedButton fullWidth={true} label={titleTmp} style={{boxShadow:"none"}} overlayStyle={{height:"auto",boxShadow:"none"}} buttonStyle={{height:"auto",boxShadow:"none"}} labelStyle={{fontSize:"16px",color:"#000",padding:"0",textTransform:"none",textAlign:"left",display: "block"}} />

                    friend = <div onClick={() => this.handleClick(menuUser[key].url)} style={menuBlock}>{title}</div>
                   break;
                case "blog":
                    titleTmp = <div  style={{textAlign: "center",height:"48px",lineHeight:"48px"}}>
                        <div style={{display:"inline-block",verticalAlign:"baseline"}} >{menuUser[key].text}</div>
                        </div>;
                        title = <RaisedButton fullWidth={true} label={titleTmp} style={{boxShadow:"none"}} overlayStyle={{height:"auto",boxShadow:"none"}} buttonStyle={{height:"auto",boxShadow:"none"}} labelStyle={{fontSize:"16px",color:"#000",padding:"0",textTransform:"none",textAlign:"left",display: "block"}} />

                    blog = <div onClick={() => this.handleClick(menuUser[key].url)} style={menuBlock}>{title}</div>
                    break;
                
                default:
                    var key_id = menuUser[key].text + "-key";
                    arrayMenu.push(<MenuItem onClick={() => this.handleClick(menuUser[key].url)} key={key_id} primaryText={menuUser[key].text} style={menuBlock}/>);
            }
        },this);
        
        menuMore = <div style={{display:"block",height: "48px",textAlign: "center", flex: "1"}}>
                <IconMenu
                style={{}}
                iconButtonElement={<IconButton style={{width:"25px",height: "48px", padding: "0"}}  ><NavigationMoreVert ></NavigationMoreVert></IconButton>}
                                    anchorOrigin={{horizontal: 'right', vertical: 'top'}}
                                    targetOrigin={{horizontal: 'right', vertical: 'top'}}
                                    >
                                    <div className="profile_menu_scroll">{arrayMenu}</div>
                                </IconMenu>
                            </div>

        return <div><div style={{display: "flex",margin:"0 -8px", background: "#fff", borderTop: "1px solid #dadde1", borderBottom: "1px solid #dadde1"}}>{info}{friend}{blog}{menuMore}</div>{album}</div>;

    }
}
class UserFeed extends React.Component {
     constructor(props) {
        super(props);
    }

    render() {
        var postFeedId = 0;
        if(window.uid != window.userId) {
            postFeedId = window.userId;
        }
        if(this.props.activites.get('isOpen') == true ){
            
            if (_.has(window, "unBlock")) {
                return <MuiThemeProvider >
                            <div style={{margin:"10px -6px 0"}}>
                                <div style={{marginTop:"10px",background:"#fff",padding:"10px",textAlign:"center",fontFamily:"Roboto, sans-serif"}} >{pharse('notFound')}</div>
                            </div>
                            </MuiThemeProvider>;
            }
            else {
                if(this.props.activites.get('isPrivateFeed') == true ) {
                    var messtmp = this.props.activites.get('records');
                    return <MuiThemeProvider >
                        <div style={{marginTop:"10px",background:"#fff",padding:"10px",textAlign:"left",fontFamily:"Roboto, sans-serif"}}>
                            <div style={{fontSize:"28px",color:"#3E3E3E"}} >{messtmp.message}</div>
                        </div>
                    </MuiThemeProvider>;
                } else if(this.props.activites.get('records').count() > 0 ){
                    var items = [];
                    var isPhone = this.props.isPhone;

                    var isFetching = this.props.activites.get('isFetching') ? true : false ;  
                    var process = <LoadMore isFetching={isFetching} ></LoadMore>;

                    this.props.activites.get('records').forEach(function(item){
                        items.push(<ActivityItem {...item} key={item.id} isPhone={isPhone} likes={this.props.likes} reactions={this.props.reactions} singleComment={true} page="users.view"/>);
                        items.push(<div key={'a'+item.id} style={{height:'7px'}}/>);
                    }.bind(this));

                    return <MuiThemeProvider >
                        <div>
                            <div style={{margin:"10px -6px 0",paddingBottom:"0"}} ><ACForm {...this.props} target_id={postFeedId} type={"User"} /><div style={{height:'7px'}}/>{items}{process}</div>
                       </div>
                    </MuiThemeProvider>;
                }else {
                    return <MuiThemeProvider >
                            <div style={{margin:"10px -6px 0"}}>
                                <ACForm {...this.props} target_id={postFeedId} type={"User"} />
                                <div style={{marginTop:"10px",background:"#fff",padding:"10px",textAlign:"center",fontFamily:"Roboto, sans-serif"}} >{pharse('notFound')}</div>
                            </div>
                            </MuiThemeProvider>;
                }  
            }
        }
        return null
    }
}
class UserProfileCompletion extends React.Component {
     constructor(props) {
        super(props);
    }
    handleClick(url) {
        if(url) {
            window.location.href = url;
        }
    }

    render() {
        var linearProgress,title,next,update;
        update = next = title = linearProgress = <div></div>;
        var profileCompletion =  JSON.parse(window.profileCompletion);

        if (_.has(profileCompletion, "is_show_widget") && _.get(profileCompletion, "is_show_widget") == true ) {
            if (_.has(profileCompletion, "total_percent_title") && _.has(profileCompletion, "total_percent") ) {
                title = <div  style={{padding: "0", textAlign: "left",height:"40px",lineHeight:"40px",position:"relative"}}>{profileCompletion.total_percent_title} </div>;
            }
            if (_.has(profileCompletion, "progress_bar_color") && _.has(profileCompletion, "total_percent") ) {
                linearProgress = <LinearProgress mode="determinate" style={{height:"20px",background:profileCompletion.remaining_bar_color}} color={profileCompletion.progress_bar_color} value={profileCompletion.total_percent} />;
            }
            if(profileCompletion.total_percent != 100) {
                if (_.has(profileCompletion, "next") && _.has(profileCompletion, "next_percent") ) {
                    next = <div  style={{padding: "0", textAlign: "left",height:"40px",lineHeight:"40px",position:"relative"}}><i style={{fontStyle:"normal",marginRight:"10px"}} >{pharse('completionNext')}</i>{profileCompletion.next}<i style={{fontStyle:"normal",marginLeft:"10px"}} >( {profileCompletion.next_percent} % )</i></div>;
                }
                update = <div  style={{color:"rgb(36, 123, 186)",padding: "0", textAlign: "left",height:"40px",lineHeight:"40px",position:"relative"}}>{pharse('completionUpdateProfile')} </div>;
            }
            return <div onClick={() => this.handleClick(mooConfig.url.full +  mooConfig.url.base + _.get(profileCompletion, "next_url"))} style={{padding:"10px",margin:"0 -8px", background: "#fff", borderTop: "1px solid #dadde1", borderBottom: "1px solid #dadde1"}}>{title}{linearProgress}{next}{update}</div>;
        }
        else {
            return <div></div>   ;
        }
    }
}

function UserViewContent(props) {
    return <UserView {...props} />
}

export default UserViewContent;
