<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('AppHelper', 'View/Helper');

class ReactionHelper extends AppHelper {

    public function getEnable() {
        return Configure::check('Reaction.reaction_enabled') ? Configure::read('Reaction.reaction_enabled') : 0;
    }

}
