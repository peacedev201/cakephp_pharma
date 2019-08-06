<div class="bar-content">
    <div class="content_center">
		<?php if (count($groups)):?>
			<div class="question-tags-list">
				<h3><?php echo __d("question","Tags");?></h3>
				<?php foreach ($groups as $key=>$tags):?>
					<div class="wrap-tag-list">
						<span class="character"><?php echo strtoupper($key);?></span>
						<div class="clearfix"></div>
						<?php foreach ($tags as $tag):?>
							<div class="tag-item">
		                    	<a href="<?php echo $tag['QuestionTag']['moo_href']?>" class="q-tag">
		                    		<?php echo $tag['QuestionTag']['title']; ?>
		                    	</a>
		                	</div>
						<?php endforeach;?>
					</div>
				<?php endforeach;?>
			</div>
		<?php endif;?>
    </div>
</div>