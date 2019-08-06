<div class="mo_breadcrumb">
    <h1><?php echo $business['Business']['name'];?> <?php echo __d('business', 'Sub Page');?></h1>
    <?php if($permission_can_manage_subpages):?>
    <a href="<?php echo $url_dashboard;?>create_branch/<?php echo $business['Business']['id'];?>" class="button button-action topButton button-mobi-top">
        <?php echo __d('business', 'Add Sub Page');?>
    </a>
    <?php endif;?>
</div>
<ul id="branches-content" class="bussiness-list"></ul>