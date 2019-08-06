<?php
	$poll = $object;
	$itemModel = MooCore::getInstance()->getModel('Poll.PollItem');
	$result = $itemModel->getItems($poll['Poll']['id'],$uid, POLL_MAX_ITEM_FEED);
	$max_answer = $result['max_answer'];	
	$items = $result['result'];
?>
<div class="activity_item">
	<div class="activity_header">
		<a href="<?php echo  $poll['Poll']['moo_href'] ?>"><?php echo  $poll['Poll']['moo_title'] ?></a>
	</div>
	<div class="<?php if (POLL_MAX_ITEM_FEED && count($items) > POLL_MAX_ITEM_FEED) echo 'is_activity'?> poll_content poll_<?php echo $poll['Poll']['id']?>">
		<?php echo $this->element('Poll.poll_detail',array('poll'=>$poll,'is_activity'=>true,'items'=>$items, 'max_answer'=>$max_answer));?>
	</div>
</div>