<?php $this->Html->scriptStart(array(
    'inline' => false, 
    'domReady' => true, 
    'requires' => array('jquery', 'store_store'), 
    'object' => array('$', 'store_store')
));?>
    store_store.initProfile('<?php echo $tab;?>');
<?php $this->Html->scriptEnd(); ?> 
<li id="tab-wishlist" class="mdl-menu__item mdl-js-ripple-effect">
    <a href="#" rel="profile-content" data-url="<?php echo $this->request->base.'/stores/wishlists';?>" id="wishlist">
		<?php echo __d('store', 'Wishlist');?>		
	</a>
</li>
<li id="tab-orders" class="mdl-menu__item mdl-js-ripple-effect">
    <a href="#" rel="profile-content" data-url="<?php echo $this->request->base.'/stores/orders';?>" id="orders">
		<?php echo __d('store', 'Orders');?>	
	</a>
</li>