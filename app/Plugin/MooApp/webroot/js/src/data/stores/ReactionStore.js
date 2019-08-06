'use strict';

import Immutable from 'immutable';
import {ReduceStore} from 'flux/utils';

import ReactionActionTypes from '../actions/ReactionActionTypes';
import ReactionActions from '../actions/ReactionActions';
import AppAction from '../../utility/AppAction';
import AppDispatcher from '../AppDispatcher';
import _ from 'lodash';
import 'whatwg-fetch';
const ReactionRecord = Immutable.Record({
    id: 0,
    objectType: "",
    isPluginActive: 0,
    countAll: 0,
    isViewerReactionType: -1,
    isViewerReactionLabel: '',
    isViewerLiked: 0,
    typeList: {}
});

function _fetchReaction(id,objectType,reactionType){
    if(_.has(mooConfig,"access_token")){
        setTimeout(function(){
            //console.log('id ne ku', id);
            //LikeActions.update(data);
            fetch(mooConfig.url.base+'/api/'+ objectType +'/reaction/'+id+'/'+reactionType, {
                                method: 'POST',
                                headers: { 'moo-access-token': mooConfig.access_token ,
                                           'Content-Type': 'application/json'} ,
                                body: JSON.stringify({id:id, reactionType: reactionType})
                            })
                           .then(function (response) {
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
                
        },100);
    }
}
function _fetchUnReaction(id,objectType,reactionType){
    if(_.has(mooConfig,"access_token")){
        setTimeout(function(){
                fetch(mooConfig.url.base+'/api/'+ objectType +'/reaction/delete/'+id+'/'+reactionType, {
                            method: 'POST',
                            headers: { 'moo-access-token': mooConfig.access_token ,
                                       'Content-Type': 'application/json'} ,
                            body: JSON.stringify({id:id, reactionType: reactionType})
                        })
                       .then(function (response) {
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
               
        },100);
    }
}

class ReactionStore extends ReduceStore {
    constructor() {
        super(AppDispatcher);
    }
    getInitialState() {
        return Immutable.Map({records:Immutable.OrderedMap()});
    }

    changeType(oldType, newType, typeList, action){
        var newTypeList = {
            like: {sysActive: typeList.like.sysActive, type: typeList.like.type, count: parseInt(typeList.like.count), reacted: typeList.like.reacted, name: typeList.like.name},
            love: {sysActive: typeList.love.sysActive, type: typeList.love.type, count: parseInt(typeList.love.count), reacted: typeList.love.reacted, name: typeList.love.name},
            haha: {sysActive: typeList.haha.sysActive, type: typeList.haha.type, count: parseInt(typeList.haha.count), reacted: typeList.haha.reacted, name: typeList.haha.name},
            wow: {sysActive: typeList.wow.sysActive, type: typeList.wow.type, count: parseInt(typeList.wow.count), reacted: typeList.wow.reacted, name: typeList.wow.name},
            sad: {sysActive: typeList.sad.sysActive, type: typeList.sad.type, count: parseInt(typeList.sad.count), reacted: typeList.sad.reacted, name: typeList.sad.name},
            angry: {sysActive: typeList.angry.sysActive, type: typeList.angry.type, count: parseInt(typeList.angry.count), reacted: typeList.angry.reacted, name: typeList.angry.name},
            cool: {sysActive: typeList.cool.sysActive, type: typeList.cool.type, count: parseInt(typeList.cool.count), reacted: typeList.cool.reacted, name: typeList.cool.name},
            confused: {sysActive: typeList.confused.sysActive, type: typeList.confused.type, count: parseInt(typeList.confused.count), reacted: typeList.confused.reacted, name: typeList.confused.name}
        };

        if(newType == 'like'){
            newTypeList.like.reacted = (action == 'like') ? 1 : 0;newTypeList.love.reacted = 0;newTypeList.haha.reacted = 0;newTypeList.wow.reacted = 0;newTypeList.sad.reacted = 0;newTypeList.angry.reacted = 0;newTypeList.cool.reacted = 0;newTypeList.confused.reacted = 0;
            newTypeList.like.count = (action == 'like') ? (newTypeList.like.count + 1) : (newTypeList.like.count - 1);
        }else if(newType == 'love'){
            newTypeList.love.reacted = (action == 'like') ? 1 : 0;newTypeList.like.reacted = 0;newTypeList.haha.reacted = 0;newTypeList.wow.reacted = 0;newTypeList.sad.reacted = 0;newTypeList.angry.reacted = 0;newTypeList.cool.reacted = 0;newTypeList.confused.reacted = 0;
            newTypeList.love.count = (action == 'like') ? (newTypeList.love.count + 1) : (newTypeList.love.count - 1);
        }else if(newType == 'haha'){
            newTypeList.haha.reacted = (action == 'like') ? 1 : 0;newTypeList.like.reacted = 0;newTypeList.love.reacted = 0;newTypeList.wow.reacted = 0;newTypeList.sad.reacted = 0;newTypeList.angry.reacted = 0;newTypeList.cool.reacted = 0;newTypeList.confused.reacted = 0;
            newTypeList.haha.count = (action == 'like') ? (newTypeList.haha.count + 1) : (newTypeList.haha.count - 1);
        }else if(newType == 'wow'){
            newTypeList.wow.reacted = (action == 'like') ? 1 : 0;newTypeList.like.reacted = 0;newTypeList.love.reacted = 0;newTypeList.haha.reacted = 0;newTypeList.sad.reacted = 0;newTypeList.angry.reacted = 0;newTypeList.cool.reacted = 0;newTypeList.confused.reacted = 0;
            newTypeList.wow.count = (action == 'like') ? (newTypeList.wow.count + 1) : (newTypeList.wow.count - 1);
        }else if(newType == 'sad'){
            newTypeList.sad.reacted = (action == 'like') ? 1 : 0;newTypeList.like.reacted = 0;newTypeList.love.reacted = 0;newTypeList.haha.reacted = 0;newTypeList.wow.reacted = 0;newTypeList.angry.reacted = 0;newTypeList.cool.reacted = 0;newTypeList.confused.reacted = 0;
            newTypeList.sad.count = (action == 'like') ? (newTypeList.sad.count + 1) : (newTypeList.sad.count - 1);
        }else if(newType == 'angry'){
            newTypeList.angry.reacted = (action == 'like') ? 1 : 0;newTypeList.like.reacted = 0;newTypeList.love.reacted = 0;newTypeList.haha.reacted = 0;newTypeList.wow.reacted = 0;newTypeList.sad.reacted = 0;newTypeList.cool.reacted = 0;newTypeList.confused.reacted = 0;
            newTypeList.angry.count = (action == 'like') ? (newTypeList.angry.count + 1) : (newTypeList.angry.count - 1);
        }else if(newType == 'cool'){
            newTypeList.cool.reacted = (action == 'like') ? 1 : 0;newTypeList.like.reacted = 0;newTypeList.love.reacted = 0;newTypeList.haha.reacted = 0;newTypeList.wow.reacted = 0;newTypeList.sad.reacted = 0;newTypeList.angry.reacted = 0;newTypeList.confused.reacted = 0;
            newTypeList.cool.count = (action == 'like') ? (newTypeList.cool.count + 1) : (newTypeList.cool.count - 1);
        }else if(newType == 'confused'){
            newTypeList.confused.reacted = (action == 'like') ? 1 : 0;newTypeList.like.reacted = 0;newTypeList.love.reacted = 0;newTypeList.haha.reacted = 0;newTypeList.wow.reacted = 0;newTypeList.sad.reacted = 0;newTypeList.angry.reacted = 0;newTypeList.cool.reacted = 0;
            newTypeList.confused.count = (action == 'like') ? (newTypeList.confused.count + 1) : (newTypeList.confused.count - 1);
        }

        if((action == 'like')){
            if(oldType == 'like'){
                newTypeList.like.count = newTypeList.like.count - 1;
            }else if(oldType == 'love'){
                newTypeList.love.count = newTypeList.love.count - 1;
            }else if(oldType == 'haha'){
                newTypeList.haha.count = newTypeList.haha.count - 1;
            }else if(oldType == 'wow'){
                newTypeList.wow.count = newTypeList.wow.count - 1;
            }else if(oldType == 'sad'){
                newTypeList.sad.count = newTypeList.sad.count - 1;
            }else if(oldType == 'angry'){
                newTypeList.angry.count = newTypeList.angry.count - 1;
            }else if(oldType == 'cool'){
                newTypeList.cool.count = newTypeList.cool.count - 1;
            }else if(oldType == 'confused'){
                newTypeList.confused.count = newTypeList.confused.count - 1;
            }
        }

        return newTypeList;
    }
    
    reduce(state, action) {
        var reactionRecord, id, objectType, isPluginActive, countAll, isViewerReactionType, isViewerReactionLabel, isViewerLiked, typeList, isViewerReactionLabelOld, isViewerReactionTypeOld;

        switch (action.type) {
            case ReactionActionTypes.DO_REACTION:
                reactionRecord = state.get(action.reactionId);

                id = _.get(reactionRecord, 'id', 0);
                objectType = _.get(reactionRecord, 'objectType', '');
                isPluginActive = _.get(reactionRecord, 'isPluginActive', 0);

                isViewerReactionLabelOld = _.get(reactionRecord, 'isViewerReactionLabel', '');
                isViewerReactionTypeOld = _.get(reactionRecord, 'isViewerReactionType', -1);

                isViewerReactionType = action.reactionType;
                isViewerReactionLabel = action.reactionTypeLabel;
                isViewerLiked = 1;

                countAll = _.get(reactionRecord, 'countAll', 0);

                if(isViewerReactionTypeOld == -1 && isViewerReactionLabelOld == ""){
                    countAll = parseInt(countAll) + 1;
                }

                typeList = _.get(reactionRecord, 'typeList', {});
                typeList = this.changeType(isViewerReactionLabelOld, isViewerReactionLabel, typeList, 'like');

                state = state.set(action.reactionId, new ReactionRecord({id, objectType, isPluginActive, countAll, isViewerReactionType, isViewerReactionLabel, isViewerLiked, typeList}));

                _fetchReaction(id,action.objectType,action.reactionType);

                return state;
            case ReactionActionTypes.DO_UN_REACTION:
                reactionRecord = state.get(action.reactionId);

                id = _.get(reactionRecord, 'id', 0);
                objectType = _.get(reactionRecord, 'objectType', '');
                isPluginActive = _.get(reactionRecord, 'isPluginActive', 0);

                isViewerReactionLabelOld = _.get(reactionRecord, 'isViewerReactionLabel', '');
                isViewerReactionType = -1;
                isViewerReactionLabel = '';
                isViewerLiked = 0;

                countAll = _.get(reactionRecord, 'countAll', 0);
                countAll = parseInt(countAll) - 1;

                typeList = _.get(reactionRecord, 'typeList', {});
                typeList = this.changeType(isViewerReactionLabelOld, action.reactionTypeLabel, typeList, 'unlike');

                state = state.set(action.reactionId, new ReactionRecord({id, objectType, isPluginActive, countAll, isViewerReactionType, isViewerReactionLabel, isViewerLiked, typeList}));

                _fetchUnReaction(id,action.objectType,action.reactionType);

                return state;
            case ReactionActionTypes.UPDATE:
               /*var id, type, like, dislike, isViewerLiked, isViewerDisliked;
                id = _.get(action.data, 'id', 0);
                type = _.get(action.data, 'type', '');
                like = _.get(action.data, 'like', 0);
                dislike = _.get(action.data, 'dislike', 0);
                isViewerLiked = _.get(action.data, 'isViewerLiked', false);
                isViewerDisliked = _.get(action.data, 'isViewerDisliked', false);
                state = state.set(_.get(action.data, 'id', 0) + _.get(action.data, 'type'), new ReactionRecord({id, type, like, dislike, isViewerLiked, isViewerDisliked}));
                return state;*/
            default:
                return state;
        }
    }
    set(data) {
        //console.log('reaction store data',data);
        var id, objectType, isPluginActive, countAll, isViewerReactionType, isViewerReactionLabel, isViewerLiked, typeList, reactionId;

        id = _.get(data, 'id', 0);
        objectType = _.get(data, 'objectType', '');
        isPluginActive = _.get(data, 'isPluginActive', 0);
        countAll = _.get(data, 'countAll', 0);
        isViewerReactionType = _.get(data, 'isViewerReactionType', -1);
        isViewerReactionLabel = _.get(data, 'isViewerReactionLabel', '');
        isViewerLiked = _.get(data, 'isViewerLiked', 0);
        typeList = _.get(data, 'typeList', {});

// if(id == 147){
//     console.log('typeList SETTTTT',typeList);
// }

        reactionId = id + 'Reaction' + objectType;

        this._state = this._state.set(reactionId, new ReactionRecord({id, objectType, isPluginActive, countAll,isViewerReactionType, isViewerReactionLabel, isViewerLiked, typeList}));
    }
}

export default new ReactionStore();