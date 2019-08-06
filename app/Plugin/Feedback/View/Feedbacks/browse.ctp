
<div class="bar-content">
    <div class="content_center">
        
    	<div class="mo_breadcrumb">
            <?php if (!$bBlockFeeback && $permission_create_feedback): ?>
            	<a href="<?php echo $this->request->base.$url_feedback.$url_ajax_create?>"  data-backdrop="static" class="topButton mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" title="<?php echo __d('feedback', 'Create New Feedback')?>"><?php echo __d('feedback', 'Create New Feedback')?></a>
			<?php endif ?>
        </div>
		<ul class="feedback-content-list" id="list-content">
			<?php echo $this->element( 'lists/feedbacks_list', array( 'more_url' => $more_url ) ); ?>	
		</ul>
	    
	</div>
</div>