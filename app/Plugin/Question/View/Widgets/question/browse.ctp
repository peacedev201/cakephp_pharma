<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooQuestion'), 'object' => array('$', 'mooQuestion'))); ?>
	mooQuestion.initBrowseQuestion('<?php echo $type;?>');
<?php $this->Html->scriptEnd(); ?>
<div class="bar-content">
    <div class="content_center">
		<div class="row select-category q_category">
			<div class="col-xs-6 current-category">
				<span>
					<?php
						switch ($type)
						{
							case 'friend':
								echo __d('question',"Friends' Questions");
								break;
							case 'my':
								echo __d('question','My Questions');
								break;
							case 'favorites':
								echo __d('question','My Favorite Questions');
								break;
							default:
								echo __d('question','Browse Questions');
								break;
						}
						
					?>
				</span>				
			</div>
			<div class="col-xs-6 text-right">
				<?php if ($uid):?>
	            	<a href="<?php echo $this->request->base?>/questions/create" class="button button-action topButton button-mobi-top"><?php echo __d('question','Create a new question');?></a>
	            <?php endif;?>
			</div>
		</div>
		<?php if ($type == 'all'):?>
			<?php
				$category_item = null;
				if ($category)
				{
					$category_item = MooCore::getInstance()->getModel("Category")->findById($category);
				}
			?>
			<div class="row q_text_search">
				<?php
					if ($is_tag)
					{
						echo __d('question','Search tag').': '.$tag['QuestionTag']['title'];
						if ($category_item)
						{
							echo ' '.__d('question','in').' '.$category_item['Category']['name'];
						}
					}
					else
					{
						if ($keyword)
						{
							echo __d('question','Search keyword').': '.htmlspecialchars($keyword);
							if ($category_item)
							{
								echo ' '.__d('question','in').' '.$category_item['Category']['name'];
							}
						}
						else
						{
							if ($category_item)
							{
								echo __d('question','Search category').': '.$category_item['Category']['name'];
							}
						}
					}
				?>
			</div>
			<form method="get" id="question_form_search">
				<input type="hidden" name="page" value="1">
				<div class="row select-category q_keyword">
					<div class="col-xs-6">
						<?php if(Configure::read('core.guest_search') || empty($uid)): ?>
							<input name="keyword" value="<?php echo htmlspecialchars($keyword);?>" placeholder="<?php echo __d('question','Enter keyword to search');?>" type="text" id="keyword">
						<?php endif;?>
					</div>
					<div class="col-xs-6 text-right">
						<div class="select-categories-wrapper">
							<div class="select-categories">
								<?php echo $this->Form->select('category',$categories,array('empty' =>  __d('question','Select category'),'class'=>'select-grey-bg','name'=>'category','value'=>$category)); ?>						
							</div>
						</div>
					</div>
				</div>
			</form>
		<?php endif;?>
		<div class="row question-filter" id="question_filter">
			<ul class="sort-questions">
				<li><a <?php if ($tab == 'last') echo ' class="active"';?> href="<?php echo $this->request->base?>/questions/index/<?php echo $url?>&tab=last" ><?php echo __d("question","Last");?></a></li>
				<li><a <?php if ($tab == 'active') echo ' class="active"';?> href="<?php echo $this->request->base?>/questions/index/<?php echo $url?>&tab=active" ><?php echo __d("question","Active");?></a></li>
				<li><a <?php if ($tab == 'votes') echo ' class="active"';?> href="<?php echo $this->request->base?>/questions/index/<?php echo $url?>&tab=votes" ><?php echo __d("question","Votes");?></a></li>
				<li><a <?php if ($tab == 'feature') echo ' class="active"';?> href="<?php echo $this->request->base?>/questions/index/<?php echo $url?>&tab=feature" ><?php echo __d("question","Featured");?></a></li>
				<li><a <?php if ($tab == 'unanswered') echo ' class="active"';?> href="<?php echo $this->request->base?>/questions/index/<?php echo $url?>&tab=unanswered" ><?php echo __d("question","Unanswered");?></a></li>				
			</ul>
			<div class="tab-content">
				<ul class="list_question_browse">
					<?php if (count($questions)):?>
						<?php echo $this->element('lists/questions',array('questions'=>$questions), array('plugin'=>'Question'));?>
					<?php else:?>		
						<li class="clear text-center no_result"><?php echo __d('question','No more results found');?></li>
					<?php endif;?>
					
				</ul>
			    <div class="pagination">
			        <?php echo $this->Paginator->first('First');?>&nbsp;
			        <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('question','Prev')) : '';?>&nbsp;
					<?php echo $this->Paginator->numbers();?>&nbsp;
					<?php echo $this->Paginator->hasPage(2) ?  $this->Paginator->next(__d('question','Next')) : '';?>&nbsp;
					<?php echo $this->Paginator->last('Last');?>
			    </div>
			 </div>       
        </div>
    </div>
</div>