<?php if (in_array($type, array('home', 'profile')) && $page == 1): ?>
    <div class="content_center_home">
        <?php if ($type == 'home' || ($uid == $param)): ?>
            <div class="mo_breadcrumb">
                <h1><?php echo __d('contest', 'Contests'); ?></h1>
                <a href="<?php echo $this->request->base ?>/contests/create" class="button button-action topButton button-mobi-top"><?php echo __d('contest', 'Create New Contest'); ?></a>
            </div>
        <?php else: ?>
            <div class="mo_breadcrumb">
                <h1><?php echo __d('contest', 'Contests'); ?></h1>
            </div>
        <?php endif; ?>
        <ul id="list-content" class="contest-content-list">
            <?php if (count($contests)): ?>
                <?php echo $this->element('lists/contests'); ?>
            <?php else: ?>	
                <div class="clear text-center"><?php echo __d('contest', 'No more results found'); ?></div>
            <?php endif; ?>
        </ul>
    </div>
    <?php return;
endif; ?>
<?php
echo $this->element('lists/contests');