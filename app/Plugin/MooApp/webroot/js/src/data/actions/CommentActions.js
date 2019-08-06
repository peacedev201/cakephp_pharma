'use strict';

import CommentActionTypes from './CommentActionTypes';
import AppDispatcher from '../AppDispatcher';

const Actions = {
    fetchComment(id,object){
        AppDispatcher.dispatch({
            type:CommentActionTypes.FETCH_COMMENT,
            id,object
        });
    },
    fetchNextComment(){
        AppDispatcher.dispatch({
            type:CommentActionTypes.FETCH_NEXT_COMMENT,
        });
    },
    add(data){
        AppDispatcher.dispatch({
            type:CommentActionTypes.ADD_COMMENT,
            data
        });
    },
    refeshAndScrollToBottom(){
        AppDispatcher.dispatch({
            type:CommentActionTypes.REFESH_AND_SCROLL_TO_BOTTOM,
        });
    },
    stopScrollToBottom(){
        AppDispatcher.dispatch({
            type:CommentActionTypes.STOP_SCROLL_TO_BOTTOM,
        });
    },
    fetchDeleteComment(object){
        AppDispatcher.dispatch({
            type:CommentActionTypes.FETCH_DELETE_COMMENT,
            object
        });
    }
};
export default Actions;