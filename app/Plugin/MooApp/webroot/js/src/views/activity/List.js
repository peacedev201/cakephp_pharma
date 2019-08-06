'use strict';


import React from 'react';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import ActivityItem from './Item.js';
import {isPhone} from '../../utility/MobileDetector';
import AppAction from '../../utility/AppAction';
import {initLoadMoreBehavior} from '../../utility/mooApp';
import ACForm from './ACForm';
import {FilterTypes} from '../../utility/mooConstant';
import {LoadMore} from '../utility/LoadMoreContent';
import {pharse} from '../../utility/mooApp';
import {FirstLoading} from "../utility/FirstLoading";
//import {SpotLight} from './plugin/ACSpotLight';
import _ from 'lodash';

class ActivityLayout extends React.Component {
     constructor(props) {
        super(props);
    }
    componentWillMount(){
        this.props.fetchActivities(1,false);
        this.props.fetchMe();
        // Test autorefech
        //setInterval(function(){ this.props.fetchActivities(0); console.log("do fetch"); }.bind(this), 6000);
        initLoadMoreBehavior(function(){
            this.props.fetchNextActivities();
        }.bind(this));

        // Hacking for auto hide ios header
        var lastScrollTop = 0;
        var isSendCommandHideNavigationBar = false;
        var isSendCommandShowNavigationBar = true;
        
        // element should be replaced with the actual target element on which you have applied scroll, use window in case of no target element.
        window.addEventListener("scroll", function(){ // or window.addEventListener("scroll"....
            var st = window.pageYOffset || document.documentElement.scrollTop; // Credits: "https://github.com/qeremy/so/blob/master/so.dom.js#L426"
            if ((window.innerHeight + window.pageYOffset ) >= document.body.offsetHeight  ) {
                return false;
            }
            //var body = document.getElementsByTagName("body")[0];
            //if(body.style.overflow != "hidden") { 
                if (st > lastScrollTop && st > 0  ){
                    AppAction.hideNavigationBar()
                }
                if(st < lastScrollTop){
                    AppAction.showNavigationBar()
                }
                lastScrollTop = st;
            //}
        }, false);

    }
    componentDidUpdate(prevProps, prevState) {
        setTimeout(function () {
            if(prevProps.activites.get('isOffline') != true && prevProps.activites.get('isSubscriptionMode') != true  ) {
                if(prevProps.activites.get('shouldCheck') == true) {
                    prevProps.removeActivityByRefesh();
                    prevProps.fetchMe();
                    var userMe = prevProps.users.get('userMe');
                    if(userMe.email_validation != window.email_validation ) {
                        window.location.reload();
                    }
                    else if((typeof userMe.check_reload === 'undefined' && typeof window.check_reload !== 'undefined') || (typeof userMe.check_reload !== 'undefined' && !userMe.check_reload && typeof window.check_reload === 'undefined') || (typeof window.check_reload !== 'undefined'  && window.check_reload != userMe.check_reload)){
                            window.location.reload();
                    }
                }
            }
        }, 1);
    }
    render() {
        {/*
        var spotLight = <div></div>;
        if(_.has(window,"spotlight_user")) {
            spotLight = <SpotLight {...this.props} />;
        }*/}
        if(this.props.activites.get('isOpen') == true ){
            if(this.props.activites.get('isSubscriptionMode') == true ) {
                var messtmp = this.props.activites.get('records');
                var message = messtmp.get('showSubMessage');
                return <MuiThemeProvider >
                    <div style={{marginTop:"10px",background:"#fff",padding:"10px",textAlign:"left",fontFamily:"Roboto, sans-serif"}}>
                        <div style={{fontSize:"28px",color:"#3E3E3E"}} >{message.message}</div>
                    </div>
                </MuiThemeProvider>;
            } else if(this.props.activites.get('isOffline') == true ) {
                var messtmp = this.props.activites.get('records');
                var message = messtmp.get('offline');
                return <MuiThemeProvider >
                    <div style={{marginTop:"10px",background:"#fff",padding:"10px",textAlign:"left",fontFamily:"Roboto, sans-serif"}}>
                        <div style={{fontSize:"28px",color:"#3E3E3E"}} >{message.setting.mainMessage}</div>
                        <div style={{marginTop:"10px"}} >{message.setting.offlineMessage}</div>
                    </div>
                </MuiThemeProvider>;
            } else {
                if(this.props.activites.get('records').count() > 0 ){ 
                    var items = [];
                    var isPhone = this.props.isPhone;
                    var isFetching = this.props.activites.get('isFetching') ? true : false ;  
                    var process = <LoadMore isFetching={isFetching} ></LoadMore>;

                    this.props.activites.get('records').forEach(function(item){
                        if(FilterTypes._FRIEND_ == this.props.activites.get('filter') && item.filter == FilterTypes._EVERYONE_){
                            return 0;
                        }
                        items.push(<ActivityItem {...item} key={item.id} isPhone={isPhone} likes={this.props.likes} reactions={this.props.reactions} singleComment={true} />);
                        items.push(<div key={'a'+item.id} style={{height:'7px'}}/>);
                    }.bind(this));
                    
                    return <MuiThemeProvider >
                        <div>
                        <div style={{margin:"-6px",paddingBottom:"0"}} ><ACForm {...this.props} target_id={"0"} type={"User"} /><div style={{height:'7px'}}/>{items}{process}</div>
                       </div>
                    </MuiThemeProvider>;
                }else {
                    
                    if(this.props.activites.get('isLoading') == true ) {
                        return <FirstLoading></FirstLoading>;
                    } 
                    else {
                        return <MuiThemeProvider >
                            <div style={{margin:"-6px"}} >
                                <ACForm {...this.props} target_id={"0"} type={"User"} />
                                <div style={{marginTop:"10px",background:"#fff",padding:"10px",textAlign:"center",fontFamily:"Roboto, sans-serif"}} >{pharse('notFound')}</div>
                            </div>
                            </MuiThemeProvider>;
                    }
                }  
            }
        }else{
            return <FirstLoading></FirstLoading>;
        }

    }
}
function ActivityListView(props) {
    return <ActivityLayout {...props} isPhone={isPhone()}/>
}

export default ActivityListView;

