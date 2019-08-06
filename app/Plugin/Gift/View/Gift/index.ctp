<?php
//echo $this->Html->script(array('Gift.gift', 'Gift.jquery-ui'), array('inline' => false));
echo $this->Html->css(array('Gift.gift', 'Gift.autocomplete'), array('inline' => false));
?>

<div class="bar-content">
    <div class="content_center">
    	<div class="mo_breadcrumb">
            <h1><?php echo __d('gift', 'Gifts')?></h1>
            <?php if ($permission_can_send_gift && $permission_can_create_gift): ?>
            	<a href="<?php echo $this->request->base.'/gifts/create'?>" class="button button-action topButton button-mobi-top" title="<?php echo __d('gift', 'Create New Gift')?>">
                    <?php echo __d('gift', 'Create New Gift')?>
                </a>
			<?php endif ?>
        </div>
		<ul class="users_list" id="list-content">
            <?php if ($type == 'my'): ?>
                <?php echo $this->element( 'lists/my_gifts_list', array( 'more_url' => $more_url ) ); ?>	
            <?php else:?>
                <?php echo $this->element( 'lists/gifts_list', array( 'more_url' => $more_url ) ); ?>	
            <?php endif;?>
		</ul>
	</div>
</div>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('mooGift', 'jqueryUi'), 'object' => array('mooGift'))); ?>

<?php $this->Html->scriptEnd(); ?>