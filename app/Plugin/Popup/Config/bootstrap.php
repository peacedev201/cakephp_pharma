<?php
/*
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 */
if(Configure::read('Popup.popup_enabled'))
{
    App::uses('PopupListener','Popup.Lib');
    CakeEventManager::instance()->attach(new PopupListener());
}
