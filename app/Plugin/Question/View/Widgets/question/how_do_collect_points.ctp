<?php
	$helper = MooCore::getInstance()->getHelper("Question_Question");
	$points = $helper->getListPoints();
?>
<div class="box2">        
	<?php if (isset($title_enable) && $title_enable):?>       
		<h3><?php echo $title ?></h3>
	<?php endif;?>
	<div class="box_content">
		<ul class="list_block_point">
			<?php foreach ($points as $point):?>
				<li>
					<div class="qa_points"><?php echo $point['point']?></div>
					<div class="qa_point_desc"><?php echo $point['text']?></div>
				</li>
			<?php endforeach;?>
		</ul>
	</div>
</div>