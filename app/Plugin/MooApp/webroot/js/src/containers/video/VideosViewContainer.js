/* https://github.com/facebook/flux/blob/master/docs/Flux-Utils.md
   Are react components that control a view
   Primary job is to gather information from stores and save it in their state
   Have no props and no UI logic
*/
import VideoViewContent from '../../views/video/VideoViewContent';
import LikeStore from '../../data/stores/LikeStore';
import ReactionStore from '../../data/stores/ReactionStore';
import {Container} from 'flux/utils';
import VideoStore from '../../data/stores/VideoStore';
import VideoActions from '../../data/actions/VideoActions';

import CommentActions from '../../data/actions/CommentActions';
import CommentStore from '../../data/stores/CommentStore';

function getStores(){
    return [VideoStore,LikeStore,ReactionStore,CommentStore];
}

function getState(){
    return {
        videos:VideoStore.getState(),
        likes:LikeStore.getState(),
        reactions:ReactionStore.getState(),
        comments:CommentStore.getState(),
        viewVideo:VideoActions.view,
        fetchComment:CommentActions.fetchComment,
    };
}

export default Container.createFunctional(VideoViewContent, getStores, getState );
