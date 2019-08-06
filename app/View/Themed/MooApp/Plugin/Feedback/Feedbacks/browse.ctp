<script>
function doRefesh()
{
	window.location.href = '<?php echo $this->request->base;?>/feedbacks/feedbacks?app_no_tab=1';
}
</script>

<?php if (in_array($type,array('my','friends')) && $page == 1):?>
	<div class="content_center">
		<?php if (!$bBlockFeeback && $permission_create_feedback): ?>
	    	<div class="title_center p_m_10">
				<a href="<?php echo $this->request->base.$url_feedback.$url_ajax_create?>"  data-backdrop="static" class="topButton mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--raised mdl-button--colored1" title="<?php echo __d('feedback', 'Create New Feedback')?>"><?php echo __d('feedback', 'Create New Feedback')?></a>
	        </div>
        <?php endif;?>
		<ul id="list-content" class="feedback-content-list">
			<?php echo $this->element( 'lists/feedbacks_list', array( 'more_url' => $more_url ) ); ?>	
		</ul>
	</div>
<?php return; endif;?>
<?php
	echo $this->element( 'lists/feedbacks_list', array( 'more_url' => $more_url ) );
?>