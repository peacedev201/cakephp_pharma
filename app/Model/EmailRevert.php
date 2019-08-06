<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
class EmailRevert extends AppModel {

    public function getItem($uid, $sub = 0) {
       $result = $this->find('first', array(
           'conditions' => array(
               'user_id' => $uid,
               'sub' => $sub,
           )
       ));

        return $result;
    }

    public function deleteAllItem($uid, $sub = 0) {
        $this->deleteAll(array('user_id'=>$uid,'sub'=>$sub),true,true);
    }

    public function checkExist($uid, $sub = 0) {
        $count = $this->find('count', array(
            'conditions' => array(
                'user_id' => $uid,
                'sub' => $sub,
            ))
        );

        if($count){
            return true;
        }

        return false;
    }
}
