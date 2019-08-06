<?php if (count($tags)):?>
	<div class="box2">        
        <?php if (isset($title_enable) && $title_enable):?>       
        	<h3><?php echo $title ?></h3>
        <?php endif;?>
        <div class="box_content question-block-tag">
        	<ul class="question-tags">
				<?php foreach ($tags as $tag):?>
				<li>
					<a href="<?php echo $tag['QuestionTag']['moo_href'];?>" class="q-tag">
						<?php echo $tag['QuestionTag']['title'];?>                                
					</a>
				</li>
				<?php endforeach;?>
			</ul>
			<div class="more_tags">
				<a href="<?php echo $this->request->base?>/question/question_tags"><?php echo __d("question","See more tags");?></a>
			</div>
        </div>
    </div>
<?php endif;?>