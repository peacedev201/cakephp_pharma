<div class="profile_plg_menu" id="mobile_menu_detail_info">
    <div class="menu">
        <ul class="list2 menu_top_list">
            <?php if($bClaim):?>
            <li>
                <a href="javascript:void(0)" data-id="box_claim">
                    <i class="material-icons">directions</i>  
                    <span><?php echo __d('business', 'Claim');?></span>
                </a>
            </li>
            <?php endif;?>
            <?php if(!empty($business['facebook']) || !empty($business['twitter']) || !empty($business['linkedin']) || !empty($business['youtube']) || !empty($business['instagram'])):?>
            <li>
                <a href="javascript:void(0)" data-id="box_social">
                    <i class="material-icons">room</i>   
                    <span><?php echo __d('business', 'Social');?></span>
                </a>
            </li>
            <?php endif;?>
            <li>
                <a href="javascript:void(0)" data-id="box_raring">
                    <i class="material-icons">chat</i>   
                    <span><?php echo __d('business', 'Rating');?></span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" data-id="box_hours">
                    <i class="material-icons">query_builder</i>    
                    <span><?php echo __d('business', 'Hours');?></span>
                </a>
            </li>
            <?php if(!empty($business['BusinessPayment'])):?>
            <li>
                <a href="javascript:void(0)" data-id="box_payment">
                    <i class="material-icons">payment</i>    
                    <span><?php echo __d('business', 'Payment');?></span>
                </a>
            </li>
            <?php endif;?>
        </ul>
    </div>
</div>
<div class="menu_detail_info">
    <div class="detail_info_item" id="box_raring">
        <?php echo $this->Element('widgets/rating');?>
    </div>
    <div class="detail_info_item" id="box_hours">
        <?php echo $this->Element('widgets/open_hours');?>
    </div>
    <div class="detail_info_item" id="box_payment">
        <?php echo $this->Element('widgets/payment_gateways');?>
    </div>
    <div class="detail_info_item" id="box_claim">
        <?php echo $this->Element('widgets/claim');?>
    </div>
    <div class="detail_info_item" id="box_social">
        <?php echo $this->Element('widgets/social_link');?>
    </div>
</div>