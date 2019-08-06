<?php
$poll = $object;
$pollHelper = MooCore::getInstance()->getHelper('Poll_Poll');
?>
<div class="activity_feed_content">
	<div class="activity_text">
		<?php echo $this->Moo->getName($poll['User'], true, true) ?>
		<?php echo __d('poll','created a new poll'); ?>
	</div>
	
	<div class="parent_feed_time">
		<span class="date"><?php echo $this->Moo->getTime($poll['Poll']['created'], Configure::read('core.date_format'), $utz) ?></span>
	</div>
</div>
<div class="clear"></div>
<div class="activity_item">
     <div class="poll_img">
		<a target="_blank" href="<?php echo $poll['Poll']['moo_href']?>">
			<img width="150" alt="" src="<?php echo $pollHelper->getImage($poll);?>">			
		</a>
	</div>
    <div class="activity_right ">
        <div class="activity_header">
            <a target="_blank" href="<?php echo  $poll['Poll']['moo_href'] ?>"><?php echo  $poll['Poll']['moo_title']?></a>
        </div>  
        <div class="poll_activity_min">
        	<?php echo $this->element('Poll.poll_detail_min',array('share'=>true,'poll'=>$poll));?>
        </div>	      
    </div>
    <div class="clear"></div>
</div>