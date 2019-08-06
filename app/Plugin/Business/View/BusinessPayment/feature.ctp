<?php
    echo $this->Html->css(array('Business.business',), array('block' => 'css', 'minify'=>false));
?>
<?php if ($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery", "mooBusiness"], function($, mooBusiness) {
       mooBusiness.initBusinessFeatured();
    });
</script>
<?php endif?>
<div class="featured_business">
    <div class="featured_business_form">
        <form id="featuredForm"  method="post" action="<?php echo $this->request->base ?>/business_payment<?php echo $is_app ? "?app_no_tab=1" : "";?>">
            <div class="">
                <p><?php echo __d('business', 'How many days do you want feature your business?') ?></p>
            </div>
            <div class="">
                <div class="form_content">
                    <ul>
                        <li class="input_featured_day">
                            <div class="">
                                <?php echo $this->Form->text('feature_day', array(
                                    'value' => 30
                                )); ?>
                                <?php echo $this->Form->hidden('price'); ?>
                                <?php echo $this->Form->hidden('business_id', array('value' => $business['Business']['id'])); ?>
                                <?php echo $this->Form->hidden('pay_type', array('value' => 'featured_package')); ?>
                            </div>
                            <div class="">
                                <a class="btn btn-action" ref="<?php echo $featured_price; ?>" id="calFeaturedPrice"><?php echo __d('business', 'Submit') ?></a>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li class="m_10">
                            <div class="">
                                <label>
                                    <span><?php echo __d('business', 'Price') ?>:</span> 
                                    <span><?php echo $currency['Currency']['symbol']; ?></span><span id="featured_price"><?php echo Configure::read('Business.featured_price') * 30;?></span>
                                    <span><?php echo sprintf(__d('business', '(%s per day)'), $currency['Currency']['symbol'].Configure::read('Business.featured_price')); ?></span>
                                </label>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <li>       
                            <button style="display: none;" id="featuredBtn" type="submit" class="btn btn-action"><?php echo __d('business', 'Feature it now') ?></button>
                        </li>
                    </ul>
                    <div class="error-message" id="errorMessage" style="display:none"></div>
                </div>
            </div>
        </form>
    </div>
</div>