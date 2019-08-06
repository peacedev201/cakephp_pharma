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
import {initLoadMoreBehavior} from '../../utility/mooApp';
import ACForm from '../activity/ACForm';
import {LoadMore} from '../utility/LoadMoreContent';
import AppAction from '../../utility/AppAction';
import {isIOS} from "../../utility/MobileDetector";

import SelectField from 'material-ui/SelectField';
import EventActions from '../../data/actions/EventActions';
import NavigationMoreVert from 'material-ui/svg-icons/navigation/more-vert';
import MenuItem from 'material-ui/MenuItem';
import IconButton from 'material-ui/IconButton';
import IconMenu from 'material-ui/IconMenu';
import NavigationChevronLeft from 'material-ui/svg-icons/navigation/chevron-left';


class EventDetail extends React.Component {
    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.state = {value:window.myRSVP,showMore:false,height:"100px",overflow:"hidden"};
    }
    componentWillMount(){
        this.props.viewEventDetail(window.eventId);
        this.props.getEventActivity(window.eventId,1);
        this.props.fetchMe();
        initLoadMoreBehavior(function(){
            this.props.fetchNextEventActivity(window.eventId);
        }.bind(this));
    }
    componentDidUpdate(prevProps, prevState) {
        setTimeout(function () {
            if(prevProps.activites.get('shouldCheck') == true) {
                prevProps.removeActivityByRefesh();
            }
        }, 1);
    }
    handleShareFeed(share_type){
        console.log("openShareFeed",this.props.events.get('events'));
        AppAction.openShareFeed(this.props.events.get('events'), share_type);
    }
    handleChange (event, index, value) { 
        var id = window.eventId;
        if(value) {
            EventActions.submitRSVP(id,value);
        }
        this.setState({value:value});
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
                height:"100px",
                overflow:"hidden"
            });
        }
    }
    handleClick(url) {
            window.location.href = url;
    }
    handleEditDetail() {
        AppAction.hideComment();
		var url = mooConfig.url.full + mooConfig.url.base + '/events/create/' + window.eventId;
        AppAction.openEditNotNewTab({"link":url});
    }

    render() {
        var canDelete,canEdit,content,event,title,description,media,eventView,privacy,date,category,location,address,userName,attend,maybe,notAttend,awaiting,feed,rsvp,menu,canShare,canInvite;
        canDelete = canEdit = canInvite = canShare = menu = rsvp = feed = awaiting = notAttend = maybe = attend = userName = address = location = category = date = eventView  = media = description = title  = content = <div></div>;
        
        var mainStyle = {borderBottom:"1px solid #dfdfdf",padding:"10px 0"};
        var labelStyle = {display:"block",float:"left",width:"100px",fontSize:"13px",paddingLeft:"10px",fontFamily:"Roboto, sans-serif"};
        var textStyle = {fontSize:"13px",overflow:"hidden",fontFamily:"Roboto, sans-serif"};
        var headStyle = {fontSize:"15px",overflow:"hidden",fontFamily:"Roboto, sans-serif",margin:"10px 10px 0",fontWeight:"bold"};
        var showMore = {height:"100px",overflow:"hidden"};
        var showLess = {height:"auto",overflow:"visible"};
        
        if( this.props.events.get('isOpen') == true  ) { 
            event = this.props.events.get('events');
            if (_.has(event, "thumbnail.850")) {  
                media = <CardMedia style={{position:"relative"}}>
                    <div style={{backgroundImage:"url('"+_.get(event,"thumbnail.850")+"')",backgroundRepeat:"no-repeat",paddingBottom:"56.25%",display:"block",backgroundSize:"cover",backgroundPosition:"center center"}}></div>
                </CardMedia>;
            }
            if (_.has(window, "canInvite")) {
                canInvite = <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/events/invite/' + window.eventId )} primaryText={pharse("inviteFriend")} />
            }
            if (_.has(window, "canEdit")) { 
                    canEdit = <MenuItem onClick={() => this.handleEditDetail()} primaryText={pharse("edit")} />;
            } 
            if (_.has(window, "canDelete")) { 
                    canDelete = <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/events/do_delete/' + window.eventId )} primaryText={pharse("delete")} />;
            } 
            if (_.has(window, "canShare")) {
                //canShare = <MenuItem onClick={() => this.handleShare()} primaryText={pharse("shareEvent")} />
                
            canShare = <MenuItem
                    innerDivStyle = {{padding:"0px 0px 0px 52px"}}
                    primaryText={pharse("shareEvent")}
                    leftIcon={<NavigationChevronLeft></NavigationChevronLeft>}
                    menuItems={[
                      <MenuItem primaryText={pharse('myWall')} onClick={() => this.handleShareFeed('#me')}/>,
                      <MenuItem primaryText={pharse('friendWall')} onClick={() => this.handleShareFeed('#friend')}/>,
                      <MenuItem primaryText={pharse('groupWall')} onClick={() => this.handleShareFeed('#group')}/>,
                      <MenuItem primaryText={pharse('email')} onClick={() => this.handleShareFeed('#email')}/>,
                    ]}
                  />
            }
            menu = <IconMenu
                    style={{display:"block",position:"absolute",right:"5px",top:"18px"}}
                    iconButtonElement={<IconButton style={{padding:"0px",height:"auto",width:"100%",display:"block"}}  ><NavigationMoreVert ></NavigationMoreVert></IconButton>}
                    anchorOrigin={{horizontal: 'right', vertical: 'top'}}
                    targetOrigin={{horizontal: 'right', vertical: 'top'}}
                >
                    {canInvite}
                    {canEdit}
                    {canDelete}
                    {canShare}
                    <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/reports/ajax_create/event_event/' + window.eventId )} primaryText={pharse("reportEvent")} />
            </IconMenu>;
            if (_.has(event, "title")) {
            title = <div style={{position:"relative"}}>
                    <div style={{padding:"20px 30px 10px 10px",color:"#000",fontSize:'20px',fontWeight:"bold",lineHeight:"20px"}} dangerouslySetInnerHTML={{__html: event.title}}></div>
                    {menu}
                </div>
            }
            if (_.has(event, "privacy")) {
                
                if(event.privacy == 1) {
                   privacy = <div style={mainStyle}><div style={labelStyle}>{pharse("privacy")}:</div><div style={textStyle}>{pharse("public")}</div></div>;
                }
                else {
                   privacy = <div style={mainStyle}><div style={labelStyle}>{pharse("privacy")}:</div><div style={textStyle}>{pharse("private")}</div></div>;
                }
            }
            if (_.has(event, "fromTime")) {
                date = <div style={mainStyle}><div style={labelStyle}>{pharse("time")}:</div><div style={textStyle}>{event.fromTime} - {event.toTime}</div></div>;
            }
            if (_.has(event, "location")) {
                location = <div style={mainStyle}><div style={labelStyle}>{pharse("location")}:</div><div style={textStyle}>{event.location}</div></div>;
            }
            if (_.has(event, "address")) {
                address = <div style={mainStyle}><div style={labelStyle}>{pharse("address")}:</div><div style={textStyle}>{event.address}<div style={{display:"inline-block",color:"#247BBA",marginLeft:"5px"}} onClick={() => this.handleClick(mooConfig.url.base + '/event/events/show_g_map/' + window.eventId )}  >({pharse("viewMap")})</div></div></div>;
            }
            if (_.has(event, "categoryName")) {
                category = <div style={mainStyle}><div style={labelStyle}>{pharse("category")}:</div><div style={textStyle}>{event.categoryName}</div></div>;
            }
            if (_.has(event, "createName")) {
                userName = <div style={mainStyle}><div style={labelStyle}>{pharse("userName")}:</div><div style={textStyle}>{event.createName}</div></div>;
            }
            if (_.has(event, "description")) {
                description = <div style={{}}><div style={headStyle}>{pharse("des")}:</div>
                        <div className="post-body" style={{padding:"10px 10px 10px ",fontSize:"15px",lineHeight:"1.5"}} dangerouslySetInnerHTML={{__html: event.description}}></div></div>
            }
            if (_.has(window, "attendUser")) {
                var user = window.attendUser;
                var arrayUser = [];
                //var titleTmp = pharse('attendTitle').replace('%s', Object.keys(user).length);
                var titleTmp = pharse('attendTitle');
                Object.keys(user).forEach(function(key) {
                    arrayUser.push(<Chip onClick={() => this.handleClick(user[key].url)} labelStyle={{overflow: "hidden",textOverflow: "ellipsis",maxWidth: "200px"}} key={user[key].name+"-1"} style={{backgroundColor:"#e9e9eb",display:"inline-flex",margin:"4px"}} >
                                <Avatar src={user[key].avatar} />
                                {user[key].name}
                              </Chip>
                      );
                },this);
                
                attend = <div style={{borderTop:"1px solid #dfdfdf"}}><div style={headStyle}>{titleTmp}</div>
                    <div style={{padding:"10px 10px 10px ",fontSize:"13px",lineHeight:"1.5"}}>{arrayUser}</div></div>
            }
            if (_.has(window, "maybeUser")) {
                var user = window.maybeUser;
                var arrayUser = [];
                //var titleTmp = pharse('maybeTitle').replace('%s', Object.keys(user).length);
                var titleTmp = pharse('maybeTitle');
                Object.keys(user).forEach(function(key) {
                    arrayUser.push(<Chip onClick={() => this.handleClick(user[key].url)} labelStyle={{overflow: "hidden",textOverflow: "ellipsis",maxWidth: "200px"}} key={user[key].name+"-1"} style={{backgroundColor:"#e9e9eb",display:"inline-flex",margin:"4px"}} >
                                <Avatar src={user[key].avatar} />
                                {user[key].name}
                              </Chip>
                      );
                },this);
                
                maybe = <div style={{borderTop:"1px solid #dfdfdf"}}><div style={headStyle}>{titleTmp}</div>
                    <div style={{padding:"10px 10px 10px ",fontSize:"13px",lineHeight:"1.5"}}>{arrayUser}</div></div>
            }
            if (_.has(window, "notAttendUser")) {
                var user = window.notAttendUser;
                var arrayUser = [];
                //var titleTmp = pharse('notAttendTitle').replace('%s', Object.keys(user).length);
                var titleTmp = pharse('notAttendTitle');
                Object.keys(user).forEach(function(key) {
                    arrayUser.push(<Chip onClick={() => this.handleClick(user[key].url)} labelStyle={{overflow: "hidden",textOverflow: "ellipsis",maxWidth: "200px"}} key={user[key].name+"-1"} style={{backgroundColor:"#e9e9eb",display:"inline-flex",margin:"4px"}} >
                                <Avatar src={user[key].avatar} />
                                {user[key].name}
                              </Chip>
                      );
                },this);
                
                notAttend = <div style={{borderTop:"1px solid #dfdfdf"}}><div style={headStyle}>{titleTmp}</div>
                    <div style={{padding:"10px 10px 10px ",fontSize:"13px",lineHeight:"1.5"}}>{arrayUser}</div></div>
            }
            if (_.has(window, "awaitingUser")) {
                var user = window.awaitingUser;
                var arrayUser = [];
                //var titleTmp = pharse('awaitingTitle').replace('%s', Object.keys(user).length);
                var titleTmp = pharse('awaitingTitle');
                Object.keys(user).forEach(function(key) {
                    arrayUser.push(<Chip onClick={() => this.handleClick(user[key].url)} labelStyle={{overflow: "hidden",textOverflow: "ellipsis",maxWidth: "200px"}} key={user[key].name+"-1"} style={{backgroundColor:"#e9e9eb",display:"inline-flex",margin:"4px"}} >
                                <Avatar src={user[key].avatar} />
                                {user[key].name}
                              </Chip>
                      );
                },this);
                
                awaiting = <div style={{borderTop:"1px solid #dfdfdf"}}><div style={headStyle}>{titleTmp}</div>
                    <div style={{padding:"10px 10px 10px ",fontSize:"13px",lineHeight:"1.5"}}>{arrayUser}</div></div>
            }
            
            rsvp = <div><div style={headStyle}>{pharse("yourRSVP")}</div>
                <SelectField
                value={this.state.value}
                onChange={this.handleChange}
                style={{width:"150px",marginLeft:"10px"}}
              >
                <MenuItem value={1} primaryText={pharse("yes")} />
                <MenuItem value={2} primaryText={pharse("no")} />
                <MenuItem value={3} primaryText={pharse("maybe")} />
                </SelectField>
                </div>
            
            //console.log(this.props.activites);
            feed = <EventFeed {...this.props} isPhone={isPhone()} ></EventFeed>;
                
            eventView = <div style={{margin:"-8px"}} >
                <Card style={{boxShadow:"none"}} >{media}{title}{rsvp}</Card>
                <div style={{background:"#fff",paddingBottom:"10px"}}>
                    <div style={{height:this.state.height,overflow:this.state.overflow}} >
                        <Card style={{boxShadow:"none"}} >{privacy}{date}{location}{address}{category}{userName}{description}{attend}{maybe}{notAttend}{awaiting}</Card>
                    </div>
                    <div onClick={() => this.handleShowMore()} style={{margin:"10px auto",textAlign:"center"}} ><RaisedButton buttonStyle={{height:"auto"}} labelStyle={{textTransform:"uppercase"}} label={this.state.showMore ? pharse('showLess'): pharse('showMore')  } /></div>
                </div>
                {feed}
            </div>;
        
        }
        else {
            content = <FirstLoading></FirstLoading>;
        }
        
        return <MuiThemeProvider ><div>{content}{eventView}</div></MuiThemeProvider>;

    }
}
class EventFeed extends React.Component {
     constructor(props) {
        super(props);
    }

