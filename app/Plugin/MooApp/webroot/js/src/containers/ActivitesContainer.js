/* https://github.com/facebook/flux/blob/master/docs/Flux-Utils.md
   Are react components that control a view
   Primary job is to gather information from stores and save it in their state
   Have no props and no UI logic
*/

import ActivityListView from '../views/activity/List';
import {Container} from 'flux/utils';
import ActivityActions from '../data/actions/ActivityActions';
import UserActions from '../data/actions/UserActions';
import ActivityStore from '../data/stores/ActivityStore';
import LikeStore from '../data/stores/LikeStore';
import ReactionStore from '../data/stores/ReactionStore';
import PhotoStore from '../data/stores/PhotoStore';
import UserStore from '../data/stores/UserStore';


function getStores(){
    return [ActivityStore,LikeStore,ReactionStore,UserStore];
}

function getState(){
    return {
        activites: ActivityStore.getState(),
        users: UserStore.getState(),
        fetchActivities:ActivityActions.fetch,
        fetchNextActivities:ActivityActions.fetchNext,
        removeActivityByRefesh:ActivityActions.removeActivityByRefesh,
        fetchMe:UserActions.fetchCurrentUserInfo,
        likes:LikeStore.getState(),
        reactions:ReactionStore.getState()
    };
}

export default Container.createFunctional(ActivityListView, getStores, getState);
