<?php
App::uses('ReactionAppModel', 'Reaction.Model');

class Reaction extends ReactionAppModel {

    public function beforeDelete($cascade = true){

        parent::beforeDelete($cascade);
    }

    public function afterDelete() {

    }

    public function getCountReaction($type, $target_id){
        $react = $this->find('first', array(
            'conditions' => array('Reaction.target_id ' => $target_id, 'Reaction.type' => $type)
        ));

        if(!empty($react)){
            $likeCount = $react['Reaction']['like_count'];
            $loveCount = $react['Reaction']['love_count'];
            $hahaCount = $react['Reaction']['haha_count'];
            $wowCount = $react['Reaction']['wow_count'];
            $sadCount = $react['Reaction']['sad_count'];
            $angryCount = $react['Reaction']['angry_count'];
            $coolCount = $react['Reaction']['cool_count'];
            $confusedCount = $react['Reaction']['confused_count'];

        }else{
            $likeModel = MooCore::getInstance()->getModel('Like');
            $block = $this->addBlockCondition(null, 'Like');
            $conditions = array(
                'Like.type' => $type,
                'Like.target_id' => $target_id,
                'Like.thumb_up' => 1
            );

            if(!empty($block)){
                $conditions = array_merge($block, $conditions);
            }
            $likeCount = $likeModel->find('count',array(
                'conditions'=> $conditions,
            ));
            $loveCount = 0;
            $hahaCount = 0;
            $wowCount = 0;
            $sadCount = 0;
            $angryCount = 0;
            $coolCount = 0;
            $confusedCount = 0;
        }

        $totalCount = $likeCount + $loveCount + $hahaCount + $wowCount + $sadCount + $angryCount + $coolCount + $confusedCount;

        return array(
            'like_count' => $likeCount,
            'love_count' => $loveCount,
            'haha_count' => $hahaCount,
            'wow_count' => $wowCount,
            'sad_count' => $sadCount,
            'angry_count' => $angryCount,
            'total_count' => $totalCount,
            'cool_count' => $coolCount,
            'confused_count' => $confusedCount
        );
    }

    private function _getReactionItemCount($medel, $type, $target_id, $reaction){
        $block = $this->addBlockCondition(null, 'Like');
        $conditions = array(
            'Like.type' => $type,
            'Like.target_id' => $target_id,
            'Like.reaction' => $reaction
        );
        if(!empty($block)){
            $conditions = array_merge($block, $conditions);
        }
        return $medel->find('count',array(
            'conditions' => $conditions
        ));
    }


