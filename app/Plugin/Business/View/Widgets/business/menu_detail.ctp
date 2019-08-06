<?php 
    $businessPackage = $business['BusinessPackage'];
?>
<div class="box2">
    <div class="box_content">
    <div class="text-center">
        <?php echo $this->Business->getPhoto($business['Business'], array(
            'prefix' => BUSINESS_IMAGE_SMALL_WIDTH.'_', 
            'width' => '180px',
            'id' => 'av-img',
            'class' => 'page-avatar'));
        ?>
        </div>

        <h1 class="bus-detail-title" style="margin-bottom: 0; font-size: 18px;">
            <?php echo $business['Business']['name']; ?>
        </h1>

        <div class="bus-detail-title-action">
            <?php if($business['Business']['verify']):?>
                <i class="verify_bus" title="<?php echo __d('business', 'Verified');?>"></i>
            <?php else:?>
                <i class="unverify_bus" title="<?php echo __d('business', 'Unverified');?>"></i>
            <?php endif;?>
            <?php if($businessPackage['favourite']):?>
                <?php echo $this->element('Business.misc/business_favourite', array(
                    'is_favourite' => $is_favourite,
                    'business_id' => $business['Business']['id']
                ));?>
            <?php endif;?>
        </div>
        
        <div class="menu block-body menu_top_list">
            <ul class="list2 menu-list">
                <li <?php if(!empty($tab) && $tab == "businesses"):?>class="current"<?php endif;?>>
                    <a href="<?php echo $business['Business']['moo_href'];?>"><?php echo __d('business', 'Details');?></a>
                </li>
                <li <?php if(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_PHOTO):?>class="current"<?php endif;?>>
                    <a href="<?php echo $business['Business']['moo_hrefphoto'];?>" data-url="" class="json-view"><?php echo __d('business', 'Photos');?></a>
                </li>
                <?php if($is_integrate_store && $this->Business->hasStore($business['Business']['id'])): ?>
                    <li <?php if(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_PRODUCT):?>class="current"<?php endif;?>>
                        <a href="<?php echo $business['Business']['moo_hrefproduct'];?>" data-url="" data-id="<?php echo $business['Business']['id'];?>" id="business_product" class="json-view"><?php echo __d('business', 'Products');?></a>
                    </li>
                <?php endif; ?>
                <li <?php if(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_REVIEW):?>class="current"<?php endif;?>>
                    <a href="<?php echo $business['Business']['moo_hrefreview'];?>" data-url="" class="json-view"><?php echo __d('business', 'Reviews');?></a>
                </li>
                <?php if($business['Business']['parent_id'] == 0):?>
                <li <?php if(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_BRANCH):?>class="current"<?php endif;?>>
                    <a href="<?php echo $business['Business']['moo_hrefbranch'];?>" data-url="" class="json-view"><?php echo __d('business', 'Sub Pages');?></a>
                </li>
                <?php endif;?>
                <li <?php if(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_CHECKIN):?>class="current"<?php endif;?>>
                    <a href="<?php echo $business['Business']['moo_hrefcheckin'];?>" data-url="" class="json-view"><?php echo __d('business', 'Check-ins');?></a>
                </li>
                <li <?php if(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_FOLLOWER):?>class="current"<?php endif;?>>
                    <a href="<?php echo $business['Business']['moo_hreffollower'];?>" data-url="" class="json-view"><?php echo __d('business', 'Followers');?></a>
                </li>
                <?php if($businessPackage['contact_form']):?>
                <li <?php if(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_CONTACT):?>class="current"<?php endif;?>>
                    <a href="<?php echo $business['Business']['moo_hrefcontact'];?>" data-url="" class="json-view"><?php echo __d('business', 'Contact');?></a>
                </li>
                <?php endif;?>
            </ul>
        </div>
    </div>
</div>