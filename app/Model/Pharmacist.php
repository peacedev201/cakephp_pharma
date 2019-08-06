<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
class Pharmacist extends AppModel {

    public function getByPhone($phone = '') {
       $result = $this->find('first', array(
           'conditions' => array(
               'find_tel' => $phone
           )
       ));

        return $result;
    }

    public function getSameArea($email){
        $result = $this->find('first', array(
            'conditions' => array(
                'email' => $email
            )
        ));

        return $result;
    }
}
