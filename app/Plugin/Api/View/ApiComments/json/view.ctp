<?php
    $isLoarmore = false;
    $isGift = false;
    if(isset($data['bIsCommentloadMore'] ) && $data['bIsCommentloadMore'] > 0 ) $isLoarmore = true;
    $commentArray = array();
    if($type == 'activity') {
        if(!empty($data)) { 
            $subject = isset($data['subject']) ? $data['subject'] : MooCore::getInstance()->getSubject();
            foreach ($data as $comment) {
                $isViewerLiked = $isViewerDisliked = false;
                foreach ($activity_likes as $like) {
                        if (isset($like['Like']) &&  $like['Like']['user_id'] == $uid && $like['Like']['target_id'] == $comment['id']  ){
                            $like['Like']['thumb_up'] == 1 ? $isViewerLiked = true : $isViewerDisliked = true;
                        }
                }
                $like = array (
                            'id' => $comment['id'] ,
                            'type' => 'core_activity_comment',
                            'isViewerLiked' => $isViewerLiked,
                            'isViewerDisliked' => $isViewerDisliked,
                );
                $imageArray = array();
                $imageArray = array(
                    '50_square' => $this->Moo->getItemPhoto(array('User' => $comment['User']), array('prefix' => '50_square'), array(), true),
                    '100_square' => $this->Moo->getItemPhoto(array('User' => $comment['User']), array('prefix' => '100_square'), array(), true),
                    '200_square' => $this->Moo->getItemPhoto(array('User' => $comment['User']), array('prefix' => '200_square'), array(), true),
                    '600' => $this->Moo->getItemPhoto(array('User' => $comment['User']), array('prefix' => '600'), array(), true),
                );
                
                
                $afterRenderActorHtml = new CakeEvent("Api.View.ApiComment.beforeRenderUserNameHtml", $this, array(
                    'activity' => $comment['Activity'],
                    'user' => $comment['User']
                ));

                $tmpUserName = '';
                $this->getEventManager()->dispatch($afterRenderActorHtml);
                if(!empty($afterRenderActorHtml->result['result'])) :
                    foreach ($afterRenderActorHtml->result['result'] as $titleArray):
                        $tmpUserName .= $titleArray['titleHtml'];
                    endforeach;
                endif;
                
                if($this->Moo->isGifImage($this->Moo->getImageUrl(array('ActivityComment'=>$comment),array()))) :
                    $isGift = true;
                endif;
                $canDelete = false;
                if ( (!empty($subject) && $subject[key($subject)]['user_id'] == $uid) ||  $comment['user_id'] == $uid || ( $uid && $cuser['Role']['is_admin'] ) || ( !empty( $data['admins'] ) && in_array( $uid, $data['admins'] ) ) ) {
                    $canDelete = true;
                }
    
                
                $_commentArray = array (
                                'id' => $comment['id'],
                                'userId' => $comment['user_id'],
                                'userName' => $comment['User']['name'],
                                'userNameHtml' => $comment['User']['name'] . $tmpUserName,
                                'userAvatar' => $imageArray,
                                'userUrl' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($comment['User']['moo_href'], 'UTF-8', 'UTF-8')) ,
                                'edited' => $comment['edited'],
                                //'message' => $comment['ActivityComment']['comment'],
                    			'message' => nl2br($this->Text->autoLink($this->Moo->parseSmilies($comment['comment']),array_merge(array('target' => '_blank', 'rel' => 'nofollow', 'escape' => false),array('no_replace_ssl' => 1)))),
                                'likeCount' => $comment['like_count'],
                                'dislikeCount' => $comment['dislike_count'],
                                'published' => $comment['created'],
                                'publishedTranslated' => $this->Moo->getTime($comment['created'], Configure::read('core.date_format'), $utz),
                                'thumnails' => $comment['thumbnail'] ? array (
                                     '200' => $this->Moo->getItemPhoto(array('ActivityComment'=>$comment), array('prefix' => '200'),array(),true),
                                     'default' =>  $this->Moo->getItemPhoto(array('ActivityComment'=>$comment), array(),array(),true) ,
                                    ): '' ,
                                'likeObject' => $like,
                                'isLoadmore' => $isLoarmore,
                                'isGiftImage' => $isGift,
                                'canDelete' => $canDelete,
                                'commentType' => 'core_activity_comment',
                                'targetId' => $comment['activity_id'],
                            );
                $afterRenderApiComment = new CakeEvent("Api.View.ApiComment.afterRenderApiComment", $this, array(
                    'item_id' => $comment['id'],
                    'item_type' => $like['type'],
                    'data' => $comment,
                    'data_json' => $_commentArray,
                ));
                $this->getEventManager()->dispatch($afterRenderApiComment);
                if(!empty($afterRenderApiComment->result['result'])) {
                    $result = $afterRenderApiComment->result['result'];
                    $_commentArray = array_merge($_commentArray, $result);
                }
                $commentArray[] = $_commentArray;
            }
        } 
    }    
    else {
        if(empty($data) || !isset($data)){
            throw new ApiNotFoundException(__d('api', 'comment not found'));
        }
        else {
            $subject = isset($data['subject']) ? $data['subject'] : MooCore::getInstance()->getSubject();
            foreach ($data['comments'] as $comment){
                $canDelete = false;
                if ( (!empty($subject) && $subject[key($subject)]['user_id'] == $uid) ||  $comment['Comment']['user_id'] == $uid || ( $uid && $cuser['Role']['is_admin'] ) || ( !empty( $data['admins'] ) && in_array( $uid, $data['admins'] ) ) ) {
                    $canDelete = true;
                }
                $isViewerLiked = $isViewerDisliked = false;

                if (isset( $data['comment_likes'][$comment['Comment']['id']] ) ) {
                    $data['comment_likes'][$comment['Comment']['id']] == 1 ? $isViewerLiked = true : $isViewerDisliked = true;
                }
                $like = array (
                                'id' => $comment['Comment']['id'] ,
                                'type' => 'comment',
                                'isViewerLiked' => $isViewerLiked,
                                'isViewerDisliked' => $isViewerDisliked,
                    );
                $imageArray = array();
                $imageArray = array(
                        '50_square' => $this->Moo->getItemPhoto(array('User' => $comment['User']), array('prefix' => '50_square'), array(), true),
                        '100_square' => $this->Moo->getItemPhoto(array('User' => $comment['User']), array('prefix' => '100_square'), array(), true),
                        '200_square' => $this->Moo->getItemPhoto(array('User' => $comment['User']), array('prefix' => '200_square'), array(), true),
                        '600' => $this->Moo->getItemPhoto(array('User' => $comment['User']), array('prefix' => '600'), array(), true),
                );
                $afterRenderActorHtml = new CakeEvent("Api.View.ApiComment.beforeRenderUserNameHtml", $this, array(
                    'user' => $comment['User']
                ));

                $tmpUserName = '';
                $this->getEventManager()->dispatch($afterRenderActorHtml);
                if(!empty($afterRenderActorHtml->result['result'])) :
                    foreach ($afterRenderActorHtml->result['result'] as $titleArray):
                        $tmpUserName .= $titleArray['titleHtml'];
                    endforeach;
                endif;
                //if($this->Moo->isGifImage($this->Moo->getImageUrl(array('Comment'=>$comment),array()))) :
                if($this->Moo->isGifImage($this->Moo->getImageUrl($comment,array()))) :
                    $isGift = true;
                endif;
                $_commentArray = array (
                        'id' => $comment['Comment']['id'],
                        'userId' => $comment['Comment']['user_id'],
                        'userName' => $comment['User']['name'],
                        'userNameHtml' => $comment['User']['name'] . $tmpUserName ,
                        'userAvatar' => $imageArray,
                        'userUrl' => FULL_BASE_URL . str_replace('?','',mb_convert_encoding($comment['User']['moo_href'], 'UTF-8', 'UTF-8')) ,
                        'edited' => $comment['Comment']['edited'],
                		'message' => nl2br($this->Text->autoLink($this->Moo->parseSmilies($comment['Comment']['message']),array_merge(array('target' => '_blank', 'rel' => 'nofollow', 'escape' => false),array('no_replace_ssl' => 1)))),                        
                        'likeCount' => $comment['Comment']['like_count'],
                        'dislikeCount' => $comment['Comment']['dislike_count'],
                        'published' => $comment['Comment']['created'],
                        'publishedTranslated' => $this->Moo->getTime($comment['Comment']['created'], Configure::read('core.date_format'), $utz),
                        'thumnails' => $comment['Comment']['thumbnail'] ? array (
                                    '200' => $this->Moo->getItemPhoto(array('Comment'=>$comment['Comment']), array('prefix' => '200'),array(),true),
                                    'default' =>  $this->Moo->getItemPhoto(array('Comment'=>$comment['Comment']), array(),array(),true) ,
                            ): '' ,
                        'likeObject' => $like,
                        'isLoadmore' => $isLoarmore,
                        'isGiftImage' => $isGift,
                        'canDelete' => $canDelete,
                        'commentType' => $comment['Comment']['type'],
                        'targetId' => $comment['Comment']['target_id'],
                    );
                $afterRenderApiComment = new CakeEvent("Api.View.ApiComment.afterRenderApiComment", $this, array(
                    'item_id' => $comment['Comment']['id'],
                    'item_type' => $like['type'],
                    'data' => $comment,
                    'data_json' => $_commentArray,
                ));
                $this->getEventManager()->dispatch($afterRenderApiComment);
                if(!empty($afterRenderApiComment->result['result'])) {
                    $result = $afterRenderApiComment->result['result'];
                    $_commentArray = array_merge($_commentArray, $result);
                }
                $commentArray[] = $_commentArray;
            };    
        }
    };
if(empty($commentArray)){
    throw new ApiNotFoundException(__d('api', 'Comment not found'));
}
echo json_encode($commentArray);