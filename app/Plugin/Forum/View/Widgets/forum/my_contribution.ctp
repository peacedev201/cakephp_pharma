<div class="box2 my_contribution_block">
	<h3><?php echo $title ?></h3>
	<div class="box_content">
		<ul class="list2 menu-list" id="browse">
			<li><a class="<?php echo (!empty($type) && $type == 'started')? 'current' : '';?> no-ajax" href="<?php echo $this->request->base;?>/forums/topic/index/started"><?php echo __d('forum','Topic Started');?></a></li>
			<li><a class="<?php echo (!empty($type) && $type == 'replies')? 'current' : '';?> no-ajax" href="<?php echo $this->request->base;?>/forums/topic/index/replies"><?php echo __d('forum',"Replies Created");?></a></li>
			<li><a class="<?php echo (!empty($type) && $type == 'signature')? 'current' : '';?> no-ajax" rel="forum_topic_content" data-url="<?php echo $this->request->base;?>/forums/topic/signature" href="<?php echo $this->request->base;?>/forums/topic/index/signature"><?php echo __d('forum',"Signature");?></a></li>
			<li><a class="<?php echo (!empty($type) && $type == 'favorite')? 'current' : '';?> no-ajax" href="<?php echo $this->request->base;?>/forums/topic/index/favorite"><?php echo __d('forum',"Favorite");?></a></li>
			<li><a class="<?php echo (!empty($type) && $type == 'subscribe')? 'current' : '';?> no-ajax" href="<?php echo $this->request->base;?>/forums/topic/index/subscribe"><?php echo __d('forum',"Subscription");?></a></li>
		</ul>
	</div>
</div>