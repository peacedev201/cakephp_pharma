/* https://github.com/facebook/flux/blob/master/docs/Flux-Utils.md
   Are react components that control a view
   Primary job is to gather information from stores and save it in their state
   Have no props and no UI logic
*/
import GroupViewContent from '../../views/group/GroupViewContent';
import {Container} from 'flux/utils';
import GroupStore from '../../data/stores/GroupStore';
import GroupActions from '../../data/actions/GroupActions';
import ActivityActions from '../../data/actions/ActivityActions';
import ActivityStore from '../../data/stores/ActivityStore';
import LikeStore from '../../data/stores/LikeStore';
import ReactionStore from '../../data/stores/ReactionStore';
import UserStore from '../../data/stores/UserStore';
import UserActions from '../../data/actions/UserActions';
import VideoStore from '../../data/stores/VideoStore';
import TopicStore from '../../data/stores/TopicStore';
import VideoActions from '../../data/actions/VideoActions';
import TopicActions from '../../data/actions/TopicActions';
import CommentActions from '../../data/actions/CommentActions';
import CommentStore from '../../data/stores/CommentStore';

function getStores(){
    return [GroupStore,ActivityStore,LikeStore,ReactionStore,UserStore,VideoStore,CommentStore,TopicStore];
}

function getState(){
    return {
        groups:GroupStore.getState(),
        activites: ActivityStore.getState(),
        likes:LikeStore.getState(),
        reactions:ReactionStore.getState(),
        users: UserStore.getState(),
        viewGroupDetail:GroupActions.viewGroup,
        getGroupActivity:ActivityActions.getGroupActivity,
        fetchNextGroupActivity:ActivityActions.fetchNextGroupActivity,
        fetchMe:UserActions.fetchCurrentUserInfo,
        removeActivityByRefesh:ActivityActions.removeActivityByRefesh,
        
        videos:VideoStore.getState(),
        comments:CommentStore.getState(),
        viewVideo:VideoActions.view,
        fetchComment:CommentActions.fetchComment,
        
        topics:TopicStore.getState(),
        viewTopic:TopicActions.view,
    };
}

export default Container.createFunctional(GroupViewContent, getStores, getState );
