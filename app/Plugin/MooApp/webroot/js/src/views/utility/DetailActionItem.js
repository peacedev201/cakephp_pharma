import React from 'react';
import _ from 'lodash';
import {Card, CardActions, CardHeader, CardMedia, CardTitle, CardText} from 'material-ui/Card';
import IconMenu from 'material-ui/IconMenu';
import MenuItem from 'material-ui/MenuItem';
import IconButton from 'material-ui/IconButton';
import ActionThumbUp from 'material-ui/svg-icons/action/thumb-up';
import ActionThumbDown from 'material-ui/svg-icons/action/thumb-down';
import ActionFavoriteBorder from 'material-ui/svg-icons/action/favorite-border';
import ActionFavorite from 'material-ui/svg-icons/action/favorite';
import EditorModeComment from 'material-ui/svg-icons/editor/mode-comment';
import SocialShare from 'material-ui/svg-icons/social/share';
import {List, ListItem} from 'material-ui/List';
import Divider from 'material-ui/Divider';
import {pharse, isShowDislike} from '../../utility/mooApp';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import getMuiTheme from 'material-ui/styles/getMuiTheme';
import LikeActions from '../../data/actions/LikeActions';
import ReactionActions from '../../data/actions/ReactionActions';
import AppAction from '../../utility/AppAction';
import {ReactionLibrary} from "./Reaction";
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
var flag = true;
export class DetailActionItem extends React.Component {
    constructor(props) {
        super(props);
        this.handleChange = this.handleChange.bind(this);
        this.handleComment = this.handleComment.bind(this);
        this.handleViewWhoLike = this.handleViewWhoLike.bind(this);
        this.handleBookmark = this.handleBookmark.bind(this);
        this.state = {
            bookmark: ( _.has(window, "isViewerBookmark") && window.isViewerBookmark == true) ? true : false
        };
    }
    autoOpenComment() {
            var string = window.location.href;
            var substring = "open_comment";
            if(string.includes(substring)) {
                this.handleComment();
            }
    }
    handleComment(){
        if(this.props.disableComment) {
            window.location.href = this.props.url + '?open_comment=1&access_token='+mooConfig.access_token;
        }
        else {
            AppAction.openComment({"objects":this.props});
        }
        
    }
    handleShareFeed(share_type){
        console.log("openShareFeed",this.props);
        AppAction.openShareFeed(this.props, share_type);
    }
    handleChange(action) {
        var object = _.get(this.props, 'object', '');
        var id = _.get(this.props, 'id', 0);
        var type = _.get(this.props, 'type', '');
        var likeId = id+type;
        if(object == 'Photo_Photo') {
           var likeId = id+"Photo_Photo";
        }
        switch (action) {
            case "like":
                LikeActions.doLike(likeId,object);
                break;
            case "unlike" :
                LikeActions.doUnlike(likeId,object);
                break;
            case "dislike" :
                LikeActions.doDislike(likeId,object);
                break;
            case "undislike" :
                LikeActions.doUndislike(likeId,object);
                break;
        }
    }
    handleViewWhoLike(action) {
        var id , type,url;
        var object = _.get(this.props, 'object', '');
        var id = _.get(this.props, 'id', 0);
        var type = _.get(this.props, 'type', '');
        if(object == 'Photo_Photo') {
           id = _.get(this.props, 'id', 0);
            type = 'Photo_Photo';
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
        var object = _.get(this.props, 'object', '');
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
        
    if(flag == true) {
        this.autoOpenComment();
        flag = false;
    }
        var feedAction = {
            display: "flex", margin: "0", background: "#fff", borderTop: "1px solid #dadde1", borderBottom: "1px solid #dadde1"
        };
        var id, type, like, strTotalLike, strTotalDislike, strTotalComment, commentCount, totalLike, totalDislike, totalLikeDiv,shareContent,bookmarkButton;
        var totalDislikeDiv, disLikeButton, likeButton;
        var object = this.props.object;
        var reaction, reactionButton, reactionView;

        if (object == 'Photo_Photo') {
            reaction = ReactionLibrary.getData(this.props.reactions.get(_.get(this.props, 'id', 0) + 'Reaction' + 'Photo_Photo', false));
        }else{
            reaction = ReactionLibrary.getData(this.props.reactions.get(_.get(this.props, 'id', 0) + 'Reaction' + _.get(this.props, 'type', 0), false));
        }

    if(reaction.isPluginActive === 1) {
        reactionButton = ReactionLibrary.ItemReactionButton(reaction);
        reactionView = ReactionLibrary.ItemReactionReview(reaction);
    }else{
        var like = this.props.likes.get(_.get(this.props, 'id', 0) + _.get(this.props, 'type', 0), false);
        
        if(object == 'Photo_Photo') {
           like = this.props.likes.get(_.get(this.props, 'id', 0) + 'Photo_Photo', false);
        }
       
        strTotalLike = strTotalDislike = strTotalComment = '';
        if (like != false) {
            totalLike = parseInt(like.like);
            totalDislike = parseInt(like.dislike);
            strTotalLike = (totalLike > 1) ? pharse('likes').replace('%s', totalLike) : pharse('like').replace('%s', totalLike);
            strTotalDislike = (totalDislike > 1) ? pharse('dislikes').replace('%s', totalDislike) : pharse('dislike').replace('%s', totalDislike);
        }

        if (like.isViewerLiked === true) {
            totalLikeDiv = <span onClick={() => this.handleViewWhoLike("like")} style={{display: "inline-block", marginRight: "10px", color: "#FB7923", fontSize: "12px"}}>{strTotalLike}</span>;
                likeButton = <MuiThemeProvider muiTheme={iconActive}>
                    <span  style={{height: "35px", padding: "0 0", textAlign: "center", flex: "1"}}>
                        <IconButton style={{height: "35px", padding: "0"}} ><ActionThumbUp  viewBox="0 -3 30 30" onClick={() => this.handleChange("unlike")} ></ActionThumbUp></IconButton>
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

        if (isShowDislike()) {
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
            disLikeButton = <div style={{display: "inline-block"}} ></div>;

        }
    }
        commentCount = _.get(this.props, 'commentCount', 0);
        strTotalComment = (commentCount > 1) ? pharse('comments').replace('%s', commentCount) : pharse('comment').replace('%s', commentCount);

        bookmarkButton = <div style={{display: "inline-block"}} ></div>;
        if(_.has(window,"isViewerBookmark" )) {
            if(_.get(window,"isViewerBookmark") == true || this.state.bookmark == true ) {
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
        
       
        
        return  <CardActions style={{padding: "0"}} >
                    <div style={{display: "block", margin: "10px", padding: "0", position: "relative",fontFamily:"Roboto, sans-serif"}}>
                        {reactionView}
                        {totalLikeDiv}
                        {totalDislikeDiv}
                        <span style={{color: "#90949c", fontSize: "12px"}}>{strTotalComment}</span>
                    </div>
                    <div style={feedAction}>
                        {reactionButton}
                        {likeButton}
                        {disLikeButton}
                        <MuiThemeProvider muiTheme={iconNotActive}>
                            <span style={{height: "35px", padding: "0 0", textAlign: "center", flex: "1", color: "#5e5e5e", fontSize: "12px"}}>
                                <IconButton style={{height: "35px", padding: "0"}} ><EditorModeComment viewBox="0 -3 30 30" onClick={() => this.handleComment()}></EditorModeComment></IconButton>
                            </span>
                        </MuiThemeProvider>
                        {shareContent}
                        {bookmarkButton}
                    </div>
                 </CardActions>
        }
    }