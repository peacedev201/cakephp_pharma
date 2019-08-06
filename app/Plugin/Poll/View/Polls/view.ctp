<?php $helper = MooCore::getInstance()->getHelper('Poll_Poll');?>
<?php $itemModel = MooCore::getInstance()->getModel('Poll.PollItem');?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooPoll'), 'object' => array('$', 'mooPoll'))); ?>
	mooPoll.initViewPoll();
<?php $this->Html->scriptEnd(); ?> 
<div class="bar-content full_content p_m_10">
    <div class="content_center">
    	<div class="post_body">	
	    	<h1 class="poll-detail-title"><?php echo $poll['Poll']['moo_title']?></h1>	    		    	
	        <div class="poll-detail-action">
	            <div class="list_option">
	                <div class="dropdown">
	                    <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
	                        <i class="material-icons">more_vert</i>
	                    </button>
	                    <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
	                        <?php if ($helper->canEdit($poll,MooCore::getInstance()->getViewer())):?>
		                        <li><a href="<?php echo $this->request->base?>/polls/create/<?php echo $poll['Poll']['id']?>" title="<?php echo __d('poll','Edit Poll')?>"><?php echo __d('poll','Edit Poll')?></a></li>
		                        <li><a href="javascript:void(0)" class="deletePoll" data-id="<?php echo $poll['Poll']['id'];?>"><?php echo __d('poll','Delete Poll')?></a></li>
		                        <li class="seperate"></li>
	                        <?php endif; ?>	                        	                        
	                        <li><a href="<?php echo $this->request->base?>/reports/ajax_create/poll_poll/<?php echo $poll['Poll']['id'];?>" data-target="#portlet-config" data-toggle="modal" title="<?php echo __d('poll','Report Poll')?>"><?php echo __d('poll', 'Report Poll')?></a></li>
	                    </ul>
	                </div>
	            </div>
	        </div>
	        <div class="clear"></div>
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
	    </div>	    
    </div>
</div>
<div class="bar-content full_content p_m_10">
    <div class="content_center">
		<?php
		$options = array();
		if ($poll['Poll']['privacy'] != PRIVACY_ME)
		{
			$options = array('shareUrl' => $this->Html->url(array(
				'plugin' => false,
				'controller' => 'share',
				'action' => 'ajax_share',
				'Poll_Poll',
				'id' => $poll['Poll']['id'],
				'type' => 'poll_item_detail'
			), true));
		}
		?>
    	<?php echo $this->renderLike($options);?>
    </div>
</div>
<div class="bar-content full_content p_m_10 blog-comment">
   	<?php echo $this->renderComment();?>
</div>