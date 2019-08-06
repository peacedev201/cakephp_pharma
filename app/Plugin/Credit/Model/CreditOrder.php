<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 */
App::uses('CreditAppModel', 'Credit.Model');
class CreditOrder extends CreditAppModel {

    public $belongsTo = array('User');
}