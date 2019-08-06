<script>
function doRefesh()
{
	location.reload();
}
</script>
<?php if (in_array($type,array('home','profile')) && $page == 1):?>
	<div class="content_center">
		<?php if ($type == 'home' || ($uid == $param)):?>
	    	<div class="title_center p_m_10">
	    		<a class="topButton btnVideo mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" href="<?php echo $this->request->base?>/documents/create" class="button button-action topButton button-mobi-top"><?php echo __d('document','Create New Document');?></a>
	            <h1><?php echo __d('document','Documents');?></h1>	            
	        </div>
        <?php else:?>
        	<div class="title_center p_m_10">	 
	            <h1><?php echo __d('document','Documents');?></h1>
	        </div>
        <?php endif;?>
		<ul id="list-content" class="document-content-list">
			<?php if (count($documents)):?>
				<?php echo $this->element('lists/documents');?>
			<?php else:?>		
				<li class="clear text-center"><?php echo __d('document','No more results found');?></li>
			<?php endif;?>
		</ul>
	</div>
<?php return; endif;?>
<?php
	if  (count($documents)):
		echo $this->element('lists/documents');
	else: 
?>
	<li class="clear text-center">
		<?php echo __d('document','No more results found')?>
	</li>
<?php endif;?>