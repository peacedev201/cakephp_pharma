<?php
echo $this->Html->css(array(
    'Store.store',
    ), array('block' => 'css', 'minify'=>false));

?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_store'), 
    'object' => array('$', 'store_store')
));?>
    store_store.initGlobal();
<?php $this->Html->scriptEnd(); ?>
<?php echo $this->Element('Store.mobile/mobile_menu');?>
    <div class="main-container default-page">
        <div class="page-content default-page">
            <article class="post-7 page type-page status-publish hentry jsn-master" id="post-7">
                <div class="checkout-wrap">
                    <ul class="checkout-bar">
                        <li class="visited first">
                            <a href="#"><?php echo  __d('store', 'Carts');?></a>
                        </li>
                        <li class="previous visited">
                            <a href="#"><?php echo  __d('store', 'Billing & Payment');?></a>
                        </li>
                        <li class="complete active"><?php echo  __d('store', 'Complete');?></li>
                    </ul>
                </div>
                <div class="entry-content">
                    <div class="pay-success">
                        <div class="alert-ct">
                            <h4><?php echo __d('store', 'Successfully checkout');?></h4>
                            <p>
                                <?php echo __d('store', 'You have successfully ordered');?>
                                <?php echo __d('store', 'We will contact with you shortly');?>. 
                                <br><?php echo  __d('store', 'Thanks for your order');?>
                            </p>
                            <ul class="">
                                <li>
                                    <a href="<?php echo STORE_URL;?>"><?php echo  __d('store', 'Continue shopping');?> »</a>
                                </li>
                                <li>
                                    <a href="<?php echo STORE_URL;?>carts"><?php echo  __d('store', 'Back to cart');?> »</a>
                                </li>
                                <?php if($uid > 0):?>
                                <li>
                                    <a href="<?php echo STORE_URL.'?type=my_orders';?>"><?php echo  __d('store', 'Go to your orders');?> »</a>
                                </li>
                                <?php endif;?>
                            </ul>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>