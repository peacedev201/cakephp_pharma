<div class="box2 filter_block">
    <h3><?php echo $title ?></h3>
    <div class="box_content box-forum-filter box-topic">
        <form method="get" action="<?php echo $this->request->base;?>/forums/topic/index">
            <?php echo $this->Form->text( 'keyword', array( 'placeholder' => '', 'class' => 'form-control json-view topic-keyword', 'value' => !empty($keyword) ? $keyword : '') );?>
            <input type="submit" class="btn btn-action" id="btn_search_topic" value="<?php echo __d('forum', 'Search'); ?>"/>
        </form>
    </div>
</div>
<?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooForum'), 'object' => array('$', 'mooForum'))); ?>
    mooForum.initSearchGlobal();
<?php $this->Html->scriptEnd(); ?>