    render() {
            if(this.props.activites.get('records').count() > 0 ){
                var items = [];
                var isPhone = this.props.isPhone;

                var isFetching = this.props.activites.get('isFetching') ? true : false ;  
                var process = <LoadMore isFetching={isFetching} ></LoadMore>;

                this.props.activites.get('records').forEach(function(item){
                    items.push(<ActivityItem {...item} key={item.id} isPhone={isPhone} likes={this.props.likes} reactions={this.props.reactions} singleComment={true} page="events.view" />);
                    items.push(<div key={'a'+item.id} style={{height:'7px'}}/>);
                }.bind(this));

                return <MuiThemeProvider >
                    <div style={{padding:"8px 0"}}>
                        <div style={{paddingBottom:"0"}} ><ACForm {...this.props} target_id={window.eventId} type={"Event_Event"} /><div style={{height:'7px'}}/>{items}{process}</div>
                   </div>
                </MuiThemeProvider>;
            }else {
                return <MuiThemeProvider >
                        <div style={{padding:"8px 0"}}>
                            <ACForm target_id={window.eventId} type={"Event_Event"} {...this.props} />
                            <div style={{marginTop:"10px",background:"#fff",padding:"10px",textAlign:"center",fontFamily:"Roboto, sans-serif"}} >{pharse('notFound')}</div>
                        </div>
                        </MuiThemeProvider>;
            }  
        
    }
}
function EventViewContent(props) {
    return <EventDetail {...props} />
}

export default EventViewContent;
