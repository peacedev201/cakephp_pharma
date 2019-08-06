<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery'), 'object' => array('$'))); ?>

    $(document).off('click', '.open_share_app');
    $(document).on('click','.open_share_app', function(e){
        var data = new Array();
        data['id']          = $(this).data('id');
        data['type']        = $(this).data('param');
        data['shareAction'] = $(this).data('action');

        var type = $(this).attr('type');
        window.mobileAction.openShareFeed(data,type);
    });

<?php $this->Html->scriptEnd(); ?>
    <li class="app_share_block">
    <a href="javascript:void(0)" data-param="<?php echo $param ?>" data-id="<?php echo $id ?>" data-action="<?php echo $action ?>" class="open_share_app" type="#me"><?php echo __('Share My Wall') ?></a>
    <a href="javascript:void(0)" data-param="<?php echo $param ?>" data-id="<?php echo $id ?>" data-action="<?php echo $action ?>" class="open_share_app" type="#friend"><?php echo __('Share Friend Wall') ?></a>
    <a href="javascript:void(0)" data-param="<?php echo $param ?>" data-id="<?php echo $id ?>" data-action="<?php echo $action ?>" class="open_share_app" type="#group"><?php echo __('Share Group Wall') ?></a>
    <a href="javascript:void(0)" data-param="<?php echo $param ?>" data-id="<?php echo $id ?>" data-action="<?php echo $action ?>" class="open_share_app" type="#email"><?php echo __('Share Email') ?></a>
</li>

<?php 
 $this->MooGzip->script(array('zip'=>'mobile.action.bundle.js.gz','unzip'=>'MooApp.mobile.action.bundle'), true);
?>