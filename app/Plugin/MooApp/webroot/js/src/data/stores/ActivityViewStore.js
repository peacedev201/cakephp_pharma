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

var isOpen = false;
var isPrivateFeed = false;
var isNotFound = false;
var isDeleted = false;
function _fetch(id){

    if(_.has(mooConfig,"access_token")){
        var statusCode = 0;
        setTimeout(function(){
                fetch(mooConfig.url.base+'/api/activity/'+ id +'?access_token='+mooConfig.access_token+'&language='+mooConfig.language)
                    .then(function(response) {
                        statusCode = response.status;
                        return response.json()
                    }).then(function(json) {
                    if(_.has(json,"errorCode")){
                        if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                        if(statusCode == "400") {
                            ActivityActions.privateActivity(json);
                            return 0;
                        }
                        if(statusCode == "404") {
                            ActivityActions.add(null);
                        }
                    }
                    ActivityActions.add(json);
                }).catch(function(ex) {
                    console.log('parsing failed', ex)
                })
        },100);
    }
}
function _fetchDeleteFeed(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/activity/delete/'+ id , {
                method: 'POST',
                headers: { 'moo-access-token': mooConfig.access_token ,
                          'Content-Type': 'application/json'} 
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
class ActivityViewStore extends ReduceStore {
    constructor() {
        super(AppDispatcher);
    }
    getInitialState() {
        return Immutable.Map({isDeleted:isDeleted,isNotFound:isNotFound,isPrivateFeed:isPrivateFeed,isOpen:isOpen,records:Immutable.OrderedMap()});
    }
    reduce(state, action) {
        var records = state.get('records');
        function _addRecords(data){
            records = records.set(data.id,data);
            //if(data.type == 'share') {
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
                    objectType: _.get(data,'objects.type',''),
                    isPluginActive: _.get(data,'reaction.isPluginActive', 0),
                    countAll: _.get(data,'reaction.countAll', 0),
                    isViewerReactionType:_.get(data,'reaction.currentType', -1),
                    isViewerReactionLabel:_.get(data,'reaction.currentTypeLabel',''),
                    isViewerLiked: _.get(data,'reaction.isLike', 0),
                    typeList: _.get(data,'reaction.typeList', {})
                });
            }
        }
        switch (action.type) {
            case ActivityActionTypes.FETCH_DELETE_FEED:
                _fetchDeleteFeed(action.id);
                records = records.delete(action.id);
                isDeleted = true;
                state = state.set('isDeleted',isDeleted);
                return state;
            case ActivityActionTypes.FETCH_ACTIVITY:
                _fetch(action.id);
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.ADD_ACTIVITY:
                if (action.data != null) {
                    _addRecords(action.data);
                }
                else {
                    isNotFound = true;
                    state = state.set('isNotFound',isNotFound);
                }
                state = state.set('isOpen', true);
                state = state.set('records',records);
                return state;
            case ActivityActionTypes.FETCH_PRIVATE_ACTIVITY:
                isPrivateFeed = true;
                state = state.set('isPrivateFeed',isPrivateFeed);
                state = state.set('isOpen', true);
                state = state.set('records',action.data);
                return state;
            default:
                return state;
        }
    }
}

export default new ActivityViewStore();