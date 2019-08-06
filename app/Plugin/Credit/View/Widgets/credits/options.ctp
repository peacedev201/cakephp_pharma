<?php if ($uid): ?>
    <?php
    if (empty($title)) $title = __d('credit', 'Earn Credits');
    if (isset($title_enable) && ($title_enable) === "") $title_enable = false; else $title_enable = true;
    ?>
    <div class="box2 send_credit">
        <?php if ($title_enable): ?>
            <h3><?php echo $title; ?></h3>
        <?php endif; ?>
        <div>
            <div style="padding: 10px;">
                <?php echo __d('credit','You can earn credits by posting various interesting social media content below, for more information please check %s.','<a href="'.$this->request->base .'/credits/index/action">'.__d('credit','here').'</a>')?>
            </div>
            <ul class="list2 block-body menu_top_list">
                <?php if (Configure::read('Blog.blog_enabled')): ?>
                    <li>
                        <a href="<?php echo $this->request->base ?>/blogs/create">
                            <i class="material-icons">&#xE3C9;</i> <?php echo __d('credit', 'Write New Entry') ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (Configure::read('Group.group_enabled')): ?>
                    <li>
                        <a href="<?php echo $this->request->base ?>/groups/create">
                            <i class="material-icons">&#xE7EF;</i> <?php echo __d('credit', 'Create New Group') ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (Configure::read('Event.event_enabled')): ?>
                    <li>
                        <a href="<?php echo $this->request->base ?>/events/create">
                            <i class="material-icons">&#xE878;</i> <?php echo __d('credit', 'Create New Event') ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (Configure::read('Photo.photo_enabled')): ?>
                    <li>
                        <a href="<?php echo $this->request->base ?>/albums/browse/my">
                            <i class="material-icons">collections</i> <?php echo __d('credit', 'Upload Photo') ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (Configure::read('Video.video_enabled')): ?>
                    <li>
                        <a href="<?php echo $this->request->base ?>/videos">
                            <i class="material-icons">videocam</i> <?php echo __d('credit', 'Share New Video') ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (Configure::read('Topic.topic_enabled')): ?>
                    <li>
                        <a href="<?php echo $this->request->base ?>/topics/create">
                            <i class="material-icons">forum</i> <?php echo __d('credit', 'Create New Topic') ?>
                        </a>
                    </li>
                <?php endif; ?>
                <?php
                $this->getEventManager()->dispatch(new CakeEvent('earnCredit.afterRenderOption', $this));
                ?>
            </ul>
        </div>
    </div>
<?php endif; ?>
