<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
class PharmaBook extends AppModel {

    public function getByPhone($phone = '') {
       $result = $this->find('first', array(
           'conditions' => array(
               'tel' => $phone
           )
       ));

        return $result;
    }

}
