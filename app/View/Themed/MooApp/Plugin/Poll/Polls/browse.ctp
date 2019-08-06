<script>
function doRefesh()
{
	location.reload();
}
</script>
<style>
button.close {
    display: block;
}
</style>
<?php if (in_array($type,array('home','profile')) && $page == 1):?>
	<div class="content_center">
		<?php if ($type == 'home' || ($uid == $param)):?>
	    	<div class="title_center p_m_10">	            
	            <a class="topButton btnVideo mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" href="<?php echo $this->request->base?>/polls/create" class="button button-action topButton button-mobi-top"><?php echo __d('poll','Create New Poll');?></a>
	            <h2 class="header_h2"><?php echo __d('poll','Polls');?></h2>
	        </div>
        <?php else:?>
        	<div class="title_center p_m_10">
	            <h2 class="header_h2"><?php echo __d('poll','Polls');?></h2>	        
	        </div>
        <?php endif;?>
		<ul id="list-content" class="poll-content-list">
			<?php if (count($polls)):?>
				<?php echo $this->element('lists/polls');?>
			<?php else:?>		
				<li class="clear text-center"><?php echo __d('poll','No more results found');?></li>
			<?php endif;?>
		</ul>
	</div>
<?php return; endif;?>
<?php
	if  (count($polls)):
		echo $this->element('lists/polls');
	else: 
?>
	<li>
		<?php echo __d('poll','No more results found')?>
	</li>
<?php endif;?>