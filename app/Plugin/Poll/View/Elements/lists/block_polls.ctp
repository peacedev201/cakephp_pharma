<?php $helper = MooCore::getInstance()->getHelper('Poll_Poll');	?>
<ul class="poll-content-block">
    <?php foreach ($polls as $poll): ?>
		<li class="list-item-inline list-item-inline-text">
			<div class="poll_img">
				<a href="<?php echo $poll['Poll']['moo_href']?>">
					<img width="70" alt="" class="img_wrapper2" src="<?php echo $helper->getImage($poll);?>">	
				</a>
			</div>
			<div class="poll_detail">
				<div class="title-list">
					<a href="<?php echo $poll['Poll']['moo_href']; ?>">
						<?php echo $this->Text->truncate($poll['Poll']['moo_title'],15);?>
						<?php if ($poll['Poll']['feature']):?>
							<img class="poll_feature" alt="feature" src="<?php echo $this->Html->assetUrl('Poll.img/star.png');?>">
						<?php endif;?>
					</a>									
				</div>
				<div class="extra_count">
					<p><?php echo __dn('poll','%s comment', '%s comments', $poll['Poll']['comment_count'], $poll['Poll']['comment_count'] )?> </p>
					<p><?php echo __dn('poll','%s like', '%s likes', $poll['Poll']['like_count'], $poll['Poll']['like_count'] )?></p>
					<p><?php echo __dn('poll','%s answer', '%s answers', $poll['Poll']['answer_count'], $poll['Poll']['answer_count'] )?></p>
					<p><?php echo __dn('poll','%s share', '%s shares', $poll['Poll']['share_count'], $poll['Poll']['share_count'] )?></p>
				</div>
			</div>
			<div class="clear"></div>
		</li>
	<?php endforeach; ?>
</ul>