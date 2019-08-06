<?php
    echo $this->Html->css(array(
            'Business.star-rating',
            'Business.business',
            'fineuploader'), array('block' => 'css', 'minify'=>false));
    $BusinessTime = $business['BusinessTime'];
    $businessPackage = $business['BusinessPackage'];
    $businessType = $business['BusinessType'];
    $BusinessCategories = $business['BusinessCategory'];
    $business = $business['Business'];
    $businessHelper = MooCore::getInstance()->getHelper('Business_Business');
?>

<!--mobile-->
<?php echo $this->Element('mobile_menu_detail');?>
<!--end mobile-->

<?php if(!empty($tab) && $tab == 'businesses'):?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness', 'business_star_rating'), 
    'object' => array('$', 'mooBusiness')
));?>
    mooBusiness.getDirection();
<?php $this->Html->scriptEnd(); ?> 

<?php if(!$is_app):?>
    <?php $this->setNotEmpty('north');?>
    <?php $this->start('north'); ?>
<?php endif;?>
        <iframe
            width="100%"
            height="333px"
            style="display:none;"
            frameborder="0" style="border:0"
             id="map_detail"
            src="" allowfullscreen>
        </iframe>
<?php if(!$is_app):?>
    <?php $this->end(); ?>
<?php endif;?>

