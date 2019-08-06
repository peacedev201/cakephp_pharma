<div class="mo_breadcrumb">
    <h1><?php echo __d('business', 'Photo');?></h1>
    <?php if(($business['Business']['parent_id'] == 0 && $permission_can_manage_photos) || ($business['Business']['parent_id'] > 0 && $permission_can_manage_subpages)):?>
    <a href="<?php echo $business['Business']['parent_id'] > 0 ? $url_dashboard.'create_branch/'.$business['Business']['parent_id'].'/'.$business['Business']['id']  : $url_dashboard.'business_photos/'.$business['Business']['id'];?>" class="button button-action topButton button-mobi-top">
        <?php echo __d('business', 'Add Photo');?>
    </a>
    <?php endif;?>
</div>
<ul id="photos-content" class="photo-list"></ul>