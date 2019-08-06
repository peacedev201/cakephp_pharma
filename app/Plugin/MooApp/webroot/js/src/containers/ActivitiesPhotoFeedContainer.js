/* https://github.com/facebook/flux/blob/master/docs/Flux-Utils.md
   Are react components that control a view
   Primary job is to gather information from stores and save it in their state
   Have no props and no UI logic
*/
import AlbumContent from '../views/activity_photo_feed/AlbumContent';
import {Container} from 'flux/utils';
import LikeStore from '../data/stores/LikeStore';
import ReactionStore from '../data/stores/ReactionStore';
import PhotoStore from '../data/stores/PhotoStore';
import PhotoActions from '../data/actions/PhotoActions';

function getStores(){
    return [LikeStore,ReactionStore,PhotoStore];
}

function getState(){
    return {
        likes:LikeStore.getState(),
        reactions:ReactionStore.getState(),
        photos:PhotoStore.getState(),
        fetchAlbumByPhotoId:PhotoActions.fetchAlbumByPhotoId,
        fetchNextAlbumByPhotoId:PhotoActions.fetchNextAlbumByPhotoId,
        fetchCurentPhoto:PhotoActions.singlePhoto
    };
}

export default Container.createFunctional(AlbumContent, getStores, getState );
