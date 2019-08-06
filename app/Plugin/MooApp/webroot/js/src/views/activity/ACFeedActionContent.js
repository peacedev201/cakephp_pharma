import React from 'react';
import _ from 'lodash';
import {Card, CardActions, CardHeader, CardMedia, CardTitle, CardText} from 'material-ui/Card';
import IconMenu from 'material-ui/IconMenu';
import MenuItem from 'material-ui/MenuItem';
import IconButton from 'material-ui/IconButton';
import ActionThumbUp from 'material-ui/svg-icons/action/thumb-up';
import ActionThumbDown from 'material-ui/svg-icons/action/thumb-down';
import EditorModeComment from 'material-ui/svg-icons/editor/mode-comment';
import SocialShare from 'material-ui/svg-icons/social/share';
import {List, ListItem} from 'material-ui/List';
import Avatar from 'material-ui/Avatar';
import Divider from 'material-ui/Divider';
import {pharse, isShowDislike} from '../../utility/mooApp';
import AppAction from '../../utility/AppAction';
import FontIcon from 'material-ui/FontIcon';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import getMuiTheme from 'material-ui/styles/getMuiTheme';
import LikeActions from '../../data/actions/LikeActions';
import ActionFavoriteBorder from 'material-ui/svg-icons/action/favorite-border';
import ActionFavorite from 'material-ui/svg-icons/action/favorite';
import {ReactionLibrary} from '../utility/Reaction';
const iconNotActive = getMuiTheme({
    svgIcon: {
        color: "#9b9b9b"
    }
});
const iconActive = getMuiTheme({
    svgIcon: {
        color: "#FB7923"
    }
});
export class ACFeedActionContent extends React.Component {
    constructor(props) {
        super(props);

        // This binding is necessary to make `this` work in the callback
        this.handleClick = this.handleClick.bind(this);
        this.handleComment = this.handleComment.bind(this);
        this.handleViewWhoLike = this.handleViewWhoLike.bind(this);
        this.handleBookmark = this.handleBookmark.bind(this);
        this.state = {
            bookmark: ( _.has(this.props, "isViewerBookmark") && this.props.isViewerBookmark == true) ? true : false
        };
    }
    componentDidMount() {}
    
//    componentDidUpdate(prevProps, prevState) {
//        //setTimeout(function () {
//            if(prevProps.action != 'FeedAds') {
//                if(typeof prevProps.isViewerBookmark != "undefined") {
//                    if(prevProps.isViewerBookmark != prevState.bookmark ) {
//                       this.setState({
//                          bookmark: !prevState.bookmark
//                        });
//                    }
//                }
//            }
//       //}.bind(this), 1);
//    }
    
