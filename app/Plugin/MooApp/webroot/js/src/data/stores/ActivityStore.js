'use strict';

import Immutable from 'immutable';
import {ReduceStore} from 'flux/utils';

import ActivityActionTypes from '../actions/ActivityActionTypes';
import ActivityActions from '../actions/ActivityActions';

import AppDispatcher from '../AppDispatcher';

import LikeStore from './LikeStore';
import ReactionStore from './ReactionStore';

import AppAction from '../../utility/AppAction';

import 'whatwg-fetch';
import _ from 'lodash';

import {FilterTypes} from '../../utility/mooConstant';
var isLoading = false;
var isOpen = false;
var isFetching = false;
var currentPage = [];
currentPage[FilterTypes._EVERYONE_] = 1;
currentPage[FilterTypes._FRIEND_] = 1;
var currentFilter = FilterTypes._EVERYONE_;
var endOfPage = false;
var isOffline = false;
var isSubscriptionMode = false;
var profilePage = 1;
var feedIds = [];
var shouldCheck = false;
var isFollowMode = false;
var currentFollowMode = false;
var isPrivateFeed = false;
function _checkFollowMode(){
    if(_.has(mooConfig,"access_token")){
        setTimeout(function(){
                fetch(mooConfig.url.base+'/api/setting/all'+'?access_token='+mooConfig.access_token)
                    .then(function(response) {
                        return response.json();
                    }).then(function(json) {
                    if (_.has(json, "errorCode")) {
                        if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                            return 0;
                    }
                    ActivityActions.checkFollowMode(json);
                }).catch(function(ex) {
                    //isFetching=false;
                    console.log('parsing failed', ex)
                })
        },100);
    }
}
function _fetch(page){
    if(isFetching){
        return false;
    }
    isFetching = true;
    currentPage[currentFilter]= page;
    var filter = (currentFilter == FilterTypes._EVERYONE_)?'everyone':'friends';
    //var filter = 'friends';
    if(_.has(mooConfig,"access_token")){
        var statusCode = 0;
        setTimeout(function(){
                fetch(mooConfig.url.base+'/api/activity/home'+'?access_token='+mooConfig.access_token+'&page='+page+'&filter='+filter+'&language='+mooConfig.language)
                    .then(function(response) {
                        isFetching=false;
                        statusCode = response.status;
                        return response.json()
                    }).then(function(json) {
                    if (_.has(json, "errorCode")) { 
                        // Call function update token when it's expired .
                        if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                        if(page == 1) {
                            if(statusCode == "404") {
                                ActivityActions.add(null);
                            }
                            if(statusCode == "402") {
                                window.showSubscriptionMessageHook();
                                ActivityActions.showSubscriptionMessage(json);
                                return 0;
                            }
                        }
                        else {
                            ActivityActions.stopFetch();
                            endOfPage = true;
                            return 0;
                        }
                    }
                    if(_.has(json,'setting.mainMessage')){ // return offline mode message.
                        ActivityActions.showOfflineModeActivity(json);
                    }
                    else {
                        if(page == 1) {
                            ActivityActions.getSetting();
                        }
                        ActivityActions.add(json,page);
                    }
                    // Test auto refesh
                    //setTimeout(function(){ _fetch(,) }, 100);
                }).catch(function(ex) {
                    isFetching=false;
                    console.log('parsing failed', ex)
                })
        },100);
    }
}
function _fetchUserActivity(id,page){
    if(isFetching){
        return false;
    }
    isFetching = true;
    profilePage = page
    //var filter = 'friends';
    if(_.has(mooConfig,"access_token")){
        var statusCode = 0;
        setTimeout(function(){
                fetch(mooConfig.url.base+'/api/user/'+id+'/activities'+'?access_token='+mooConfig.access_token+'&page='+page+'&language='+mooConfig.language)
                    .then(function(response) {
                        isFetching=false;
                        statusCode = response.status;
                        return response.json()
                    }).then(function(json) {
                    if (_.has(json, "errorCode")) {
                        if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                        if(page == 1) {
                            if(statusCode == "404") {
                                ActivityActions.add(null);
                            }
                        }
                        else {
                            ActivityActions.stopFetch();
                            endOfPage = true;
                            return 0;
                        }
                    }
                    ActivityActions.add(json,page);
                    // Test auto refesh
                    //setTimeout(function(){ _fetch(,) }, 100);
                }).catch(function(ex) {
                    isFetching=false;
                    console.log('parsing failed', ex)
                })
        },100);
    }
}
function _fetchEventActivity(id,page){
    if(isFetching){
        return false;
    }
    isFetching = true;
    profilePage = page
    //var filter = 'friends';
    if(_.has(mooConfig,"access_token")){
        var statusCode = 0;
        setTimeout(function(){
                fetch(mooConfig.url.base+'/api/event/activity/'+id+'?access_token='+mooConfig.access_token+'&page='+page+'&language='+mooConfig.language)
                    .then(function(response) {
                        isFetching=false;
                        statusCode = response.status;
                        return response.json()
                    }).then(function(json) {
                    if (_.has(json, "errorCode")) {
                        if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                        if(page == 1) {
                            if(statusCode == "404") {
                                ActivityActions.add(null,1);
                            }
                        }
                        else {
                            ActivityActions.stopFetch();
                            endOfPage = true;
                            return 0;
                        }
                    }
                    ActivityActions.add(json,page);
                    // Test auto refesh
                    //setTimeout(function(){ _fetch(,) }, 100);
                }).catch(function(ex) {
                    isFetching=false;
                    console.log('parsing failed', ex)
                })
        },100);
    }
}
function _fetchGroupActivity(id,page){
    if(isFetching){
        return false;
    }
    isFetching = true;
    profilePage = page
    //var filter = 'friends';
    if(_.has(mooConfig,"access_token")){
        var statusCode = 0;
        setTimeout(function(){
                fetch(mooConfig.url.base+'/api/group/activity/'+id+'?access_token='+mooConfig.access_token+'&page='+page+'&language='+mooConfig.language)
                    .then(function(response) {
                        isFetching=false;
                        statusCode = response.status;
                        return response.json()
                    }).then(function(json) {
                    if (_.has(json, "errorCode")) {
                        if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                        if(page == 1) {
                            if(statusCode == "404") {
                                ActivityActions.add(null,1);
                            }
                        }
                        else {
                            ActivityActions.stopFetch();
                            endOfPage = true;
                            return 0;
                        }
                    }
                    ActivityActions.add(json,page);
                    // Test auto refesh
                    //setTimeout(function(){ _fetch(,) }, 100);
                }).catch(function(ex) {
                    isFetching=false;
                    console.log('parsing failed', ex)
                })
        },100);
    }
}
function _fetchSingleActivity(id){
    if(isFetching){
        return false;
    }
    isFetching = true;
    //var filter = 'friends';
    if(_.has(mooConfig,"access_token")){
        var statusCode = 0;
        setTimeout(function(){
                fetch(mooConfig.url.base+'/api/activity/'+id+'?access_token='+mooConfig.access_token+'&language='+mooConfig.language)
                    .then(function(response) {
                        isFetching=false;
                        statusCode = response.status;
                        return response.json()
                    }).then(function(json) {
                    if (_.has(json, "errorCode")) {
                        if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                        if(statusCode == "400") {
                            ActivityActions.privateActivity(json);
                            return 0;
                        }
                        if(statusCode == "404") {
                            ActivityActions.addSingleActivity(null);
                            return 0;
                        }
                    }
                    ActivityActions.addSingleActivity(json);
                }).catch(function(ex) {
                    isFetching=false;
                    console.log('parsing failed', ex)
                })
        },100);
    }
}
function _checkFeedAfterRefesh() {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/activity/checkExistFeed/' , {
                method: 'POST',
                headers: { 'moo-access-token': mooConfig.access_token ,
                          'Content-Type': 'application/json'} ,
                body: JSON.stringify({params:feedIds})
            }).then(function (response) {
                return response.json()
            }).then(function (json) {
                if (_.has(json, "errorCode")) {
                    if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                    }
                    if(_.get(json, "errorCode") == 'error_request') {
                            ActivityActions.checkAndRemoveDeletedFeed(null);
                            return 0;
                    }
                    return 0;
                }
                //console.log(json);
                ActivityActions.checkAndRemoveDeletedFeed(json); 
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchDeleteFeed(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/activity/delete/'+ id , {
                method: 'POST',
                headers: { 'moo-access-token': mooConfig.access_token ,
                          'Content-Type': 'application/json'} ,
            }).then(function (response) {
                return response.json()
            }).then(function (json) {
                if (_.has(json, "errorCode")) {
                    if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                    }
                    return 0;
                }
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
class ActivityStore extends ReduceStore {
    constructor() {
        super(AppDispatcher);
    }
    getInitialState() {
        return Immutable.Map({isPrivateFeed:isPrivateFeed,isLoading:isLoading,shouldCheck:shouldCheck,isSubscriptionMode:isSubscriptionMode,isOffline:isOffline,isOpen:isOpen,isFetching:isFetching,filter:currentFilter,records:Immutable.OrderedMap()});
    }
    reduce(state, action) {
        state = state.set('isFetching',isFetching);
        var records = state.get('records');
        function _addRecords(data){
            feedIds.indexOf(data.items.id) == -1 ? feedIds.push(data.items.id): '';
            var item = records.get(data.items.id,false);
            
            if(item != false){
                if(_.get(item,'filter') == FilterTypes._EVERYONE_ && currentFilter == FilterTypes._FRIEND_){
                    data.items['filter'] = currentFilter;
                }else{
                    data.items['filter'] = item.filter;
                }
            }else{
                data.items['filter'] = currentFilter;
            }
            records = records.set(data.items.id,data.items);
            if( _.get(data, 'items.isActivityView') == true || (data.items.type == 'add' && _.get(data,'items.objects.type','') != 'Photo_Album' ) || data.items.type == 'share' || data.items.type == 'join' || (data.items.type == 'create' && _.get(data,'items.objects.type','') == 'Event_Event' ) || (data.items.type == 'create' && _.get(data,'items.objects.type','') == 'Group_Group' ) ) {
                LikeStore.set({
                    id: _.get(data,'items.id',0),
                    type:'Activity',
                    like:_.get(data,'items.likeCount',0),
                    dislike:_.get(data,'items.dislikeCount',0),
                    isViewerLiked:_.get(data,'items.isViewerLiked',false),
                    isViewerDisliked:_.get(data,'items.isViewerDisliked',false),
                });
                ReactionStore.set({
                    id: _.get(data,'items.id',0),
                    objectType:'Activity',
                    isPluginActive: _.get(data,'items.reaction.isPluginActive', 0),
                    countAll: _.get(data,'items.reaction.countAll', 0),
                    isViewerReactionType:_.get(data,'items.reaction.currentType', -1),
                    isViewerReactionLabel:_.get(data,'items.reaction.currentTypeLabel',''),
                    isViewerLiked: _.get(data,'items.reaction.isLike', 0),
                    typeList: _.get(data,'items.reaction.typeList', {})
                });
            }
            else {
                LikeStore.set({
                    id: _.get(data,'items.objects.id',0),
                    type:_.get(data,'items.objects.type',''),
                    like:_.get(data,'items.likeCount',0),
                    dislike:_.get(data,'items.dislikeCount',0),
                    isViewerLiked:_.get(data,'items.isViewerLiked',false),
                    isViewerDisliked:_.get(data,'items.isViewerDisliked',false),
                });
                ReactionStore.set({
                    id: _.get(data,'items.objects.id',0),
                    objectType:_.get(data,'items.objects.type',''),
                    isPluginActive: _.get(data,'items.reaction.isPluginActive', 0),
                    countAll: _.get(data,'items.reaction.countAll', 0),
                    isViewerReactionType:_.get(data,'items.reaction.currentType', -1),
                    isViewerReactionLabel:_.get(data,'items.reaction.currentTypeLabel',''),
                    isViewerLiked: _.get(data,'items.reaction.isLike', 0),
                    typeList: _.get(data,'items.reaction.typeList', {})
                });
            }
        }
        switch (action.type) {
            case ActivityActionTypes.FETCH_PRIVATE_ACTIVITY:
                isPrivateFeed = true;
                state = state.set('isPrivateFeed',isPrivateFeed);
                state = state.set('isOpen', true);
                state = state.set('records',action.data);
                return state;
            case ActivityActionTypes.FETCH_DELETE_FEED:
                _fetchDeleteFeed(action.id);
                records = records.delete(action.id);
                state = state.set('records',records);
                return state;

            case ActivityActionTypes.GET_SETTING:
                _checkFollowMode();
                return state;
            case ActivityActionTypes.CHECK_FOLLOW_MODE:
                if(isOffline != true && isSubscriptionMode != true) {
                    isFollowMode = action.data.setting.moosite_enable_follow;
                    if(isFollowMode != currentFollowMode) {
                        currentFollowMode = !currentFollowMode;
                        records = records.clear();
                        endOfPage = false;
                        isLoading = true;
                        state = state.set('isLoading',isLoading);
                        state = state.set('records',records);
                    }
                    isFetching = false;
                    state = state.set('isFetching',isFetching);
                    return state;
                }
            case ActivityActionTypes.CHECK_AND_REMOVE_DELETED_FEED:
                if(action.data != null) {
                    for(var i=0;i<action.data.length;i++){
                        var idcheck = action.data[i].id;
                        if(idcheck.includes("ads") == false) {
                            records = records.delete(action.data[i].id);
                        }
                    }
                }
                else {
                    records = records.clear();
                    endOfPage = false;
                    isLoading = true;
                }
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.REMOVE_ACTIVITY_BY_REFESH:
                _checkFeedAfterRefesh();
                shouldCheck = false;
                state = state.set('shouldCheck',shouldCheck);
                return state;
            case ActivityActionTypes.FETCH_ACTIVITY:
                if(action.page == 1) {
                  _checkFollowMode(); 
                    isFetching = false;
                    state = state.set('isFetching',isFetching);
                }
                _fetch(action.page);
                if(action.flag != false) {
                    shouldCheck = true;
                    state = state.set('shouldCheck',shouldCheck);
                }
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.FETCH_USER_ACTIVITY:
                _fetchUserActivity(action.id,action.page);
                shouldCheck = true;
                state = state.set('shouldCheck',shouldCheck);
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.FETCH_EVENT_ACTIVITY:
                _fetchEventActivity(action.id,action.page);
                shouldCheck = true;
                state = state.set('shouldCheck',shouldCheck);
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.FETCH_GROUP_ACTIVITY:
                _fetchGroupActivity(action.id,action.page);
                shouldCheck = true;
                state = state.set('shouldCheck',shouldCheck);
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.FETCH_NEXT_ACTIVITY:
                if(!endOfPage){
                    _fetch(currentPage[currentFilter]+1);
                }
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.FETCH_NEXT_GROUP_ACTIVITY:
                if(!endOfPage){
                    _fetchGroupActivity(action.id,profilePage+1);
                }
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.FETCH_NEXT_EVENT_ACTIVITY:
                if(!endOfPage){
                    _fetchEventActivity(action.id,profilePage+1);
                }
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.FETCH_NEXT_USER_ACTIVITY:
                if(!endOfPage){
                    _fetchUserActivity(action.id,profilePage+1);
                }
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.FETCH_SINGLE_ACTIVITY:
                _fetchSingleActivity(action.id);
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.ADD_ACTIVITY:
                if(isOffline == true || isSubscriptionMode == true) {
                    records = records.clear();
                    endOfPage = false;
                }
                if (action.data != null) {
                    if(records.size == 0){
                       // It means this is first loading so we make a trick here
                        var i=0;
                        while (i < action.data.length) {
                            _addRecords(action.data[i]);
                            i++;
                        }
                        setTimeout(function(){ ActivityActions.add(action.data); }, 100);

                    }else{
                        for(var i=0;i<action.data.length;i++){
                            _addRecords(action.data[i]);
                        }
                    }
                    if(records.size != 0){ 
                        records = records.sort((a, b) => {
                            var aId = new Date(a.modified.replace(/-/g, "/"));
                            var bId = new Date(b.modified.replace(/-/g, "/"));
                            if (aId > bId) { return -1; }
                            if (aId < bId) { return 1; }
                            if (aId === bId) { return 0; }
                        });
                    }
                }
                else {
                    isLoading = false;
                    state = state.set('isLoading',isLoading);
                }
                isOffline = false;
                isSubscriptionMode = false;
                state = state.set('isOffline',isOffline);
                state = state.set('isSubscriptionMode',isSubscriptionMode);
                state = state.set('isOpen', true);
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.STOP_FETCH:
                isFetching = false;
                state = state.set('isFetching',isFetching);
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.ADD_SINGLE_ACTIVITY:
                var records = state.get('records');
                if (action.data != null) {
                    var data = action.data;
                    records = records.set(data.id,data);
                    if( _.get(data, 'isActivityView') == true || (data.type == 'add' && _.get(data,'objects.type','') != 'Photo_Album' ) || data.type == 'share' || data.type == 'join' || (data.type == 'create' && _.get(data,'objects.type','') == 'Event_Event' ) || (data.type == 'create' && _.get(data,'objects.type','') == 'Group_Group' ) ) {
                        LikeStore.set({
                            id: _.get(data,'id',0),
                            type:'Activity',
                            like:_.get(data,'likeCount',0),
                            dislike:_.get(data,'dislikeCount',0),
                            isViewerLiked:_.get(data,'isViewerLiked',false),
                            isViewerDisliked:_.get(data,'isViewerDisliked',false),
                        });
                        ReactionStore.set({
                            id: _.get(data,'id',0),
                            objectType:'Activity',
                            isPluginActive: _.get(data,'reaction.isPluginActive', 0),
                            countAll: _.get(data,'reaction.countAll', 0),
                            isViewerReactionType:_.get(data,'reaction.currentType', -1),
                            isViewerReactionLabel:_.get(data,'reaction.currentTypeLabel',''),
                            isViewerLiked: _.get(data,'reaction.isLike', 0),
                            typeList: _.get(data,'reaction.typeList', {})
                        });
                    }
                    else {
                        LikeStore.set({
                            id: _.get(data,'objects.id',0),
                            type:_.get(data,'objects.type',''),
                            like:_.get(data,'likeCount',0),
                            dislike:_.get(data,'dislikeCount',0),
                            isViewerLiked:_.get(data,'isViewerLiked',false),
                            isViewerDisliked:_.get(data,'isViewerDisliked',false),
                        });
                        ReactionStore.set({
                            id: _.get(data,'objects.id',0),
                            objectType:_.get(data,'objects.type',''),
                            isPluginActive: _.get(data,'reaction.isPluginActive', 0),
                            countAll: _.get(data,'reaction.countAll', 0),
                            isViewerReactionType:_.get(data,'reaction.currentType', -1),
                            isViewerReactionLabel:_.get(data,'reaction.currentTypeLabel',''),
                            isViewerLiked: _.get(data,'reaction.isLike', 0),
                            typeList: _.get(data,'reaction.typeList', {})
                        });
                    }
                }
                else {
                    records = records.clear();
                }
                shouldCheck = true;
                state = state.set('shouldCheck',shouldCheck);
                state = state.set('isOpen', true);
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.SET_FILTER:
                currentFilter = action.filter;
                state = state.set('filter',currentFilter);
                endOfPage = false;
                isLoading = true;
                
                //Update select filter , remove all items and add new record.
                records = records.clear();
                //if(currentPage[currentFilter] == 1){
                    _fetch(1);
                //}
                state = state.set('isLoading',isLoading);
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.SHOW_OFFLINE_MODE_ACTIVITY:
                isOffline = true;
                var records = state.get('records');
                records = records.clear();
                records = records.set('offline',action.data);
                state = state.set('records',records);
                state = state.set('isOffline',isOffline);
                state = state.set('isOpen', true);
                return state;
            case ActivityActionTypes.SHOW_SUBSCRIPTION_MESSAGE:
                isSubscriptionMode = true;
                var records = state.get('records');
                records = records.clear();
                records = records.set('showSubMessage',action.data);
                state = state.set('records',records);
                state = state.set('isSubscriptionMode',isSubscriptionMode);
                state = state.set('isOpen', true);
                return state;
            default:
                return state;
        }
    }
}

export default new ActivityStore();