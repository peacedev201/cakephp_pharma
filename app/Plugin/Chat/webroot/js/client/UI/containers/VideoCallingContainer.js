/* https://github.com/facebook/flux/blob/master/docs/Flux-Utils.md
   Are react components that control a view
   Primary job is to gather information from stores and save it in their state
   Have no props and no UI logic
*/


import {Container} from 'flux/utils';
import VideoCallingStore from '../../stores/VideoCallingStore';
import VideoCallingView from '../videoCalling/VideoCallingView';

function getStores(){
    return [VideoCallingStore];
}

function getState(){
    return {
        videoCallings: VideoCallingStore.getState(),
        user_id: root.getAttribute('user_id'),
        receiver_id: root.getAttribute('receiver_id'),
        caller_id: root.getAttribute('caller_id'),
        room_id: root.getAttribute('room_id'),
        members: root.getAttribute('members'),
        token: root.getAttribute('token')
    };
}

export default Container.createFunctional(VideoCallingView, getStores, getState);
