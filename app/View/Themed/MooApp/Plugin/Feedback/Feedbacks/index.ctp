<?php 
echo $this->addPhraseJs(array(
    'tmaxsize' => __d('feedback', 'Can not upload file more than ' . $file_max_upload),
    'tdesc' => __d('feedback', 'Drag or click here to upload photo'),
	'tdescfile' => __d('feedback', 'Click or Drap your file here'),
));
echo $this->Html->css(array('jquery.mp'), null, array('inline' => false));
echo $this->Html->script(array('jquery.mp.min'), array('inline' => false)); 

echo $this->Html->css('Feedback.feedback.css');

echo $this->Html->script(array('jquery.fileuploader', 'Feedback.feedback'), array('inline' => false));
echo $this->Html->css(array( 'fineuploader' ));

?>

<div class="bar-content">
    <div class="content_center">
        
    	<div class="mo_breadcrumb">
            <?php  if ($type != 'cat') :?>
	        	<div class="dropdown cat_select_dropdown">
	   			 	<a href="#" data-toggle="dropdown"><span class="text"><?php echo __('All Categories')?></span> <i class="material-icons">arrow_drop_down</i></a>
					<ul class="dropdown-menu" id="browse">
					    <?php foreach ($aCategories as $category): ?>
						    <li>
		                		<a class="json-view" href="<?php echo $this->base?>/feedback/feedbacks/index/cat/<?php echo $category['FeedbackCategory']['id'];?>" data-url="<?php echo $this->base?>/feedback/feedbacks/ajax_browse/cat/<?php echo $category['FeedbackCategory']['id'];?>"><?php echo $category['FeedbackCategory']['name']?><span class="badge_counter"><?php echo $category['FeedbackCategory']['use_time']?></span></a>
		                	</li>
					    <?php endforeach; ?>
					</ul>
				</div>
            <?php  endif;?>          
            <?php if (!$bBlockFeeback && $permission_create_feedback): ?>
            	<a href="<?php echo $this->request->base.$url_feedback.$url_ajax_create?>"  data-backdrop="static" class="topButton mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" title="<?php echo __d('feedback', 'Create New Feedback')?>"><?php echo __d('feedback', 'Create New Feedback')?></a>
			<?php endif ?>
        </div>
        <?php  if ($type != 'cat') :?>
	         <?php if(Configure::read('core.guest_search') || empty($uid)): ?>
				<div id="filters" style="margin-top:5px">
					<input name="data[keyword]" placeholder="<?php echo __d('feedback','Search Feedback');?>" rel="feedback/feedbacks" type="text" id="keyword">
				</div>
			<?php endif;?>
            <?php  endif;?>
		<ul class="feedback-content-list" id="list-content">
			<?php echo $this->element( 'lists/feedbacks_list', array( 'more_url' => $more_url ) ); ?>	
		</ul>
	    
	</div>
</div>
<?php if ($is234): ?>
            <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery','mooFeedback'), 'object' => array('$','mooFeedback'))); ?>
                mooFeedback.initOnIndex();
            <?php $this->Html->scriptEnd(); ?>                
<?php endif; ?>

<script>
function doRefesh()
{
	window.location.href = '<?php echo $this->request->base;?>/feedbacks/feedbacks?app_no_tab=1';
}
</script>