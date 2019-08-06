/* https://github.com/facebook/flux/blob/master/docs/Flux-Utils.md
   Are react components that control a view
   Primary job is to gather information from stores and save it in their state
   Have no props and no UI logic
*/
import TopicViewContent from '../../views/topic/TopicViewContent';
import LikeStore from '../../data/stores/LikeStore';
import ReactionStore from '../../data/stores/ReactionStore';
import {Container} from 'flux/utils';
import TopicStore from '../../data/stores/TopicStore';
import TopicActions from '../../data/actions/TopicActions';

import CommentActions from '../../data/actions/CommentActions';
import CommentStore from '../../data/stores/CommentStore';

function getStores(){
    return [TopicStore,LikeStore,ReactionStore,CommentStore];
}

function getState(){
    return {
        topics:TopicStore.getState(),
        likes:LikeStore.getState(),
        reactions:ReactionStore.getState(),
        comments:CommentStore.getState(),
        viewTopic:TopicActions.view,
        fetchComment:CommentActions.fetchComment,
    };
}

export default Container.createFunctional(TopicViewContent, getStores, getState );
