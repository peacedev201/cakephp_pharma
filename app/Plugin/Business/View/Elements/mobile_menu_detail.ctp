<?php 
if($is_app)
{
    echo $this->Element('misc/cover');
}
?>
<?php

$businessPackage = $business['BusinessPackage'];
?>
<div class="profile_plg_menu bus-menu-detial ">
    <div class="menu">
        <ul class="list2 menu_top_list">
            <li <?php if(!empty($tab) && $tab == "businesses"):?>class="current"<?php endif;?>>
                <a href="<?php echo $business['Business']['moo_href'];?>"><?php echo __d('business', 'Details');?></a>
            </li>
            <li <?php if(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_PHOTO):?>class="current"<?php endif;?>>
                <a href="<?php echo $business['Business']['moo_hrefphoto'];?>" data-url="" class="json-view"><?php echo __d('business', 'Photos');?></a>
            </li>
            <?php if($is_integrate_store && $this->Business->hasStore($business['Business']['id'])): ?>
            <li <?php if(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_PRODUCT):?>class="current"<?php endif;?>>
                <a href="<?php echo $business['Business']['moo_hrefproduct'];?>" data-url="" class="json-view"><?php echo __d('business', 'Products');?></a>
            </li>
            <?php endif; ?>
            <li <?php if(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_REVIEW):?>class="current"<?php endif;?>>
                <a href="<?php echo $business['Business']['moo_hrefreview'];?>" data-url="" class="json-view"><?php echo __d('business', 'Reviews');?></a>
            </li>

            <li class="dropdown  <?php if(!empty($tab) && in_array($tab, array(BUSINESS_DETAIL_LINK_BRANCH, BUSINESS_DETAIL_LINK_CHECKIN, BUSINESS_DETAIL_LINK_FOLLOWER, BUSINESS_DETAIL_LINK_CONTACT))):?>current<?php endif;?>">
                <a href="javascript:void(0)" id="profile_menu" data-toggle="dropdown">
                    <?php echo __d('business', 'More');?>
                </a>
                <ul aria-labelledby="dropdown-edit" class="dropdown-menu mobileDropdown" for="profile_menu">
                    <?php if($business['Business']['parent_id'] == 0):?>
                        <li <?php if(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_BRANCH):?>class="current"<?php endif;?>>
                            <a href="<?php echo $business['Business']['moo_hrefbranch'];?>" data-url="" class="json-view">
                        <?php echo __d('business', 'Sub Pages');?>
                            </a>
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
            </li>
        </ul>
    </div>
</div>
<?php if(!empty($tab) && $tab == 'businesses'):?>
<div class="mobile_detail_image">
    <?php echo $this->Business->getPhoto($business['Business'], array(
        'prefix' => BUSINESS_IMAGE_SEO_WIDTH.'_', 
        'width' => '140px',
        'id' => 'av-img',
        'class' => 'page-avatar'));
    ?>
</div>
<?php endif;?>