<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooContest"], function($,mooContest) {
    	mooContest.initMyContests();
    });
</script>
<?php else: ?>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooContest'), 'object' => array('$', 'mooContest'))); ?>
	mooContest.initMyContests();
<?php $this->Html->scriptEnd(); ?> 
<?php endif; ?>
<div class="content_center_home">
    <div class="mo_breadcrumb">
        <div class="my_contest_ul">
            <a class="active"  id="my_contest" href="javascript:void(0);"><?php echo __d('contest', 'My Posted Contests') ?></a>
            <a id="my_submit_contest" href="javascript:void(0);"><?php echo __d('contest', 'My Joined Contests') ?></a>
        </div>
    </div>
    <ul id="contest-content">
        <?php echo $this->element('lists/contest_list', array('contests' => $contests, 'more_url' => $more_url, 'is_more_url' => $is_more_url, 'params' => $params)); ?>
    </ul>
</div>