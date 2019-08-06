import ChatMessageActionCreators from '../../actions/ChatMessageActionCreators';
import ChatMooUtils from '../ChatMooUtils';
import CHAT_CONSTANTS from '../../constants/ChatConstants';
import ChatWebAPIUtils from '../ChatWebAPIUtils';

export function newMessage(data) {
    if(ChatWebAPIUtils.getChatStatus() == CHAT_CONSTANTS.USER_OFFLINE){

    }else{
        ChatMessageActionCreators.newMessage(data);
        // Core conversations integration
        ChatMooUtils.refeshMessagePage();
    }
}

