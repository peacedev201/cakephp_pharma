<div class="bar-content">
    <div class="content_center">
        <div class="mo_breadcrumb">
            <h1 class="text-all-bu" ><?php echo __d('business', 'All Businesses');?></h1>
            <a class="button button-action topButton button-mobi-top" href="<?php echo $this->request->base."/businesses/create";?>">
                <?php echo __d('business', 'Add Business');?>
            </a>
        </div>
        <?php if($businesses != null):?>
            <ul id="list-content" class="bussiness-list">
                <?php echo $this->Element('Business.lists/business_list', array(
                    'businesses' => $businesses
                ));?>
            </ul>
        <?php else: ?>
            <div class="clear" align="center"><?php echo __d('business', 'No more results found') ?></div>
        <?php endif;?>
    </div>
</div>