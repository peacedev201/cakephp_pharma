<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
class University extends AppModel {

    public function getUniversities() {
       $universities = $this->find('all', array());

        return $universities;
    }

    public function getListUniversities() {
        $universities = $this->find('list', array(
            'fields' => array('code','name')
        ));

        return $universities;
    }

}
