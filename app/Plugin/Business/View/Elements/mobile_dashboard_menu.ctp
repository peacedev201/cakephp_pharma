<div class="business-page ">
    <h1>
        <a href="<?php echo $business['Business']['moo_href'];?>">
                <?php echo $business['Business']['name'];?>
        </a>
    </h1>
    <div class="dashboard_action bussiness_dashboard ">
            <?php if($business['Business']['status'] == BUSINESS_STATUS_APPROVED && $permission_can_upgrade_page): ?>
        <p class="current_package">
                <?php echo __d('business', 'Current Package');?>: <?php echo $business['BusinessPackage']['name'];?>
        </p>
            <?php endif; ?>
            <?php if($business['Business']['user_id'] == $uid && (!Configure::read('Business.business_auto_approve') && $business['Business']['status'] != BUSINESS_STATUS_APPROVED) || $business['Business']['status'] != BUSINESS_STATUS_APPROVED):?>
        <div class="small" id="flashMessage">
                    <?php echo __d('business', 'Your page is pending for approval!');?>
        </div>
            <?php endif;?>
            <?php if($business['Business']['user_id'] == $uid && ($business['Business']['status'] == BUSINESS_STATUS_REJECTED || $business['Business']['status'] == BUSINESS_STATUS_PENDING) && (empty($claim_id) && empty($business['Business']['claim_id']))):?>
        <a href="<?php echo $url.'submit_for_reviewing/'.$business['Business']['id'];?><?php echo $is_app ? "?app_no_tab=1" : "";?>" class="button">
                <?php echo __d('business', 'Submit for reviewing again')?>
        </a>
            <?php endif;?>
            <?php if($business['Business']['status'] == BUSINESS_STATUS_APPROVED && $permission_can_upgrade_page): ?>
        <a class="btn btn-action" href="<?php echo $this->request->base;?>/businesses/dashboard/upgrade/<?php echo $business['Business']['id'];?>"> 
                <?php echo __d('business', 'Upgrade my page');?>
        </a>
            <?php endif; ?>
            <?php if($business['Business']['status'] == BUSINESS_STATUS_APPROVED && $permission_can_featured_page): ?>
        <a class="btn btn-action" href="<?php echo $this->request->base;?>/businesses/dashboard/feature/<?php echo $business['Business']['id'];?>"> 
                <?php echo __d('business', 'Feature my business');?>
        </a>
            <?php endif; ?>
    </div>
    <div class="profile_plg_menu">
        <div class="menu">
            <ul class="list2 menu_top_list">
            <?php if($business['Business']['user_id'] == $uid || $is_admin || $bBusinessAdmin):?>
                <li <?php if($active_dashboard == 'edit'):?>class="current"<?php endif;?>>
                    <a href="<?php echo $url_dashboard;?>edit/<?php echo $business_id;?>">
                    <?php echo __d('business', 'Edit business');?>
                    </a>
                </li>
            <?php endif;?>
            <?php if($permission_can_manage_photos):?>
                <li <?php if($active_dashboard == 'business_photos'):?>class="current"<?php endif;?>>
                    <a href="<?php echo $url_dashboard;?>business_photos/<?php echo $business_id;?>">
                        <?php echo __d('business', 'Photos');?>
                    </a>
                </li>
            <?php endif;?>
                <li class="dropdown <?php if(in_array($active_dashboard, array('branches', 'create_branch', 'admins', 'permissions', 'verify'))):?>current<?php endif;?>">
                    <a href="javascript:void(0)" id="profile_menu" data-toggle="dropdown">
                    <?php echo __d('business', 'More');?>
                    </a>
                    <ul aria-labelledby="dropdown-edit" class="dropdown-menu mobileDropdown" for="profile_menu">
                    <?php if($permission_can_manage_subpages):?>
                        <li <?php if($active_dashboard == 'branches' || $active_dashboard == 'create_branch'):?>class="current"<?php endif;?>>
                            <a href="<?php echo $url_dashboard;?>branches/<?php echo $business_id;?>">
                                <?php echo __d('business', 'Manage sub pages');?>
                            </a>
                        </li>
                    <?php endif;?>
                    <?php if(($business['BusinessPackage']['manage_admin'] || $business['BusinessPackage']['send_verification_request'])):?>
                        <?php if($business['BusinessPackage']['manage_admin'] && $permission_can_manage_admins):?>
                        <li <?php if($active_dashboard == 'admins'):?>class="current"<?php endif;?>>
                            <a href="<?php echo $url_dashboard;?>admins/<?php echo $business_id;?>">
                                    <?php echo __d('business', 'Manage Admins');?>
                            </a>
                        </li>
                        <?php endif;?>
                        <?php if(($business['Business']['user_id'] == $uid && $business['BusinessPackage']['manage_admin']) || ($cuser != null && $cuser['Role']['is_admin'])):?>
                        <li <?php if($active_dashboard == 'permissions'):?>class="current"<?php endif;?>>
                            <a href="<?php echo $url_dashboard;?>permissions/<?php echo $business_id;?>">
                                    <?php echo __d('business', 'Permissions Manager');?>
                            </a>
                        </li>
                        <?php endif;?>
                        <?php if($business['BusinessPackage']['send_verification_request'] && $permission_can_send_verification_request):?>
                        <li <?php if($active_dashboard == 'verify'):?>class="current"<?php endif;?>>
                            <a href="<?php echo $url_dashboard;?>verify/<?php echo $business_id;?>">
                                    <?php echo __d('business', 'Send Verification Request');?>
                            </a>
                        </li>
                        <?php endif;?>
                    <?php endif;?>
                    </ul>
                </li>
            </ul>
        </div>
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