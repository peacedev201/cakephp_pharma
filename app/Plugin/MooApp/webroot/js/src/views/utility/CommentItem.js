import React from 'react';
import _ from 'lodash';
import {Card, CardActions, CardHeader, CardMedia, CardTitle, CardText} from 'material-ui/Card';
import IconMenu from 'material-ui/IconMenu';
import MenuItem from 'material-ui/MenuItem';
import IconButton from 'material-ui/IconButton';
import ActionThumbUp from 'material-ui/svg-icons/action/thumb-up';
import ActionThumbDown from 'material-ui/svg-icons/action/thumb-down';
import NavigationMoreVert from 'material-ui/svg-icons/navigation/more-vert';

import {List, ListItem} from 'material-ui/List';
import Avatar from 'material-ui/Avatar';
import Divider from 'material-ui/Divider';
import {pharse, isShowDislike} from '../../utility/mooApp';

import FontIcon from 'material-ui/FontIcon';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import LikeStore from '../../data/stores/LikeStore';
import LikeActions from '../../data/actions/LikeActions';
import CommentActions from '../../data/actions/CommentActions';
import {ReactionLibrary} from "./Reaction";

export class CommentItem extends React.Component {
    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
        this.handleLoadMoreComment = this.handleLoadMoreComment.bind(this);
        this.handleDeleteComment = this.handleDeleteComment.bind(this);
    }
    handleClick(url) {
            window.location.href = url;
    }
    handleLoadMoreComment() {
        CommentActions.fetchNextComment();
    }
    handleDeleteComment(obj) {
        CommentActions.fetchDeleteComment(obj);
    }
    render() {
        
        
        var id, type, commentSection,commentContent,commentBlock ,block,loadMoreComment,contentComment,deleteComment;
        deleteComment = contentComment = loadMoreComment = commentBlock = commentSection = <div></div>;
        var commentArray = [];
        var cnt = 1;
        var isLoadmore = true;
        var reaction, reactionButton, reactionView, commentLikeSection;

        contentComment = this.props.comments.get('comments');
        if (this.props.comments.get('comments')) {
            block = contentComment.forEach(function(obj) { 
                var thumbnail,commentStyle ;
                if(cnt == 1) {
                    commentStyle = {padding:"15px 0",position:"relative"}
                }
                else {
                    commentStyle = {borderTop:"1px solid #dadde1",padding:"15px 0",position:"relative"}
                }
                cnt++;
                if (_.has(obj, "isGiftImage") && _.get(obj, "isGiftImage") == true ) {
                   if (_.has(obj, "thumnails.default")) {  
                        thumbnail = <div style={{padding: "5px 0 0 40px"}}> <img width="200" src={_.get(obj,"thumnails.default")}/></div>;
                    }
                }
                else {
                    if (_.has(obj, "thumnails.200")) {  
                        thumbnail = <div style={{padding: "5px 0 0 40px"}}> <img width="200" src={_.get(obj,"thumnails.200")}/></div>;
                    }
                }
                if (_.has(obj, "isLoadmore") && _.get(obj, "isLoadmore") == false ) {  
                        isLoadmore = false;
                }
                if(_.has(obj, 'canDelete','') && (_.get(obj, 'canDelete') == true )) {
                    
                    deleteComment = <IconMenu
                    style={{display:"block",position:"absolute",right:"5px",top:"8px"}}
                    iconButtonElement={<IconButton style={{padding:"0px",height:"auto",width:"100%",display:"block"}}  ><NavigationMoreVert ></NavigationMoreVert></IconButton>}
                    anchorOrigin={{horizontal: 'right', vertical: 'top'}}
                    targetOrigin={{horizontal: 'right', vertical: 'top'}}
                    >
                        <MenuItem onClick={() => this.handleDeleteComment(obj)} primaryText={pharse("deleteComment")} />
                    </IconMenu> 
                }
		reaction = ReactionLibrary.getData(this.props.reactions.get(_.get(obj, 'likeObject.id', 0) + 'Reaction' + _.get(obj, 'likeObject.type', 0) , false));
                reactionButton = ReactionLibrary.CommentReactionButton(reaction);
                reactionView = ReactionLibrary.CommentReactionReview(reaction);

                if(reaction.isPluginActive == 1){
                    commentLikeSection = <div style={{display: "inline-block", position: "relative", verticalAlign:"middle"}}>{reactionButton}{reactionView}</div>;
                }else{
                    commentLikeSection = <CommentLike {...obj} likes={this.props.likes} reactions={this.props.reactions} />;
                }
                commentSection = <div style={commentStyle} >
                        <ListItem 
                        style={{}}
                        innerDivStyle={{padding: "0 40px 0 40px"}}
                        primaryText={ < div onClick={() => this.handleClick(_.get(obj,'userUrl',''))} style = {{fontSize: "14px", fontWeight: "600",color:"#247BBA"}}   dangerouslySetInnerHTML={{__html: _.get(obj, 'userNameHtml', '')}}></div>}
                        leftAvatar={ < Avatar onClick={() => this.handleClick(_.get(obj,'userUrl',''))} style = {{borderRadius: 0, top: 0, left: 0}} size={32} src={_.get(obj, 'userAvatar.50_square', '')} />}
                        />
                        <div style = {{wordWrap:"break-word",fontFamily:"Roboto, sans-serif",lineHeight:"18px",padding: "5px 0 0 40px",height: "auto", fontSize: "14px", fontWeight: "400"}}   dangerouslySetInnerHTML={{__html: _.get(obj, 'message', '')}}></div>
                        {thumbnail}
                        <div style={{fontFamily:"Roboto, sans-serif",color: "#aaa", fontSize: "12px", padding: "0 0 0 40px",marginTop:"0"}} >{_.get(obj, 'publishedTranslated', '')}{commentLikeSection}</div>
                        {deleteComment}
                    </div>;
                     commentArray.push(<div key={obj.id}>{commentSection}</div>);
             }, this);
             
             if(isLoadmore == true) {
                 loadMoreComment = <div style={{fontFamily:"Roboto, sans-serif",color:"#247BBA",fontSize:"16px"}} onClick={() => this.handleLoadMoreComment()}>{pharse('viewMoreComment')}</div>
             }
             if(commentArray.length) commentBlock = <List style={{padding: "10px 10px 10px 10px",background:"#fff"}} >{loadMoreComment}{commentArray}</List>;
                 else commentBlock = <div style={{fontSize:"12px",padding: "10px 10px 10px 10px",background:"#fff",fontFamily:"Roboto, sans-serif"}}>{pharse('noComment')}</div>
            
        }else{
            commentBlock = <div></div>;
        }
        return   <div>{commentBlock}</div>;
    }
}

