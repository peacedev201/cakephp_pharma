<?php $business = $business['Business'];?>
<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'mooBusiness', 'business_star_rating'), 
    'object' => array('$', 'mooBusiness')
));?>
    mooBusiness.initBusinessDetailPage(<?php echo $business['id'];?>, '<?php echo $tab;?>', '<?php echo $review_id;?>');
<?php $this->Html->scriptEnd(); ?> 
    

    
<?php if(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_REVIEW):?>
    <div class="box2 filter_block bus_detail_tab">
        <?php echo $this->Element('Business.review', array(
            'business_id' => $business['id'],
            'is_reviewed' => $is_reviewed
        ));?>
    </div>
<?php elseif(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_PHOTO):?>
    <div class="box2 filter_block bus_detail_tab">
        <?php echo $this->Element('Business.photo', array(
            'permission_can_manage_photos' => $permission_can_manage_photos
        ));?>
    </div>
<?php elseif(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_PRODUCT && $is_integrate_store && $store != null):?>
    <div class="box2 filter_block bus_detail_tab">
        <?php echo $this->Element('Business.product', array(
            'permission_can_manage_products' => $permission_can_manage_products
        ));?>
    </div>
<?php elseif(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_BRANCH):?>
    <div class="box2 filter_block bus_detail_tab">
        <?php echo $this->Element('Business.branch', array(
            'permission_can_manage_subpages' => $permission_can_manage_subpages
        ));?>
    </div>
<?php elseif(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_CHECKIN):?>
    <div class="box2 filter_block bus_detail_tab">
        <?php echo $this->Element('Business.checkin');?>
    </div>
<?php elseif(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_FOLLOWER):?>
    <div class="box2 filter_block bus_detail_tab">
        <?php echo $this->Element('Business.follower');?>
    </div>
<?php elseif(!empty($tab) && $tab == BUSINESS_DETAIL_LINK_CONTACT):?>
    <div class="box2 filter_block bus_detail_tab">
        <?php echo $this->Element('Business.contact_form', array(
            'business_id' => $business['id']
        ));?>
    </div>
<?php else:?>
    <?php if(MooCore::getInstance()->getViewer(true) > 0):?>
    <div id="status_box" class="statusHome" style="">
        <?php echo $this->element('activity_form', array(
            'type' => BUSINESS_ACTIVITY_TYPE,
            'text' => __d('business', "Share what's new"),
            'target_id' => $business['id'],
        ));?>
    </div>
    <?php endif;?>
    <div id="activities-content">
        <ul class="list6 comment_wrapper" id="list-content"></ul>
    </div>
<?php endif;?>
