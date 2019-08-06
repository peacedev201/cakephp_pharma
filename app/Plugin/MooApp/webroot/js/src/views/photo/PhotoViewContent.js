import React from 'react';
import _ from 'lodash';
import {Card, CardActions, CardHeader, CardMedia, CardTitle, CardText} from 'material-ui/Card';
import {GridList, GridTile} from 'material-ui/GridList';
import {List, ListItem} from 'material-ui/List';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import {pharse} from '../../utility/mooApp';
import {CommentItem} from '../utility/CommentItem.js';
import {DetailActionItem} from '../utility/DetailActionItem';
import CommentActions from '../../data/actions/CommentActions';
import {FirstLoading} from "../utility/FirstLoading";

class ViewPhoto extends React.Component {
    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
    }
    componentWillMount(){
        this.props.viewPhoto(window.photoId);
        if(window.canView == 1) {
            this.props.fetchComment(window.photoId,'Photo_Photo');
        }
    }
    componentDidUpdate(){
        if(this.props.comments.get("isScrollToBottom")){
            window.scrollTo(0,document.body.scrollHeight);
            setTimeout(function () {
                CommentActions.stopScrollToBottom();
            }, 1);
        }
    }
    handleClick(url) {
        window.location.href = url;
    }
    render() {
        var content,block,tagContent,photoView,title,description,media,date,by,url,userName,tmp;
        userName = tagContent = by = date = media = description = title = photoView = content = <div></div>;
        tmp = [];
        
        if( this.props.photos.get('isOpen') == true  ) { 
            var photo = this.props.photos.get('photos');
            if (_.has(photo, "thumbnail.850")) {  
                media = <CardMedia>
                    <img src={_.get(photo,"thumbnail.850")}/>
                </CardMedia>;
            }
            if (_.has(photo, "albumTitle")) {
                 title = <div style={{padding:"10px 10px 0 ",color:"#000",fontSize:'16px',fontWeight:"bold",lineHeight:"20px"}} dangerouslySetInnerHTML={{__html: photo.albumTitle}}></div>
            }
            if (_.has(photo, "publishedTranslated")) {
                by = <div style={{display:"inline-block",margin:"5px"}} >{pharse('by')}<div onClick={() => this.handleClick(_.get(photo,'userUrl',''))} style={{margin:"0 5px",display:"inline-block",color:"#247BBA",fontSize:'12px',fontWeight:"600"}} >{photo.userName}</div></div>;
                date = <div style={{padding:"10px 10px",fontSize:"12px",color:"#999"}}>{photo.publishedTranslated}{by}</div>;
            }
            if (_.has(photo, "caption")) {
                description = <div className="post-body" style={{padding:"10px 10px 10px ",fontSize:"15px",lineHeight:"1.5"}} dangerouslySetInnerHTML={{__html: photo.caption}}></div>
            }
            if (_.get(photo, "tagged")) {
                block = photo.tagged.map(function(obj) { 
                    
                    if (_.has(obj, "name")) {  
                        userName = obj.name;
                    }
                    tmp.push(<div onClick={() => this.handleClick(_.get(obj,'url',''))} key={obj.id} style={{display:"inline-block",margin:"0 5px",color:"#247BBA",fontSize:'16px',fontWeight:"bold"}}>{userName}</div>);
                }.bind(this));
                tagContent = <div style={{padding:"10px 10px",fontSize:"13px",lineHeight:"1.5"}}><div style={{display:"inline-block",margin:"0 5px 0 0"}} >{pharse('tagLabel')}</div>{tmp}</div>;
            }
            
            if(window.canView == 1) {
                photoView = <div style={{margin:"-8px"}} >
                    <Card style={{boxShadow:"none"}} >{title}{date}{media}{tagContent}{description}</Card>
                    <DetailActionItem {...photo} likes={this.props.likes} reactions={this.props.reactions} object={'Photo_Photo'} />
                    <CommentItem {...this.props} likes={this.props.likes} reactions={this.props.reactions} />
                </div>;
            }
            else {
                photoView = <Card style={{boxShadow:"none"}} >{media}
                        <div style={{fontSize:"16px",padding: "10px 10px",lineHeight:"22px",fontFamily:"Roboto, sans-serif"}}>{pharse('canNotView')}</div>
                    </Card>
            }
        }
        else {
            content = <FirstLoading></FirstLoading>;
        }
        
        return <MuiThemeProvider ><div>{content}{photoView}</div></MuiThemeProvider>;
        
    }
}
function PhotoViewContent(props) {
    return <ViewPhoto {...props} />
}

export default PhotoViewContent;
