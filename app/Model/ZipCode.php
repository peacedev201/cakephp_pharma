<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
class ZipCode extends AppModel {

    public function getZipcodes($param, $limit = 5, $page = 1) {
        $result = $this->find('all', array(
            'conditions' => $param,
            'limit' => $limit,
            'page' => $page
        ));

        return $result;
    }

    public function getByZipcode($code){
        $result = $this->find('first', array(
            'conditions' => array(
                'zip_code' => $code
            )
        ));

        return $result;
    }
}
