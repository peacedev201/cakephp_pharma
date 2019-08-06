'use strict';

import Immutable from 'immutable';
import {ReduceStore} from 'flux/utils';

import CommentActionTypes from '../actions/CommentActionTypes';
import CommentActions from '../actions/CommentActions';
import LikeStore from './LikeStore';
import ReactionStore from './ReactionStore';
import AppAction from '../../utility/AppAction';
import AppDispatcher from '../AppDispatcher';
import _ from 'lodash';
const CommentRecord = Immutable.Record({
    id: 0,
    type:"",
    message:'',
    image:'',
    created:''
});
var currentId = 0;
var currentObject = '';
var isScrollToBottom = false;
var cPage = 1;
function _fetchComments(id,object,page = 1) {
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/'+ object +'/comment/'+ id + '?page=' + page+'&language='+mooConfig.language, {
                method: 'GET',
                headers: {'moo-access-token': mooConfig.access_token,
                    'Content-Type': 'application/json'},

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
                CommentActions.add(json);
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchDeleteComment(object) {
    if (_.has(mooConfig, "access_token")) {
        var url,body ;
        if(object.commentType == 'core_activity_comment') {
            url = mooConfig.url.base + '/api/activity/' + object.targetId + '/comment/delete';
            body = JSON.stringify({comment_id:object.id});
        }else {
            url = mooConfig.url.base + '/api/' + object.commentType + '/comment/delete';
            body = JSON.stringify({comment_id:object.id,item_id:object.targetId});
        }
        setTimeout(function () {
            fetch(url , {
                method: 'POST',
                headers: { 'moo-access-token': mooConfig.access_token ,
                          'Content-Type': 'application/json'} ,
                body : body
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
class CommentStore extends ReduceStore {
    constructor() {
        super(AppDispatcher);
    }
    getInitialState() {
        return Immutable.Map({comments:Immutable.OrderedMap(),isScrollToBottom:isScrollToBottom});
    }
    reduce(state, action) {
        var comments = state.get('comments');
        function _addRecords(data){
            comments = comments.set(data.id,data);
            LikeStore.set({
                    id: _.get(data,'likeObject.id',0),
                    type:_.get(data,'likeObject.type',''),
                    like:_.get(data,'likeCount',0),
                    dislike:_.get(data,'dislikeCount',0),
                    isViewerLiked:_.get(data,'likeObject.isViewerLiked',false),
                    isViewerDisliked:_.get(data,'likeObject.isViewerDisliked',false),
            });
            ReactionStore.set({
                id: _.get(data,'likeObject.id',0),
                objectType:_.get(data,'likeObject.type',''),
                isPluginActive: _.get(data,'reaction.isPluginActive', 0),
                countAll: _.get(data,'reaction.countAll', 0),
                isViewerReactionType:_.get(data,'reaction.currentType', -1),
                isViewerReactionLabel:_.get(data,'reaction.currentTypeLabel',''),
                isViewerLiked: _.get(data,'reaction.isLike', 0),
                typeList: _.get(data,'reaction.typeList', {})
            });
        }
        switch (action.type) {
            case CommentActionTypes.FETCH_DELETE_COMMENT:
                _fetchDeleteComment(action.object);
                comments = comments.delete(action.object.id);
                state = state.set('comments',comments);
                return state;
            case CommentActionTypes.FETCH_COMMENT:
                _fetchComments(action.id,action.object,1);
		currentId = action.id;
		currentObject = action.object;
                isScrollToBottom = false;
                state = state.set('isScrollToBottom',isScrollToBottom);
                return state;
            case CommentActionTypes.FETCH_NEXT_COMMENT:
                _fetchComments(currentId,currentObject,cPage + 1);
                isScrollToBottom = false;
                state = state.set('isScrollToBottom',isScrollToBottom);
                return state;
            case CommentActionTypes.ADD_COMMENT:
                var i = 0; 
                action.data = action.data.sort((a, b) => {
                        var aId = new Date(a.published.replace(/-/g, "/"));
                        var bId = new Date(b.published.replace(/-/g, "/"));
                        if (aId > bId) { return 1; }
                        if (aId < bId) { return -1; }
                        if (aId === bId) { return 0; }
                    });
                while (i < action.data.length) {
                    _addRecords(action.data[i]);
                    i++;
                }
               comments = comments.sort((a, b) => {
                        var aId = new Date(a.published.replace(/-/g, "/"));
                        var bId = new Date(b.published.replace(/-/g, "/"));
                        if (aId > bId) { return 1; }
                        if (aId < bId) { return -1; }
                        if (aId === bId) { return 0; }
                    });
                state = state.set('comments', comments);
                isScrollToBottom = true;
                state = state.set('isScrollToBottom',isScrollToBottom);
                return state;
            case CommentActionTypes.REFESH_AND_SCROLL_TO_BOTTOM:
//                isScrollToBottom = true;
//                state = state.set('isScrollToBottom',isScrollToBottom);
                _fetchComments(currentId,currentObject);
                return state;
            case CommentActionTypes.STOP_SCROLL_TO_BOTTOM:
                isScrollToBottom = false;
                state = state.set('isScrollToBottom',isScrollToBottom);
                return state;
            default:
                return state;
        }
    }
    set(data){
        var id,target_id,type,message,image,created,comments;
        id = _.get(data,'id',0);
        target_id = _.get(data,'target_id',0);
        type = _.get(data,'type','');
        message = _.get(data,'message','');
        image = _.get(data,'image','');
        created = _.get(data,'created','');
        comments = this._state.get(target_id+type,Immutable.Map()).set(id,new CommentRecord({id,type,message,image,created}));
        this._state = this._state.set( target_id+type,comments);
    }
}

export default new CommentStore();