/* https://github.com/facebook/flux/blob/master/docs/Flux-Utils.md
   Are react components that control a view
   Primary job is to gather information from stores and save it in their state
   Have no props and no UI logic
*/
import UserViewContent from '../views/user/UserViewContent';
import {Container} from 'flux/utils';
import UserStore from '../data/stores/UserStore';
import UserActions from '../data/actions/UserActions';
import ActivityStore from '../data/stores/ActivityStore';
import ActivityActions from '../data/actions/ActivityActions';
import LikeStore from '../data/stores/LikeStore';
import ReactionStore from '../data/stores/ReactionStore';

function getStores(){
    return [UserStore,ActivityStore,LikeStore,ReactionStore];
}

function getState(){
    return {
        users:UserStore.getState(),
        activites: ActivityStore.getState(),
        likes:LikeStore.getState(),
        reactions:ReactionStore.getState(),
        viewUserById:UserActions.viewUser,
        fetchMe:UserActions.fetchCurrentUserInfo,
        //getUserMenu:UserActions.getUserMenu,
        getUserActivity:ActivityActions.getUserActivity,
        fetchNextUserActivities:ActivityActions.fetchNextUserActivity,
        fetchSingleActivity:ActivityActions.fetchSingleActivity,
        removeActivityByRefesh:ActivityActions.removeActivityByRefesh,
    };
}

export default Container.createFunctional(UserViewContent, getStores, getState );
