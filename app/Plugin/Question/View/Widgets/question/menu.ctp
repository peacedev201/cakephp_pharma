<div class="box2 filter_block">
	<div class="box_content">
		<ul class="list2 menu-list">
			<li <?php if ($type == 'all'):?>class="current"<?php endif;?> ><a href="<?php echo $this->base?>/questions/index/all"><?php echo __d('question','All Questions');?></a></li> 
			<?php if (!empty($uid)): ?>				
				<li <?php if ($type == 'friend'):?>class="current"<?php endif;?>><a href="<?php echo $this->base?>/questions/index/friend"><?php echo __d('question',"Friends' Questions");?></a></li>
				<li <?php if ($type == 'my'):?>class="current"<?php endif;?>><a href="<?php echo $this->base?>/questions/index/my"><?php echo __d('question','My Questions');?></a></li>
				<li <?php if ($type == 'favorites'):?>class="current"<?php endif;?>><a href="<?php echo $this->base?>/questions/index/favorites"><?php echo __d('question','My Favorite Questions');?></a></li>  
			<?php endif;?>
			<li <?php if ($type == 'badges'):?>class="current"<?php endif;?> ><a href="<?php echo $this->base?>/questions/badges"><?php echo __d('question','Badges');?></a></li>
			<li <?php if ($type == 'ratings'):?>class="current"<?php endif;?> ><a href="<?php echo $this->base?>/questions/ratings"><?php echo __d('question','Top Q&A Contributors');?></a></li>
			<li class="separate"></li>			
		</ul>
		<ul class="list2 menu-list">
			<?php $category_id = ($key == 'category') ? $value : null; ?>
		    <li class="cat-header cat_toggle"><?php echo __d('question','Categories') ?></li>
		    <?php foreach ($categories as $cat): ?>
		        <?php if ($cat['Category']['header']): ?>
		            <li class="category_header"><?php echo $cat['Category']['name'] ?></li>
		
		            <?php foreach ($cat['children'] as $subcat): ?>
		
		                <li class="sub-cat <?php if ($category_id == $subcat['Category']['id']) echo 'current'; ?>">
		                    <a href="<?php echo $this->request->base?>/questions/index/all?category=<?php echo $subcat['Category']['id']?>" <?php if (!empty($subcat['Category']['description'])): ?>class="tip" title="<?php echo nl2br($subcat['Category']['description']) ?>"<?php endif ?>><?php echo $subcat['Category']['name'] ?> 
		                      
		                     </a>
		                </li>
		
		            <?php endforeach; ?>
		        <?php else: ?>
		
		            <li <?php if ($category_id == $cat['Category']['id']) echo 'class="current"'; ?>>
		                <a href="<?php echo $this->request->base?>/questions/index/all?category=<?php echo $cat['Category']['id']?>" <?php if (!empty($cat['Category']['description'])): ?>class="tip" title="<?php echo nl2br($cat['Category']['description']) ?>"<?php endif ?>><?php echo $cat['Category']['name'] ?> 
		                    
		                </a>
		            </li>
		        <?php endif; ?>
		    <?php endforeach; ?>
		</ul>
	</div>
</div>