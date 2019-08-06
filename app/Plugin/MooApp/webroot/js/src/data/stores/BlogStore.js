'use strict';


import {ReduceStore} from 'flux/utils';

import BlogActionTypes from '../actions/BlogActionTypes';
import BlogActions from '../actions/BlogActions';
import AppDispatcher from '../AppDispatcher';
import Immutable from 'immutable';
import LikeStore from './LikeStore';
import ReactionStore from './ReactionStore';
import AppAction from '../../utility/AppAction';
var isOpen = false;
var isFetching = false;
var endOfPage = false;
var cPage = 1;
var cType = 'all';
function _fetchBlogs(type, page) {
    if(isFetching){
        return false;
    }
    isFetching = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        if (!type) type = cType;
        var statusCode = 0;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/blog/' + type + '?page=' + page+'&language='+mooConfig.language, {
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
                            BlogActions.fetchBlog(null);
                        }
                    }
                    else {
                        BlogActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                BlogActions.fetchBlog(json,page);
            }).catch(function (ex) {
                isFetching = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchFriendBlogs(id, page) {
    if(isFetching){
        return false;
    }
    isFetching = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        var statusCode = 0;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/user/' + id + '/blogs?page=' + page+'&language='+mooConfig.language, {
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
                            BlogActions.fetchBlog(null);
                        }
                    }
                    else {
                        BlogActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                BlogActions.fetchBlog(json,page);
            }).catch(function (ex) {
                isFetching = false;
                console.log('parsing failed', ex)
            })
        }, 1100);
    }
}
function _viewBlog(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/blog/' + id+'?language='+mooConfig.language, {
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
                BlogActions.fetchBlogDetail(json);
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}

class BlogStore extends ReduceStore {
    constructor() {
        super(AppDispatcher);
    }
    getInitialState() {
        return Immutable.Map({isFetching: isFetching, isOpen: false, blogs: Immutable.OrderedMap()});
    }
    reduce(state, action) {
        var blogs = state.get('blogs');
        state = state.set('isFetching', isFetching);
        switch (action.type) {
            case BlogActionTypes.VIEW_BLOG:
                _viewBlog(action.id);
                return state;
            case BlogActionTypes.FETCH_BLOG_DETAIL:
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
                state = state.set('blogs', action.data);
                return state;
                
            case BlogActionTypes.BROWSE_BLOG:
                cType = action.filter;
                _fetchBlogs(action.filter, 1);
                return state;
            case BlogActionTypes.FETCH_NEXT_BLOG:
                if (!endOfPage) {
                    _fetchBlogs(action.filter, cPage + 1);
                }
                state = state.set('blogs' ,blogs);
                return state;
            case BlogActionTypes.STOP_FETCH:
                isFetching = false;
                state = state.set('isFetching', isFetching);
                return state;
            case BlogActionTypes.FETCH_BLOG: 
                state = state.set('isOpen', true);
                if(action.page == 1) {
                    blogs = blogs.clear();
                    endOfPage = false;
                }
                if (action.data != null) {
                    if (blogs.size == 0) {
                            // It means this is first loading so we make a trick here
                            var i = 0;
                            while (i < action.data.length) {
                                blogs = blogs.set(action.data[i].id, action.data[i]);
                                i++;
                            }

                    } else {
                            for (var i = 0; i < action.data.length; i++) {
                                blogs = blogs.set(action.data[i].id, action.data[i]);
                            }
                    }
                }
                state = state.set('blogs', blogs);
                return state;
                case BlogActionTypes.FRIEND_BLOG:
                    _fetchFriendBlogs(action.id, 1);
                    return state;
                case BlogActionTypes.FETCH_NEXT_FRIEND_BLOG:
                    if (!endOfPage) {
                        _fetchFriendBlogs(action.id, cPage + 1);
                    }
                    state = state.set('blogs' ,blogs);
                return state;
            default:
                return state;
        }
    }
}
export default new BlogStore();