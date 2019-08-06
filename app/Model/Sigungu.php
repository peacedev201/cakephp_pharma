<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
class Sigungu extends AppModel {

    public function getSigungus($param, $limit = 5, $page = 1) {
       $result = $this->find('all', array(
           'conditions' => $param,
           'limit' => $limit,
           'page' => $page
       ));

        return $result;
    }

}
