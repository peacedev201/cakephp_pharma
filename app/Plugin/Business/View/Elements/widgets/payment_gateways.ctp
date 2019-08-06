<?php if(!empty($business['BusinessPayment'])):?>
<div class="box2 filter_block bus_payment_method">
    <h3><?php echo __d('business', 'Payment Methods');?></h3>
    <div class="box_content">
        <?php
            echo $this->element('Business.misc/payment_type', array(
                'payments' => $business['BusinessPayment']
            ));
        ?>
    </div>
</div>
<?php endif;?>