<?php 
echo $this->Html->css(array('Business.business-widget', 'Business.star-rating')); ?>
<?php if ($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery", "mooBehavior", "mooBusiness"], function($, mooBehavior, mooBusiness) {
        mooBehavior.initMoreResults();
        mooBusiness.initReviewStar();
    });
</script>
<?php else:?>
    <?php $this->Html->scriptStart(array(
        'inline' => false, 
        'domReady' => true, 
        'requires' => array('jquery', 'mooBusiness'. 'mooBehavior'), 
        'object' => array('$', 'mooBusiness', 'mooBehavior')
    ));?>
        mooBusiness.initReviewStar();
        mooBehavior.initMoreResults();
    <?php $this->Html->scriptEnd(); ?>
<?php endif?>
<?php if ($businesses != null):  ?>
    <ul class="bussiness-list">
        <?php
            foreach ($businesses as $business):
        ?> 
            <?php echo $this->Element('Business.misc/business_item', array(
                'business' => $business
            ));?>
        <?php endforeach;?>
        <?php if (count($businesses) > 0 && !empty($more_url)): ?>
            <?php $this->Html->viewMore($more_url) ?>
        <?php endif; ?>
    </ul>   
<?php else:?> 
    <?php echo '<div class="clear" align="center">'.__d('business', 'No more results found').'</div>';?>
<?php endif;?>
