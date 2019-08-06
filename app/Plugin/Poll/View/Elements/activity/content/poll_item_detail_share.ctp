<?php
$pollHelper = MooCore::getInstance()->getHelper('Poll_Poll');
$poll = MooCore::getInstance()->getItemByType('Poll_Poll',$activity['Activity']['parent_id']);
?>


<div class="comment_message">
    <?php echo $this->viewMore(h($activity['Activity']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <?php if(!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>
</div>


<div class="share-content activity_item">
     <div class="poll_img">
		<a href="<?php echo $poll['Poll']['moo_href']?>">
			<img alt="" width="150" src="<?php echo $pollHelper->getImage($poll);?>">			
		</a>		
	</div>
    <div class="activity_right ">
        <div class="activity_header">
            <a href="<?php echo  $poll['Poll']['moo_href'] ?>"><?php echo  $poll['Poll']['moo_title'] ?></a>
        </div>
        <div class="poll_activity_min">
        	<?php echo $this->element('Poll.poll_detail_min',array('poll'=>$poll));?>
        </div>	
    </div>
    <div class="clear"></div>
</div>