<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreSetting extends StoreAppModel
{
    function loadHelpData()
    {
        $data = array(
            'help_currency_position' => __d('store', 'This controls the position of currency symbol. Example: left $5, right 5$'),
            'help_currency' => __d('store', 'This controls what currency prices are listed in the catalog and which currency gateways will take payments in.', 'Store' ),
            'title_seo_currency' => __d('store', 'This is the SEO title of your store.', 'Store' ),
            'descp_seo_currency' => __d('store', 'This is the SEO description of your store.', 'Store' ),
            'keyword_seo_currency' => __d('store', 'This is the SEO keywork of your store.', 'Store' ),
            'slider_image' => __d('store', 'Resolution of image must be 750x400 for best quality  .', 'Store' ),
            'verify_comment' => __d('store', 'Allow store onwer verify comment before publishing.', 'Store' ),
            'logo_tip' => __d('store', 'Resolution of logo must be 200x60 for best quality .', 'Store' ),
        );
        return $data;
    }
    
    function loadCurrencyList()
    {
        $mCurrency = MooCore::getInstance()->getModel('Currency');

        $data = $mCurrency->find('all', array(
            'fields' => array('Currency.name', 'CONCAT_WS("|", Currency.currency_code, Currency.symbol) as code'),
        ));
        $result = null;
        if($data != null)
        {
            foreach($data as $item)
            {
                $result[$item[0]['code']] = $item['Currency']['name'];
            }
        }
        return $result;
    }
    
    function loadCurrencyCode($id)
    {
        $mCurrency = MooCore::getInstance()->getModel('Currency');
        
        $data = $mCurrency->findById($id);
        if($data != null)
        {
            return $data['Currency']['currency_code'];
        }
        return '';
    }
    
    function loadCurrentCurrency()
    {
        $mCurrency = MooCore::getInstance()->getModel('Currency');
        
        return $mCurrency->findById($this->setting('currency'));
    }
    
    function loadSettings($store_id = null)
    {
        if($store_id == null)
        {
            $store_id = Configure::read('store.store_id');
        }
        $settings = $this->find('list', array(
            'conditions' => array(
                'StoreSetting.store_id' => $store_id
            ),
            'fields' => array('StoreSetting.setting_key', 'StoreSetting.setting_value')
        ));
        
        $currency = array();
        if(!empty($settings['currency']))
        {
            $currency = explode('|', $settings['currency']);
        }
        $settings['currency_code'] = !empty($currency[0]) ? $currency[0] : '';
        $settings['currency_symbol'] = !empty($currency[1]) ? $currency[1] : '';
        return $settings;
    }
    
    function setting($key)
    {
        $data = $this->findByStoreIdAndSettingKey(Configure::read('store.store_id'), $key);
        if($data != null)
        {
            return $data['StoreSetting']['setting_value'];
        }
        return '';
    }
    
    function saveSettings($data, $store_id = null)
    {
        if($data != null)
        {
            if($store_id == null)
            {
                $store_id = Configure::read('store.store_id');
            }
            $success = true;
            $settings = $this->find('list', array(
                'conditions' => array(
                    'StoreSetting.store_id' => $store_id
                ),
                'fields' => array('StoreSetting.setting_key')
            ));
            foreach($data as $key => $value)
            {
                //$this->create();
                if(in_array($key, $settings))
                {
                    if(!$this->updateAll(array(
                        'setting_value' => "'$value'"
                    ), array(
                        'store_id' => $store_id,
                        'setting_key' => $key
                    )))
                    {
                        $success = false;
                        break;
                    }
                }
                else 
                {
                    $this->create();
                    if(!$this->save(array(
                        'store_id' => $store_id,
                        'setting_key' => $key,
                        'setting_value' => $value
                    )))
                    {
                        $success = false;
                        break;
                    }
                }
            }
        }
        return true;
    }
    
    public function integrateCredit()
    {
        if(CakePlugin::loaded("Credit") && Configure::read('Credit.credit_enabled') && Configure::read('Store.store_integrate_credit')){
            $actionTypeModel = MooCore::getInstance()->getModel('Credit.CreditActiontypes');
            $action_type = $actionTypeModel->getActionTypeFormModule('buy_product');
            if(empty($action_type))
            {
                $data = array(
                    'action_type' => 'buy_product',
                    'action_type_name' => 'Buy Product',
                    'action_module' => 'Store',
                    'action_name' => 'Buy Product',
                    'type' => 'model',
                    'plugin' => 'Store',
                    'show' => 0
                );
                $actionTypeModel->create();
                $actionTypeModel->save($data);
            }
            
            $action_type = $actionTypeModel->getActionTypeFormModule('sell_product');
            if(empty($action_type))
            {
                $data = array(
                    'action_type' => 'sell_product',
                    'action_type_name' => 'Sell Product',
                    'action_module' => 'Store',
                    'action_name' => 'Sell Product',
                    'type' => 'model',
                    'plugin' => 'Store',
                    'show' => 0
                );
                $actionTypeModel->create();
                $actionTypeModel->save($data);
            }
            
            $action_type = $actionTypeModel->getActionTypeFormModule('store_profit');
            if(empty($action_type))
            {
                $data = array(
                    'action_type' => 'store_profit',
                    'action_type_name' => 'Store Profit',
                    'action_module' => 'Store',
                    'action_name' => 'Store Profit',
                    'type' => 'model',
                    'plugin' => 'Store',
                    'show' => 0
                );
                $actionTypeModel->create();
                $actionTypeModel->save($data);
            }
        }
    }
}