export class CommentLike extends React.Component {
    constructor(props) {
        super(props);
        this.handleViewWhoLike = this.handleViewWhoLike.bind(this);
        // This binding is necessary to make `this` work in the callback
        //this.handleClick = this.handleClick.bind(this);
    }
    componentDidMount() {}

    handleChange(action) {
        var object = _.get(this.props, 'likeObject.type','');
        var id = _.get(this.props, 'likeObject.id', 0);
        var type = _.get(this.props, 'likeObject.type', 0)
        var likeId = id+type;

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
        var id = _.get(this.props, 'likeObject.id', 0);
        var type = _.get(this.props, 'likeObject.type', 0)
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
    render() {
        
        
        var id, type, like, commentCount, totalLike, totalDislike, totalLikeDiv;
        var totalDislikeDiv, disLikeButton, likeButton;
        var object = _.get(this.props, 'likeObject.type','');
        var like = this.props.likes.get(_.get(this.props, 'likeObject.id', 0) + _.get(this.props, 'likeObject.type',''), false);
        var dot = <span style={{display:"inline-block",margin:"0 5px"}}>&#8226;</span>;
       
        if (like != false) {
            totalLike = parseInt(like.like);
            totalDislike = parseInt(like.dislike);
        }

        if (like.isViewerLiked === true) {
            totalLikeDiv = <span onClick={() => this.handleChange("unlike")} style={{display: "inline-block", color: "#FB7923", fontSize: "13px",textTransform:"capitalize"}}>{pharse('Like')}</span>;
                likeButton = <span onClick={() => this.handleViewWhoLike("like")} style={{padding: "0 0", textAlign: "center",}}>
                        <IconButton style={{verticalAlign:"sub",width:"25px",height:"normal", padding: "0"}} ><ActionThumbUp color="#FB7923" viewBox="0 -16 40 40" ></ActionThumbUp></IconButton>
                            <span style={{display:"inline-block",verticalAlign:"baseline",color: "#FB7923",marginLeft:"-5px",marginRight:"10px"}} >{totalLike}</span>
                    </span>;
        } else {
            totalLikeDiv = <span onClick={() => this.handleChange("like")} style={{display: "inline-block", color: "#aaa", fontSize: "13px",textTransform:"capitalize"}}>{pharse('Like')}</span>;
            likeButton =  <span onClick={() => this.handleViewWhoLike("like")} style={{padding: "0 0", textAlign: "center"}}>
                <IconButton style={{verticalAlign:"sub",width:"25px",height:"normal", padding: "0"}} ><ActionThumbUp color="#ccc" viewBox="0 -16 40 40"></ActionThumbUp></IconButton>
                <span style={{display:"inline-block",verticalAlign:"baseline",marginLeft:"-5px",marginRight:"10px"}} >{totalLike}</span>
            </span>;
        }

        if (isShowDislike()) {
            if (like.isViewerDisliked === true) {
                totalDislikeDiv = <div style={{display: "inline-block"}} >{dot}<span onClick={() => this.handleChange("undislike")} style={{display: "inline-block",color: "#FB7923", fontSize: "12px"}}>{pharse('Dislike')}</span></div>;
                disLikeButton = <span onClick={() => this.handleViewWhoLike("dislike")} style={{padding: "0 0", textAlign: "center", fontSize: "12px"}}>
                        <IconButton style={{verticalAlign:"sub",width:"25px",height:"normal", padding: "0"}} ><ActionThumbDown color="#FB7923" viewBox="0 -16 40 40"  ></ActionThumbDown></IconButton>
                         <span style={{display:"inline-block",verticalAlign:"baseline",color: "#FB7923",marginLeft:"-5px",marginRight:"10px"}} >{totalDislike}</span>
                    </span>;
                  
            } else {
                totalDislikeDiv = <div style={{display: "inline-block"}} >{dot}<span onClick={() => this.handleChange("dislike")} style={{display: "inline-block",color: "#aaa", fontSize: "12px"}}>{pharse('Dislike')}</span></div>;
                disLikeButton = <span onClick={() => this.handleViewWhoLike("dislike")} style={{padding: "0 0", textAlign: "left", fontSize: "12px"}}>
                    <IconButton style={{verticalAlign:"sub",width:"25px",height:"normal", padding: "0"}} ><ActionThumbDown color="#ccc" viewBox="0 -16 40 40" ></ActionThumbDown></IconButton>
                        <span style={{display:"inline-block",verticalAlign:"baseline",marginLeft:"-5px"}} >{totalDislike}</span>
                </span>;
            }
        } else {
            totalDislikeDiv = <div style={{display: "inline-block"}} ></div>;
            disLikeButton = <div style={{display: "inline-block"}} ></div>;

        }
        

        
        
        return  <div style={{display: "inline-block", position: "relative"}}>
                        {dot}{totalLikeDiv}{totalDislikeDiv}{dot}{likeButton}{disLikeButton}
                    </div>
        }
}
