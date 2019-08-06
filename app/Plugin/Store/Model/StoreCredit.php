<?php 
App::uses('StoreAppModel', 'Store.Model');
class StoreCredit extends StoreAppModel
{
    public function isAllowCredit()
    {
        if(CakePlugin::loaded("Credit") && Configure::read('Store.store_integrate_credit') && Configure::read('Credit.credit_enabled'))
        {
            return true;
        }
        return false;
    }
    
    public function exchangeToCredit($amount)
    {
        return $amount * Configure::read('Credit.credit_currency_exchange');
    }
    
    public function chargeCredit($price, $item_id)
    {
        if (Configure::read('Credit.credit_enabled')) {
            $uid = MooCore::getInstance()->getViewer(true);
            $mBalances = MooCore::getInstance()->getModel('Credit.CreditBalances');
            return $mBalances->pluginUseCredit($price, 'buy_product', 'store_store_order', $uid, $item_id);
        }
        return true;
    }
    
    public function addCreditForSeller($amount, $user_id, $item_id)
    {
        $mBalances = MooCore::getInstance()->getModel('Credit.CreditBalances');
        $mCreditLogs = MooCore::getInstance()->getModel('Credit.CreditLogs');
        $amount = floatval($amount);
        $mBalances->addCredit($user_id, $amount);
        $mCreditLogs->addLogByType('sell_product', $amount, $user_id, 'store_store_order', $item_id);
    }
    
    public function addCreditProfitForSiteAdmin($amount, $item_id)
    {
        $mBalances = MooCore::getInstance()->getModel('Credit.CreditBalances');
        $mCreditLogs = MooCore::getInstance()->getModel('Credit.CreditLogs');
        $amount = floatval($amount);
        $user_id = 1;
        $mBalances->addCredit($user_id, $amount);
        $mCreditLogs->addLogByType('store_profit', $amount, $user_id, 'store_store_order', $item_id);
    }
    
    public function getCurrentCreditBalance()
    {
        $mCreditBalances = MooCore::getInstance()->getModel("Credit.CreditBalances");
        $item = $mCreditBalances->getBalancesUser(MooCore::getInstance()->getViewer(true));
        if(isset($item['CreditBalances']['current_credit']))
        {
            return $item['CreditBalances']['current_credit'];
        }
        return 0;
    }
}