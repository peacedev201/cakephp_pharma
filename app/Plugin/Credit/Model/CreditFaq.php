<?php

/**
 * mooSocial - The Web 2.0 Social Network Software
 *
 * @website: http://www.moosocial.com
 */
App::uses('CreditAppModel', 'Credit.Model');

class CreditFaq extends CreditAppModel
{
    public $validationDomain = 'credit';
    public $belongsTo = array('User');

    public $validate = array(
        'question' => array(
            'notEmpty' => array(
                'rule' => 'notBlank',
                'message' => 'Question is required'
            )
        )
    );

    public function getFaqActive($page = 1, $limit = 2)
    {
        $faqs = $this->find('all', array('conditions' => array('CreditFaq.active' => 1),
                'order' => 'CreditFaq.created desc',
                'limit' => $limit,
                'page' => $page)
        );
        return $faqs;
    }
}
