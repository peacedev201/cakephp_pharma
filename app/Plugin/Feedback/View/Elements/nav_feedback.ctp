<div class="box2 filter_block box2_feedback">
    <h3 class="visible-xs visible-sm"><?php echo __d('feedback', 'Browse')?></h3>
    <div class="box_content">
        <ul class="list2 menu-list" id="browse">
            <li <?php if(empty($type)):?>class="current"<?php endif;?> id="browse_all"><a data-url="<?php echo $this->request->base.$url_feedback?>/ajax_browse/all" href="<?php echo $this->request->base.$url_feedback?>"><?php echo __d('feedback', 'All Feedback')?></a></li>
            <?php if(MooCore::getInstance()->getViewer(true) > 0):?>
            <li><a data-url="<?php echo $this->request->base.$url_feedback?>/ajax_browse/my" href="#"><?php echo __d('feedback', 'My Feedback')?></a></li>
            <li><a data-url="<?php echo $this->request->base.$url_feedback?>/ajax_browse/friends" href="#"><?php echo __d('feedback',"Friends' Feedback")?></a></li>
            <?php endif;?>
            <li class="separate"></li>
            <li class="cat-header"><?php echo __d('feedback', 'Categories')?></li>
            <?php foreach($aCategories as $aCategory): ?>
            <li <?php if(!empty($type) && $type == 'cat' && $param == $aCategory['FeedbackCategory']['id']):?>class="current"<?php endif;?>>
                <a data-url="<?php echo $this->request->base.$url_feedback?>/ajax_browse/cat/<?php echo  $aCategory['FeedbackCategory']['id']?>" href="#" title="<?php echo  $aCategory['FeedbackCategory']['description']?>">
                    <?php echo  $aCategory['FeedbackCategory']['name']?>
                    <span class="badge_counter"><?php echo $aCategory['FeedbackCategory']['use_time']?></span>
                </a>
            </li>
            <?php endforeach;?>
            <li class="separate"></li>
            <li class="cat-header"><?php echo __d('feedback', 'Status')?></li>
            <?php foreach($aStatuses as $aStatus): ?>
                <li <?php if(!empty($type) && $type == 'sta' && $param == $aStatus['FeedbackStatus']['id']):?>class="current"<?php endif;?>>
                    <a data-url="<?php echo $this->request->base.$url_feedback?>/ajax_browse/sta/<?php echo  $aStatus['FeedbackStatus']['id']?>" href="#">
                        <?php echo  $aStatus['FeedbackStatus']['name']?>
                        <span class="badge_counter"><?php echo $aStatus['FeedbackStatus']['use_time']?></span>
                    </a>
                </li>
            <?php endforeach ?>  
        </ul>
        <div id="filters" style="margin-top:5px">
            <?php echo $this->Form->text( 'keyword', array( 'placeholder' => __d('feedback', 'Enter keyword to search'), 'rel' => 'feedback/feedbacks' ) );?>
        </div>
    </div>
</div>
<?php //echo $this->element('blocks/tags_block'); ?>