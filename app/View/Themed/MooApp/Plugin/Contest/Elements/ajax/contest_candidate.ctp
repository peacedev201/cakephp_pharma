<?php $helper = MooCore::getInstance()->getHelper('Contest_Contest');?>
<?php if(!empty($contest)): ?>
	<?php if($this->request->is('ajax')): ?>
	<script>
	    require(["jquery","mooContest"], function($,mooContest) {
	        mooContest.initCandidateList();
	    });
	</script>
	<?php else: ?>
	    <?php $this->Html->scriptStart(array('inline' => false,'requires'=>array('jquery','mooContest'),'object'=>array('$','mooContest'))); ?>
	        mooContest.initCandidateList();
	    <?php $this->Html->scriptEnd(); ?>
	<?php endif; ?>
	<h2><?php echo __d('contest', 'Candidates') ?></h2>
	<ul id="list-content">
	    <?php echo $this->element( 'lists/candidate_list', array('candidates' => $candidates, 'more_url' => $more_url, 'is_more_url' => $is_more_url , 'params' => $params) ); ?>
	</ul>
<?php else : ?>
	<p><?php echo __d('contest', 'Contest does not exist') ?></p>
<?php endif; 
