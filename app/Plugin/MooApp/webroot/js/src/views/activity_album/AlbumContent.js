import React from 'react';
import _ from 'lodash';
import {Card, CardActions, CardHeader, CardMedia, CardTitle, CardText} from 'material-ui/Card';
import NavigationClose from 'material-ui/svg-icons/navigation/close';
import IconButton from 'material-ui/IconButton';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import getMuiTheme from 'material-ui/styles/getMuiTheme';
import {List, ListItem} from 'material-ui/List';
import {pharse} from '../../utility/mooApp';
import PopupActionTypes from '../../data/actions/PopupActionTypes';
import PhotoActionTypes from '../../data/actions/PhotoActionTypes';
import {DetailActionItem} from '../utility/DetailActionItem';

import RaisedButton from 'material-ui/RaisedButton';
import RefreshIndicator from 'material-ui/RefreshIndicator';
import {FirstLoading} from "../utility/FirstLoading";
const iconNotActive = getMuiTheme({
    svgIcon: {
        color: "#fff"
    }
});
function imagesLoaded(parentNode) {
        const imgElements = parentNode.querySelectorAll('img');
        for (const img of imgElements) {
          if (!img.complete) {
            return false;
          }
        }
        return true;
    }
class AlbumList extends React.Component {
    constructor(props) {
        super(props);
        this.state = {isOpen:false};
    }
    componentWillMount(){
        
        if(_.has(window,"uid")) {
            this.props.fetchTagAlbum(window.photoId,window.uid);
        }
        else {
            this.props.fetchAlbum(window.albumId,window.photoId);
        }
        //this.props.fetchCurentPhoto(window.photoId);
    }
    //componentDidMount () {
    componentDidUpdate () {
//        var id = window.photoId;
//        if( this.props.photos.get('isFetching') == false  )  {
//            
//            const galleryElement = this.refs.gallery;//console.log(this.imagesLoaded(galleryElement));
//            if(imagesLoaded(galleryElement)) { console.log("done");
//                var elmnt = document.getElementById("photo-" + id );
//                if(elmnt) {
//                    elmnt.scrollIntoView({block: "end", behavior: "smooth"});
//                    window.scrollBy(0, -10);
//                }
//            }
//        }
    }
    handleLoadMore() {
        if(_.has(window,"uid")) {
            this.props.fetchNextTagAlbum(window.photoId,window.uid);
        }
    }
    handleViewAlbum(id) {
        if(id) {
            var url = mooConfig.url.base + '/albums/view/' + id + '?access_token='+mooConfig.access_token;
            window.location.href = url;
        }
    }
    handleImageChange () {
        var id = window.photoId;
        if( this.props.photos.get('isFetching') == false  )  {

            const galleryElement = this.refs.gallery;
            if(imagesLoaded(galleryElement)) {
                var elmnt = document.getElementById("photo-" + id );
                if(elmnt) {
                    setTimeout(function () {
                        elmnt.scrollIntoView();
                        window.scrollBy(0, -10);
                        this.setState({isOpen:true});
                    }.bind(this), 2000);
                    
                }
            }
        }
    }
    handleClick(url) {
            window.location.href = url + '?access_token='+mooConfig.access_token;
    }
    
