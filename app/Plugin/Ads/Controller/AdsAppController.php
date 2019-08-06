<?php 
App::uses('AppController', 'Controller');
class AdsAppController extends AppController{
    public function beforeFilter() 
    {
        parent::beforeFilter();
        
        //default currency
        $currency = $this->loadDefaultGlobalCurrency();
        if($currency != null)
        {
            Configure::write('Ads.currency_symbol', $currency['Currency']['symbol']);
            Configure::write('Ads.currency_code', $currency['Currency']['currency_code']);
        }
    }
    
    function loadDefaultGlobalCurrency()
    {
        $mCurrency = MooCore::getInstance()->getModel('Currency');
        return $mCurrency->findByIsDefault(1);
    }
    
    protected function _redirectError($msg, $url)
    {
        if($msg != null)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-danger fade in'
            ));
        }
        $this->redirect($url);
    }
    
    protected function _redirectSuccess($msg, $url)
    {
        if($msg != null)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-success fade in'
            ));
        }
        $this->redirect($url);
    }

    protected function _jsonSuccess($msg, $flashMsg = false, $params = null)
    {
        $this->autoRender = false;
        if($flashMsg)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-success fade in'
            ));
        }
        $data = array(
            'result' => 1,
            'message' => $msg
        );
        if($params != null)
        {
            $data = array_merge($data, $params);
        }
        echo json_encode($data);
        exit;
    }
    
    protected function _jsonError($msg, $flashMsg = false, $params = null)
    {
        $this->autoRender = false;
        if($flashMsg)
        {
            $this->Session->setFlash($msg, 'default', array(
                'class' => 'Metronic-alerts alert alert-danger fade in'
            ));
        }
        $data = array(
            'result' => 0,
            'message' => $msg
        );
        if($params != null)
        {
            $data = array_merge($data, $params);
        }
        
        echo json_encode($data);
        exit;
    }
}