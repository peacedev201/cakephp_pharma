'use strict';


import {ReduceStore} from 'flux/utils';
import AppAction from '../../utility/AppAction';
import VideoActionTypes from '../actions/VideoActionTypes';
import VideoActions from '../actions/VideoActions';
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
var message = '';
function _fetchVideos(type, page) {
    if(isFetching){
        return false;
    }
    isFetching = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        if (!type) type = cType;
        var statusCode = 0;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/video/' + type + '?page=' + page+'&language='+mooConfig.language, {
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
                            VideoActions.fetchVideo(null);
                        }
                    }
                    else {
                        VideoActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                VideoActions.fetchVideo(json,page);
            }).catch(function (ex) {
                isFetching = false;
                console.log('parsing failed', ex)
            })
        }, 1100);
    }
}
function _fetchVideoGroup(id, page) {
    if(isFetching){
        return false;
    }
    isFetching = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        var statusCode = 0;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/video/group/' + id + '?page=' + page+'&language='+mooConfig.language, {
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
                            VideoActions.fetchVideo(null);
                        }
                    }
                    else {
                        VideoActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                VideoActions.fetchVideo(json,page);
            }).catch(function (ex) {
                isFetching = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchFriendVideos(id, page) {
    if(isFetching){
        return false;
    }
    isFetching = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        var statusCode = 0;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/user/' + id + '/videos?page=' + page+'&language='+mooConfig.language, {
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
                            VideoActions.fetchVideo(null);
                        }
                    }
                    else {
                        VideoActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                VideoActions.fetchVideo(json,page);
            }).catch(function (ex) {
                isFetching = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _viewVideo(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/video/' + id+'?language='+mooConfig.language, {
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
                    VideoActions.showMessage(json);
                }
                VideoActions.fetchVideoDetail(json);
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
class VideoStore extends ReduceStore {
    constructor() {
        super(AppDispatcher);
    }
    getInitialState() {
        return Immutable.Map({message: message,isShowMessage: isShowMessage,isFetching: isFetching, isOpen: false, videos: Immutable.OrderedMap()});
    }
    reduce(state, action) {
        var videos = state.get('videos');
        state = state.set('isFetching', isFetching);
        switch (action.type) {
            case VideoActionTypes.SHOW_MESSAGE:
                state = state.set('isShowMessage', true);
                state = state.set('message', action.data);
                return state;
            case VideoActionTypes.VIEW_VIDEO:
                _viewVideo(action.id);
                return state;
            case VideoActionTypes.FETCH_VIDEO_DETAIL:
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
                state = state.set('videos', action.data);
                return state;
            case VideoActionTypes.BROWSE_VIDEO:
                cType = action.filter;
                _fetchVideos(action.filter, 1);
                return state;
            case VideoActionTypes.FETCH_NEXT_VIDEO:
                if (!endOfPage) {
                    _fetchVideos(action.filter, cPage + 1);
                }
                state = state.set('videos' ,videos);
                return state;
            case VideoActionTypes.BROWSE_VIDEO_GROUP:
                _fetchVideoGroup(action.id, 1);
                return state;
            case VideoActionTypes.FETCH_NEXT_VIDEO_GROUP:
                if (!endOfPage) {
                    _fetchVideoGroup(action.id, cPage + 1);
                }
                state = state.set('videos' ,videos);
                return state;
            case VideoActionTypes.STOP_FETCH:
                isFetching = false;
                state = state.set('isFetching', isFetching);
                return state;
            case VideoActionTypes.FETCH_VIDEO:
                state = state.set('isOpen', true);
                if(action.page == 1) {
                    videos = videos.clear();
                    endOfPage = false;
                }
                if (action.data != null) {
                    if (videos.size == 0) {
                            // It means this is first loading so we make a trick here
                            var i = 0;
                            while (i < action.data.length) {
                                videos = videos.set(action.data[i].id, action.data[i]);
                                i++;
                            }

                    } else {
                            for (var i = 0; i < action.data.length; i++) {
                                videos = videos.set(action.data[i].id, action.data[i]);
                            }
                    }
                }
                state = state.set('videos', videos);
                return state;
                case VideoActionTypes.FRIEND_VIDEO:
                    _fetchFriendVideos(action.id, 1);
                    return state;
                case VideoActionTypes.FETCH_NEXT_FRIEND_VIDEO:
                    if (!endOfPage) {
                        _fetchFriendVideos(action.id, cPage + 1);
                    }
                    state = state.set('videos' ,videos);
                return state;
            default:
                return state;
        }
    }
}
export default new VideoStore();