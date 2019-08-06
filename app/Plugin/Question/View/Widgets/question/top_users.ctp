<?php if (count($users)):?>
	<div class="box2">        
        <?php if (isset($title_enable) && $title_enable):?>       
        	<h3><?php echo $title ?></h3>
        <?php endif;?>
        <div class="box_content">
        	<ul class="block_question_top">
        		<?php foreach ($users as $key=>$user):?>
					<li>
						<span class="number"><?php echo $key + 1;?></span>
						<span class="username">
							<a class="moocore_tooltip_link" data-item_id="<?php echo $user['User']['id']?>" data-item_type="user" href="<?php echo $user['User']['moo_href']?>" title="<?php echo $user['User']['moo_title']?>"><?php echo $user['User']['moo_title']?></a>
						</span>
						<span class="question-count" title="<?php echo $user['QuestionUser']['total_question']?> <?php echo __d("question","Question(s)");?>">
							<i class="material-icons">question_answer</i>
							<span><?php echo $user['QuestionUser']['total_question']?></span>
						</span>
					</li>
				<?php endforeach;?>	        	        	
			</ul>        	
        </div>
    </div>
<?php endif;?>