<?php 
$uid = MooCore::getInstance()->getViewer(true);
$viewer = MooCore::getInstance()->getViewer();
?>
<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooPoll"], function($,mooPoll) {
    	mooPoll.initOnAnswer();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooPoll'), 'object' => array('$', 'mooPoll'))); ?>
	mooPoll.initOnAnswer();
<?php $this->Html->scriptEnd(); ?> 
<?php endif; ?>
<?php if (!in_array('poll_view',$uacos)):?>
	<?php echo __d('poll','You have not permission view poll');	return;?>
<?php endif;?>
<?php if ($poll['Poll']['visiable'] || ($uid && $viewer['Role']['is_admin'])):?>
	<?php
	$helper = MooCore::getInstance()->getHelper('Poll_Poll');
	?>
	<?php foreach ($items as $i => $item):
		$active = false;
		if (isset($is_activity) && $is_activity && POLL_MAX_ITEM_FEED && $i + 1 > POLL_MAX_ITEM_FEED)
			break;
		?>
		<div id="poll_<?php echo $poll['Poll']['id']?>_item_<?php echo $item['PollItem']['id']; ?>" class="poll_item <?php if ($uid && $item['PollItem']['mark_check']) echo 'active'?>">		
			<div class="poll_user_right">
				<?php $count_user = 0;?>			
				<ul>
					<?php if (count($item['PollItem']['FriendsAnswer'])):?>
						<?php foreach ($item['PollItem']['FriendsAnswer'] as $j=>$friend):?>
							<?php if ($j > POLL_MAX_USER) break;?>
							<?php $count_user++;?>
							<li>
								<?php echo $this->Moo->getItemPhoto(array('User' => $friend['User']),array( 'prefix' => '50_square'), array('class' => 'img_wrapper'))?>
							</li>
						<?php endforeach;?>
					<?php endif;?>
					<?php if ($item['PollItem']['total_user'] > $count_user):?>
						<li class="more_user">
							<a href="<?php echo $this->request->base?>/polls/ajax_show_user_answer/<?php echo $item['PollItem']['id']?>" data-target="#langModal" data-toggle="modal" data-dismiss="modal" data-backdrop="true">+<?php echo ($item['PollItem']['total_user'] - $count_user) ?></a>
						</li>
					<?php endif;?>
				</ul>
				
			</div>
			
			<div class="poll_user_left">				
				<div class="poll_checkbox">
					<input type="<?php echo $poll['Poll']['type'] ? 'checkbox' : 'radio';?>" name="poll_answer_<?php echo $poll['Poll']['id']?>" class="poll_answer" data-item-id="<?php echo $item['PollItem']['id']?>" data-poll-id="<?php echo $poll['Poll']['id'];?>" <?php if ($item['PollItem']['mark_check']){echo 'checked '; if (!$poll['Poll']['type']) echo 'disabled';};?> >
				</div>
				<div class="poll_result">
					<div class="a" title="<?php echo $item['PollItem']['name'];?>">
						<div class="shaded" style="width:<?php echo ($max_answer ? round(($item['PollItem']['total_user']/$max_answer) * 100) : '0') ?>%"></div>
						<div class="label_item"><?php echo $item['PollItem']['name']?></div>
						<div class="click_target poll_answer" data-item-id="<?php echo $item['PollItem']['id']?>" data-poll-id="<?php echo $poll['Poll']['id'];?>"></div>
						<div class="poll_vote"><span><?php echo $item['PollItem']['total_user'];?> <?php if ($item['PollItem']['total_user'] > 1) echo __d('poll','votes'); else echo __d('poll','vote')?></span></div>
					</div>
				</div>
			</div>
			
		</div>
	<?php endforeach;?>
	<?php if (POLL_MAX_ITEM_FEED && count($items) > POLL_MAX_ITEM_FEED && isset($is_activity) && $is_activity):?>
		<div class="more_result poll_item">
			<div class="poll_user_right">
			</div>
			<div class="poll_user_left">
				<?php if ($uid):?>
					<div class="poll_checkbox">					
					</div>
				<?php endif;?>
				<div class="poll_result">
					<div class="a">
						<div class="shaded"></div>
						<div class="label_item"><?php echo str_replace('{number}', count($items) - POLL_MAX_ITEM_FEED , __d('poll','{number} More...'))?></div>
						<a class="click_target" data-target="#pollModal" data-toggle="modal" data-dismiss="modal" data-backdrop="true" href="<?php echo $this->request->base?>/polls/ajax_view/<?php echo $poll['Poll']['id']?>"></a>					
					</div>
				</div>
			</div>
		</div>
	<?php elseif ($poll['Poll']['create_new_answer'] && $uid):?>
		<div class="add_more poll_item">
			<div class="poll_user_right">
				<?php if ($helper->isMobile()):?>
					<button class="btn btn-action poll_button_add_more" data-poll-id="<?php echo $poll['Poll']['id']?>"><?php echo __d('poll','Add');?></button>
				<?php endif;?>
			</div>
			<div class="poll_user_left">
				<input type="text" data-poll-id=<?php echo $poll['Poll']['id']?> class="poll_add_more" placeholder="<?php echo __d('poll','Add an option...');?>">
			</div>
		</div>
	<?php endif;?>
<?php else:?>
	<?php echo __d('poll','This poll is not visible');?>
<?php endif;?>