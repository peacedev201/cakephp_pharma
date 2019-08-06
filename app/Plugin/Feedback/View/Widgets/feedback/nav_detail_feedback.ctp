<div class="box2 box2_feedback">
    <h3><?php echo __d('feedback', 'Feedback Creator')?></h3>
    <div class="box_content">
        <?php if(!empty($aFeedback['User']['name'])):?>
            <?php echo $this->Moo->getImage( array('User' => $aFeedback['User']) , array("prefix" => "200_square", "class" => "main-feedback-creator", "alt"=>h($aFeedback['User']['name']) )); ?>
        <?php else:?>
            <img alt="<?php echo h($aFeedback['Feedback']['fullname'])?>" class="main-feedback-creator" prefix="200_square" src="<?php echo $this->request->base;?>/user/img/noimage/Male-user.png">
        <?php endif;?>
        <div class="menu box_content feedback-creator-name">

            <?php if(!empty($aFeedback['User']['name'])):?>
                <a href="<?php echo $this->request->base?>/users/view/<?php echo $aFeedback['User']['id']?>"> <?php echo h($aFeedback['User']['name'])?></a>         
                <?php if ( !empty($uid) && ($uid != $aFeedback['User']['id']) && !$areFriends ): ?>
                <a href="<?php echo $this->request->base?>/friends/ajax_add/<?php echo $aFeedback['User']['id']?>" data-target="#themeModal" data-toggle="modal" class="" title="<?php printf( __d('feedback', 'Send %s a friend request'), h($aFeedback['User']['name']) )?>"> <?php echo __d('feedback', 'Add as Friend')?></a>
                <?php endif; ?> 
            <?php else:?>
                    <a href="javascript:void(0)">
                        <!--<i class="icon-user icon-small"></i>--> 
                        <?php echo h($aFeedback['Feedback']['fullname'])?>
                    </a>
            <?php endif;?>

        </div>
    </div>
</div>
<?php //echo $this->element('hooks', array('position' => 'blog_detail_sidebar') ); ?>

<?php if (!empty($tags)):?>
<div class="box2 box2_feedback">
    <h3><?php echo __d('feedback', 'Tags')?></h3>
    <div class="box_content">
        <?php echo $this->element( 'blocks/tags_item_block' ); ?>
    </div>
</div>
<?php endif;?>