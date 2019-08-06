<?php 
	$helper = MooCore::getInstance()->getHelper('Poll_Poll');	
	$pollModel = MooCore::getInstance()->getModel('Poll_Poll');
	$itemModel = MooCore::getInstance()->getModel('Poll.PollItem');
	$no_id = isset($no_list_id) ? $no_list_id : false;
?>
<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooPoll"], function($,mooPoll) {
    	mooPoll.initOnListing();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooPoll'), 'object' => array('$', 'mooPoll'))); ?>
	mooPoll.initOnListing();
<?php $this->Html->scriptEnd(); ?> 
<?php endif; ?>
<?php if (count($polls)): ?>
	<?php if ($no_id):?>
		<ul id="list-content" class="poll-content-list">
	<?php endif;?>
	<?php foreach ($polls as $poll):?>
		<?php $like = $pollModel->getLikePollByUser($poll['Poll']['id'],$uid);?>
		<li class="full_content p_m_10 poll_list">
			<div class="poll-info">
				<a href="<?php echo $poll['Poll']['moo_href']; ?>" class="title">
					<?php echo $poll['Poll']['moo_title'];?>
					<?php if ($poll['Poll']['feature']):?>
						<img alt="" src="<?php echo $this->Html->assetUrl('Poll.img/star.png');?>">
					<?php endif;?>
				</a>				
				<div class="extra_info">
					<?php echo __d( 'poll','Posted by')?> <?php echo $this->Moo->getName($poll['User'], false)?>
					<?php echo $this->Moo->getTime( $poll['Poll']['created'], Configure::read('core.date_format'), $utz )?> &nbsp;
					<?php
						switch($poll['Poll']['privacy']){
							case 1:
								$icon = '<i class="material-icons">public</i>';
								$tooltip = __d('poll','Shared with: Everyone');
								break;
							case 2:
								$icon = '<i class="material-icons">people</i>';
								$tooltip = __d('poll','Shared with: Friends Only');
								break;
							case 3:
								$icon = '<i class="material-icons">lock</i>';
								$tooltip = __d('poll','Shared with: Only Me');
								break;
						}
					?>
					<a class="tip" href="javascript:void(0);" original-title="<?php echo  $tooltip ?>"> <?php echo $icon;?></a>
				</div>
				<div class="poll-description-truncate">
					<?php							
						$result = $itemModel->getItems($poll['Poll']['id'],$uid, POLL_MAX_ITEM_FEED);
						$max_answer = $result['max_answer'];
						$items = $result['result'];
					?>
					<div class="<?php if (POLL_MAX_ITEM_FEED && count($items) > POLL_MAX_ITEM_FEED) echo 'is_activity'?> poll_content poll_<?php echo $poll['Poll']['id']?>">						
						<?php echo $this->element('Poll.poll_detail',array('poll'=>$poll,'is_activity'=>true,'items'=>$items, 'max_answer'=>$max_answer));?>
					</div>
					<div class="like-section">
						<div class="like-action">
							<a href="<?php echo $poll['Poll']['moo_href']; ?>/#comments">
								<i class="material-icons md-24">comment</i>&nbsp;<span id="comment_count"><?php echo $poll['Poll']['comment_count']?></span>
							</a>
							<a href="<?php echo $poll['Poll']['moo_href']; ?>" class="<?php if ($like && $like['Like']['thumb_up']): ?>active<?php endif; ?>">
								<i class="material-icons md-24">thumb_up</i>
							</a>
							<a href="<?php echo $poll['Poll']['moo_href']; ?>">
								<span id="like_count"><?php echo $poll['Poll']['like_count']?></span>
							</a>
							<?php if(empty($hide_dislike)): ?>
								<a href="<?php echo $poll['Poll']['moo_href']; ?>" class="<?php if ($like && !$like['Like']['thumb_up']): ?>active<?php endif; ?>">
									<i class="material-icons md-24">thumb_down</i>
								</a>
							
								<a href="<?php echo $poll['Poll']['moo_href']; ?>">
									<span id="dislike_count"><?php echo $poll['Poll']['dislike_count']?></span>
								</a>
							<?php endif;?>
							<a href="<?php echo $poll['Poll']['moo_href']; ?>">
								<i class="material-icons md-24">reply</i>&nbsp;<span><?php echo $poll['Poll']['share_count']?></span>
							</a>
						</div>
					</div>
				</div>
			</div>
		</li>	
	<?php endforeach;?>
	<?php if (isset($is_view_more) && $is_view_more): ?>
		<?php $this->Html->viewMore($url_more) ?>
	<?php endif; ?>
	<?php if ($no_id):?>
		</ul>
	<?php endif;?>
<?php else:?>
	<li class="clear text-center"><?php echo __d('poll','No more results found');?></li>
<?php endif;?>
