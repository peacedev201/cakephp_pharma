<?php

/**
 * Copyright (c) SocialLOFT LLC
 * mooSocial - The Web 2.0 Social Network Software
 * @website: http://www.moosocial.com
 * @author: mooSocial
 * @license: https://moosocial.com/license/
 * */
App::uses('AppHelper', 'View/Helper');

class FeelingHelper extends AppHelper {
    public $helpers = array('Storage.Storage');

    public function getFeelingImage($item, $options = array()) {
        //var_dump($item);
        $prefix = (isset($options['prefix'])) ? $options['prefix'] . '_' : '';
        return $this->Storage->getUrl($item['Feeling']['id'], $prefix, $item['Feeling']['icon'], "feelings");
    }

    public function getCategoryImage($item, $options = array()) {
        //var_dump($item);
        $prefix = (isset($options['prefix'])) ? $options['prefix'] . '_' : '';
        return $this->Storage->getUrl($item['FeelingCategory']['id'], $prefix, $item['FeelingCategory']['photo'], "feeling_categories");
    }

    /*public function getBackground($thumb)
    {
        $thumb = substr($thumb, 1, strlen($thumb) - 1);
        return $this->Storage->getImage($thumb);
    }*/

    public function getEnable() {
        return Configure::check('Feeling.feeling_enabled') ? Configure::read('Feeling.feeling_enabled') : 0;
    }

    public function getCategoryLabelTranslation($categoryId){
        $modelCategory = MooCore::getInstance()->getModel('Feeling.FeelingCategory');
        /*$category = $modelCategory->find('first', array(
            'fields' => array('i18nTranslation.content'),
            'conditions' => array(
                'FeelingCategory.id' => $categoryId,
                'i18nTranslation.locale' => Configure::read('Config.language'),
                'i18nTranslation.model' => 'FeelingCategory',
                'i18nTranslation.foreign_key' => $categoryId,
            ),
            'joins' => array(
                array(
                    'table' => 'i18n',
                    'alias' => 'i18nTranslation',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'i18nTranslation.foreign_key = FeelingCategory.id'
                    )
                )
            ),

        ));

        if(!empty($category)){
            return $category['i18nTranslation']['content'];
        }
        return '';*/
        $category = $modelCategory->findById($categoryId);
        return $category['FeelingCategory']['label'];
    }
}
