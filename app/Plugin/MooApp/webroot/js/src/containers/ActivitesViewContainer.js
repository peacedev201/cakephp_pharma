/* https://github.com/facebook/flux/blob/master/docs/Flux-Utils.md
   Are react components that control a view
   Primary job is to gather information from stores and save it in their state
   Have no props and no UI logic
*/

import ActivityListView from '../views/activity_view/ActivityView';
import {Container} from 'flux/utils';
import ActivityViewActions from '../data/actions/ActivityViewActions';
import CommentActions from '../data/actions/CommentActions';
import ActivityViewStore from '../data/stores/ActivityViewStore';
import LikeStore from '../data/stores/LikeStore';
import ReactionStore from '../data/stores/ReactionStore';
import CommentStore from '../data/stores/CommentStore';

function getStores(){
    return [ActivityViewStore,LikeStore,ReactionStore,CommentStore];
}

function getState(){
    return {
        activites: ActivityViewStore.getState(),
        fetchActivity:ActivityViewActions.fetch,
        fetchComment:CommentActions.fetchComment,
        //fetchObjectComment:CommentActions.fetchObjectComment,
        likes:LikeStore.getState(),
        reactions:ReactionStore.getState(),
        comments:CommentStore.getState()
    };
}

export default Container.createFunctional(ActivityListView, getStores, getState);
