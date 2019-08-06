<div class="box2 filter_block">
	<div class="box_content">
		<ul class="list2 menu-list" id="browse">
			<li <?php if (!$type && !$params):?>class="current"<?php endif;?> ><a class="json-view" data-url="<?php echo $this->base?>/poll/polls/browse/all" href="<?php echo $this->base?>/poll/polls"><?php echo __d('poll','All Polls');?></a></li> 
			<?php if (!empty($uid)): ?>
				<li <?php if ($type == 'my'):?>class="current"<?php endif;?>><a class="json-view" href="<?php echo $this->base?>/poll/polls/index/type:my" data-url="<?php echo $this->base?>/poll/polls/browse/my"><?php echo __d('poll','My Polls');?></a></li>
				<li <?php if ($type == 'friend'):?>class="current"<?php endif;?>><a class="json-view" href="<?php echo $this->base?>/poll/polls/index/type:friend" data-url="<?php echo $this->base?>/poll/polls/browse/friend"><?php echo __d('poll',"Friends' Polls");?></a></li> 
			<?php endif;?>                  
			<li class="separate"></li>
			<li class="cat-header"><?php echo __d('poll','Categories')?></li>
			<?php foreach ($categories as $category):?>
				<li <?php if ($params == $category['Category']['id']):?>class="current"<?php endif;?>>
                	<a class="json-view" href="<?php echo $this->base?>/poll/polls/index/category:<?php echo $category['Category']['id'];?>" data-url="<?php echo $this->base?>/poll/polls/browse/category/<?php echo $category['Category']['id'];?>"><?php echo $category['Category']['name']?><span class="badge_counter"><?php echo $category['Category']['item_count']?></span></a>
                </li>
			<?php endforeach;?>
		</ul>    
		<?php if(Configure::read('core.guest_search') || empty($uid)): ?>
			<div id="filters" style="margin-top:5px">
				<input name="data[keyword]" placeholder="<?php echo __d('poll','Enter keyword to search');?>" rel="polls" class="json-view" type="text" id="keyword">
			</div>
		<?php endif;?>
	</div>
</div>