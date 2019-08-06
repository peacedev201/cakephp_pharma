import ChatFriendActionCreators from '../../actions/ChatFriendActionCreators';
import ChatWebAPIUtils from '../ChatWebAPIUtils';
export function getMyFriendsCallBack(friends) {
    ChatFriendActionCreators.receiveAll(friends);
    ChatWebAPIUtils.getSocket().emit("getMyFriendsOnline");
}
export function getMyFriendsHaveIdsCallBack(friends) {
    ChatFriendActionCreators.add(friends);
    ChatWebAPIUtils.getSocket().emit("getMyFriendsOnline");
}
export function getMyFriendsOnlineCallBack(friends) {
    ChatFriendActionCreators.setOnline(friends);
}
export function friendIsLogged(uId) {
    ChatFriendActionCreators.setOnline([uId]);
}
export function friendIsLogout(uId) {
    ChatFriendActionCreators.setOffline([uId]);
}
export function friendChangeOnlineStatusCallback(friend) {
    ChatFriendActionCreators.friendChangeOnlineStatusCallback([friend]);
}