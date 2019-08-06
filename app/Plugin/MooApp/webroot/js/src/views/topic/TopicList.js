import React from 'react';
import _ from 'lodash';
import {Card, CardActions, CardHeader, CardMedia, CardTitle, CardText} from 'material-ui/Card';
import {GridList, GridTile} from 'material-ui/GridList';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import getMuiTheme from 'material-ui/styles/getMuiTheme';
import {List, ListItem} from 'material-ui/List';
import Avatar from 'material-ui/Avatar';
import {pharse} from '../../utility/mooApp';
import {initLoadMoreBehavior} from '../../utility/mooApp';
import {LoadMore} from '../utility/LoadMoreContent';
import RaisedButton from 'material-ui/RaisedButton';

import FloatingActionButton from 'material-ui/FloatingActionButton';
import ContentAdd from 'material-ui/svg-icons/content/add';

import IconButton from 'material-ui/IconButton';

import {ReactionLibrary} from "../utility/Reaction";

const iconCreate = getMuiTheme({
    svgIcon: {
        color: "#000"
    }
});
export class TopicList extends React.Component {
    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
        this.handleCreateTopic = this.handleCreateTopic.bind(this);
    }
    handleClick(url) {
            window.location.href = url;
    }
    handleCreateTopic() {
         if(this.props.groupId) {
            var url = mooConfig.url.base + '/topics/create/group_id:' + this.props.groupId;
            window.location.href = url;
        }
        else {
           var url = mooConfig.url.base + '/topics/create';
            window.location.href = url;
        }
    }

    render() {
        var content,block,contentTopic,topicList,title,description,media,tmp,loadmore,isFetching,topicArray,createTopic,infoContent,by,date;
        by = date = infoContent = topicArray = loadmore = tmp = media = description = title = topicList = content = <div></div>;
        var topicArray = tmp  = [];
        contentTopic = this.props.topics.get('topics');
            if(this.props.canCreate == true){
                createTopic =  <FloatingActionButton iconStyle={{fill: '#000'}} backgroundColor="#fff" onClick={() => this.handleCreateTopic()} style={{position:"fixed",bottom:"80px",zIndex:"100",right:"20px"}}>
                    <ContentAdd></ContentAdd>
                              </FloatingActionButton>
            }
            if(contentTopic.count() > 0) {
                block = contentTopic.forEach(function(obj) {
                    if (_.has(obj, "title")) {
                            title = <CardText style={{padding:"10px 10px 0",color:"#247BBA",fontSize:'16px',fontWeight:"bold",lineHeight:"20px"}} dangerouslySetInnerHTML={{__html: _.unescape(obj.title)}}></CardText>;
                    }
                    if (_.has(obj, "commentCount")) {
                        var totalComment = parseInt(obj.commentCount);
                        var totalLike = parseInt(obj.likeCount);
                        var totalDislike = parseInt(obj.dislikeCount);
                        var shareTotal = parseInt(obj.shareCount);
                        var strTotalLike = (totalLike > 1) ? pharse('likes').replace('%s', totalLike) : pharse('like').replace('%s', totalLike);
                        var strTotalDislike = (totalDislike > 1) ? pharse('dislikes').replace('%s', totalDislike) : pharse('dislike').replace('%s', totalDislike);
                        var strTotalComment = (totalComment > 1) ? pharse('comments').replace('%s', totalComment) : pharse('comment').replace('%s', totalComment);
                        var strTotalShare = (shareTotal > 1) ? pharse('shares').replace('%s', shareTotal) : pharse('share').replace('%s', shareTotal);
                        var reaction, reactionView;
                        reaction = ReactionLibrary.getDataRequest(_.get(obj, 'reaction', false));
                        reactionView = ReactionLibrary.BrowserReactionReview(reaction);
                        
                        if(reaction != false){
                            if(parseInt(reaction.countAll) == 0){
                                infoContent = <CardText style={{padding:"0 10px ",fontSize:"13px",lineHeight:"1.5",color:"#999"}} >{strTotalComment} . {strTotalLike} . {strTotalShare}</CardText>;
                            }else{
                                reactionView = ReactionLibrary.BrowserReactionReview(reaction);
                                infoContent = <CardText style={{padding:"0 10px ",fontSize:"13px",lineHeight:"1.5",color:"#999"}} >{strTotalComment} . {reactionView} . {strTotalShare}</CardText>;
                            }
                        }else{
                        infoContent = <CardText style={{padding:"0 10px ",fontSize:"13px",lineHeight:"1.5",color:"#999"}} >{strTotalComment} . {strTotalLike} . {strTotalDislike} . {strTotalShare}</CardText>;
                        }
                    }
                    if (_.has(obj, "body")) {
                        description = <CardText className="browseDes" style={{padding:"10px 10px 10px ",fontSize:"15px",lineHeight:"1.5"}} dangerouslySetInnerHTML={{__html: _.unescape(obj.body)}} ></CardText>;
                    }
                    if (_.has(obj, "publishedTranslated")) {
                        by = <div onClick={() => this.handleClick(_.get(obj,'userUrl',''))} style={{margin:"0 5px",display:"inline-block",color:"#247BBA",fontSize:'13px',fontWeight:"600"}} >{obj.userName}</div>;
                        date = <div style={{padding:"10px 10px",fontSize:"13px",color:"#999"}}>{pharse('by')} {by} {pharse('on')} {obj.publishedTranslated}</div>;
                    }
                    if (_.has(obj, "thumbnail.850")) {  
                        media = <CardMedia>
                            <div style={{backgroundImage:"url('"+_.get(obj,"thumbnail.850")+"')",backgroundRepeat:"no-repeat",paddingBottom:"56.25%",display:"block",backgroundSize:"cover",backgroundPosition:"center center"}}></div>
                        </CardMedia>;
                    }
                    topicArray.push(<Card onClick={() => this.handleClick(_.get(obj,'url',''))} id={"blog-"+obj.id} style={{marginTop:"10px"}} key={obj.id}>{media}{title}{date}{description}{infoContent}</Card>);

                },this);
                topicList = <div>{createTopic}{topicArray}</div>;
                isFetching = this.props.topics.get('isFetching') ? true : false ;  
                loadmore = <LoadMore isFetching={isFetching} ></LoadMore>;
            }
            else {
                topicList = <div>{createTopic}<div style={{background:"#fff",padding:"10px",textAlign:"center",fontFamily:"Roboto, sans-serif"}} >{pharse('notFound')}</div></div>;
            }
        
        
        
        return <MuiThemeProvider ><div>{topicList}{loadmore}</div></MuiThemeProvider>;

    }
}
