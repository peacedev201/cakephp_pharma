<?php
    if ($is_integrate_to_business) 
    {
        $business = $store['Business'];
    }
    $store = $store['Store'];
?>

<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
    <?php echo $this->Element('Store.manager_menu'); ?>
<?php $this->end(); ?>

<script type="text/template" id="buy_feature_store_confirm">
    <?php echo sprintf(__d('store', 'This feature costs %s for %s day(s). If this store already set as featured, expiration date will be expanded. Are you sure you want to buy?'), $this->Store->formatMoney($package['StorePackage']['price']), $package['StorePackage']['period']);?>
</script>
<div class="bar-content">
    <?php echo $this->Element('Store.mobile/mobile_manager_menu'); ?>
    <div class="content_center">
        <div class="post_body">
            <div class="mo_breadcrumb seller-breadcrumb">
                <?php if(!$allowSeller):?>
                <div id="flashMessage" class="Metronic-alerts alert alert-danger fade in">
                    <?php echo __d('store', 'This seller is waiting for verification or currently unavailable. Please contact site admin for more information.');?>
                </div>
                <?php endif;?>
                <h1 class="pull-left"><?php echo __d('store', 'Seller Information'); ?></h1>
                <?php if($allowSeller):?>
                <a class="btn btn-primary pull-right" href="<?php echo STORE_URL;?>create/<?php echo $store['id'];?>">
                    <?php echo __d('store', 'Edit'); ?>
                </a>
                <?php endif;?>
            </div>
            <ul class="list6 info info2 list-store-seller">
                <?php if(Configure::read('Store.store_buy_featured_store')):?>
                <li>
                    <span class="<?php echo $is_app ? "col-xs-4" : "col-xs-2";?>"><?php echo __d('store', 'Featured'); ?>:</span> 
                    <span class="col-xs-8">
                        <?php if($store['featured'] && $store['unlimited_feature']):?>
                            <?php echo __d('store', 'Unlimited'); ?>
                        <?php elseif($store['featured']):?>
                            <?php echo __d('store', 'Expire').": ".date('M d Y', strtotime($store['feature_expiration_date']));?>
                            <a class="btn btn-primary buy_featured_store" href="javascript:void(0)">
                                <?php echo __d('store', 'Upgrade');?>
                            </a>
                        <?php else:?>
                            <a class="btn btn-primary buy_featured_store" href="javascript:void(0)">
                                <?php echo __d('store', 'Buy'); ?>
                            </a>
                        <?php endif;?>
                    </span> 
                </li>
                <?php endif;?>
                <?php if ($is_integrate_to_business) :?>
                    <li>
                        <span class="<?php echo $is_app ? "col-xs-4" : "col-xs-2";?>"><?php echo __d('store', 'Business Page'); ?>:</span> 
                        <span class="col-xs-8"><?php echo $business['name'];?></span>
                    </li>
                <?php endif; ?>
                <li>
                    <span class="<?php echo $is_app ? "col-xs-4" : "col-xs-2";?>"><?php echo __d('store', 'Name'); ?>:</span> 
                    <span class="col-xs-8"><?php echo $store['name'];?></span> 
                </li>
                <li>
                    <span class="<?php echo $is_app ? "col-xs-4" : "col-xs-2";?>"><?php echo __d('store', 'Email'); ?>:</span> 
                    <span class="col-xs-8"><?php echo $store['email'];?></span> 
                </li>
                <li>
                    <span class="<?php echo $is_app ? "col-xs-4" : "col-xs-2";?>"><?php echo __d('store', 'Address'); ?>:</span> 
                    <span class="col-xs-8"><?php echo $store['address'];?></span>
                </li>
                <li>
                    <span class="<?php echo $is_app ? "col-xs-4" : "col-xs-2";?>"><?php echo __d('store', 'Phone'); ?>:</span> 
                    <span class="col-xs-8"><?php echo $store['phone'];?></span> 
                </li>
                <li>
                    <span class="<?php echo $is_app ? "col-xs-4" : "col-xs-2";?>"><?php echo __d('store', 'Payments'); ?>:</span> 
                    <span class="col-xs-8"><?php echo $this->Store->getPaymentName($store['payments']);?></span> 
                </li>
                <?php if(!empty($store['paypal_email'])):?>
                <li>
                    <span class="<?php echo $is_app ? "col-xs-4" : "col-xs-2";?>"><?php echo __d('store', 'Paypal Email'); ?>:</span> 
                    <span class="col-xs-8"><?php echo $store['paypal_email'];?></span> 
                </li>
                <?php endif;?>
            </ul>
        </div>
    </div>
</div>