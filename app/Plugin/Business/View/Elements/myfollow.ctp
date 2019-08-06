<div class="bar-content">
    <div class="content_center follow_bus_active">
        <?php echo $this->Element('Business.profile_business_tabs');?>
        <h3 class="header_green">
            <?php echo __d('business', 'My followed businesses');?>
        </h3>
        <ul id="followed_business_list" class="bus_list bussiness-list">
            <?php echo $this->Element('Business.lists/follow_list');?>
        </ul>
        <div class="clear"></div>
    </div>
</div>