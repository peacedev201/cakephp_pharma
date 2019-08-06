<?php
$reactionList = array(
    REACTION_DISLIKE => __d('reaction','Like'),//__d('reaction','Dislike'),
    REACTION_LIKE => __d('reaction','Like'),
    REACTION_LOVE => __d('reaction','Love'),
    REACTION_HAHA => __d('reaction','Haha'),
    REACTION_WOW => __d('reaction','Wow'),
    REACTION_SAD => __d('reaction','Sad'),
    REACTION_ANGRY => __d('reaction','Angry'),
    REACTION_COOL => __d('reaction','Cool'),
    REACTION_CONFUSED => __d('reaction','Confused')
);
?>
<div id="<?php echo $element_prefix.'reaction_'.$element_id?>" class="reaction-options <?php echo $class?>">
    <div class="react-overview"></div>
    <a class="react-btn" href="#" data-id="<?php echo $data_id ?>" data-type="<?php echo $data_type ?>" data-reaction="<?php echo $current_reaction_type ?>" data-label="<?php echo $reactionList[$current_reaction_type] ?>"><span class="<?php echo $current_reaction_class ?>"><i class="material-icons">thumb_up</i><?php echo $reactionList[$current_reaction_type] ?></span></a>
    <div class="reacts">

        <?php //if(Configure::read('Reaction.reaction_like_enabled')): ?>
        <div class="react-circle<?php echo (($current_reaction_active == REACTION_LIKE)? ' react-active': '') ?>" data-id="<?php echo $data_id ?>" data-type="<?php echo $data_type ?>" data-reaction="<?php echo REACTION_LIKE ?>" data-label="<?php echo  __d('reaction', 'Like') ?>">
            <div class="react-icon react-like" data-name="<?php echo __d('reaction', 'Like') ?>"></div>
        </div>
        <?php //endif; ?>
        <?php if(Configure::read('Reaction.reaction_love_enabled')): ?>
        <div class="react-circle<?php echo  (($current_reaction_active == REACTION_LOVE)? ' react-active': '') ?>" data-id="<?php echo $data_id ?>" data-type="<?php echo $data_type ?>" data-reaction="<?php echo REACTION_LOVE ?>" data-label="<?php echo __d('reaction', 'Love') ?>">
            <div class="react-icon react-love" data-name="<?php echo __d('reaction', 'Love') ?>"></div>
        </div>
        <?php endif; ?>
        <?php if(Configure::read('Reaction.reaction_haha_enabled')): ?>
        <div class="react-circle<?php echo  (($current_reaction_active == REACTION_HAHA)? ' react-active': '') ?>" data-id="<?php echo $data_id ?>" data-type="<?php echo $data_type ?>" data-reaction="<?php echo REACTION_HAHA ?>" data-label="<?php echo __d('reaction', 'Haha') ?>">
            <div class="react-icon react-haha" data-name="<?php echo __d('reaction', 'Haha') ?>"></div>
        </div>
        <?php endif; ?>
        <?php if(Configure::read('Reaction.reaction_wow_enabled')): ?>
        <div class="react-circle<?php echo (($current_reaction_active == REACTION_WOW)? ' react-active': '') ?>" data-id="<?php echo $data_id ?>" data-type="<?php echo $data_type ?>" data-reaction="<?php echo REACTION_WOW ?>" data-label="<?php echo __d('reaction', 'Wow') ?>">
            <div class="react-icon react-wow" data-name="<?php echo __d('reaction', 'Wow') ?>"></div>
        </div>
        <?php endif; ?>
        <?php if(Configure::read('Reaction.reaction_cool_enabled')): ?>
        <div class="react-circle<?php echo (($current_reaction_active == REACTION_COOL)? ' react-active': '') ?>" data-id="<?php echo $data_id ?>" data-type="<?php echo $data_type ?>" data-reaction="<?php echo REACTION_COOL ?>" data-label="<?php echo __d('reaction', 'Cool') ?>">
            <div class="react-icon react-cool" data-name="<?php echo __d('reaction', 'Cool') ?>"></div>
        </div>
        <?php endif; ?>
        <?php if(Configure::read('Reaction.reaction_confused_enabled')): ?>
        <div class="react-circle<?php echo (($current_reaction_active == REACTION_CONFUSED)? ' react-active': '') ?>" data-id="<?php echo $data_id ?>" data-type="<?php echo $data_type ?>" data-reaction="<?php echo REACTION_CONFUSED ?>" data-label="<?php echo __d('reaction', 'Confused') ?>">
            <div class="react-icon react-confused" data-name="<?php echo __d('reaction', 'Confused') ?>"></div>
        </div>
        <?php endif; ?>
        <?php if(Configure::read('Reaction.reaction_sad_enabled')): ?>
        <div class="react-circle<?php echo (($current_reaction_active == REACTION_SAD)? ' react-active': '') ?>" data-id="<?php echo $data_id ?>" data-type="<?php echo $data_type ?>" data-reaction="<?php echo REACTION_SAD ?>" data-label="<?php echo __d('reaction', 'Sad') ?>">
            <div class="react-icon react-sad" data-name="<?php echo __d('reaction', 'Sad') ?>"></div>
        </div>
        <?php endif; ?>
        <?php if(Configure::read('Reaction.reaction_angry_enabled')): ?>
        <div class="react-circle<?php echo (($current_reaction_active == REACTION_ANGRY)? ' react-active': '') ?>" data-id="<?php echo $data_id ?>" data-type="<?php echo $data_type ?>" data-reaction="<?php echo REACTION_ANGRY ?>" data-label="<?php echo __d('reaction', 'Angry') ?>">
            <div class="react-icon react-angry" data-name="<?php echo __d('reaction', 'Angry') ?>"></div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if($this->request->is('ajax')): ?>
    <script type="text/javascript">
        require(["jquery","mooReaction"], function($, mooReaction) {
            <?php if($action == 'Activity'): ?>
            mooReaction.initActivityReaction("<?php echo $element_id ?>");
            <?php elseif($action == 'Comment'): ?>
            mooReaction.initCommentReaction('<?php echo $element_id ?>');
            <?php elseif ($action == 'Item'): ?>
            mooReaction.initItemReaction('<?php echo $element_id ?>');
            <?php elseif ($action == 'Photo'): ?>
            mooReaction.initPhotoReaction('<?php echo $element_id ?>');
            <?php endif; ?>
        });
    </script>
<?php else: ?>
    <?php $this->Html->scriptStart(array('inline' => false,'requires'=>array('jquery', 'mooReaction'),'object'=>array('$', 'mooReaction'))); ?>
    <?php if($action == 'Activity'): ?>
        mooReaction.initActivityReaction("<?php echo $element_id ?>");
    <?php elseif($action == 'Comment'): ?>
        mooReaction.initCommentReaction('<?php echo $element_id ?>');
    <?php elseif ($action == 'Item'): ?>
        mooReaction.initItemReaction('<?php echo $element_id ?>');
    <?php elseif ($action == 'Photo'): ?>
        mooReaction.initPhotoReaction('<?php echo $element_id ?>');
    <?php endif; ?>
    <?php $this->Html->scriptEnd(); ?>
<?php endif; ?>