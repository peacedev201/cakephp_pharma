<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>

<?php echo $this->element('menu');?>

<?php $this->end(); ?>

<div id="forum_topic_content">
    <div class="bar-content">
    <div class="content_center">
        <div class="mo_breadcrumb">
            <h1><?php echo __d('forum','Search results for ')."'". h($keyword) ."'";?></h1>
            <div class="topic-filter">
                <form method="get" action="<?php echo $this->request->base;?>/forums/topic/index">
                <?php echo $this->Form->text( 'keyword', array( 'placeholder' => '', 'class' => 'json-view topic-keyword', 'value' => !empty($keyword) ? $keyword : '') );?>
                <input type="submit" class="btn btn-action" id="btn_search_topic" value="<?php echo __d('forum', 'Search'); ?>"/>
                </form>
            </div>
        </div>

        <div class="row pagination">
            <span><?php echo __d('forum','Viewing %s results', $this->Paginator->counter('{:current}')).' - '.$this->Paginator->counter('{:start}').__d('forum',' through %s (of %s total)', $this->Paginator->counter('{:end}'), $this->Paginator->counter('{:count}')) ;?></span>
            <?php echo $this->Paginator->first(__d('forum','First'));?>&nbsp;
            <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('forum','Prev')) : '';?>&nbsp;
            <?php echo $this->Paginator->numbers();?>&nbsp;
            <?php echo $this->Paginator->hasPage(2) ?  $this->Paginator->next(__d('forum','Next')) : '';?>&nbsp;
            <?php echo $this->Paginator->last(__d('forum','Last'));?>
        </div>

        <div class="wrap-list-topic">
            <div id="list-content">
                <?php if($type == 'hashtag'):?>
                    <?php echo $this->element('lists/topic_list_m');?>
                <?php else:?>
                    <?php echo $this->element('lists/search_topic_list_m');?>
                <?php endif;?>

            </div>
        </div>
    </div>
</div>
</div>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooForum"], function($,mooForum) {
        mooForum.initOnTopicListing();
    });
</script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooForum'), 'object' => array('$', 'mooForum'))); ?>
        mooForum.initOnTopicListing();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>

