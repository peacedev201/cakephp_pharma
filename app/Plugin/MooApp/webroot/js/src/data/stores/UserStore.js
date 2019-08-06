'use strict';


import {ReduceStore} from 'flux/utils';
import AppAction from '../../utility/AppAction';
import UserActionTypes from '../actions/UserActionTypes';
import UserActions from '../actions/UserActions';
import AppDispatcher from '../AppDispatcher';
import Immutable from 'immutable';
import {pharse} from '../../utility/mooApp';
var statusCode = 0;
var isOpen = false;
var isFetching = false;
var endOfPage = false;
var isShowSearchForm = false;
var cPage = 1;
var cType = 'all';
var searchString = '';
const UserRecord = Immutable.Record({
    id: 0,
    userCase: 0,
    userStatus: "",
    requestId: 0
});
function _searchUser(keyword) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/user/search'+'?language='+mooConfig.language , {
                method: 'POST',
                headers: { 'moo-access-token': mooConfig.access_token ,
                          'Content-Type': 'application/json'} ,
                body: JSON.stringify({name:keyword})

            }).then(function (response) {
                searchString = keyword;
                statusCode = response.status;
                return response.json()
            }).then(function (json) {
                if (_.has(json, "errorCode")) {
                    if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                    if(statusCode == "404") {
                        UserActions.fetchUser(null);
                    }
                }
                searchString = keyword;
                UserActions.fetchUser(json);
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _doFollow(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/user/follow' , {
                method: 'POST',
                headers: { 'moo-access-token': mooConfig.access_token ,
                          'Content-Type': 'application/json'} ,
                body: JSON.stringify({user_id:id})

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
function _doUnFollow(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/user/unfollow' , {
                method: 'POST',
                headers: { 'moo-access-token': mooConfig.access_token ,
                          'Content-Type': 'application/json'} ,
                body: JSON.stringify({user_id:id})

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
function _doUnBlock(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/user/unblock' , {
                method: 'POST',
                headers: { 'moo-access-token': mooConfig.access_token ,
                          'Content-Type': 'application/json'} ,
                body: JSON.stringify({user_id:id})

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
function _fetchAddFriend(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/friend/add' , {
                method: 'POST',
                headers: { 'moo-access-token': mooConfig.access_token ,
                          'Content-Type': 'application/json'} ,
                body: JSON.stringify({user_id:id})

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
function _fetchCancelRequest(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/friend/cancel' , {
                method: 'POST',
                headers: { 'moo-access-token': mooConfig.access_token ,
                          'Content-Type': 'application/json'} ,
                body: JSON.stringify({user_id:id})

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
function _fetchRemoveFriend(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/friend/delete' , {
                method: 'POST',
                headers: { 'moo-access-token': mooConfig.access_token ,
                          'Content-Type': 'application/json'} ,
                body: JSON.stringify({user_id:id})

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
function _fetchRespondYes(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/friend/accept' , {
                method: 'POST',
                headers: { 'moo-access-token': mooConfig.access_token ,
                          'Content-Type': 'application/json'} ,
                body: JSON.stringify({id:id})

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
function _fetchRespondNo(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/friend/reject' , {
                method: 'POST',
                headers: { 'moo-access-token': mooConfig.access_token ,
                          'Content-Type': 'application/json'} ,
                body: JSON.stringify({id:id})

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
function _fetchTagUser(tagId) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/activity/tag/' + tagId + "?language=" + mooConfig.language, {
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
                UserActions.fetchUser(json);
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchUsers(type, page) {
    if(isFetching){
        return false;
    }
    isFetching = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        if (!type) type = cType;
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/user/browse/' + type + '?page=' + page+'&language='+mooConfig.language, {
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
                            UserActions.fetchUser(null);
                        }
                    }
                    else {
                        UserActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                UserActions.fetchUser(json);
            }).catch(function (ex) {
                isFetching = false;
                console.log('parsing failed', ex)
            })
        }, 1100);
    }
}
function _fetchProfileFriend(id, page) {
    if(isFetching){
        return false;
    }
    isFetching = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/user/' + id + '/friends?page=' + page+'&language='+mooConfig.language, {
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
                            UserActions.fetchUser(null);
                        }
                    }
                    else {
                        UserActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                UserActions.fetchUser(json);
            }).catch(function (ex) {
                isFetching = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchProfileBlockMember(id, page) {
    if(isFetching){
        return false;
    }
    isFetching = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/user/' + id + '/blocked?page=' + page+'&language='+mooConfig.language, {
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
                            UserActions.fetchUser(null);
                        }
                    }
                    else {
                        UserActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                UserActions.fetchUser(json,page);
            }).catch(function (ex) {
                isFetching = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchFollowingUser(page) {
    if(isFetching){
        return false;
    }
    isFetching = true;
    cPage = page;
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/user/follow?page=' + page+'&language='+mooConfig.language, {
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
                            UserActions.fetchUser(null);
                        }
                    }
                    else {
                        UserActions.stopFetch();
                        endOfPage = true;
                        return 0;
                    }
                }
                UserActions.fetchUser(json);
            }).catch(function (ex) {
                isFetching = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _viewUser(id) {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/user/' + id+'?language='+mooConfig.language , {
                method: 'GET',
                headers: {'moo-access-token': mooConfig.access_token,
                    'Content-Type': 'application/json'},

            }).then(function (response) {
                statusCode = response.status;
                return response.json()
            }).then(function (json) {
                if (_.has(json, "errorCode")) {
                    if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                        return 0;
                }
                UserActions.fetchProfile(json);
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 1100);
    }
}
function _fetchCurrentUserInfo() {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/user/me'+'?language='+mooConfig.language , {
                method: 'GET',
                headers: {'moo-access-token': mooConfig.access_token,
                    'Content-Type': 'application/json'},

            }).then(function (response) {
                statusCode = response.status;
                return response.json()
            }).then(function (json) {
                if (_.has(json, "errorCode")) {
                    if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                        }
                        return 0;
                }
                UserActions.showCurrentUserInfo(json);
            }).catch(function (ex) {
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}
function _fetchFriendRequest() {
    if (_.has(mooConfig, "access_token")) {
        setTimeout(function () {
            fetch(mooConfig.url.base + '/api/friend/requests/?language='+mooConfig.language, {
                method: 'GET',
                headers: {'moo-access-token': mooConfig.access_token,
                    'Content-Type': 'application/json'},

            }).then(function (response) {
                statusCode = response.status;
                return response.json()
            }).then(function (json) {
                if (_.has(json, "errorCode")) {
                    if(_.get(json, "errorCode") == 'token_expired') {
                            AppAction.refreshToken();
                            return 0;
                    }
                    if(statusCode == "404") {
                            UserActions.fetchFriendRequest(null);
                    }
                    else {
                        UserActions.stopFetch();
                        return 0;
                    }
                }
                UserActions.fetchUser(json);
            }).catch(function (ex) {
                isFetching = false;
                console.log('parsing failed', ex)
            })
        }, 100);
    }
}

class UserStore extends ReduceStore {
    constructor() {
        super(AppDispatcher);
    }
    getInitialState() {
        return Immutable.Map({isShowSearchForm:isShowSearchForm,searchString:searchString,isFetching: isFetching, isOpen: false, userMe: Immutable.OrderedMap(),users: Immutable.OrderedMap()});
    }
    reduce(state, action) {
        var users = state.get('users');
        state = state.set('isFetching', isFetching);
        function _addRecords(data){
            users = users.set(data.id+searchString , data);
            var id = data.id;
            var userCase = data.userCase;
            var userStatus = data.userStatus;
            var requestId = data.requestId;
            state = state.set(data.id, new UserRecord({id, userCase, userStatus, requestId}));
            
        }
        function _addFriendRequest(data){
            users = users.set(data.id , data);
            var id = data.id;
            var userCase = data.userCase;
            var userStatus = data.userStatus;
            var requestId = data.requestId;
            state = state.set(data.id, new UserRecord({id, userCase, userStatus, requestId}));
            
        }
        switch (action.type) {

            case UserActionTypes.FETCH_CURRENT_USER_INFO:
                _fetchCurrentUserInfo();
                return state;
            case UserActionTypes.SHOW_CURRENT_USER_INFO:
                state = state.set('userMe',action.data);
                return state;
            case UserActionTypes.TOGGLE_SEARCH_FORM:
                isShowSearchForm = !isShowSearchForm;
                state = state.set('isShowSearchForm',isShowSearchForm)
                return state;
            case UserActionTypes.SEARCH_USER:
                _searchUser(action.keyword);
                return state;
            case UserActionTypes.FOLLOW_USER:
                _doFollow(action.id);
                return state;
            case UserActionTypes.UNFOLLOW_USER:
                _doUnFollow(action.id);
                return state;
            case UserActionTypes.UNBLOCK_USER:
                _doUnBlock(action.id);
                return state;
            case UserActionTypes.ADD_FRIEND:
                var userRecord = state.get(action.id);
                
                var id, userCase ,userStatus,requestId;
                id = _.get(userRecord, 'id', 0);
                userCase = _.get(userRecord, 'userCase', '');
                userStatus = _.get(userRecord, 'userStatus', '');
                requestId = _.get(userRecord, 'requestId', '');
                
                userCase = 4;
                userStatus = pharse('cancel');
                
                state = state.set(action.id, new UserRecord({id, userCase, userStatus,requestId}));
                
                _fetchAddFriend(action.id);
                return state;
            case UserActionTypes.CANCEL_FRIEND_REQUEST:
                var userRecord = state.get(action.id);
                
                var id, userCase ,userStatus,requestId;
                id = _.get(userRecord, 'id', 0);
                userCase = _.get(userRecord, 'userCase', '');
                userStatus = _.get(userRecord, 'userStatus', '');
                requestId = _.get(userRecord, 'requestId', '');
                
                userCase = 1;
                userStatus = pharse('add');
                
                state = state.set(action.id, new UserRecord({id, userCase, userStatus,requestId}));
                
                _fetchCancelRequest(action.id);
                return state;
            case UserActionTypes.REMOVE_FRIEND:
                var userRecord = state.get(action.id);
                
                var id, userCase ,userStatus,requestId;
                id = _.get(userRecord, 'id', 0);
                userCase = _.get(userRecord, 'userCase', '');
                userStatus = _.get(userRecord, 'userStatus', '');
                requestId = _.get(userRecord, 'requestId', '');
                
                userCase = 1;
                userStatus = pharse('add');
                
                state = state.set(action.id, new UserRecord({id, userCase, userStatus,requestId}));
                
                _fetchRemoveFriend(action.id);
                return state;
                
            case UserActionTypes.RESPOND_REQUEST:
                var userRecord = state.get(action.id);
                
                var id, userCase ,userStatus,requestId;
                id = _.get(userRecord, 'id', 0);
                userCase = _.get(userRecord, 'userCase', '');
                userStatus = _.get(userRecord, 'userStatus', '');
                requestId = _.get(userRecord, 'requestId', '');
                
                if(action.flag == 1) {
                    userCase = 2;
                    userStatus = pharse('remove');
                    _fetchRespondYes(requestId);
                }
                else {
                    userCase = 1;
                    userStatus = pharse('add');
                    _fetchRespondNo(requestId);
                }
                
                state = state.set(action.id, new UserRecord({id, userCase, userStatus,requestId}));
                
                
                return state;

            case UserActionTypes.FETCH_TAG_USER: 
                _fetchTagUser(action.id);
                return state;
            case UserActionTypes.BROWSE_USER:
                cType = action.filter;
                _fetchUsers(action.filter, cPage);
                return state;
            case UserActionTypes.FETCH_NEXT_USER:
                if (!endOfPage) {
                    _fetchUsers(action.filter, cPage + 1);
                }
                state = state.set('users' ,users);
                return state;
            case UserActionTypes.STOP_FETCH:
                isFetching = false;
                state = state.set('isFetching', isFetching);
                return state;
            case UserActionTypes.FETCH_USER:
                state = state.set('isOpen', true);
                if(action.page == 1) {
                    users = users.clear();
                    endOfPage = false;
                }
                if (action.data != null) {
                    if (users.size == 0) {
                            var i = 0;
                            while (i < action.data.length) {
                                _addRecords(action.data[i]);
                                i++;
                            }

                    } else {
                            for (var i = 0; i < action.data.length; i++) {
                                _addRecords(action.data[i]);
                            }
                    }
                }
                state = state.set('searchString', searchString);
                state = state.set('users' + searchString , users);
                return state;
            case UserActionTypes.VIEW_USER:
                _viewUser(action.id);
                return state;
            case UserActionTypes.FETCH_PROFILE:
                state = state.set('isOpen', true);
                if (action.data != null) {
                    if (users.size == 0) {
                            _addRecords(action.data);

                    } 
                }
                //state = state.set('users' + searchString , users);
                state = state.set('users', action.data);
                return state;
            case UserActionTypes.FETCH_NEXT_PROFILE_FRIEND:
                if (!endOfPage) {
                    _fetchProfileFriend(action.id, cPage + 1);
                }
                state = state.set('users' ,users);
            case UserActionTypes.PROFILE_FRIEND:
                _fetchProfileFriend(action.id, cPage);
                return state;
            case UserActionTypes.FETCH_NEXT_PROFILE_BLOCK_MEMBER:
                if (!endOfPage) {
                    _fetchProfileBlockMember(action.id, cPage + 1);
                }
                state = state.set('users' ,users);
                return state;
            case UserActionTypes.PROFILE_BLOCK_MEMBER:
                _fetchProfileBlockMember(action.id, 1);
                return state;
            case UserActionTypes.FETCH_NEXT_FOLLOWING_USER:
                if (!endOfPage) {
                    _fetchFollowingUser(cPage + 1);
                }
                state = state.set('users' ,users);
                return state;
            case UserActionTypes.FETCH_FOLLOWING_USER:
                _fetchFollowingUser(cPage);
                return state;
            case UserActionTypes.FRIEND_REQUEST:
                users = users.clear();
                _fetchFriendRequest();
                state = state.set('users' , users);
                return state;
            case UserActionTypes.FETCH_FRIEND_REQUEST:
                state = state.set('isOpen', true);
                if (action.data != null) {
                    for (var i = 0; i < action.data.length; i++) {
                        _addFriendRequest(action.data[i]);
                    }
                }
                else {
                    users = null;
                }
                state = state.set('users' , users);
                return state;
            default:
                return state;
        }
    }
    set(data) {
        var id, userCase,userStatus,requestId;
        id = _.get(data, 'id', 0);
        userCase = _.get(data, 'userCase', 0);
        userStatus = _.get(data, 'userStatus','');
        requestId = _.get(data, 'requestId','');
        this._state = this._state.set(_.get(data, 'id', 0), new UserRecord({id, userCase, userStatus,requestId}));
    }
}
export default new UserStore();