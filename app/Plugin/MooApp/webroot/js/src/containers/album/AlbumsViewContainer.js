/* https://github.com/facebook/flux/blob/master/docs/Flux-Utils.md
   Are react components that control a view
   Primary job is to gather information from stores and save it in their state
   Have no props and no UI logic
*/
import AlbumViewContent from '../../views/album/AlbumViewContent';
import LikeStore from '../../data/stores/LikeStore';
import ReactionStore from '../../data/stores/ReactionStore';
import {Container} from 'flux/utils';
import AlbumStore from '../../data/stores/AlbumStore';
import AlbumActions from '../../data/actions/AlbumActions';

import PhotoStore from '../../data/stores/PhotoStore';
import PhotoActions from '../../data/actions/PhotoActions';

import CommentActions from '../../data/actions/CommentActions';
import CommentStore from '../../data/stores/CommentStore';

function getStores(){
    return [AlbumStore,LikeStore,ReactionStore,CommentStore,PhotoStore];
}

function getState(){
    return {
        albums:AlbumStore.getState(),
        likes:LikeStore.getState(),
        reactions:ReactionStore.getState(),
        photos:PhotoStore.getState(),
        comments:CommentStore.getState(),
        viewAlbum:AlbumActions.view,
        fetchComment:CommentActions.fetchComment,
        fetchPhotoFromAlbum:PhotoActions.fetchPhotoFromAlbum,
    };
}

export default Container.createFunctional(AlbumViewContent, getStores, getState );
