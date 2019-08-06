<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery', 'mooQuestion'), 'object' => array('$', 'mooQuestion'))); ?>
	mooQuestion.initBrowseQuestion('<?php echo $type;?>');
	$('#li_question li a').click(function(e){
		if ($(this).hasClass('stop'))
		{
			e.preventDefault();
		}
		else
		{
			$(this).addClass('stop');
		} 
	});
	$('#li_question li').removeClass('disabled');
	$('.pagination a').each(function(){
		var href = $(this).attr('href');
		if (href.indexOf('app_no_tab') == -1)
		{
			$(this).attr('href',href + '&app_no_tab=1');
		}
	});
	
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
	            	<a href="<?php echo $this->request->base?>/questions/create" class="topButton btnVideo mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1"><?php echo __d('question','Create a new question');?></a>
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
				<input type="hidden" name="app_no_tab" value="1">
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
			<ul id="li_question" class="sort-questions">
				<li class="disabled"><a <?php if ($tab == 'last') echo ' class="active"';?> href="<?php echo $this->request->base?>/questions/index/<?php echo $url?>&tab=last&app_no_tab=1" ><?php echo __d("question","Last");?></a></li>
				<li class="disabled"><a <?php if ($tab == 'active') echo ' class="active"';?> href="<?php echo $this->request->base?>/questions/index/<?php echo $url?>&tab=active&app_no_tab=1" ><?php echo __d("question","Active");?></a></li>
				<li class="disabled"><a <?php if ($tab == 'votes') echo ' class="active"';?> href="<?php echo $this->request->base?>/questions/index/<?php echo $url?>&tab=votes&app_no_tab=1" ><?php echo __d("question","Votes");?></a></li>
				<li class="disabled"><a <?php if ($tab == 'feature') echo ' class="active"';?> href="<?php echo $this->request->base?>/questions/index/<?php echo $url?>&tab=feature&app_no_tab=1" ><?php echo __d("question","Featured");?></a></li>
				<li class="disabled"><a <?php if ($tab == 'unanswered') echo ' class="active"';?> href="<?php echo $this->request->base?>/questions/index/<?php echo $url?>&tab=unanswered&app_no_tab=1" ><?php echo __d("question","Unanswered");?></a></li>				
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

<script>
function doRefesh()
{
	location.reload();
}
</script>