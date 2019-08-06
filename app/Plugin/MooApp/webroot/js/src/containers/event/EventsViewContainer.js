/* https://github.com/facebook/flux/blob/master/docs/Flux-Utils.md
   Are react components that control a view
   Primary job is to gather information from stores and save it in their state
   Have no props and no UI logic
*/
import EventViewContent from '../../views/event/EventViewContent';
import {Container} from 'flux/utils';
import EventStore from '../../data/stores/EventStore';
import EventActions from '../../data/actions/EventActions';
import ActivityActions from '../../data/actions/ActivityActions';
import ActivityStore from '../../data/stores/ActivityStore';
import LikeStore from '../../data/stores/LikeStore';
import ReactionStore from '../../data/stores/ReactionStore';
import UserStore from '../../data/stores/UserStore';
import UserActions from '../../data/actions/UserActions';

function getStores(){
    return [EventStore,ActivityStore,LikeStore,ReactionStore,UserStore];
}

function getState(){
    return {
        events:EventStore.getState(),
        activites: ActivityStore.getState(),
        likes:LikeStore.getState(),
        reactions:ReactionStore.getState(),
        users: UserStore.getState(),
        viewEventDetail:EventActions.viewEvent,
        getEventActivity:ActivityActions.getEventActivity,
        fetchNextEventActivity:ActivityActions.fetchNextEventActivity,
        fetchMe:UserActions.fetchCurrentUserInfo,
        removeActivityByRefesh:ActivityActions.removeActivityByRefesh,
    };
}

export default Container.createFunctional(EventViewContent, getStores, getState );
