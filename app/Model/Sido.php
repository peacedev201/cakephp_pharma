<?php

/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
class Sido extends AppModel {
    public $useTable = 'sido';

    public function getAllSido() {
       $result = $this->find('all', array());

        return $result;
    }

}
