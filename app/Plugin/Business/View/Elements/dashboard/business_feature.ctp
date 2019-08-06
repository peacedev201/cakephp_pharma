<?php
echo $this->Html->script(array('Business.business',), array('inline' => false));
echo $this->Html->css(array('Business.business',), array('block' => 'css', 'minify'=>false));
echo $this->addPhraseJs(array(
    'f_must_interger' => __d('business', 'number of days must be interger and greater than 0'),
    'f_not_blank' => __d('business', 'Please add number of days you want to feature'),
));
?>


<div class="featured_business">
    <h3>
        <?php echo __d('business', 'Featured my business'); ?>
    </h3>

    <div class="feature_description">   
        <?php if ($business['Business']['featured']): ?>
            <?php echo __d('business', "Your page is featured page now. I will expire on %s", date('M, d, Y', strtotime($expired_time))) ?>
        <?php else: ?>
            <p><?php echo __d('business', 'Place this page on top of the list and highlight in the category it belong to') ?></p>
            <p><?php echo __d('business', 'Place this business on the slideshow.') ?></p>
        <?php endif; ?>
        <button class="btn btn-action" data-toggle="modal" data-target="#businessModal" href="<?php echo $this->request->base ?>/business_payment/feature/<?php echo $business['Business']['id']; ?>">
            <?php if (!$business['Business']['featured']): ?>
                <?php echo __d('business', 'Feature it now') ?>
            <?php else: ?>
                <?php echo __d('business', 'Buy more featured days') ?>
            <?php endif; ?>
        </button>  
    </div>
</div>
