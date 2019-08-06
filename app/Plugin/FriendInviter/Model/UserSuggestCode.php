<?php

/**
 * Invites Model
 *
 * @property Client $Client
 */
class UserSuggestCode extends AppModel {

    public function isIdExist($id) {
        return $this->hasAny(array('id' => $id));
    }

}
