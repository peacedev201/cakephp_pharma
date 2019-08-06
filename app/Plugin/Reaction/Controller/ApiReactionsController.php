<?php
App::uses('ReactionsController','Reaction.Controller');
App::uses('ReactionAppController','Reaction.Controller');

class ApiReactionsController extends ReactionsController{
    public function beforeFilter() {
        parent::beforeFilter();
        $this->OAuth2 = $this->Components->load('OAuth2');
        $this->OAuth2->verifyResourceRequest(array('token'));
        //$this->loadModel('Activity');
        $this->loadModel('Like');
    }

    // POST remove reaction
    public function delete() {
        $objectType = $this->request->params['objectType'];
        $itemId = $this->request->params['id'];
        $reactionType = $this->request->params['reactionType'];
        $uid = $this->Auth->user('id');

        $type = $this->_getType($objectType);
        $like = $this->Like->getUserLike($itemId, $uid, $type);
        if (empty($like)) {
            throw new ApiBadRequestException(__d('api', 'This item is not reacted yet '));
        }else{
            if ($like['Like']['reaction'] != $reactionType) {
                throw new ApiBadRequestException(__d('api', 'This item is not reacted yet '));
            }else if($like['Like']['thumb_up'] != 1){
                throw new ApiBadRequestException(__d('api', 'This item is not reacted yet '));
            }
        }
        //$thumb_up = 1;
        $reactionResult = parent::ajax_add($type,$itemId,$reactionType, 'array');

        $this->autoRender = true;
        $this->set(array(
            'success' => true,
            'reaction' => $reactionResult,
            '_serialize' => array('success', 'reaction')
        ));
    }

    // POST reaction an item
    public function add() {
        $objectType = $this->request->params['objectType'];
        $itemId = $this->request->params['id'];
        $reactionType = $this->request->params['reactionType'];
        $uid = $this->Auth->user('id');

        $type = $this->_getType($objectType);
        $like = $this->Like->getUserLike($itemId, $uid, $type);
        if (!empty($like)) { // user already reacted this item
            if( $like['Like']['thumb_up'] == 1 && $like['Like']['reaction'] == $reactionType ){
                throw new ApiBadRequestException(__d('api', 'Item already reacted'));
            }
        }

        $reactionResult = parent::ajax_add($type,$itemId,$reactionType, 'array');

        $this->autoRender = true;
        $this->set(array(
            'success' => true,
            'reaction' => $reactionResult,
            '_serialize' => array('success', 'reaction')
        ));
    }

    // GET display people who reacted that item
    public function view() {
        $objectType = $this->request->params['objectType'];
        $reactionType = $this->request->params['reactionType'];
        $itemId = $this->request->params['id'];

        $type = $this->_getType($objectType);

        $this->set('title_for_layout', __d('reaction', 'Reactions'));
        parent::ajax_show($type,$itemId,$reactionType);

    }
}