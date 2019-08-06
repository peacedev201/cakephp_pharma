<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('SpotlightAppModel', 'Spotlight.Model');
class SpotlightTran extends SpotlightAppModel {

    public $mooFields = array('plugin','type','title', 'href');

    public function getTitle(&$row)
    {
        return __d('spotlight','join spotlight');
    }

    public function getHref($row)
    {
        $request = Router::getRequest();
        return $request->base.'/';
    }

    public function findById($id)
    {
        $spotlight_transaction = MooCore::getInstance()->getModel('Spotlight.SpotlightTransaction');
        return $spotlight_transaction->findById($id);
    }
}