<?php
echo $this->Html->css(array('jquery.mp'), null, array('inline' => false));
echo $this->Html->script(array('jquery.mp.min'), array('inline' => false));

$filter = array(
    'all' => __d('activitylog','All'),
    'like' => __d('activitylog','Like'),
    'dislike' => __d('activitylog','Dislike'),
    'comment' => __d('activitylog','Comment'),
    'share' => __d('activitylog','Share'),
    'album' => __d('activitylog','Album'),
    'blog' => __d('activitylog','Blog'),
    'event' => __d('activitylog','Event'),
    'group' => __d('activitylog','Group'),
    'photo' => __d('activitylog','Photo'),
    'topic' => __d('activitylog','Topic'),
    'video' => __d('activitylog','Video'),
);
?>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooActivitylog"], function($,mooActivitylog) {
        mooActivitylog.initOnView();
    });
</script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooActivitylog'), 'object' => array('$', 'mooActivitylog'))); ?>
    mooActivitylog.initOnView();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
<div class="bar-content">
    <div class="profile-info-menu">
        <?php echo $this->element('profilenav', array("cmenu" => "activitylog"));?>
    </div>
</div>
<?php $this->end(); ?>

<div class="bar-content">
    <div class="content_center">
        <div class="mo_breadcrumb">
            <h1><?php echo __d('activitylog','Activity log')?></h1>
        </div>
        <div class="form-group log-filter">
            <div class="col-md-12">
                <?php echo $this->Form->select('filter_log', $filter, array('value' => $type, 'empty' => false),array('class'=>'form-control')); ?>
            </div>
            <div class="clear"></div>
        </div>
        <ul id="list-content" class="activity-log-list">
            <?php echo $this->element( 'lists/activities_list', array() ); ?>
        </ul>
    </div>
</div>
