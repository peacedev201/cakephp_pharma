import React from 'react';
import _ from 'lodash';
import {Card, CardActions, CardHeader, CardMedia, CardTitle, CardText} from 'material-ui/Card';
import {GridList, GridTile} from 'material-ui/GridList';
import {List, ListItem} from 'material-ui/List';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import {pharse} from '../../utility/mooApp';
import {CommentItem} from '../utility/CommentItem.js';
import {DetailActionItem} from '../utility/DetailActionItem';
import RaisedButton from 'material-ui/RaisedButton';
import {isIOS} from "../../utility/MobileDetector";
import AppAction from '../../utility/AppAction';
import CommentActions from '../../data/actions/CommentActions';
import {FirstLoading} from "../utility/FirstLoading";

import PhotoActions from '../../data/actions/PhotoActions';
import FloatingActionButton from 'material-ui/FloatingActionButton';
import ContentAdd from 'material-ui/svg-icons/content/add';

import NavigationMoreVert from 'material-ui/svg-icons/navigation/more-vert';
import MenuItem from 'material-ui/MenuItem';
import IconMenu from 'material-ui/IconMenu';
import IconButton from 'material-ui/IconButton';

class ViewAlbum extends React.Component {
    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
        this.handleUploadPhoto = this.handleUploadPhoto.bind(this);
        this.handleEditDetail = this.handleEditDetail.bind(this);
    }
    componentWillMount(){
        this.props.viewAlbum(window.albumId);
        this.props.fetchPhotoFromAlbum(window.albumId);
        this.props.fetchComment(window.albumId,'Photo_Album');
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
    handleUploadPhoto(id) {
        AppAction.hideComment();
        if(id) {
            var url = mooConfig.url.base + '/photo/photos/ajax_upload/Photo_Album/' + id;
            window.location.href = url;
        }
    }
    handleEditDetail(url) {
        AppAction.hideComment();
        var url = mooConfig.url.full + mooConfig.url.base + '/albums/create/' + window.albumId;
        AppAction.openEditNotNewTab({"link":url});
    }
    render() {
        var content,block,contentAlbum,albumView,title,description,media,date,by,uploadPhoto,category,canEdit,canDelete,menu;
        menu = canDelete = canEdit = uploadPhoto = by = date = media = description = title = albumView = content = <div></div>;
        
        if( this.props.albums.get('isOpen') == true  ) { 
            var album = this.props.albums.get('albums');
            //if (_.has(album, "photoObject")) { 
                //var photos = this.props.photos.get('photos');
                media = <PhotoContent {...this.props} ></PhotoContent>;
            //}
            if (_.has(window, "canEdit")) { 
                canEdit = <MenuItem onClick={() => this.handleEditDetail()} primaryText={pharse("edit")} />;
            } 
            if (_.has(window, "canDelete")) { 
                canDelete = <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/albums/do_delete/' + window.albumId )} primaryText={pharse("delete")} />;
            } 
            menu = <IconMenu
                    style={{display:"block",position:"absolute",right:"5px",top:"18px"}}
                    iconButtonElement={<IconButton style={{padding:"0px",height:"auto",width:"100%",display:"block"}}  ><NavigationMoreVert ></NavigationMoreVert></IconButton>}
                    anchorOrigin={{horizontal: 'right', vertical: 'top'}}
                    targetOrigin={{horizontal: 'right', vertical: 'top'}}
                >
                    {canEdit}
                    {canDelete}
                    <MenuItem onClick={() => this.handleClick(mooConfig.url.base + '/reports/ajax_create/photo_album/' + window.albumId )} primaryText={pharse("report")} />
            </IconMenu>;
            if (_.has(album, "title")) {
                title = <div style={{position:"relative"}}>
                    <div style={{padding:"20px 30px 10px 10px",color:"#000",fontSize:'16px',fontWeight:"bold",lineHeight:"20px"}} dangerouslySetInnerHTML={{__html: album.title}}></div>
                    {menu}
                   </div>
            }
            if (_.has(window, "albumCategory")) {
                category = <div style={{display:"inline-block",margin:"5px 0 5px 5px"}} >{pharse('in')}<div style={{margin:"0 0 0 5px ",display:"inline-block",color:"#999",fontSize:'12px',fontWeight:"600"}} >{window.albumCategory}</div></div>;
            }
            if (_.has(album, "publishedTranslated")) {
                by = <div style={{display:"inline-block",margin:"5px"}} >{pharse('by')}<div onClick={() => this.handleClick(_.get(album,'userUrl',''))} style={{margin:"0 5px",display:"inline-block",color:"#247BBA",fontSize:'12px'}} >{album.userName}</div></div>;
                date = <div style={{padding:"10px 10px",fontSize:"12px",color:"#999"}}>{album.publishedTranslated}{category}{by}</div>;
            }
            if (_.has(album, "description")) {
                description = <div className="post-body" style={{padding:"10px 10px 10px ",fontSize:"15px",lineHeight:"1.5"}} dangerouslySetInnerHTML={{__html: album.description}}></div>
            }
            if (_.get(album, "canUploadPhoto") == true) {
                    uploadPhoto = <FloatingActionButton iconStyle={{fill: '#000'}}  backgroundColor="#fff" onClick={() => this.handleUploadPhoto(album.id)} style={{position:"fixed",bottom:"80px",zIndex:"100",right:"20px"}}>
                    <ContentAdd color="#000" ></ContentAdd>
                              </FloatingActionButton>;
            }
                
            albumView = <div style={{margin:"-8px"}} >
                {uploadPhoto}
                <Card style={{boxShadow:"none"}} >{title}{date}{media}{description}</Card>
                <DetailActionItem {...album} likes={this.props.likes} reactions={this.props.reactions} object={'Photo_Album'} />
                <CommentItem {...this.props} likes={this.props.likes} reactions={this.props.reactions} />
            </div>;
        }
        else {
            content = <FirstLoading></FirstLoading>;
        }
        
        return <MuiThemeProvider ><div>{content}{albumView}</div></MuiThemeProvider>;
        
    }
}