<div class="bar-content">
    <div class="content_center bus-view-detail">
        <div class="post_body">
            <div class="mo_breadcrumb bus-detail-breadcrumb">
                <?php if($business['featured']):?>
                    <sup class="featured_busieness_label"><?php echo __d('business', 'Featured');?></sup>
                <?php endif;?>
                <div class="list_option">
                    <div class="dropdown">
                        <button class="button" data-toggle="dropdown" data-target="#" id="dropdown-edit"><!--dropdown-user-box-->
                            <i class="material-icons dp-18">more_vert</i>
                        </button>
                        <ul aria-labelledby="dropdown-edit" class="dropdown-menu" role="menu">
                            <li>
                                <a href="javascript:void(0)" class="business_report" data-id="<?php echo $business['id'];?>"> 
                                    <?php echo $business['parent_id'] == 0 ? __d('business', 'Report Business') : __d('business', 'Report Page');?>
                                </a>                        
                            </li>  
                            <?php if(($cuser != null && $cuser['Role']['is_admin']) || $is_busines_admin || $permission_can_manage_subpages):?>
                            <li>
                                <a href="<?php echo $business['parent_id'] == 0 ? $url_dashboard.'edit/'.$business['id'] : $url_dashboard.'create_branch/'.$business['parent_id'].'/'.$business['id'].'/';?>">
                                    <?php echo __d('business', 'Edit');?>
                                </a>
                            </li>
                            <?php endif;?>
                            <?php if(($cuser != null && $cuser['Role']['is_admin']) || $is_busines_admin || $permission_can_manage_subpages):?>
                            <li>
                                <?php if($business['parent_id'] == 0):?>
                                <a class="delete_business" href="javascript:void(0)" data-id="<?php echo $business['id'];?>"> 
                                    <?php echo __d('business', 'Delete Business');?>
                                </a>
                                <?php else:?>
                                <a class="delete_branch" href="javascript:void(0)" data-url="<?php echo $this->request->base;?>/business_branch/delete_branch/<?php echo $business['parent_id'];?>/<?php echo $business['id'];?><?php echo $is_app ? "?app_no_tab=1" : "";?>">
                                    <?php echo __d('business', 'Delete Page');?>
                                </a>
                                <?php endif;?>
                            </li>
                            <?php endif;?>
                        </ul>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
            <ul class="list6 info">
                <li class="visible-xs visible-sm">
                    <strong><?php echo $business['name']; ?></strong>
                    <?php if($business['verify']):?>
                        <i class="verify_bus" title="<?php echo __d('business', 'Verified');?>"></i>
                    <?php else:?>
                        <i class="unverify_bus" title="<?php echo __d('business', 'Unverified');?>"></i>
                    <?php endif;?>
                    <?php if($businessPackage['favourite']):?>
                        <?php echo $this->element('Business.misc/business_favourite', array(
                            'is_favourite' => $is_favourite,
                            'business_id' => $business['id']
                        ));?>
                    <?php endif;?>
                </li>
                <li>
                    <label><?php echo __d('business', 'Category');?>:</label>
                    <div>
                        <?php if(!empty($BusinessCategories)):?>
                            <?php foreach($BusinessCategories as $k => $BusinessCategory):?>
                                <a href="<?php echo $BusinessCategory['moo_href'];?>" style="display: inline-block;font-weight: normal">
                                    <?php echo $BusinessCategory['name'];?>
                                </a>
                                <?php if($k < count($BusinessCategories) - 1):?>&#44; <?php endif;?>
                            <?php endforeach;?>
                        <?php endif;?>
                    </div>
                </li>
                <li>
                    <label><?php echo __d('business', 'Business Type');?>:</label>
                    <div><?php echo $businessType['name'];?></div>
                </li>
                <li>
                    <label><?php echo __d('business', 'Address');?>:</label>
                    <div><?php echo $business['address'];?></div>
                    <div class="bus_main_address">
                        <a href="javascript:void(0)" data-address="<?php echo $business['address']; ?>" class=" btn_toggle get_direction"> <i class="material-icons ">directions</i>
                            <span class="hidden-xs hiden-sm"><?php echo __d('business', 'Directions ') ?></span>
                        </a>
                        <span class="btn_toggle">
                        <a href="javascript:void(0)" class=" btn_show_map" data-item="#map_detail" data-link="<?php echo $url.'load_map/?address='.urlencode($business['name']).'&lat='.$business['lat'].'&lng='.$business['lng'];?>&position=right&scrollwheel=0&hide_info=1">
                            <i class="material-icons ">location_on</i>
                            <span class="hidden-xs hiden-sm"><?php echo __d('business', 'Show map') ?></span>
                        </a>
                        </span>
                    </div>
                </li>
                <?php if(!empty($business['phone'])):?>
                    <li>
                        <label><?php echo __d('business', 'Tel');?>:</label>
                        <div>
                            <a class="business_info" href="javascript:void(0)" data-id="<?php echo $business['id'];?>" data-task="tel">
                                <?php echo __d('business', 'view');?>
                            </a>
                        </div>
                    </li>
                <?php endif;?>
                <?php if(!empty($business['fax'])):?>
                    <li>
                        <label><?php echo __d('business', 'Fax');?>:</label>
                        <div>
                            <a class="business_info" href="javascript:void(0)" data-id="<?php echo $business['id'];?>" data-task="fax">
                                <?php echo __d('business', 'view');?>
                            </a>
                        </div>
                    </li>
                <?php endif;?>
                <?php if(!empty($business['website'])):?>
                    <li>
                        <label><?php echo __d('business', 'Website');?>:</label>
                        <a href="<?php echo $this->Business->getFullUrl($business['website']);?>" target="_blank">
                            <?php echo $this->Business->getFullUrl($business['website']);?>
                        </a>
                    </li>
                <?php endif;?>

                <?php if(!empty($business['company_number'])):?>
                    <li>
                        <label><?php echo __d('business', 'Company Number');?>:</label>
                        <div><?php echo $business['company_number'];?></div>
                    </li>
                <?php endif;?>

                <?php if($parent_business != null):?>
                <li>
                    <label><?php echo __d('business', 'Parent page');?>:</label>
                    <div>
                        <a href="<?php echo $parent_business['Business']['moo_href'];?>">
                            <?php echo $parent_business['Business']['name'];?>
                        </a>
                    </div>
                </li>
                <?php endif;?>

                <?php $descripton = trim(strip_tags($business['description']));
                    $descripton = str_replace('&nbsp;', '', $descripton);
                    if($descripton != ''):?>
                <li>
                    <label><?php echo __d('business', 'Description');?>:</label>
                    <div class="bu-description">
                        <div class="truncate" data-more-text="<?php echo __d('business', 'Show More')?>" data-less-text="<?php echo __d('business', 'Show Less')?>">
                            <?php echo $this->Moo->cleanHtml($this->Text->convert_clickable_links_for_hashtags($business['description'] , Configure::read('Business.business_enable_hashtag') ))?>
                        </div>
                    </div>
                </li>
                <?php endif;?>
            </ul>
        </div>
    </div>
</div>
<?php endif;?>
    
<!--mobile-->
<?php if(!empty($tab) && $tab == 'businesses'):?>
    <?php echo $this->Element('mobile_menu_detail_info');?>
<?php endif;?>
<!--end mobile-->