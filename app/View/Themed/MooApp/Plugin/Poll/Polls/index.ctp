<?php
echo $this->Html->css(array('jquery.mp'), null, array('inline' => false));
echo $this->Html->script(array('jquery.mp.min'), array('inline' => false));
$pollModel = MooCore::getInstance()->getModel('Poll.Poll');
$categories = $pollModel->getCateogries(array('user_id'=>$uid));
?>
<style>
button.close {
    display: block;
}
.title-modal
{
	padding-right: 40px;
}
</style>
<div class="bar-content moo_app">
    <div class="content_center">
        <div class="mo_breadcrumb">
        	<?php if ($type == 'all') :?>
	        	<div class="dropdown cat_select_dropdown">
	   			 	<a href="#" data-toggle="dropdown"><span class="text"><?php echo __d('poll','All Categories')?></span> <i class="material-icons">arrow_drop_down</i></a>
					<ul class="dropdown-menu" id="browse">
					    <?php foreach ($categories as $category): ?>
						    <li>
		                		<a class="json-view" href="<?php echo $_SERVER['REQUEST_URI']?>" data-url="<?php echo $this->base?>/poll/polls/browse/category/<?php echo $category['Category']['id'];?>"><?php echo $category['Category']['name']?><span class="badge_counter"><?php echo $category['Category']['item_count']?></span></a>
		                	</li>
					    <?php endforeach; ?>
					</ul>
				</div>
			<?php endif;?>
            <?php if ($uid):?>
            	<a href="<?php echo $this->request->base?>/polls/create" class="topButton mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1"><?php echo __d('poll','Create New Poll');?></a>
            <?php endif;?>
         </div>
         <?php if ($type == 'all') :?>
	         <?php if(Configure::read('core.guest_search') || empty($uid)): ?>
				<div id="filters" style="margin-top:5px">
					<input name="data[keyword]" placeholder="<?php echo __d('poll','Enter keyword to search');?>" rel="polls" class="json-view" type="text" id="keyword">
				</div>
			<?php endif;?>
		<?php endif;?>
		<ul id="list-content" class="poll-content-list">
			<?php if (count($polls)):?>
				<?php echo $this->element('lists/polls', array('is_view_more'=>$is_view_more,'url_more'=>$url_more,'polls' =>$polls), array('plugin'=>'Poll')); ?>
			<?php else:?>		
				<li class="clear text-center"><?php echo __d('poll','No more results found');?></li>
			<?php endif;?>
		</ul>
    </div>
</div>

<script>
function doRefesh()
{
	location.reload();
}
</script>