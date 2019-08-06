<ul class="question-content-block">
    <?php foreach ($questions as $question): ?>
		<li>
			<span class="topic-avatar">
				<a href="<?php echo $question['Question']['moo_href'];?>">
					<img src="<?php echo $this->Moo->getItemPhoto(array('User' => $question['User']),array( 'prefix' => '50_square'), array(), true);?>" class="avatar" alt="">
				</a>
			</span>
			<div class="topic-title">
				<a href="<?php echo $question['Question']['moo_href'];?>">
					<?php echo $question['Question']['moo_title'];?><?php if ($question['Question']['feature']):?>  <span class="tip question-icon-fetured" original-title="<?php echo __d("question","Featured");?>"></span><?php endif;?>
				</a>
			</div>
			
			<div class="clear"></div>			
		</li>
	<?php endforeach; ?>
</ul>