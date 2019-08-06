'use strict';

import ReactionActionTypes from './ReactionActionTypes';
import AppDispatcher from '../AppDispatcher';

const Actions = {
    update(data){
        AppDispatcher.dispatch({
            type:ReactionActionTypes.UPDATE ,
            data
        });
    },
    doReaction(reactionId,objectType,reactionType,reactionTypeLabel){
        AppDispatcher.dispatch({
            type:ReactionActionTypes.DO_REACTION ,
            reactionId,objectType,reactionType,reactionTypeLabel
        });
    },
    doUnReaction(reactionId,objectType,reactionType,reactionTypeLabel){
        AppDispatcher.dispatch({
            type:ReactionActionTypes.DO_UN_REACTION ,
            reactionId,objectType,reactionType,reactionTypeLabel
        });
    }
};
export default Actions;