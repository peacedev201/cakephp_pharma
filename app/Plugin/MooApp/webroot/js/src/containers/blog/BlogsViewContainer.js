/* https://github.com/facebook/flux/blob/master/docs/Flux-Utils.md
   Are react components that control a view
   Primary job is to gather information from stores and save it in their state
   Have no props and no UI logic
*/
import BlogViewContent from '../../views/blog/BlogViewContent';
import LikeStore from '../../data/stores/LikeStore';
import ReactionStore from '../../data/stores/ReactionStore';
import {Container} from 'flux/utils';
import BlogStore from '../../data/stores/BlogStore';
import BlogActions from '../../data/actions/BlogActions';

import CommentActions from '../../data/actions/CommentActions';
import CommentStore from '../../data/stores/CommentStore';

function getStores(){
    return [BlogStore,LikeStore,ReactionStore,CommentStore];
}

function getState(){
    return {
        blogs:BlogStore.getState(),
        likes:LikeStore.getState(),
        reactions:ReactionStore.getState(),
        comments:CommentStore.getState(),
        viewBlog:BlogActions.view,
        fetchComment:CommentActions.fetchComment,
    };
}

export default Container.createFunctional(BlogViewContent, getStores, getState );
