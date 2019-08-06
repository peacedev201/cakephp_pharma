'use strict';


import React from 'react';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import ActivityItem from '../activity/Item.js';
import {CommentItem} from '../utility/CommentItem.js';
import {isPhone} from '../../utility/MobileDetector';
import AppAction from '../../utility/AppAction';
import CommentActions from '../../data/actions/CommentActions';
import {FirstLoading} from "../utility/FirstLoading";
import {pharse} from '../../utility/mooApp';
import _ from 'lodash';

class ActivityLayout extends React.Component {
     constructor(props) {
        super(props);
    }
    componentDidUpdate(){
        if(this.props.comments.get("isScrollToBottom")){
            window.scrollTo(0,document.body.scrollHeight);
            setTimeout(function () {
                CommentActions.stopScrollToBottom();
            }, 1);
        }
        if(this.props.activites.get('isNotFound') == true || this.props.activites.get('isDeleted') == true  ) {
            AppAction.hideComment();
        }
    }
    componentWillMount(){
        if(!_.has(window,"youtubeId")){
            if(_.has(window,"activityId")){
                this.props.fetchActivity(window.activityId);
                if(window.targetPhotoId) {
                    this.props.fetchComment(window.targetPhotoId,'Photo_Photo');
                }
                else {
                    this.props.fetchComment(window.activityId,'Activity');
                }
            }
        }


    }
    render() {
        var comment;
        comment = <div></div>; 
        if(_.has(window,"youtubeId")){
            return <iframe style={{minHeight:"200px"}} width={'100%'} height={'100%'} src={'https://www.youtube.com/embed/' + _.get(window,"youtubeId")+'?wmode=opaque'} frameBorder={0} allowFullScreen></iframe>
        }
        else {
            if(this.props.activites.get('isOpen') == true ){

                if(this.props.activites.get('isPrivateFeed') == true ) {
                    var messtmp = this.props.activites.get('records');
                    return <MuiThemeProvider >
                        <div style={{marginTop:"10px",background:"#fff",padding:"10px",textAlign:"left",fontFamily:"Roboto, sans-serif"}}>
                            <div style={{fontSize:"28px",color:"#3E3E3E"}} >{messtmp.message}</div>
                        </div>
                    </MuiThemeProvider>;
                } else if(this.props.activites.get('isNotFound') == true ) {
                    return <MuiThemeProvider >
                        <div style={{marginTop:"10px",background:"#fff",padding:"10px",textAlign:"left",fontFamily:"Roboto, sans-serif"}}>
                            <div style={{fontSize:"15px",color:"#3E3E3E"}} >{pharse('notFoundActivity')}</div>
                        </div>
                    </MuiThemeProvider>;
                } else if(this.props.activites.get('isDeleted') == true ) {
                    return <MuiThemeProvider >
                        <div style={{marginTop:"10px",background:"#fff",padding:"10px",textAlign:"left",fontFamily:"Roboto, sans-serif"}}>
                            <div style={{fontSize:"15px",color:"#3E3E3E"}} >{pharse('feedDeleted')}</div>
                        </div>
                    </MuiThemeProvider>;
                } else if(this.props.activites.get('records').count() > 0 ){
                    var items = [];
                    var isPhone = this.props.isPhone;
                    this.props.activites.get('records').forEach(function(item){
                        items.push(<ActivityItem {...item} key={item.id} isPhone={isPhone} likes={this.props.likes} reactions={this.props.reactions} singleActivity={true} />);
                    }.bind(this));

                    return <MuiThemeProvider >
                        <div>
                            <div>{items}<CommentItem {...this.props} likes={this.props.likes} reactions={this.props.reactions} /></div>
                       </div>
                    </MuiThemeProvider>;
                }
            }
            else{
                return <FirstLoading></FirstLoading>;
            }
        }

    }
}
function ActivityListView(props) {

    return <ActivityLayout {...props}  isPhone={isPhone()}/>
}

export default ActivityListView;

