<?php

App::uses('AppHelper', 'View/Helper');

class FaqHelper extends AppHelper {
	public $helpers = array('Storage.Storage');
    public function canEdit($item, $viewer) {
        if (!$viewer)
            return false;

        if ($viewer['Role']['is_admin'] == $viewer['User']['id'])
            return true;

        return false;
    }

    public function canDelete($item, $viewer) {
        return $this->canEdit($item, $viewer);
    }

    public function getEnable() {
        return Configure::read('Faq.faq_enabled');
    }

//
    public function checkSeeComment($faq, $uid) {

        if ($faq['Faq']['alow_comment'] == PRIVACY_EVERYONE) {
            return true;
        }

        return $this->checkPostStatus($faq, $uid);
    }

    public function checkPostStatus($faq, $uid) {
        if (!$uid)
            return false;

        if ($uid == $faq['Faq']['user_id'])
            return true;

        if ($faq['Faq']['alow_comment'] == PRIVACY_EVERYONE) {
            return true;
        }
        return false;
    }

    public function checkCateHaveChild($category_id, $active = FALSE) {
        $categoryModel = MooCore::getInstance()->getModel('Faq.FaqHelpCategorie');
        return $categoryModel->checkHaveChild($category_id, $active);
    }

    public function getCategory($category_id, $active = FALSE, $language = 'eng') {
        $categoryModel = MooCore::getInstance()->getModel('Faq.FaqHelpCategorie');
        return $categoryModel->getCategoryById($category_id, $active, $language);
    }

    public function getCateParent($category_id) {
        $categoryModel = MooCore::getInstance()->getModel('Faq.FaqHelpCategorie');
        return $categoryModel->getParent($category_id);
    }

    public function getCateChild($category_id, $active = FALSE, $language = 'eng') {
        $categoryModel = MooCore::getInstance()->getModel('Faq.FaqHelpCategorie');
        return $categoryModel->getAllCateChild($category_id, $active, $language);
    }

    public function getFaqById($faq_id, $language = 'eng') {
        $faqModel = MooCore::getInstance()->getModel('Faq.Faq');
        $faqModel->setLanguage($language);
        return $faqModel->findById($faq_id);
    }

    public function getItemSitemMap($name, $limit, $offset) {
        if (!MooCore::getInstance()->checkPermission(null, 'faq_view'))
            return null;

        $faqModel = MooCore::getInstance()->getModel('Faq.Faq');
        $faqs = $faqModel->find('all', array(
            'conditions' => array('Faq.active' => PRIVACY_PUBLIC),
            'limit' => $limit,
            'offset' => $offset
        ));

        $urls = array();
        foreach ($faqs as $faq) {
            $urls[] = FULL_BASE_URL . $faq['Faq']['moo_href'];
        }

        return $urls;
    }

    public function getLastupdateFaq($faq_id) {
        $faqModel = MooCore::getInstance()->getModel('Faq.Faq');
        $faqResultModel = MooCore::getInstance()->getModel('Faq.FaqResult');
        $faq = $faqModel->findById($faq_id);
        $last_update = $faq['Faq']['modified'];
        $last_result = $faqResultModel->getLastUpdate($faq_id);
        if (!empty($last_result)){
            $last_update = $last_result['FaqResult']['modified'];
        }
        $date=date_create($last_update);
        return date_format($date,"d/m/Y");
    }

    public function getResultSubmitFaq($faq_id) {
        $faqResultModel = MooCore::getInstance()->getModel('Faq.FaqResult');
        $choice['choice'] = 2; //not choise
        $choice['choice_id'] = 0; //not choise
        $uid = MooCore::getInstance()->getViewer(true);
        if ($uid) {
            $result = $faqResultModel->getResults($faq_id, $uid);
            if (!empty($result)) {
                $choice['choice'] = $result[0]['FaqResult']['vote'];
                $choice['choice_id'] = $result[0]['FaqResult']['helpfull_id'];
            }
        }
        return $choice;
    }

    
    public function getImage($item) {
    	
    	$type = key($item);
    	if ($type == 'Faq')
    	{
    		return $this->Storage->getImage('faq/img/no_faq.png');
    	}
    	if ($item[$type]['icon'])
    		return $this->Storage->getUrl($item[$type]['id'], '', $item[$type]['icon'], "fa_category_icon");
    	else
    		return '';
    }
    
    
    public function getBackground($thumb)
    {
    	$thumb = substr($thumb, 1, strlen($thumb) - 1);
    	return $this->Storage->getImage($thumb);
    }
}
