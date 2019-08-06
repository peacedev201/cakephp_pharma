<?php if (in_array($type,array('home','profile')) && $page == 1):?>
	<div class="content_center_home">
		<?php if ($type == 'home' || ($uid == $param)):?>
	    	<div class="mo_breadcrumb">
	            <h1><?php echo __d('poll','Polls');?></h1>
	            <a href="<?php echo $this->request->base?>/polls/create" class="button button-action topButton button-mobi-top"><?php echo __d('poll','Create New Poll');?></a>
	        </div>
        <?php else:?>
        	<div class="mo_breadcrumb">
	            <h1><?php echo __d('poll','Polls');?></h1>	        
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