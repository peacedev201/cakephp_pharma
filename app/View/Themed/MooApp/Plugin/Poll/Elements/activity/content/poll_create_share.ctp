<?php
$pollHelper = MooCore::getInstance()->getHelper('Poll_Poll');
?>
<div class="comment_message">
	<?php echo $this->viewMore(h($activity['Activity']['content']), null, null, null, true, array('no_replace_ssl' => 1)); ?>
    <?php if(!empty($activity['UserTagging']['users_taggings'])) $this->MooPeople->with($activity['UserTagging']['id'], $activity['UserTagging']['users_taggings']); ?>
    
    <div class="share-content">
    	<?php
	    	$activityModel = MooCore::getInstance()->getModel('Activity');
	    	$parentFeed = $activityModel->findById($activity['Activity']['parent_id']);
	    	$poll = MooCore::getInstance()->getItemByType($parentFeed['Activity']['item_type'], $parentFeed['Activity']['item_id']);
    	?>
    	<div class="activity_feed_content">
            
            <div class="activity_text">
                <?php echo $this->Moo->getName($parentFeed['User']) ?>
                <?php echo __d('poll','created a new poll'); ?>
            </div>
            
            <div class="parent_feed_time">
                <span class="date"><?php echo $this->Moo->getTime($parentFeed['Activity']['created'], Configure::read('core.date_format'), $utz) ?></span>
            </div>
            
        </div>
		<div class="activity_item moo_app_poll_activity_item">
		     <div class="poll_img">
				<a href="<?php echo $poll['Poll']['moo_href']?>">
					<img width="150" alt="" src="<?php echo $pollHelper->getImage($poll);?>">
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
	</div>
</div>