    public function updateReactionCounter($type, $target_id){
        if(empty($target_id)){
            return false;
        }

        $likeModel = MooCore::getInstance()->getModel('Like');

        $react = $this->find('first', array(
            'conditions' => array('Reaction.target_id ' => $target_id, 'Reaction.type' => $type)
        ));

        $likeCount = $this->_getReactionItemCount($likeModel, $type, $target_id, REACTION_LIKE);
        $loveCount = $this->_getReactionItemCount($likeModel, $type, $target_id, REACTION_LOVE);
        $hahaCount = $this->_getReactionItemCount($likeModel, $type, $target_id, REACTION_HAHA);
        $wowCount = $this->_getReactionItemCount($likeModel, $type, $target_id, REACTION_WOW);
        $sadCount = $this->_getReactionItemCount($likeModel, $type, $target_id, REACTION_SAD);
        $angryCount = $this->_getReactionItemCount($likeModel, $type, $target_id, REACTION_ANGRY);
        $coolCount = $this->_getReactionItemCount($likeModel, $type, $target_id, REACTION_COOL);
        $confusedCount = $this->_getReactionItemCount($likeModel, $type, $target_id, REACTION_CONFUSED);
        $totalCount = $likeCount + $loveCount + $hahaCount + $wowCount + $sadCount + $angryCount + $coolCount + $confusedCount;

        if(!empty($react)){
            //update count
            $data = array(
                'id' => $react['Reaction']['id'],
                'target_id' => $target_id,
                'type' => $type,
                'total_count' => $totalCount,
                'like_count' => $likeCount,
                'love_count' => $loveCount,
                'haha_count' => $hahaCount,
                'wow_count' => $wowCount,
                'sad_count' => $sadCount,
                'angry_count' => $angryCount,
                'cool_count' => $coolCount,
                'confused_count' => $confusedCount,
                'is_update' => 0
            );
            $newReaction = $this->save($data);
            //Cache::delete('reaction.reaction_item.'.$target_id.$type, 'reaction');
            Cache::write('reaction.reaction_item.'.$target_id.$type,$newReaction,'reaction');
        }else{
            //insert new
            $data = array(
                'target_id' => $target_id,
                'type' => $type,
                'total_count' => $totalCount,
                'like_count' => $likeCount,
                'love_count' => $loveCount,
                'haha_count' => $hahaCount,
                'wow_count' => $wowCount,
                'sad_count' => $sadCount,
                'angry_count' => $angryCount,
                'cool_count' => $coolCount,
                'confused_count' => $confusedCount,
                'is_update' => 0
            );
            $newReaction = $this->save($data);
            Cache::write('reaction.reaction_item.'.$target_id.$type,$newReaction,'reaction');
        }

        return $newReaction;
    }

    public function getUserReactions( $id, $type, $reaction, $limit = null, $page = 1 )
    {
        $likeModel = MooCore::getInstance()->getModel('Like');

        if($reaction == -1){
            $conditions = array(
                'Like.type' => $type,
                'Like.target_id' => $id,
                'Like.thumb_up' => 1
            );
        }else{
            $conditions = array(
                'Like.type' => $type,
                'Like.target_id' => $id,
                'Like.reaction' => $reaction
            );
        }

        $block = $this->addBlockCondition(null, 'Like');
        if(!empty($block)){
            $conditions = array_merge($block, $conditions);
        }

        $likes = $likeModel->find( 'all', array( 'conditions' => $conditions,
            'limit' => $limit,
            'page' => $page
        ));
        return $likes;
    }

    public function getCountByReactionItem($id, $type, $reaction = -1)
    {
        $react = $this->find('first', array(
            'conditions' => array(
                'Reaction.target_id ' => $id,
                'Reaction.type' => $type)
        ));

        if(!empty($react)){
            switch ($reaction){
                case REACTION_LIKE:
                    $col_name = 'like_count';
                    break;
                case REACTION_LOVE:
                    $col_name = 'love_count';
                    break;
                case REACTION_HAHA:
                    $col_name = 'haha_count';
                    break;
                case REACTION_WOW:
                    $col_name = 'wow_count';
                    break;
                case REACTION_SAD:
                    $col_name = 'sad_count';
                    break;
                case REACTION_ANGRY:
                    $col_name = 'angry_count';
                    break;
                case REACTION_COOL:
                    $col_name = 'cool_count';
                    break;
                case REACTION_CONFUSED:
                    $col_name = 'confused_count';
                    break;
                default:
                    $col_name = 'total_count';
                    break;
            }
            return $react['Reaction'][$col_name];
        }else{
            $likeModel = MooCore::getInstance()->getModel('Like');
            $block = $this->addBlockCondition(null, 'Like');
            if($reaction >= 0){
                $conditions = array(
                    'Like.type' => $type,
                    'Like.target_id' => $id,
                    'Like.reaction' => $reaction
                );
            }else{
                $conditions = array(
                    'Like.type' => $type,
                    'Like.target_id' => $id,
                    'Like.thumb_up' => 1
                );
            }

            if(!empty($block)){
                $conditions = array_merge($block, $conditions);
            }

            return $likeModel->find('count',array(
                'conditions'=> $conditions
            ));
        }
    }
}