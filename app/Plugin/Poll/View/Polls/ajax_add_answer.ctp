<?php
	$itemModel = MooCore::getInstance()->getModel('Poll.PollItem');
	$result = $itemModel->getItems($poll['Poll']['id'],$uid,0);
	$max_answer = $result['max_answer'];
	$items = $result['result'];
?>
<?php
	echo $this->element('Poll.poll_detail',array('poll'=>$poll,'items'=>$items, 'max_answer'=>$max_answer));
?>