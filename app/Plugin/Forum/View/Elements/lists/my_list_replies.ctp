<?php if (!empty($replies)): ?>
<?php $helper = MooCore::getInstance()->getHelper('Forum_Forum');?>
<div class="forum-list-reply-body">
<?php foreach ($replies as $reply):?>
    <?php if(!empty($reply['ParentTopic'])):?>
    <div class="forum-reply-item">
        <div class="my-reply-to">
            <?php echo __d('forum','In reply to');?>: <a href="<?php echo $reply['ParentTopic']['moo_href'];?>"><?php echo $reply['ParentTopic']['moo_title'];?></a>
        </div>

        <div class="forum-reply-head">
            <div class="forum-reply-user">
            <?php if(!empty($reply['User']['id'])):?>
                <div class="forum-reply-avatar">
                    <?php echo $this->Moo->getItemPhoto(array('User' => $reply['User']), array('prefix' => '50_square'), array('class' => 'reply-author-avatar'))?>
                </div>
                <div class="forum-reply-info">
                    <span class="forum-reply-name">
                        <?php echo $this->Moo->getName($reply['User'])?>
                    </span>
                    <?php $this->getEventManager()->dispatch(new CakeEvent('element.comments.afterRenderUserNameComment', $this,array('user'=>$reply['User']))); ?>
                    <?php if($helper->checkModerator($reply, array('Forum' => $reply['Forum']))):?>
                        <div class="forum-reply-role"><?php echo __d('forum','Moderator');?></div>
                    <?php endif;?>
                </div>
            <?php else:?>
                <div class="forum-reply-avatar">
                    <a class="user-img-deleted">
                        <img src="<?php echo $this->request->base;?>/user/img/noimage/Unknown-user-sm.png" class="reply-author-avatar" alt="<?php echo __d('forum','Deleted Account');?>" title="<?php echo __d('forum','Deleted Account');?>">
                    </a>
                </div>
                <div class="forum-reply-info">
                        <span class="forum-reply-name">
                           <a class="username-deleted"><b><?php echo __d('forum','Deleted Account');?></b></a>
                        </span>
                </div>
            <?php endif;?>
            </div>
        </div>
        <div class="forum-reply-body">
            <div class="forum-reply-content">
                <?php echo $this->Moo->cleanHtml($helper->bbcodetohtml($reply['ForumTopic']['description'],true));?>
            </div>
        </div>
        <div class="forum-reply-footer">
            <div class="forum-reply-date">
                <i class="material-icons">schedule</i><?php echo $this->Moo->getTime( $reply['ForumTopic']['created'], Configure::read('core.date_format'), $utz )?>
            </div>
        </div>
    </div>
    <?php endif;?>
<?php endforeach;?>
</div>
<?php else: ?>
    <div class="topic-body text-center"><?php echo __d('forum','No more results found');?></div>
<?php endif;?>