'use strict';


import {ReduceStore} from 'flux/utils';
import AppAction from '../../utility/AppAction';
import TopicActionTypes from '../actions/TopicActionTypes';
import TopicActions from '../actions/TopicActions';
import AppDispatcher from '../AppDispatcher';
import Immutable from 'immutable';
import LikeStore from './LikeStore';
import ReactionStore from './ReactionStore';
var isOpen = false;
var isFetching = false;
var endOfPage = false;
var cPage = 1;
var cType = 'all';
var isShowMessage = false;
var isShowErrorMessage = false;
var isReload = false;
var message = '';
function _fetchTopics(type, page) {
    if(isFetching){
        return false;
    }
    isFetching = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        if (!type) type = cType;
        var statusCode = 0;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/topic/' + type + '?page=' + page+'&language='+mooConfig.language, {
                method: 'GET',
                headers: {'moo-access-token': mooConfig.access_token,
                    'page': page,
                    'Content-Type': 'application/json'},

            }).then(function (response) {
                statusCode = response.status;
                isFetching = false;
                return response.json()
            }).then(function (json) {
                if (_.has(json, "errorCode")) {
                    if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                    if(page == 1) {
                        if(statusCode == "404") {
                            TopicActions.fetchTopic(null);
                        }
                    }
                    else {
                        TopicActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                TopicActions.fetchTopic(json,page);
            }).catch(function (ex) {
                isFetching = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchTopicGroup(id, page) {
    if(isFetching){
        return false;
    }
    isFetching = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        var statusCode = 0;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/topic/group/' + id + '?page=' + page+'&language='+mooConfig.language, {
                method: 'GET',
                headers: {'moo-access-token': mooConfig.access_token,
                    'page': page,
                    'Content-Type': 'application/json'},

            }).then(function (response) {
                statusCode = response.status;
                isFetching = false;
                return response.json()
            }).then(function (json) {
                if (_.has(json, "errorCode")) {
                    if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                    if(page == 1) {
                        if(statusCode == "404") {
                            TopicActions.fetchTopic(null);
                        }
                    }
                    else {
                        TopicActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                TopicActions.fetchTopic(json,page);
            }).catch(function (ex) {
                isFetching = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchFriendTopics(id, page) {
    if(isFetching){
        return false;
    }
    isFetching = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        var statusCode = 0;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/user/' + id + '/topics?page=' + page+'&language='+mooConfig.language, {
                method: 'GET',
                headers: {'moo-access-token': mooConfig.access_token,
                    'page': page,
                    'Content-Type': 'application/json'},

            }).then(function (response) {
                statusCode = response.status;
                isFetching = false;
                return response.json()
            }).then(function (json) {
                if (_.has(json, "errorCode")) {
                    if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                    if(page == 1) {
                        if(statusCode == "404") {
                            TopicActions.fetchTopic(null);
                        }
                    }
                    else {
                        TopicActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                TopicActions.fetchTopic(json,page);
            }).catch(function (ex) {
                isFetching = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _viewTopic(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/topic/' + id+'?language='+mooConfig.language, {
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
                    TopicActions.showErrorMessage(json);
                }
                TopicActions.fetchTopicDetail(json);
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _pinTopic(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/topic/pin/'+ id , {
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
               TopicActions.showMessage(json); 
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _unpinTopic(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/topic/unpin/'+ id , {
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
               TopicActions.showMessage(json); 
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _lockTopic(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/topic/lock/'+ id , {
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
               TopicActions.showMessage(json); 
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _unLockTopic(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/topic/unlock/'+ id , {
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
               TopicActions.showMessage(json); 
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
class TopicStore extends ReduceStore {
    constructor() {
        super(AppDispatcher);
    }
    getInitialState() {
        return Immutable.Map({isReload: isReload,message: message,isShowErrorMessage: isShowErrorMessage,isShowMessage: isShowMessage,isFetching: isFetching, isOpen: false, topics: Immutable.OrderedMap()});
    }
    reduce(state, action) {
        var topics = state.get('topics');
        state = state.set('isFetching', isFetching);
        switch (action.type) {
            case TopicActionTypes.SHOW_ERROR_MESSAGE:
                state = state.set('isShowErrorMessage', true);
                state = state.set('message', action.data);
                return state;
            case TopicActionTypes.SHOW_MESSAGE:
                state = state.set('isShowMessage', true);
                state = state.set('message', action.data);
                return state;
            case TopicActionTypes.PIN_TOPIC:
                _pinTopic(action.id);
                return state;
            case TopicActionTypes.UNPIN_TOPIC:
                _unpinTopic(action.id);
                return state;
            case TopicActionTypes.LOCK_TOPIC:
                _lockTopic(action.id);
                state = state.set('isReload', true);
                return state;
            case TopicActionTypes.UNLOCK_TOPIC:
                _unLockTopic(action.id);
                state = state.set('isReload', true);
                return state;
            case TopicActionTypes.VIEW_TOPIC:
                _viewTopic(action.id);
                return state;
            case TopicActionTypes.FETCH_TOPIC_DETAIL:
                LikeStore.set({
                    id: _.get(action.data, 'id', 0),
                    type: _.get(action.data, 'type', ''),
                    like: _.get(action.data, 'likeCount', 0),
                    dislike: _.get(action.data, 'dislikeCount', 0),
                    isViewerLiked: _.get(action.data, 'isViewerLiked', false),
                    isViewerDisliked: _.get(action.data, 'isViewerDisliked', false),
                });
                ReactionStore.set({
                    id: _.get(action.data, 'id', 0),
                    objectType: _.get(action.data, 'type', ''),
                    isPluginActive: _.get(action.data,'reaction.isPluginActive', 0),
                    countAll: _.get(action.data,'reaction.countAll', 0),
                    isViewerReactionType:_.get(action.data,'reaction.currentType', -1),
                    isViewerReactionLabel:_.get(action.data,'reaction.currentTypeLabel',''),
                    isViewerLiked: _.get(action.data,'reaction.isLike', 0),
                    typeList: _.get(action.data,'reaction.typeList', {})
                });
                state = state.set('isOpen', true);
                state = state.set('topics', action.data);
                return state;
            case TopicActionTypes.BROWSE_TOPIC:
                cType = action.filter;
                _fetchTopics(action.filter, 1);
                return state;
            case TopicActionTypes.FETCH_NEXT_TOPIC:
                if (!endOfPage) {
                    _fetchTopics(action.filter, cPage + 1);
                }
                state = state.set('topics' ,topics);
                return state;
            case TopicActionTypes.BROWSE_TOPIC_GROUP:
                _fetchTopicGroup(action.id, cPage);
                return state;
            case TopicActionTypes.FETCH_NEXT_TOPIC_GROUP:
                if (!endOfPage) {
                    _fetchTopicGroup(action.id, cPage + 1);
                }
                state = state.set('topics' ,topics);
                return state;
            case TopicActionTypes.STOP_FETCH:
                isFetching = false;
                state = state.set('isFetching', isFetching);
                return state;
            case TopicActionTypes.FETCH_TOPIC:
                state = state.set('isOpen', true);
                if(action.page == 1) {
                    topics = topics.clear();
                    endOfPage = false;
                }
                if (action.data != null) {
                    if (topics.size == 0) {
                            // It means this is first loading so we make a trick here
                            var i = 0;
                            while (i < action.data.length) {
                                topics = topics.set(action.data[i].id, action.data[i]);
                                i++;
                            }

                    } else {
                            for (var i = 0; i < action.data.length; i++) {
                                topics = topics.set(action.data[i].id, action.data[i]);
                            }
                    }
                }
                state = state.set('topics', topics);
                return state;
                case TopicActionTypes.FRIEND_TOPIC:
                    _fetchFriendTopics(action.id, 1);
                    return state;
                case TopicActionTypes.FETCH_NEXT_FRIEND_TOPIC:
                    if (!endOfPage) {
                        _fetchFriendTopics(action.id, cPage + 1);
                    }
                    state = state.set('topics' ,topics);
                return state;
            default:
                return state;
        }
    }
}
export default new TopicStore();