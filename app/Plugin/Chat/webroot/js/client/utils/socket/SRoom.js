import ChatRoomActionCreators from '../../actions/ChatRoomActionCreators';
import ChatGroupActionCreators from '../../actions/ChatGroupActionCreators';
import ChatMessageActionCreators from '../../actions/ChatMessageActionCreators';
import ChatCounterUnseenMesasgeActionCreators from '../../actions/ChatCounterUnseenMesasgeActionCreators';
import ChatWebAPIUtils from '../ChatWebAPIUtils';
import CHAT_CONSTANTS from '../../constants/ChatConstants';

export function createChatWindowBySystemCallback(data) {
    ChatRoomActionCreators.createForAUserBySystemCallback(data);
}

export function getRoomMessagesCallback(data) {
    ChatMessageActionCreators.getRoomMessagesCallback(data);
}

export function getRoomMessagesMoreCallback(data){
    ChatMessageActionCreators.getRoomMessagesMoreCallback(data);
}

export function markMessagesIsSeenInRoomsCallback(data) {
    ChatRoomActionCreators.markMessagesIsSeenInRoomsCallback(data);
}

export function getRoomHasUnreadMessageCallback(rooms) {
    if (rooms.length != 0) {
        for (var i = 0; i < rooms.length; i++) {
            ChatWebAPIUtils.sendRequestCreateChatWindowByRoomId(rooms[i].room_id);
        }
    }
}

export function markReadAllMessagesCallback() {
    ChatCounterUnseenMesasgeActionCreators.markReadAllMessagesCallback();
}

export function setOnlineCallback() {
    ChatWebAPIUtils.sendRequestForGetRoomHasUnreadMessage();
}

export function getMyGroupsCallBack(groups) {
    ChatGroupActionCreators.receiveAll(groups);
}

export function deleteConversationCallback(rId) {
    ChatWebAPIUtils.sendRequestGetRoomMessages(rId);
}

export function reportMessageSpamCallback(data) {
    if (data.error == CHAT_CONSTANTS.ERROR.REPORT_ROOM_MESSAGE_SPAM_IS_EXIST) {
        ChatWebAPIUtils.openAlertModal(__.t("report_chat_session"),__.t("we_marked_this_message_as_spam"));
    }
}

export function leaveConversationCallback(rId){
    ChatWebAPIUtils.destroyARoom(rId);
    ChatWebAPIUtils.sendRequestGetMyGroups();
}

export function addUsersToARoomCallback(data){
    ChatRoomActionCreators.addUsersToARoomCallback(data.roomId,data.users);
}

export function blockMessagesCallback(rId){
    ChatWebAPIUtils.refeshARoom(rId);
}

export function unblockMessagesCallback(rId){
    ChatWebAPIUtils.refeshARoom(rId);
}

export function refreshStatusChatWindowByRoomIdCallback(data){
    ChatRoomActionCreators.refeshStatusARoom(data);
}

export function startTypingCallback(data){
    ChatRoomActionCreators.startTyping(data);
}

export function stopTypingCallback(data){
    ChatRoomActionCreators.stopTyping(data);
}