    render() {
        var hideOverlay ,loadmore,contentAlbum , contentPhoto , content, photoArray,photoCaption,media,block,photoLike,description;
        hideOverlay = loadmore = description = photoLike = photoArray = photoCaption = media = <div></div>;
        var tmp = contentPhoto = [];
        var isLoadMore = false;
       
        if(!this.state.isOpen){
            hideOverlay = <div style={{WebkitTapHighlightColor: "rgba(0, 0, 0, 0.5)",position:"fixed",boxSizing: "border-box", zIndex: "1500",top:0,left:0,width: "100%", height: "100%", transition: "left 0ms cubic-bezier(0.23, 1, 0.32, 1) 0ms", paddingTop: 0,background:"#fff"}}>
            <RefreshIndicator style={{positon:"fixed",transform: "translate(-50%, -50%)",top:'50%',left:'50%'}} size={40} left={10} top={0} status="loading"  />
            </div>;
        }
        else {
          hideOverlay =  <div></div>;
        }
          
        if( this.props.photos.get('isFetching') == false  ) {
            contentAlbum = this.props.photos.get('albums');
            contentPhoto = this.props.photos.get('photoArray').count() > 0 ? this.props.photos.get('photoArray') : this.props.photos.get('photos') ;
            if(!_.has(window,"uid")) {    
                if(_.get(contentAlbum, 'currentType') != 'newsfeed') {
                    if (_.has(contentAlbum, "description") && _.get(contentAlbum, 'description')) {  
                        description = <CardText className="post-body" style={{fontSize:"15px",margin:"0px 0 10px",padding:"0 16px"}} dangerouslySetInnerHTML={{__html: _.get(contentAlbum, 'description','')}} ></CardText>
                    }
                    else {
                        description = <div></div>;
                    }

                    content = <Card style={{position:"relative"}}>
                        <CardHeader
                            title={<div style={{fontWeight:"bold",fontSize:"16px"}} dangerouslySetInnerHTML={{__html: _.get(contentAlbum, 'title','')}}></div>}
                            subtitle={<div dangerouslySetInnerHTML={{__html: _.get(contentAlbum, 'publishedTranslated','')}}></div>}
                            textStyle={{display:'block',overflow:'hidden'}}
                        />
                        {description}
                        <DetailActionItem {...contentAlbum} likes={this.props.likes} reactions={this.props.reactions} object={'Photo_Album'} disableComment={true} />
                    </Card>;
                }
            }
            block = contentPhoto.map(function(obj) { 
                        if (_.has(obj, "caption") && _.get(obj,"caption")) {
                            photoCaption = <CardText style={{padding:"10px 10px ",fontSize:"12px"}} >{obj.caption}</CardText>;
                        }
                        if (_.has(obj, "thumbnail.850")) {  
                            media = <CardMedia onClick={() => this.handleClick(_.get(obj,'url',''))}>
                                <img src={_.get(obj,"thumbnail.850")} onLoad={this.handleImageChange.bind(this)} onError={this.handleImageChange.bind(this)}/>
                            </CardMedia>;
                        }
                        if(!_.has(window,"uid")) {
                            if (_.has(obj, "id") && _.get(obj, "canView") == true ) {  
                                photoLike = <DetailActionItem {...obj} likes={this.props.likes} reactions={this.props.reactions} object={'Photo_Photo'} disableComment={true} />;
                            }
                        }
                        if (obj.isLoadmore == true) {
                            isLoadMore = true;
                        }
                        else {
                             isLoadMore = false;
                        }

                        tmp.push(<Card id={"photo-"+obj.id} style={{marginTop:"10px"}} key={obj.id}>{media}{photoCaption}{photoLike}</Card>);
                    },this);
            photoArray = <div className="gallery" ref="gallery">{tmp}</div>;
            if (contentAlbum.isLoadmore == true) {
                isLoadMore = true;
            }
            else {
                isLoadMore = false;
            }
            if (isLoadMore) {
                loadmore = <div onClick={() => this.handleLoadMore()} style={{margin:"10px auto",textAlign:"center"}} ><RaisedButton buttonStyle={{height:"auto"}} labelStyle={{textTransform:"uppercase"}} label={pharse('loadMore')} /></div>;
            }
            else {
                if(_.get(contentAlbum, 'currentType')  && _.get(contentAlbum, 'currentType') != 'newsfeed') {
                    loadmore = <div onClick={() => this.handleViewAlbum(_.get(contentAlbum, 'id'))} style={{margin:"10px auto",textAlign:"center"}} ><RaisedButton buttonStyle={{height:"auto"}} labelStyle={{textTransform:"uppercase"}} label={pharse('viewAlbum')} /></div>;
                }
            }
        }
        else {
            content = <FirstLoading></FirstLoading>;
        }
        
        return <MuiThemeProvider ><div>{hideOverlay}<div style={{position:"relative"}}>{content}{photoArray}{loadmore}</div></div></MuiThemeProvider>;

    }
}
function AlbumContent(props) {
    return <AlbumList {...props} />
}

export default AlbumContent;
