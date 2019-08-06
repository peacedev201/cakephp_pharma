<?php

if($aAds): ?>
<?php $item_status = array(
                        'pending' => __d('ads', 'Pending'),
                        'active' => __d('ads', 'Active'),
                        'disable' => __d('ads', 'Disable'),
); ?>
<?php foreach($aAds as $ads):  ?>
<?php $adsCampaign = $ads['AdsCampaign']; ?>
        <div class="ads_detail_content">
            <div style="display:none" class="col-md-12 ads_message text-right"></div>
            <div class="ads-wrapper">
            <div class="ads-image">
                <img class="img-responsive" src="<?php echo $this->request->base . ADS_BANNER_URL . $adsCampaign['ads_image'] ?>" />
            </div>
            <div class="ads-info">
                <div class="ads-info-wrap">
            <?php if(!empty($adsCampaign['ads_title'])) : ?>
                    <div class="ads-title"><h5><?php echo h($adsCampaign['ads_title']); ?></h5></div>
            <?php endif; ?>
                    <div class="list_option">
                        <div class="dropdown">
                            <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">edit</i>
                            </button>
                            <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
                            <?php if(!$adsCampaign['payment_status'] || $adsCampaign['is_expired']): ?>
                                <li>  
                                    <a href="#/" onclick="handleAdsAction('<?php echo $adsCampaign['id']; ?>', 'delete', this)"><?php echo __d('ads', 'Delete') ?></a>
                                </li>
                            <?php endif; ?>
                            <?php if($adsCampaign['item_status'] == ADS_STATUS_ACTIVE || $adsCampaign['item_status'] == ADS_STATUS_DISABLE ): ?>
                                <?php if ($adsCampaign['is_hide']):?>
                                <li>  
                                    <a class="ads_show_<?php echo $adsCampaign['id']; ?>" href="#/" onclick="handleAdsAction('<?php echo $adsCampaign['id']; ?>', 'show', this)"><?php echo __d('ads', 'Show') ?></a>
                                    <a style="display: none;" class="ads_hide_<?php echo $adsCampaign['id']; ?>" href="#/" onclick="handleAdsAction('<?php echo $adsCampaign['id']; ?>', 'hide', this)"><?php echo __d('ads', 'Hide') ?></a>
                                </li>
                                <?php else: ?>
                                <li>  
                                    <a class="ads_hide_<?php echo $adsCampaign['id']; ?>" href="#/" onclick="handleAdsAction('<?php echo $adsCampaign['id']; ?>', 'hide', this)"><?php echo __d('ads', 'Hide') ?></a>
                                    <a style="display:none" class="ads_show_<?php echo $adsCampaign['id']; ?>" href="#/" onclick="handleAdsAction('<?php echo $adsCampaign['id']; ?>', 'show', this)"><?php echo __d('ads', 'Show') ?></a>
                                </li>
                                <?php endif; ?>
                            <?php endif; ?>

                                <li class="seperate"></li>
                            </ul>
                        </div>
                    </div>
                    <div class="ads-desc">
                        <p><?php echo h($adsCampaign['description']); ?></p>
                        <p>
                    <?php $days = ($ads['AdsPlacement']['period'] > 1)?__d('ads','days'):__d('ads','day')  ?>
                            <span class=""><?php echo __d('ads', 'Placement') ?></span> : <?php echo $ads['AdsPlacement']['name']; ?></br>
                            <span class=""><?php echo __d('ads', 'Current Click') ?></span>: <?php echo $adsCampaign['click_count'] . '/' . $ads['AdsPlacement']['click_limit'] ?>
                            <span class=""><?php echo __d('ads', 'Current View') ?></span>: <?php echo $adsCampaign['view_count'] . '/' . $ads['AdsPlacement']['view_limit'] ?></br>
                            <span class=""><?php echo __d('ads', 'Ad Status') ?></span> : <?php echo $item_status[$adsCampaign['item_status']]; ?> <span class="ads_cur_status"><?php echo $adsCampaign['item_status']==ADS_STATUS_ACTIVE?('/'. $aAdsStatus[$adsCampaign['is_hide']]):''; ?></span></br>
                            <span class=""><?php echo __d('ads', 'Price') ?></span> : <?php echo $ads['AdsPlacement']['price'] . '/' . $ads['AdsPlacement']['period'] .' '.$days;?>
                            <span class=""><?php echo __d('ads', 'Payment Status') ?></span>: <?php echo $aPaymentStatus[$adsCampaign['payment_status']] ?></br>

                        </p>
                        <p>
                    <?php if(!empty($adsCampaign['link_report'])): ?>
                            <span><a style="text-decoration: none;" target="_blank" href="<?php echo $adsCampaign['link_report']; ?>"><?php echo __d('ads','View details report') ?></a></span>
                    <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            </div>
        </div> 
<?php endforeach; ?>
<?php endif;?>
