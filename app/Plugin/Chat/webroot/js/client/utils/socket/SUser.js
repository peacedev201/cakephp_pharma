import ChatViewerActionCreators from '../../actions/ChatViewerActionCreators';
import ChatUserActionCreators from '../../actions/ChatUserActionCreators';
import ChatMooUtils from '../ChatMooUtils';
import ChatWebAPIUtils from '../ChatWebAPIUtils';
import CHAT_CONSTANTS from '../../constants/ChatConstants';

export function isLoggedCallback(uId, chat_online_status) {
    ChatViewerActionCreators.userIsLoggedCallback(uId, chat_online_status);
}

export function getUsersCallback(data) {
    ChatUserActionCreators.add(data);
}

export function getUsersByRoomIdsAtBootingCallback(data) {
    ChatUserActionCreators.add(data);
    var roomsIsOpened = ChatMooUtils.getRoomIsOpen();
    for(var i =0;i<roomsIsOpened.isCreated.length;i++){
        ChatWebAPIUtils.sendRequestCreateChatWindowByRoomId(roomsIsOpened.isCreated[i],roomsIsOpened[roomsIsOpened.isCreated[i]].minimized,CHAT_CONSTANTS.NOT_FOCUSED_CHAT_WINDOW); // m min minimized
    }
}

export function iChangeOnlineStatus(chat_online_status) {
    ChatViewerActionCreators.iChangeOnlineStatus(chat_online_status);
}