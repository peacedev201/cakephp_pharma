<?php $this->setNotEmpty('west');?>
<?php $this->start('west'); ?>
<?php echo $this->element('menu');?>
<?php $this->end(); ?>

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

        <div class="content-pagination forum-pagination clearfix">
            <div class="pagination-count">
                <?php if($type == 'replies'){
                    echo __d('forum','Viewing %s replies', $this->Paginator->counter('{:current}')).' - '.$this->Paginator->counter('{:start}').__d('forum',' through %s (of %s total)', $this->Paginator->counter('{:end}'), $this->Paginator->counter('{:count}')) ;
                }else{
                    echo __d('forum','Viewing %s topics', $this->Paginator->counter('{:current}')).' - '.$this->Paginator->counter('{:start}').__d('forum',' through %s (of %s total)', $this->Paginator->counter('{:end}'), $this->Paginator->counter('{:count}')) ;
                }?>
            </div>
            <ul class="pagination">
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('forum', 'First'), array('class' => 'paginate_button previous', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('forum', 'First').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('forum', 'Prev'), array('class' => 'paginate_button previous', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('forum', 'Previous').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
                <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'separator' => '', 'tag' => 'li', 'currentLink' => true, 'currentClass' => 'active', 'currentTag' => 'span')); ?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('forum', 'Next'), array('class' => 'paginate_button next', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('game', 'Next').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('forum', 'Last'), array('class' => 'paginate_button next', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('game', 'Last').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
            </ul>
        </div>

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
        <div class="content-pagination forum-pagination clearfix">
            <div class="pagination-count">
                <?php echo __d('forum','Viewing %s topics', $this->Paginator->counter('{:current}')).' - '.$this->Paginator->counter('{:start}').__d('forum',' through %s (of %s total)', $this->Paginator->counter('{:end}'), $this->Paginator->counter('{:count}')) ;?>
            </div>
            <ul class="pagination">
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->first(__d('forum', 'First'), array('class' => 'paginate_button previous', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('forum', 'First').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->prev(__d('forum', 'Prev'), array('class' => 'paginate_button previous', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('forum', 'Previous').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
                <?php echo $this->Paginator->numbers(array('class' => 'paginate_button', 'separator' => '', 'tag' => 'li', 'currentLink' => true, 'currentClass' => 'active', 'currentTag' => 'span')); ?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->next(__d('forum', 'Next'), array('class' => 'paginate_button next', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('game', 'Next').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
                <?php echo $this->Paginator->hasPage(2) ? $this->Paginator->last(__d('forum', 'Last'), array('class' => 'paginate_button next', 'tag' => 'li'), '<a href="javascript:void(0)">'.__d('game', 'Last').'</a>', array('class' => 'paginate_button disabled', 'tag' => 'li', 'escape' => false)) : '';?>
            </ul>
        </div>
    </div>
</div>
</div>

<?php if($this->request->is('ajax')): ?>
<script type="text/javascript">
    require(["jquery","mooForum"], function($,mooForum) {
        mooForum.initOnTopicListing();
        mooForum.initOnTopicBrowse();
    });
</script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false, 'domReady' => true, 'requires'=>array('jquery','mooForum'), 'object' => array('$', 'mooForum'))); ?>
        mooForum.initOnTopicListing();
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>
