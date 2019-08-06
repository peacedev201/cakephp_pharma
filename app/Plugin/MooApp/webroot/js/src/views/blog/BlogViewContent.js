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
import RaisedButton from 'material-ui/RaisedButton';
import {FirstLoading} from "../utility/FirstLoading";

import NavigationMoreVert from 'material-ui/svg-icons/navigation/more-vert';
import MenuItem from 'material-ui/MenuItem';
import IconMenu from 'material-ui/IconMenu';
import IconButton from 'material-ui/IconButton';

import {isIOS} from "../../utility/MobileDetector";
import AppAction from '../../utility/AppAction';

class ViewBlog extends React.Component {
    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
        this.handleEditDetail = this.handleEditDetail.bind(this);
    }
    componentWillMount(){
        this.props.viewBlog(window.blogId);
        this.props.fetchComment(window.blogId,'Blog_Blog');
    }
    componentDidUpdate(){
        if(this.props.comments.get("isScrollToBottom")){
            window.scrollTo(0,document.body.scrollHeight);
            setTimeout(function () {
                CommentActions.stopScrollToBottom();
            }, 1);
        }
    }
    handleEditDetail() {
        AppAction.hideComment();
        var url = mooConfig.url.full + mooConfig.url.base + '/blogs/create/' + window.blogId;
        AppAction.openEditNotNewTab({"link":url});
    }
    handleClick(url) {
            window.location.href = url;
    }
    render() {
        var content,block,contentBlog,blogView,title,description,media,date,by,canEdit,category,menu,canDelete;
        canDelete = menu = category = canEdit = by = date = media = description = title = blogView = content = <div></div>;
        
        if( this.props.blogs.get('isOpen') == true  ) { 
            var blog = this.props.blogs.get('blogs'); 
            if (_.has(blog, "thumbnail.850")) {  
                media = <CardMedia>
                            <div style={{backgroundImage:"url('"+_.get(blog,"thumbnail.850")+"')",backgroundRepeat:"no-repeat",paddingBottom:"56.25%",display:"block",backgroundSize:"cover",backgroundPosition:"center center"}}></div>
                        </CardMedia>;
            }
            if (_.has(window, "canEdit")) { 
                canEdit = <MenuItem onClick={() => this.handleEditDetail()} primaryText={pharse("edit")} />;
            } 
            if (_.has(window, "canDelete")) { 
                canDelete = <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/blogs/delete/' + window.blogId )} primaryText={pharse("delete")} />;
            } 
            menu = <IconMenu
                    style={{display:"block",position:"absolute",right:"5px",top:"18px"}}
                    iconButtonElement={<IconButton style={{padding:"0px",height:"auto",width:"100%",display:"block"}}  ><NavigationMoreVert ></NavigationMoreVert></IconButton>}
                    anchorOrigin={{horizontal: 'right', vertical: 'top'}}
                    targetOrigin={{horizontal: 'right', vertical: 'top'}}
                >
                    {canEdit}
                    {canDelete}
                    <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/reports/ajax_create/Blog_Blog/' + window.blogId )} primaryText={pharse("report")} />
            </IconMenu>;
            if (_.has(blog, "title")) {
                 title = <div style={{position:"relative"}}>
                    <div style={{padding:"20px 30px 10px 10px",color:"#000",fontSize:'16px',fontWeight:"bold",lineHeight:"20px"}} dangerouslySetInnerHTML={{__html: blog.title}}></div>
                    {menu}
                   </div>
            }
            if (_.has(window, "blogCategory")) {
                category = <div style={{display:"inline-block",margin:"5px 0 5px 5px"}} >{pharse('in')}<div style={{margin:"0 0 0 5px",display:"inline-block",fontSize:'12px',fontWeight:"600"}} >{window.blogCategory}</div></div>;
            }
            if (_.has(blog, "publishedTranslated")) {
                by = <div style={{display:"inline-block",margin:"5px"}} >{pharse('by')}<div onClick={() => this.handleClick(_.get(blog,'userUrl',''))} style={{margin:"0 5px",display:"inline-block",color:"#247BBA",fontSize:'12px'}} >{blog.userName}</div></div>;
                date = <div style={{padding:"10px 10px",fontSize:"12px",color:"#999"}}>{blog.publishedTranslated}{category}{by}</div>;
            }
            if (_.has(blog, "body")) {
                description = <div className="post-body" style={{padding:"10px 10px 10px ",fontSize:"15px",lineHeight:"1.5"}} dangerouslySetInnerHTML={{__html: blog.body}}></div>
            }
                
            blogView = <div style={{margin:"-8px"}} >
                <Card style={{boxShadow:"none"}} >{media}{title}{date}{description}</Card>
                <DetailActionItem {...blog} likes={this.props.likes} reactions={this.props.reactions} object={'Blog_Blog'} />
                <CommentItem {...this.props} likes={this.props.likes} reactions={this.props.reactions} object={'Blog_Blog'} object_id={window.blogId} />
            </div>;
        }
        else {
            content = <FirstLoading></FirstLoading>;
        }
        
        return <MuiThemeProvider ><div>{content}{blogView}</div></MuiThemeProvider>;
        
    }
}
function BlogViewContent(props) {
    return <ViewBlog {...props} />
}

export default BlogViewContent;
