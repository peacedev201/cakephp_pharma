<div class="title-modal">
    <?php echo __d('ads', 'Placement info');?>              
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span></button>
</div>
<div class="modal-body">
    <?php if($ads_placement != null):
        $adsHelper = MooCore::getInstance()->getHelper('Ads_Ads');
        $ads_position = $ads_placement['AdsPosition'];
        $ads_placement = $ads_placement['AdsPlacement'];
    ?> 
        <div class="col-md-12 ads-placement">
            <img src="<?php echo Router::url('/', true).ADS_POSITION_IMAGE_URL.$ads_position['image'];?>" />
            <h1><?php echo $ads_placement['name'];?></h1>
            <p>
                <?php echo sprintf(__d('ads', '%s views or %s clicks'), (empty($ads_placement['view_limit']) || $ads_placement['view_limit'] == 0) ? __d('ads', 'Unlimited') : $ads_placement['view_limit'], (empty($ads_placement['click_limit']) || $ads_placement['click_limit'] == 0) ? __d('ads', 'Unlimited') : $ads_placement['click_limit']);?>
            </p>
            <p class="ads-placement-price">
                <?php echo $adsHelper->formatMoney($ads_placement['price'], $ads_placement['period']);?>
            </p>
        </div>
        <div class="clear"></div>
    <?php endif;?>
</div>