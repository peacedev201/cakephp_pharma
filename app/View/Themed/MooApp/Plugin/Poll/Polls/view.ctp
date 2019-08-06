<?php $helper = MooCore::getInstance()->getHelper('Poll_Poll');?>
<?php 
	$itemModel = MooCore::getInstance()->getModel('Poll.PollItem');
	$tagModel = MooCore::getInstance()->getModel("Tag");
	$tags = $tagModel->getContentTags($poll['Poll']['id'], 'Poll_Poll');
?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooPoll'), 'object' => array('$', 'mooPoll'))); ?>
	mooPoll.initViewPoll();
<?php $this->Html->scriptEnd(); ?> 
<style>
button.close {
    display: block;
}
</style>
<div class="bar-content">
    <div>
    	<div class="content_center full_content moo_app_view_poll">	
	    	<h1 class="poll-detail-title"><?php echo $poll['Poll']['moo_title']?></h1>	    		    	
	        <div class="poll-detail-action">
	            <div class="list_option">
	                <div class="dropdown">
	                    <button id="video_edit_<?php echo $poll["Poll"]["id"] ?>" class="mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon">
	                        <i class="material-icons">more_vert</i>
	                    </button>
	                    <ul class="mdl-menu mdl-menu--bottom-right mdl-js-menu mdl-js-ripple-effect" for="video_edit_<?php echo $poll["Poll"]["id"] ?>">
	                        <?php if ($helper->canEdit($poll,MooCore::getInstance()->getViewer())):?>
		                        <li class="mdl-menu__item"><a href="<?php echo $this->request->base?>/polls/create/<?php echo $poll['Poll']['id']?>?app_no_tab=1" title="<?php echo __d('poll','Edit Poll')?>"><?php echo __d('poll','Edit Poll')?></a></li>
		                        <li class="mdl-menu__item"><a href="javascript:void(0)" class="deletePoll" data-id="<?php echo $poll['Poll']['id'];?>"><?php echo __d('poll','Delete Poll')?></a></li>
		                        <li class="seperate"></li>
	                        <?php endif; ?>	                        	                        
	                        <li class="mdl-menu__item"><a href="<?php echo $this->request->base?>/reports/ajax_create/poll_poll/<?php echo $poll['Poll']['id'];?>" title="<?php echo __d('poll','Report Poll')?>"><?php echo __d('poll', 'Report Poll')?></a></li>
	                        <?php if ($poll['Poll']['privacy'] != PRIVACY_ME): ?>
		                        <?php echo $this->element('share/menu',array('param' => 'Poll_Poll','action' => 'poll_item_detail' ,'id'=>$poll['Poll']['id'])); ?>
                        	<?php endif; ?>
	                    </ul>
	                </div>
	            </div>
	        </div>
	        <div class="clear"></div>
	        <?php if (count($tags)):?>
	    		<div><?php echo $this->element( 'blocks/tags_item_block',array("tags"=>$tags) ); ?></div>
	    	<?php endif;?>
	        <div class="extra_info">
	        <span>
	    	<?php echo __d('poll', 'Posted by %s', $this->Moo->getName($poll['User']))?> <?php echo __d('poll','in')?> <a href="<?php echo $this->request->base?>/polls/index/category:<?php echo $poll['Poll']['category_id']?>/<?php echo seoUrl($poll['Category']['name'])?>"><?php echo $poll['Category']['name']?></a> <?php echo $this->Moo->getTime($poll['Poll']['created'], Configure::read('core.date_format'), $utz)?>
	    	&nbsp;&middot;&nbsp;<?php if ($poll['Poll']['privacy'] == PRIVACY_PUBLIC): ?>
	                        <?php echo __d('poll','Public') ?>
	                        <?php elseif ($poll['Poll']['privacy'] == PRIVACY_ME): ?>
	                        <?php echo __d('poll','Private') ?>
	                        <?php elseif ($poll['Poll']['privacy'] == PRIVACY_FRIENDS): ?>
	                        <?php echo __d('poll','Friend') ?>
	                        <?php endif; ?></span>
	       
	        </div> 	        
		   	<?php							
				$result = $itemModel->getItems($poll['Poll']['id'],$uid);
				$max_answer = $result['max_answer'];
				$items = $result['result'];
			?>
			<div class="poll_content poll_<?php echo $poll['Poll']['id']?>">						
				<?php echo $this->element('Poll.poll_detail',array('poll'=>$poll,'items'=>$items, 'max_answer'=>$max_answer));?>
			</div>
			<div>
				<br/>
				<?php echo $this->renderLike();?>
			</div>
	        <div class="clear"></div>
	    </div>	    
    </div>
</div>
<?php echo $this->renderComment();?>