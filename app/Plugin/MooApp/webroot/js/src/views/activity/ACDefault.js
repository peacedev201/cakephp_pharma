import React from 'react';
import {Card, CardActions, CardHeader, CardMedia, CardTitle, CardText} from 'material-ui/Card';
import _ from 'lodash';
import {List, ListItem} from 'material-ui/List';
import IconButton from 'material-ui/IconButton';
import ActionMore from 'material-ui/svg-icons/navigation/expand-more';

import {GridList, GridTile} from 'material-ui/GridList';
import Avatar from 'material-ui/Avatar';


import {ACMedia,ACMediaPhone} from './ACMedia'


export  class ACDefault extends React.Component {
    constructor(props) {
        super(props);
        this.handleClick = this.handleClick.bind(this);
    }

    handleClick() {
        if (_.has(this.props, 'objects.url')) {
            window.location.href = this.props.objects.url;
        }
    }

    render() {
        var media, title, desc,text;
        text = media = title = desc = <div></div>;
        if (_.has(this.props, "objects.title")) {
            title = <CardText style={{padding:"10px",color:"#247BBA",fontSize:'16px',fontWeight:"bold",lineHeight:"20px"}} dangerouslySetInnerHTML={{__html: _.unescape(this.props.objects.title)}}></CardText>;
        }
        if (_.has(this.props, "objects.description")) {
            desc = <CardText className="browseDes" style={{padding:"0 10px 10px ",fontSize:"15px",lineHeight:"1.5",wordBreak:"break-word"}} dangerouslySetInnerHTML={{__html: _.get(this.props,'objects.description','')}}  ></CardText>;
        }
        if (_.has(this.props, "objects.images.850")) {
            if (_.get(this.props, "action") != "FeedAds" ) {
                media = <CardMedia>
                            <div style={{backgroundImage:"url('"+_.get(this.props,"objects.images.850")+"')",backgroundRepeat:"no-repeat",paddingBottom:"56.25%",display:"block",backgroundSize:"cover",backgroundPosition:"center center"}}></div>
                        </CardMedia>;
            }
            else {
                media = <img style={{width:"100%",height:"auto"}} src={_.get(this.props,"objects.images.850")} />;
            }
        }
        if (_.has(this.props, "objects.contentHtml") && !_.isEmpty(this.props.objects.contentHtml) ) {
                text = <CardText style={{margin:"0 0 10px 0",padding:"0 0px"}} dangerouslySetInnerHTML={{__html: _.get(this.props, 'objects.contentHtml','')}}></CardText>;
        }
        if (_.has(this.props, "isShare") && _.get(this.props, "isShare") == true ) {
            return <div onClick={this.handleClick}>
                {media}
                {text}
                {title}
                {desc}
            </div>;
        }
        else {
            return <div style={{padding:"8px"}} >{text}<Card onClick={this.handleClick}>
                {media}
                {title}
                {desc}
                </Card></div>;
            
        }
    }
//    render() {
//
//        var text = <div></div>;
//        var media  = <div></div>;
//        if(_.has(this.props,'objects.contentHtml')){
//            if(!_.isEmpty(this.props.objects.contentHtml)){
//                //text = <CardText>{this.props.objects.contentHtml}</CardText>;
//                text = <CardText style={{margin:"0 0 10px 0",padding:"0 0px"}} dangerouslySetInnerHTML={{__html: _.get(this.props, 'objects.contentHtml','')}}></CardText>;
//            }
//        }
//        if(_.has(this.props,'objects.images')){
//            if(_.isArray(this.props.objects.images)){
//                media = <CardMedia><img src={_.get(this.props,'objects.images[0].850')}/></CardMedia>
//            }
//        }
//        return <div>{text}{media}</div>;
//    }
}

export  class ACDefaultMedia extends React.Component {
    render() {
        //if(_.get(this.props,"isPhone",false)==true){
            return <ACMediaPhone {...this.props}/>;
//        }else{
//            var props={};
//            props['key'] = _.get(this.props,'id',0);
//            props['primaryText']= _.get(this.props,'objects.title','');
//            props['secondaryText']=_.get(this.props,'objects.description','');
//            props['url'] = _.get(this.props,"objects.url",'');
//            if(_.has(this.props,"objects.images.850")){
//                props['image'] = _.get(this.props,"objects.images.850");
//            }
//
//            return <ACMedia {...props} />;
//        }
    }
}

