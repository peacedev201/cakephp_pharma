<?php if ($page == 1):?>
	<div class="content_center_home">
		<?php if ($type == 'home' || ($uid == $param)):?>
	    	<div class="mo_breadcrumb question_tab_header">
	            <h1><?php echo __d('question','Questions');?></h1>
	            <a href="<?php echo $this->request->base?>/questions/create" class="button button-action topButton button-mobi-top"><?php echo __d('question','Create a new question');?></a>
	        </div>
        <?php else:?>
        	<div class="mo_breadcrumb question_tab_header">
	            <h1><?php echo __d('question','Questions');?></h1>
	        </div>
        <?php endif;?>
		<ul id="list-content" class="list_question_browse question-content-list">
			<?php if (count($questions)):?>
				<?php echo $this->element('Question.lists/questions');?>
			<?php else:?>		
				<li class="clear text-center"><?php echo __d('question','No more results found');?></li>
			<?php endif;?>
		</ul>
	</div>
<?php return; endif;?>
<?php
	if  (count($questions)):
		echo $this->element('Question.lists/questions');
	else: 
?>
	<li>
		<?php echo __d('question','No more results found')?>
	</li>
<?php endif;?>