    componentWillReceiveProps(nextProps) {
        if(nextProps.action != 'FeedAds') {
                if(typeof nextProps.isViewerBookmark != "undefined") {
                    if(nextProps.isViewerBookmark != this.state.bookmark ) {
                        this.setState({
                            bookmark: !this.state.bookmark
                        });
                    }
                }
        }
    }
    
    
    handleClick() {
        if (_.has(this.props, 'objects.url')) {
            window.location.href = this.props.objects.url + '?access_token='+mooConfig.access_token;
        }
    }
    handleCommentBoxCLick(type) {
        if(type == 'Activity') {
            if(_.has(this.props,'objects.images') && this.props.objects.images.length == 1 ) {
                    window.location.href = this.props.objects.url + '?access_token='+mooConfig.access_token+'&targetPhotoId='+this.props.objects.images[0].idPhoto;
            }
            else {
                var url = mooConfig.url.base + '/activities/view/' + this.props.id + '?access_token='+mooConfig.access_token;
                window.location.href = url;
            }
        }
        else {
            if (_.has(this.props, 'objects.url')) {
                if(type == 'Photo_Photo') {
                    if(this.props.objects.images.length == 1 ) {
                        window.location.href = this.props.objects.url + '?access_token='+mooConfig.access_token+'&targetPhotoId='+this.props.comment.target_id;
                    }
                }
                else {
                    window.location.href = this.props.objects.url + '?access_token='+mooConfig.access_token;
                }
            }
        }
    }
    handleComment(){
        console.log("handelComment",this.props);

        var data = new Array();
        var page = _.get(this.props,'page','');
        
        if (_.has(this.props, 'objects.images') && _.get(this.props, 'objects.images').length == 1  ){ 
            if(_.get(this.props, 'action') == 'wall_post') {
                data['id'] = _.get(this.props, 'id');
                data['type'] = _.get(this.props, 'objects.type');
                data['action'] = _.get(this.props, 'action');
                data['url'] = _.get(this.props, 'objects.url') + '?access_token='+mooConfig.access_token+'&targetPhotoId='+this.props.objects.images[0].idPhoto ;
            }
            if(_.get(this.props, 'action') == 'photos_add') {
                if( _.get(this.props, 'objects.images[0].type') != 'Photo_Album'  ) {
                    
                    if(_.has(this.props,"page")) {
                        var url = mooConfig.url.full +  mooConfig.url.base + '/activities/view/' + this.props.id + '?access_token='+mooConfig.access_token+'&targetPhotoId='+this.props.objects.images[0].idPhoto ;
                        data['id'] = _.get(this.props, 'id');
                        data['type'] = 'Activity';
                        data['action'] = _.get(this.props, 'action');
                        data['url'] = url;
                    }
                    else {
                        data['id'] = _.get(this.props, 'id');
                        data['type'] = 'Activity';
                        data['action'] = 'wall_post';
                        data['url'] = _.get(this.props, 'objects.url') + '?access_token='+mooConfig.access_token+'&targetPhotoId='+this.props.objects.images[0].idPhoto ;
                    }
                }
                else{
                    data['id'] = _.get(this.props, 'objects.images[0].idAlbum');
                    data['type'] = _.get(this.props, 'objects.type');
                    data['action'] = _.get(this.props, 'action');
                    data['url'] = _.get(this.props, 'objects.url');
                }
            }
            if(_.get(this.props, 'isActivityView') == true) {
                var url = mooConfig.url.full +  mooConfig.url.base + '/activities/view/' + this.props.id + '?access_token='+mooConfig.access_token;
                data['id'] = _.get(this.props, 'id');
                data['type'] = 'Activity';
                data['action'] = _.get(this.props, 'action');
                data['url'] = url;
            }
            if(typeof data != "undefined") {
                AppAction.openComment({"objects":data,"page":page});
            }
            else {
                AppAction.openComment(this.props);
            }
        }
        else if(_.get(this.props, 'type') == 'share' || _.get(this.props, 'objects.type') == 'Group_Group' || _.get(this.props, 'objects.type') == 'Event_Event' || _.get(this.props, 'action') == 'friend_add') {
            var url = mooConfig.url.full +  mooConfig.url.base + '/activities/view/' + this.props.id + '?access_token='+mooConfig.access_token;
            data['id'] = _.get(this.props, 'id');
            data['type'] = 'Activity';
            data['action'] = _.get(this.props, 'action');
            data['url'] = url;
            AppAction.openComment({"objects":data,"page":page});
        }
        else if(_.get(this.props, 'isActivityView') == true) {
            var url = mooConfig.url.full +  mooConfig.url.base + '/activities/view/' + this.props.id + '?access_token='+mooConfig.access_token;
            data['id'] = _.get(this.props, 'id');
            data['type'] = 'Activity';
            data['action'] = _.get(this.props, 'action');
            data['url'] = url;
            AppAction.openComment({"objects":data,"page":page});
        }
        else if(_.has(this.props,"page")) {
            if(_.get(this.props, 'action') == 'photos_add') {
                var url = mooConfig.url.full +  mooConfig.url.base + '/activities/view/' + this.props.id + '?access_token='+mooConfig.access_token;
                data['id'] = _.get(this.props, 'id');
                data['type'] = 'Activity';
                data['action'] = _.get(this.props, 'action');
                data['url'] = url;
                AppAction.openComment({"objects":data,"page":page});
            }
            else {
                AppAction.openComment(this.props);
            }
        }
        else {
        	AppAction.openComment(this.props);
        }
        
    }
    handleShareFeed(share_type){
        console.log("openShareFeed",this.props);
        AppAction.openShareFeed(this.props, share_type);
    }
    handleChange(action) {
        
        
        var id , type;
        if( _.get(this.props, 'isActivityView') == true || (_.get(this.props, 'type') == 'add' && _.get(this.props, 'objects.type') != 'Photo_Album'  ) || _.get(this.props, 'type') == 'join' || _.get(this.props, 'type') == 'share'  || (_.get(this.props, 'type') == 'create' && _.get(this.props, 'objects.type') == 'Event_Event' ) || (_.get(this.props, 'type') == 'create' && _.get(this.props, 'objects.type') == 'Group_Group' ) ) {
            id = _.get(this.props, 'id', 0);
            type = 'Activity';
        }
        else {
            id = _.get(this.props, 'objects.id', 0);
            type = _.get(this.props, 'objects.type', '');
        }
        var likeId = id+type;
        switch (action) {
            case "like":
                LikeActions.doLike(likeId,type);
                break;
            case "unlike" :
                LikeActions.doUnlike(likeId,type);
                break;
            case "dislike" :
                LikeActions.doDislike(likeId,type);
                break;
            case "undislike" :
                LikeActions.doUndislike(likeId,type);
                break;
        }
    }
    handleViewWhoLike(action) {
        var id , type,url;
        if( _.get(this.props, 'objects.type') == 'Activity_Link' || _.get(this.props, 'isActivityView') == true || (_.get(this.props, 'type') == 'add' && _.get(this.props, 'objects.type') != 'Photo_Album'  ) || _.get(this.props, 'type') == 'join' || _.get(this.props, 'type') == 'share'  || (_.get(this.props, 'type') == 'create' && _.get(this.props, 'objects.type') == 'Event_Event' ) || (_.get(this.props, 'type') == 'create' && _.get(this.props, 'objects.type') == 'Group_Group' ) ) {
            id = _.get(this.props, 'id', 0);
            type = 'activity';
        }
        else {
            id = _.get(this.props, 'objects.id', 0);
            type = _.get(this.props, 'objects.type', '');
        }
        switch (action) {
            case "like":
                var url = mooConfig.url.full +  mooConfig.url.base + '/likes/ajax_show/' + type + '/' + id + '?access_token='+mooConfig.access_token;
                break;
            case "dislike" :
                var url = mooConfig.url.full +  mooConfig.url.base + '/likes/ajax_show/' + type + '/' + id + '/1?access_token='+mooConfig.access_token;
                break;
        }
        window.location.href = url;
    }
    handleBookmark(action) {
        var object = 'Activity'
        var id = _.get(this.props, 'id', 0);
        switch (action) {
            case "add":
                LikeActions.doBookmark(id,object);
                break;
            case "remove" :
                LikeActions.doRemoveBookmark(id,object);
                break;
        }
        this.setState({
                bookmark: !this.state.bookmark
            });
    }
    render() {
        
        
        var feedAction = {
            display: "flex", margin: "0", background: "#fff", borderTop: "1px solid #dadde1", borderBottom: "1px solid #dadde1"
        };
        var id, type, like, strTotalLike, strTotalDislike, strTotalComment, commentCount, totalLike, totalDislike, commentSection, totalLikeDiv,shareContent;
        var totalDislikeDiv, disLikeButton, likeButton,bookmarkButton;
        var reaction, reactionButton, reactionView;

        if( _.get(this.props, 'isActivityView') == true || (_.get(this.props, 'type') == 'add' && _.get(this.props, 'objects.type') != 'Photo_Album'  ) || _.get(this.props, 'type') == 'join' || _.get(this.props, 'type') == 'share' || (_.get(this.props, 'type') == 'create' && _.get(this.props, 'objects.type') == 'Event_Event' ) || (_.get(this.props, 'type') == 'create' && _.get(this.props, 'objects.type') == 'Group_Group' ) ) {
            id = _.get(this.props, 'id', 0);
            type = 'Activity';
        }
        else {
            id = _.get(this.props, 'objects.id', 0);
            type = _.get(this.props, 'objects.type', '');
        }

        reaction = ReactionLibrary.getData(this.props.reactions.get(id + 'Reaction' + type, false));

    if(reaction.isPluginActive === 1) {
        reactionButton = ReactionLibrary.ActivityReactionButton(reaction);
        reactionView = ReactionLibrary.ActivityReactionReview(reaction);
    }else{
        like = this.props.likes.get(id + type, false);

        
        strTotalLike = strTotalDislike = strTotalComment = '';
        if (like != false) {
            totalLike = parseInt(like.get('like'));
            totalDislike = parseInt(like.get('dislike'));
            strTotalLike = (totalLike > 1) ? pharse('likes').replace('%s', totalLike) : pharse('like').replace('%s', totalLike);
            strTotalDislike = (totalDislike > 1) ? pharse('dislikes').replace('%s', totalDislike) : pharse('dislike').replace('%s', totalDislike);
        }

        if (like.isViewerLiked === true) {
            totalLikeDiv = <span onClick={() => this.handleViewWhoLike("like")} style={{display: "inline-block", marginRight: "10px", color: "#FB7923", fontSize: "12px"}}>{strTotalLike}</span>;
                likeButton = <MuiThemeProvider muiTheme={iconActive}>
                    <span  style={{height: "35px", padding: "0 0", textAlign: "center", flex: "1"}}>
                        <IconButton style={{height: "35px", padding: "0"}} ><ActionThumbUp viewBox="0 -3 30 30" onClick={() => this.handleChange("unlike")} ></ActionThumbUp></IconButton>
                    </span>
                </MuiThemeProvider>;
        } else {
            totalLikeDiv = <span onClick={() => this.handleViewWhoLike("like")} style={{display: "inline-block", marginRight: "10px", color: "#90949c", fontSize: "12px"}}>{strTotalLike}</span>;
            likeButton = <MuiThemeProvider muiTheme={iconNotActive}>
            <span  style={{height: "35px", padding: "0 0", textAlign: "center", flex: "1"}}>
                <IconButton style={{height: "35px", padding: "0"}} ><ActionThumbUp viewBox="0 -3 30 30" onClick={() => this.handleChange("like")} ></ActionThumbUp></IconButton>
            </span>
            </MuiThemeProvider>;
        }

        if (_.get(this.props,'isHideDislike') == false) {
            if (like.isViewerDisliked === true) {
                totalDislikeDiv = <span onClick={() => this.handleViewWhoLike("dislike")} style={{display: "inline-block", marginRight: "10px", color: "#FB7923", fontSize: "12px"}}>{strTotalDislike}</span>;
                disLikeButton = <MuiThemeProvider muiTheme={iconActive}>
                    <span  style={{height: "35px", padding: "0 0", textAlign: "center", flex: "1", color: "#5e5e5e", fontSize: "12px"}}>
                        <IconButton style={{height: "35px", padding: "0"}} ><ActionThumbDown viewBox="0 -3 30 30" onClick={() => this.handleChange("undislike")} ></ActionThumbDown></IconButton>
                    </span>
                   </MuiThemeProvider>
            } else {
                totalDislikeDiv = <span onClick={() => this.handleViewWhoLike("dislike")} style={{display: "inline-block", marginRight: "10px", color: "#90949c", fontSize: "12px"}}>{strTotalDislike}</span>;
                disLikeButton = <MuiThemeProvider muiTheme={iconNotActive}>
                <span style={{height: "35px", padding: "0 0", textAlign: "center", flex: "1", color: "#5e5e5e", fontSize: "12px"}}>
                    <IconButton style={{height: "35px", padding: "0"}} ><ActionThumbDown viewBox="0 -3 30 30" onClick={() => this.handleChange("dislike")} ></ActionThumbDown></IconButton>
                </span>
            </MuiThemeProvider>
            }
        } else {
            totalDislikeDiv = <div style={{display: "inline-block"}} ></div>;
            disLikeButton = <div></div>;

        }
    }

        commentCount = _.get(this.props, 'commentCount', 0);
        strTotalComment = (commentCount > 1) ? pharse('comments').replace('%s', commentCount) : pharse('comment').replace('%s', commentCount);

        bookmarkButton = <div style={{display: "inline-block"}} ></div>;
        if(_.has(this.props,"isViewerBookmark" )) {
            if(this.state.bookmark == true ) {
                bookmarkButton = <MuiThemeProvider muiTheme={iconActive}>
                    <span  style={{height: "35px", padding: "0 0", textAlign: "center", flex: "1", color: "#5e5e5e", fontSize: "12px"}}>
                        <IconButton style={{height: "35px", padding: "0"}} ><ActionFavorite viewBox="0 -3 30 30" onClick={() => this.handleBookmark("remove")} ></ActionFavorite></IconButton>
                    </span>
                   </MuiThemeProvider>
            }
            else {
                bookmarkButton = <MuiThemeProvider muiTheme={iconNotActive}>
                <span style={{height: "35px", padding: "0 0", textAlign: "center", flex: "1", color: "#5e5e5e", fontSize: "12px"}}>
                    <IconButton style={{height: "35px", padding: "0"}} ><ActionFavoriteBorder viewBox="0 -3 30 30" onClick={() => this.handleBookmark("add")} ></ActionFavoriteBorder></IconButton>
                </span>
                </MuiThemeProvider>
            }
            
        }
        
        if (_.get(this.props, "share", true) == true) {
            shareContent = <MuiThemeProvider muiTheme={iconNotActive}>
                            <span style={{height: "35px", padding: "0 0", textAlign: "center", flex: "1", color: "#5e5e5e", fontSize: "12px"}}>
                                
                                    <IconMenu
                                        style={{}}
                                        iconButtonElement={<IconButton style={{height: "35px", padding: "0"}}  ><SocialShare viewBox="0 -3 30 30" ></SocialShare></IconButton>}
                                        anchorOrigin={{horizontal: 'right', vertical: 'top'}}
                                        targetOrigin={{horizontal: 'right', vertical: 'top'}}
                                    >
                                        <MenuItem primaryText={pharse('myWall')} onClick={() => this.handleShareFeed('#me')}/>
                                        <MenuItem primaryText={pharse('friendWall')} onClick={() => this.handleShareFeed('#friend')}/>
                                        <MenuItem primaryText={pharse('groupWall')} onClick={() => this.handleShareFeed('#group')}/>
                                        <MenuItem primaryText={pharse('email')} onClick={() => this.handleShareFeed('#email')}/>
                                </IconMenu>
                                    
                            </span>
                        </MuiThemeProvider>;
        }
        else {
            shareContent = <div></div>;
        }
        
        
        if (this.props.singleComment) {
            if (this.props.commentCount > 0 ) {
                //if (this.props.modified != this.props.published ) {
                    var thumbnail,type ;
                    if (_.has(this.props, "comment.thumbnail")) {  
                                thumbnail = <img width="200" src={_.get(this.props,"comment.thumbnail")}/>;
                    }
                    type = _.get(this.props,"comment.type");
                    commentSection = <div className="commentFeed" onClick={() => this.handleCommentBoxCLick(type)} >
                        <List style={{padding: "10px 10px 10px 10px"}} >
                            <ListItem 
                            style={{}}
                            innerDivStyle={{padding: "0 0 0 40px"}}
                            primaryText={ < div style = {{fontSize: "14px", fontWeight: "700"}}   dangerouslySetInnerHTML={{__html: _.get(this.props, 'comment.user.name', '')}}></div>}
                            secondaryText={ < div style = {{wordWrap:"break-word",height: "auto", fontSize: "14px", fontWeight: "400"}}   dangerouslySetInnerHTML={{__html: _.get(this.props, 'comment.message', '')}}></div> }
                            leftAvatar={ < Avatar style = {{borderRadius: 0, top: 0, left: 0}} size={32} src={_.get(this.props, 'comment.user.image.50_square', '')} />}
                            secondaryTextLines = {2}
                            />
                            <div style={{padding: "5px 0 0 40px"}}>{thumbnail}</div>
                            <div style={{color: "#888", fontSize: "12px", padding: "5px 0 0 40px"}} >{_.get(this.props, 'comment.createdTranslated', '')}</div>
                        </List>
                        </div>;
                //}
            }else{
                commentSection = <div></div>;
            }
        }
            if (_.get(this.props, "hideLikeAndComment", true) == true) {
                return <div style={{borderTop:"1px solid #dadde1"}}>{commentSection}</div>;
            }
            else {
            return  <CardActions style={{padding: "0"}} >
                        <div style={{display: "block", margin: "10px", padding: "0", position: "relative"}}>
                            {reactionView}
                            {totalLikeDiv}
                            {totalDislikeDiv}
                            <span onClick={this.handleComment} style={{color: "#90949c", fontSize: "12px"}}>{strTotalComment}</span>
                        </div>
                        <div style={feedAction}>
                            {reactionButton}
                            {likeButton}
                            {disLikeButton}
                            <MuiThemeProvider muiTheme={iconNotActive}>
                                <span  style={{height: "35px", padding: "0 0", textAlign: "center", flex: "1", color: "#5e5e5e", fontSize: "12px"}}>
                                    <IconButton style={{height: "35px", padding: "0"}}  ><EditorModeComment viewBox="0 -3 30 30" onClick={this.handleComment} ></EditorModeComment></IconButton>
                                </span>
                            </MuiThemeProvider>
                            {shareContent}
                            {bookmarkButton}
                        </div>
                        {commentSection}
                     </CardActions>
            }
        }
    }