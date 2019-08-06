<li>
<b><?php echo __d('forum','Forum');?></b>
</li>
<li>
	<?php echo $this->Form->checkbox('create_forum_topic', array('checked' => isset($notification_setting['create_forum_topic']) ? $notification_setting['create_forum_topic'] : true)); ?>
	<?php echo __d('forum','When people posted a new topic in forum that I subscribed to');?>
</li>
<li>
	<?php echo $this->Form->checkbox('reply_forum_topic', array('checked' => isset($notification_setting['reply_forum_topic']) ? $notification_setting['reply_forum_topic'] : true)); ?>
	<?php echo __d('forum','When people reply to my forum topic I subscribed to');?>
</li>