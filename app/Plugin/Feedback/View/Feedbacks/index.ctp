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

<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>

<?php echo $this->element('Feedback.nav_feedback');?>

<?php $this->end(); ?>

<div class="bar-content">
    <div class="content_center">
        
    	<div class="mo_breadcrumb">
            <h1><?php echo __d('feedback', 'Feedback')?></h1>
            <?php if (!$bBlockFeeback && $permission_create_feedback): ?>
            	<a href="<?php echo $this->request->base.$url_feedback.$url_ajax_create?>"  data-backdrop="static" data-target="#themeModal" data-toggle="modal" class="button button-action topButton button-mobi-top" title="<?php echo __d('feedback', 'Create New Feedback')?>"><?php echo __d('feedback', 'Create New Feedback')?></a>
			<?php endif ?>
        </div>

		<ul class="list6 comment_wrapper list-mobile" id="list-content">
			<?php echo $this->element( 'lists/feedbacks_list', array( 'more_url' => $more_url ) ); ?>	
		</ul>
	    
	</div>
</div>
<?php if ($is234): ?>
            <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires' => array('jquery','mooFeedback'), 'object' => array('$','mooFeedback'))); ?>
                mooFeedback.initOnIndex();
            <?php $this->Html->scriptEnd(); ?>
                
<?php endif; ?>