'use strict';

import Immutable from 'immutable';
import {ReduceStore} from 'flux/utils';

import PhotoActionTypes from '../actions/PhotoActionTypes';
import PhotoActions from '../actions/PhotoActions';
import LikeStore from './LikeStore';
import ReactionStore from './ReactionStore';
import AppAction from '../../utility/AppAction';
import AppDispatcher from '../AppDispatcher';
import _ from 'lodash';
import 'whatwg-fetch';
var idPhoto = 0;
var isOpen = false;
var isFetching = true;
var isFetchingPhoto = false;
var isLoadmorePhoto = false;
var cPage = 1;
var endOfPage = false;
function _fetchPhotoFromGroup(groupId,page) {
    if(isFetchingPhoto){
        return false;
    }
    isFetchingPhoto = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        var statusCode = 0;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/photo/group/' + groupId + '?page=' + page+'&language='+mooConfig.language, {
                method: 'GET',
                headers: {'moo-access-token': mooConfig.access_token,
                    'page': page,
                    'Content-Type': 'application/json'},

            }).then(function (response) {
                statusCode = response.status;
                isFetchingPhoto = false;
                return response.json()
            }).then(function (json) {
                if (_.has(json, "errorCode")) {
                    if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                    if(page == 1) {
                        if(statusCode == "404") {
                            PhotoActions.fetchObjectPhotos(null);
                        }
                    }
                    else {
                        PhotoActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                PhotoActions.fetchObjectPhotos(json);
            }).catch(function (ex) {
                isFetchingPhoto = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchPhotoTagByUser(id,page) {
    if(isFetchingPhoto){
        return false;
    }
    isFetchingPhoto = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        var statusCode = 0;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/user/' + id + '/photos?page=' + page+'&language='+mooConfig.language, {
                method: 'GET',
                headers: {'moo-access-token': mooConfig.access_token,
                    'page': page,
                    'Content-Type': 'application/json'},

            }).then(function (response) {
                statusCode = response.status;
                isFetchingPhoto = false;
                return response.json()
            }).then(function (json) {
                if (_.has(json, "errorCode")) {
                    if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                    if(page == 1) {
                        if(statusCode == "404") {
                            PhotoActions.fetchObjectPhotos(null);
                        }
                    }
                    else {
                        PhotoActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                PhotoActions.fetchObjectPhotos(json);
            }).catch(function (ex) {
                isFetchingPhoto = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchPhotoFromAlbum(albumId,page) {
    if(isLoadmorePhoto){
        return false;
    }
    isLoadmorePhoto = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        var statusCode = 0;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/photo/album/' + albumId + '?page=' + page+'&language='+mooConfig.language, {
                method: 'GET',
                headers: {'moo-access-token': mooConfig.access_token,
                    'page': page,
                    'Content-Type': 'application/json'},

            }).then(function (response) {
                statusCode = response.status;
                isLoadmorePhoto = false;
                return response.json()
            }).then(function (json) {
                if (_.has(json, "errorCode")) {
                    if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                    if(page == 1) {
                        if(statusCode == "404") {
                            PhotoActions.fetchObjectPhotos(null);
                        }
                    }
                    else {
                        PhotoActions.stopLoadMore();
                        return 0;
                    }
                }
                PhotoActions.fetchObjectPhotos(json);
            }).catch(function (ex) {
                isLoadmorePhoto = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchPhoto(id,style) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/photo/' + id+'?language='+mooConfig.language, {
                method: 'GET',
                headers: {'moo-access-token': mooConfig.access_token,
                    'Content-Type': 'application/json'},
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
                if(style == 'single') PhotoActions.fetchPhoto(json);
                if(style == 'multi') PhotoActions.fetchMultiPhoto(json);
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })

        }, 100);
    }
}
function _fetchPhotoAlbum(albumId) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/album/' + albumId+'?language='+mooConfig.language, {
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
                PhotoActions.fetchAlbum(json);
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchPhotoAlbumHasGivenPhoto(albumId,photoId) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/album/' + albumId + '/?photoId='+ photoId+'&language='+mooConfig.language, {
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
                PhotoActions.fetchAlbum(json);
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchTagAlbum(photoId,uid,page) {
    if (_.has(mooConfig, "access_token")) {
        cPage = page;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/tagAlbumPhoto/' + photoId + '/?uid='+ uid +'&page=' + page+'&language='+mooConfig.language, {
                method: 'GET',
                headers: {'moo-access-token': mooConfig.access_token,
                    'Content-Type': 'application/json'},

            }).then(function (response) {
                return response.json()
            }).then(function (json) {
                if (_.has(json, "errorCode")) {
                    endOfPage = true;
                    return 0;
                }
                PhotoActions.fetchMultiTagPhoto(json);
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchAlbumByPhotoId(photoId,page) {
    if(isFetchingPhoto){
        return false;
    }
    isFetchingPhoto = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        var statusCode = 0;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/photo/browse/feed/' + photoId + '?page=' + page+'&language='+mooConfig.language, {
                method: 'GET',
                headers: {'moo-access-token': mooConfig.access_token,
                    'page': page,
                    'Content-Type': 'application/json'},

            }).then(function (response) {
                statusCode = response.status;
                isFetchingPhoto = false;
                return response.json()
            }).then(function (json) {
                if (_.has(json, "errorCode")) {
                    if(page == 1) {
                        if(statusCode == "404") {
                            PhotoActions.fetchObjectPhotos(null);
                        }
                    }
                    else {
                        PhotoActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                PhotoActions.fetchObjectPhotos(json);
            }).catch(function (ex) {
                isFetchingPhoto = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _viewPhoto(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/photo/' + id+'?language='+mooConfig.language, {
                method: 'GET',
                headers: {'moo-access-token': mooConfig.access_token,
                    'Content-Type': 'application/json'},

            }).then(function (response) {
                return response.json()
            }).then(function (json) {
                if (_.has(json, "errorCode")) {
                    return 0;
                }
                PhotoActions.fetchPhotoDetail(json);
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
class PhotoStore extends ReduceStore {
    constructor() {
        super(AppDispatcher);
    }
    getInitialState() {
        return Immutable.Map({isFetchingPhoto:isFetchingPhoto,isOpen: false,isLoadmorePhoto:isLoadmorePhoto,isFetching: isFetching, photos: Immutable.OrderedMap(), albums: Immutable.OrderedMap(), photoArray: Immutable.OrderedMap()});
    }

    reduce(state, action) {
        var photoArray = state.get('photoArray');
        var photos = state.get('photos');
        state = state.set('isFetchingPhoto', isFetchingPhoto);
        switch (action.type) {
            case PhotoActionTypes.FETCH_ALBUM_BY_PHOTO_ID: 
                _fetchAlbumByPhotoId(action.id,1);
                return state;
            case PhotoActionTypes.FETCH_NEXT_ALBUM_BY_PHOTO_ID:
                if (!endOfPage) {
                    _fetchAlbumByPhotoId(action.id, cPage + 1);
                }
                return state;
            case PhotoActionTypes.FETCH_PHOTO_FROM_GROUP: 
                _fetchPhotoFromGroup(action.id,1);
                return state;
            case PhotoActionTypes.FETCH_NEXT_PHOTO_FROM_GROUP:
                if (!endOfPage) {
                    _fetchPhotoFromGroup(action.id, cPage + 1);
                }
                return state;
            case PhotoActionTypes.FETCH_PHOTO_TAG_BY_USER: 
                _fetchPhotoTagByUser(action.id,1);
                return state;
            case PhotoActionTypes.FETCH_NEXT_PHOTO_TAG_BY_USER:
                if (!endOfPage) {
                    _fetchPhotoTagByUser(action.id, cPage + 1);
                }
                return state;
            case PhotoActionTypes.STOP_FETCH:
                isFetchingPhoto = false;
                state = state.set('isFetchingPhoto', isFetchingPhoto);
                return state;    
            case PhotoActionTypes.VIEW_PHOTO:
                _viewPhoto(action.id);
                return state;
            case PhotoActionTypes.FETCH_PHOTO_DETAIL:
                LikeStore.set({
                    id: _.get(action.data, 'id', 0),
                    type: 'Photo_Photo',
                    like: _.get(action.data, 'likeCount', 0),
                    dislike: _.get(action.data, 'dislikeCount', 0),
                    isViewerLiked: _.get(action.data, 'isViewerLiked', false),
                    isViewerDisliked: _.get(action.data, 'isViewerDisliked', false),
                });
                ReactionStore.set({
                    id: _.get(action.data, 'id', 0),
                    objectType: 'Photo_Photo',
                    isPluginActive: _.get(action.data,'reaction.isPluginActive', 0),
                    countAll: _.get(action.data,'reaction.countAll', 0),
                    isViewerReactionType:_.get(action.data,'reaction.currentType', -1),
                    isViewerReactionLabel:_.get(action.data,'reaction.currentTypeLabel',''),
                    isViewerLiked: _.get(action.data,'reaction.isLike', 0),
                    typeList: _.get(action.data,'reaction.typeList', {})
                });
                state = state.set('isOpen', true);
                state = state.set('photos', action.data);
                return state;
            case PhotoActionTypes.OPEN_PHOTO:
                _fetchPhoto(action.id,'single');
                return state;
            case PhotoActionTypes.FETCH_PHOTO:
                LikeStore.set({
                    id: _.get(action.data, 'id', 0),
                    type: 'Photo_Photo',
                    like: _.get(action.data, 'likeCount', 0),
                    dislike: _.get(action.data, 'dislikeCount', 0),
                    isViewerLiked: _.get(action.data, 'isViewerLiked', false),
                    isViewerDisliked: _.get(action.data, 'isViewerDisliked', false),
                });
                ReactionStore.set({
                    id: _.get(action.data, 'id', 0),
                    objectType: 'Photo_Photo',
                    isPluginActive: _.get(action.data,'reaction.isPluginActive', 0),
                    countAll: _.get(action.data,'reaction.countAll', 0),
                    isViewerReactionType:_.get(action.data,'reaction.currentType', -1),
                    isViewerReactionLabel:_.get(action.data,'reaction.currentTypeLabel',''),
                    isViewerLiked: _.get(action.data,'reaction.isLike', 0),
                    typeList: _.get(action.data,'reaction.typeList', {})
                });
                photoArray = photoArray.set(action.data.id + action.data.type ,action.data);
                state = state.set('photos', photoArray);
                state = state.set('isFetching', false);
                return state;

            case PhotoActionTypes.CLOSE_PHOTO:
                state = state.set('isFetching', true);
                return state;
            case PhotoActionTypes.MULTI_TAG_PHOTO:
                _fetchTagAlbum(action.photoId,action.uid,1);
                return state;
            case PhotoActionTypes.MULTI_NEXT_TAG_PHOTO:
                if (!endOfPage) {
                    _fetchTagAlbum(action.photoId,action.uid,cPage + 1);
                }
                return state;
            case PhotoActionTypes.OPEN_ALBUM:
                    _fetchPhotoAlbumHasGivenPhoto(action.albumId,action.photoId);
                return state;
            case PhotoActionTypes.FETCH_MULTI_PHOTO:
                photoArray = photoArray.set(action.data.id + action.data.type ,action.data);
                LikeStore.set({
                    id: _.get(action.data, 'id', 0),
                    type: "Photo_Photo",
                    like: _.get(action.data, 'likeCount', 0),
                    dislike: _.get(action.data, 'dislikeCount', 0),
                    isViewerLiked: _.get(action.data, 'isViewerLiked', false),
                    isViewerDisliked: _.get(action.data, 'isViewerDisliked', false),
                });
                ReactionStore.set({
                    id: _.get(action.data, 'id', 0),
                    objectType: 'Photo_Photo',
                    isPluginActive: _.get(action.data,'reaction.isPluginActive', 0),
                    countAll: _.get(action.data,'reaction.countAll', 0),
                    isViewerReactionType:_.get(action.data,'reaction.currentType', -1),
                    isViewerReactionLabel:_.get(action.data,'reaction.currentTypeLabel',''),
                    isViewerLiked: _.get(action.data,'reaction.isLike', 0),
                    typeList: _.get(action.data,'reaction.typeList', {})
                });
                state = state.set('photoArray', photoArray);
                state = state.set('isFetching', false);
                return state;
            case PhotoActionTypes.FETCH_MULTI_TAG_PHOTO:
                var albums = '';
                if (action.data != null) {
                    var i = 0;
                    if (action.data != null) {
                        while (i < action.data.length) {
                            _fetchPhoto(action.data[i].id,'multi');
                            albums = action.data[i];
                            i++;
                        }
                    }
                }
                state = state.set('albums', albums);
                return state;
            case PhotoActionTypes.FETCH_ALBUM:
                if (action.data != null) {
                    var i = 0;
                    if (action.data.photoObject != null) {
                        while (i < action.data.photoObject.length) {
                            _fetchPhoto(action.data.photoObject[i].id,'multi');
                            i++;
                        }
                    }
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
                }
                
                state = state.set('albums', action.data);
                return state;
            case PhotoActionTypes.FETCH_PHOTO_FROM_ALBUM:
                _fetchPhotoFromAlbum(action.id,1);
                return state;
            case PhotoActionTypes.STOP_LOAD_MORE:
                isLoadmorePhoto = false;
                state = state.set('isLoadmorePhoto', isLoadmorePhoto);
                return state;
            case PhotoActionTypes.FETCH_NEXT_PHOTO:
                _fetchPhotoFromAlbum(action.id, cPage + 1);
                state = state.set('photos' ,photos);
                return state;
            case PhotoActionTypes.FETCH_OBJECT_PHOTO:
                if (action.data != null) {
                    if (photos.size == 0) {
                            // It means this is first loading so we make a trick here
                            var i = 0;
                            while (i < action.data.length) {
                                photos = photos.set(action.data[i].id, action.data[i]);
                                i++;
                            }

                    } else {
                            for (var i = 0; i < action.data.length; i++) {
                                photos = photos.set(action.data[i].id, action.data[i]);
                            }
                    }
                }
                state = state.set('isOpen', true);
                state = state.set('photos',photos);
                state = state.set('isFetching', false);
                return state;
            default:
                return state;
        }
    }
}

export default new PhotoStore();