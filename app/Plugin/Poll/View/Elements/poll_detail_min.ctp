<?php
$helper = MooCore::getInstance()->getHelper('Poll_Poll');
$itemModel = MooCore::getInstance()->getModel('Poll.PollItem');
$items = $itemModel->getItemsMin($poll['Poll']['id']);
$uid = MooCore::getInstance()->getViewer(true);
$viewer = MooCore::getInstance()->getViewer();
$blockModel = MooCore::getInstance()->getModel("UserBlock");
if ($uid)
{
	$user_blocks = $blockModel->getBlockedUsers($uid);
	
}
?>
<?php if (!in_array('poll_view',$uacos) || in_array($poll['Poll']['user_id'],$user_blocks)):?>
	<?php echo __d('poll','You do not have permission to view this poll');	return;?>
<?php endif;?>
<?php if ($poll['Poll']['visiable'] || ($uid && $viewer['Role']['is_admin'])):?>
	<?php foreach ($items as $i => $item):?>
		<?php if (POLL_MAX_ITEM_FEED && $i + 1 > POLL_MAX_ITEM_FEED)
			break;
		?>
		<div class="row">
			<?php echo $item['PollItem']['name']?> (<?php echo __dn('poll','%s vote', '%s votes', $item['PollItem']['total_user'], $item['PollItem']['total_user'] )?>)
		</div>
	<?php endforeach;?>
	<?php if (count($items) > POLL_MAX_ITEM_FEED):?>
		<div class="row">
			<?php if (isset($share) && $share):?>
				<a target="_blank" href="<?php echo $poll['Poll']['moo_href']?>"><?php echo __d('poll','View more')?></a>
			<?php else:?>
				<a href="<?php echo $poll['Poll']['moo_href']?>"><?php echo __d('poll','View more')?></a>
			<?php endif;?>
		</div>
	<?php endif;?>
<?php else:?>
	<?php echo __d('poll','This poll is not visible');?>
<?php endif;?>