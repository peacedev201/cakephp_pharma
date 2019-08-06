'use strict';


import {ReduceStore} from 'flux/utils';

import AlbumActionTypes from '../actions/AlbumActionTypes';
import AlbumActions from '../actions/AlbumActions';
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
function _fetchAlbums(type, page) {
    if(isFetching){
        return false;
    }
    isFetching = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        if (!type) type = cType;
        var statusCode = 0;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/album/' + type + '?page=' + page+'&language='+mooConfig.language, {
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
                            AlbumActions.fetchAlbum(null);
                        }
                    }
                    else {
                        AlbumActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                AlbumActions.fetchAlbum(json,page);
            }).catch(function (ex) {
                isFetching = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchUserAlbums(id, page) {
    if(isFetching){
        return false;
    }
    isFetching = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        var statusCode = 0;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/user/' + id + '/albums?page=' + page+'&language='+mooConfig.language, {
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
                            AlbumActions.fetchAlbum(null);
                        }
                    }
                    else {
                        AlbumActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                AlbumActions.fetchAlbum(json,page);
            }).catch(function (ex) {
                isFetching = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _viewAlbum(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/album/' + id+'?language='+mooConfig.language, {
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
                AlbumActions.fetchAlbumDetail(json);
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
class AlbumStore extends ReduceStore {
    constructor() {
        super(AppDispatcher);
    }
    getInitialState() {
        return Immutable.Map({isFetching: isFetching, isOpen: false, albums: Immutable.OrderedMap()});
    }
    reduce(state, action) {
        var albums = state.get('albums');
        state = state.set('isFetching', isFetching);
        switch (action.type) {
            case AlbumActionTypes.VIEW_ALBUM:
                _viewAlbum(action.id);
                return state;
            case AlbumActionTypes.FETCH_ALBUM_DETAIL:
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
                state = state.set('albums', action.data);
                return state;
            case AlbumActionTypes.BROWSE_ALBUM:
                cType = action.filter;
                _fetchAlbums(action.filter, 1);
                return state;
            case AlbumActionTypes.BROWSE_USER_ALBUM:
                _fetchUserAlbums(action.id, 1);
                return state;
            case AlbumActionTypes.FETCH_NEXT_ALBUM:
                if (!endOfPage) {
                    _fetchAlbums(action.filter, cPage + 1);
                }
                state = state.set('albums' ,albums);
                return state;
            case AlbumActionTypes.FETCH_NEXT_USER_ALBUM:
                _fetchUserAlbums(action.id, cPage + 1);
                state = state.set('albums' ,albums);
                return state;
            case AlbumActionTypes.STOP_FETCH:
                isFetching = false;
                state = state.set('isFetching', isFetching);
                return state;
            case AlbumActionTypes.FETCH_ALBUM:
                state = state.set('isOpen', true);
                if(action.page == 1) {
                    albums = albums.clear();
                    endOfPage = false;
                }
                if (action.data != null) {
                    if (albums.size == 0) {
                            // It means this is first loading so we make a trick here
                            var i = 0;
                            while (i < action.data.length) {
                                albums = albums.set(action.data[i].id, action.data[i]);
                                i++;
                            }

                    } else {
                            for (var i = 0; i < action.data.length; i++) {
                                albums = albums.set(action.data[i].id, action.data[i]);
                            }
                    }
                }
                state = state.set('albums', albums);
                return state;
            default:
                return state;
        }
    }
}
export default new AlbumStore();