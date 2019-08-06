<div class="bar-content">
    <div class="content_center">
        <div class="mo_breadcrumb">
            <h1><?php echo __d('poll','Polls');?></h1>
            <?php if ($uid):?>
            	<a href="<?php echo $this->request->base?>/polls/create" class="button button-action topButton button-mobi-top"><?php echo __d('poll','Create New Poll');?></a>
            <?php endif;?>
         </div>
		<ul id="list-content" class="poll-content-list">
			<?php if (count($polls)):?>
				<?php echo $this->element('lists/polls', array('is_view_more'=>$is_view_more,'url_more'=>$url_more,'polls' =>$polls) ,array('plugin'=>'Poll')); ?>
			<?php else:?>		
				<li class="clear text-center"><?php echo __d('poll','No more results found');?></li>
			<?php endif;?>
		</ul>
    </div>
</div>