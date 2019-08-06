<div id="forum_topic_content">
    <div class="bar-content">
    <div class="content_center">
        <?php if(!empty($title)): ?>
        <div class="mo_breadcrumb">
            <h1><?php echo $title;?></h1>
            <?php if($type == 'started' || $type == 'replies'):?>
                <form method="get" class="form_index_search_topic" action="<?php echo $this->request->base. '/forums/topic/index/'.$type;?>" id="form_index_search_topic">
                    <?php echo $this->Form->text('search_keyword', array('class' => 'json-view topic-keyword', 'placeholder' => __d('forum','Search'), 'value' => !empty($keyword) ? $keyword : ''));?>
                    <button class="btn btn-action" id="btn_index_search_topic"><?php echo __d('forum', 'Search'); ?></button>
                </form>
            <?php endif;?>
        </div>
        <?php endif; ?>

        <div class="forum-list-topics">
            <div id="list-content">
                <?php
                switch($type){
                    case 'replies':
                        echo $this->element( 'lists/my_list_replies', array('replies' => $topics) );
                        break;
                    case 'subscribe':
                        echo $this->element( 'my_forum_subscribe', array() );
                        echo $this->element( 'my_topic_subscribe', array() );
                        break;
                    default:
                        echo $this->element( 'lists/topic_list_m', array() );
                        break;
                };?>
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
