/* https://github.com/facebook/flux/blob/master/docs/Flux-Utils.md
   Are react components that control a view
   Primary job is to gather information from stores and save it in their state
   Have no props and no UI logic
*/
import PhotoViewContent from '../../views/photo/PhotoViewContent';
import LikeStore from '../../data/stores/LikeStore';
import ReactionStore from '../../data/stores/ReactionStore';
import {Container} from 'flux/utils';
import PhotoStore from '../../data/stores/PhotoStore';
import PhotoActions from '../../data/actions/PhotoActions';

import CommentActions from '../../data/actions/CommentActions';
import CommentStore from '../../data/stores/CommentStore';

function getStores(){
    return [PhotoStore,LikeStore,ReactionStore,CommentStore];
}

function getState(){
    return {
        photos:PhotoStore.getState(),
        likes:LikeStore.getState(),
        reactions:ReactionStore.getState(),
        comments:CommentStore.getState(),
        viewPhoto:PhotoActions.view,
        fetchComment:CommentActions.fetchComment,
    };
}

export default Container.createFunctional(PhotoViewContent, getStores, getState );
