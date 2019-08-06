<?php
$poll = $object;
$pollHelper = MooCore::getInstance()->getHelper('Poll_Poll');
?>
<div class="poll_img">
	<a target="_blank" href="<?php echo $poll['Poll']['moo_href']?>">
		<img alt="" width="150" src="<?php echo $pollHelper->getImage($poll);?>">			
	</a>		
</div>
<div class="activity_right ">
	<div class="activity_header">
		<a target="_blank" href="<?php echo  $poll['Poll']['moo_href'] ?>"><?php echo  $poll['Poll']['moo_title'] ?></a>
	</div>  
	<div class="poll_activity_min">
		<?php echo $this->element('Poll.poll_detail_min',array('share'=>true,'poll'=>$poll));?>
	</div>	      
</div>
<div class="clear"></div>