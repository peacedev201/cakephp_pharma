/* https://github.com/facebook/flux/blob/master/docs/Flux-Utils.md
   Are react components that control a view
   Primary job is to gather information from stores and save it in their state
   Have no props and no UI logic
*/
import AlbumContent from '../views/activity_album/AlbumContent';
import {Container} from 'flux/utils';
import LikeStore from '../data/stores/LikeStore';
import ReactionStore from '../data/stores/ReactionStore';
import PhotoStore from '../data/stores/PhotoStore';
import PopupStore from '../data/stores/PopupStore';
import PhotoActions from '../data/actions/PhotoActions';

function getStores(){
    return [LikeStore,ReactionStore,PhotoStore,PopupStore];
}

function getState(){
    return {
        likes:LikeStore.getState(),
        reactions:ReactionStore.getState(),
        photos:PhotoStore.getState(),
        //popups:PopupStore.getState(),
        fetchAlbum:PhotoActions.multiPhoto,
        fetchTagAlbum:PhotoActions.multiTagPhoto,
        fetchNextTagAlbum:PhotoActions.multiNextTagPhoto,
        fetchCurentPhoto:PhotoActions.singlePhoto
    };
}

export default Container.createFunctional(AlbumContent, getStores, getState );