export class PhotoContent extends React.Component {
   constructor(props) {
        super(props);

        // This binding is necessary to make `this` work in the callback
        this.handleClick = this.handleClick.bind(this);
        this.handleLoadMore = this.handleLoadMore.bind(this);
    }
    handleClick(url) {
            window.location.href = url + '?access_token='+mooConfig.access_token;
    }
    handleLoadMore() {
        var id = window.albumId;
        PhotoActions.fetchNextPhoto(id);
    }
    render() {
        
        var media, title, userCount,url,photoContent,loadmore;
        loadmore = photoContent = media = title = userCount = <div></div>;
        var tmp = [];
        var photos = this.props.photos.get("photos");
        var isLoadMore = false;
        var block = this.props.photos.get("photos").map(function(obj) {
            if (_.has(obj, "thumbnail.150_square")) {  
                media = <div style={{backgroundImage:"url(" + _.get(obj,"thumbnail.150_square") +" )",backgroundRepeat:"no-repeat",backgroundSize:"cover",backgroundPosition:"center center",padding:"0 0 100%"}} ></div>
            }
            if (obj.isLoadmore == true) {
                isLoadMore = true;
            }
            else {
                 isLoadMore = false;
            }
            tmp.push(<div style={{width:"50%",padding:"3px",boxSizing:"border-box"}} key={obj.id} onClick={() => this.handleClick(_.get(obj,'url',''))} >{media}</div>);
        }, this);
        photoContent = <div style={{overflow:"hidden",textAlign:"center",display:"flex",flexWrap:"wrap"}} >{tmp}</div>;
        
        if (isLoadMore) {
            loadmore = <div onClick={() => this.handleLoadMore()} style={{margin:"10px auto",textAlign:"center"}} ><RaisedButton buttonStyle={{height:"auto"}} labelStyle={{textTransform:"uppercase"}} label={pharse('loadMore')} /></div>;
        }
  
        return <div> {photoContent}{loadmore}</div>;
        
        

    }
}




function AlbumViewContent(props) {
    return <ViewAlbum {...props} />
}

export default AlbumViewContent;
