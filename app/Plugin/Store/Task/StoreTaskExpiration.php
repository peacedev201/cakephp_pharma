<?php
App::import('Cron.Task','CronTaskAbstract');
class StoreTaskExpiration extends CronTaskAbstract
{
    public function execute()
    {
        $mStoreTransaction = MooCore::getInstance()->getModel('Store.StoreTransaction');
        $mStoreProduct = MooCore::getInstance()->getModel('Store.StoreProduct');
        $mStore = MooCore::getInstance()->getModel('Store.Store');
        $cMooMail = MooCore::getInstance()->getComponent('Mail.MooMail');
        
        //expire product
        $products = $mStoreTransaction->getExpiredFeatureProducts();
        if($products != null)
        {
            foreach($products as $product)
            {
                $product = $product['StoreProduct'];
                if($mStoreProduct->updateAll(array(
                    "StoreProduct.featured" => 0,
                    "StoreProduct.feature_expiration_date" => "NULL"
                ), array(
                    "StoreProduct.id" => $product["id"]
                )))
                {
                    //notification
                    $mStore->sendNotification($product['user_id'], $product['user_id'], 'product_featured_expried', $product['moo_url'], $product['name'], 'Store');

                    //send mail
                    $cMooMail->send($product['user_id'], 'store_featured_product_expiration', array( 
                        'product_name' => $product['name'],
                        'link' => Router::url('/', true).'stores/manager/products/?product_id='.$product['id']
                    ));
                }
            }
        }
        
        //expire store
        $stores = $mStoreTransaction->getExpiredFeatureStores();
        if($stores != null)
        {
            foreach($stores as $store)
            {
                $store = $store['Store'];
                if($mStore->updateAll(array(
                    "Store.featured" => 0,
                    "Store.feature_expiration_date" => "''"
                ), array(
                    "Store.id" => $store["id"]
                )))
                {
                    //notification
                    $mStore->sendNotification($store['user_id'], $store['user_id'], 'store_featured_expried', $store['moo_url'], $store['name'], 'Store');

                    //send mail
                    $cMooMail->send($store['user_id'], 'store_featured_store_expiration', array( 
                        'store_name' => $store['name'],
                        'link' => Router::url('/', true).'stores/manager/'
                    ));
                }
            }
        }
        
        //reminder expired featured product
        $products = $mStoreTransaction->getFeaturedProductExpirationReminder();
        if($products != null)
        {
            foreach($products as $product)
            {
                $product = $product['StoreProduct'];
                
                $mStoreProduct->updateAll(array(
                    "StoreProduct.sent_expiration_email" => 1,
                ), array(
                    "StoreProduct.id" => $product["id"]
                ));
                
                //send mail
                $cMooMail->send($product['user_id'], 'store_featured_product_expiration_reminder', array( 
                    'product_name' => $product['name'],
                    'expire_time' => date('Y-m-d', strtotime($product['feature_expiration_date'])),
                    'link' => Router::url('/', true).'stores/manager/products/?product_id='.$product['id']
                ));
            }
        }
        
        //reminder expired featured store
        $stores = $mStoreTransaction->getFeaturedStoreExpirationReminder();
        if($stores != null)
        {
            foreach($stores as $store)
            {
                $store = $store['Store'];
                
                $mStore->updateAll(array(
                    "Store.sent_expiration_email" => 1,
                ), array(
                    "Store.id" => $store["id"]
                ));
                
                //send mail
                $cMooMail->send($store['user_id'], 'store_featured_store_expiration_reminder', array( 
                    'store_name' => $store['name'],
                    'expire_time' => date('Y-m-d', strtotime($store['feature_expiration_date'])),
                    'link' => Router::url('/', true).'stores/manager/'
                ));
            }
        }
    }
}