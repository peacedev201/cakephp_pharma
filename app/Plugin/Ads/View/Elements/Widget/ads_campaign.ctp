
    <?php $linkClick= $this->request->base.'/ads/update_click_count/'.$campaign['AdsCampaign']['id'];?>
    <?php if($ads_type == "html"): ?>
    <div class="row commercial_item">
            <?php if($campaign['AdsCampaign']['ads_image']): ?>
        <div class="col-md-12 ads_image" >
            <a data-value="<?php echo $campaign['AdsCampaign']['id']; ?>" href="<?php echo $linkClick; ?>" target="_blank"><img  src="<?php echo  FULL_BASE_URL . $this->request->webroot.'uploads/commercial/'. $campaign['AdsCampaign']['ads_image'] ?>"></a>
        </div>
            <?php endif; ?>
        <div class="col-md-12 ads_title">
            <div class="col-md-12">
                <a data-value="<?php echo $campaign['AdsCampaign']['id']; ?>" href="<?php echo $linkClick; ?>" target="_blank"><?php echo h($campaign['AdsCampaign']['ads_title']); ?></a>
            </div>
            <?php if($campaign['AdsCampaign']['description']): ?>
            <div class="col-md-12 ads_description">
                <p><?php echo h($campaign['AdsCampaign']['description']); ?></p>
            </div> 
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php if($ads_type == 'image'): ?>
        <div class="row commercial_item">
            <div><a data-value="<?php echo $campaign['AdsCampaign']['id']; ?>" href="<?php echo $linkClick; ?>" target="_blank"><img class="img-responsive" src="<?php echo  FULL_BASE_URL . $this->request->webroot.'uploads/commercial/'. $campaign['AdsCampaign']['ads_image'] ?>"></a></div>
        </div>
    <?php endif; ?>
