<?php if ($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery", "mooBehavior"], function($, mooBehavior) {
        mooBehavior.initMoreResults();
    });
</script>
<?php endif?>
<?php if($business_admins != null):?>
	<?php foreach ($business_admins as $business_admin):?>
        <?php echo $this->Element('Business.misc/admin_item', array(
            'business_admin' => $business_admin
        ));?>
    <?php endforeach; ?>
    <?php if($iLoadMore > 0):?>
        <?php $this->Html->viewMore($more_admin_url, 'admin_content') ?>
    <?php endif;?>
<?php else:?>
	<?php echo '<div class="clear text-center" id="noResult">' . __d('business', 'No more results found') . '</div>';?>
<?php endif;?>