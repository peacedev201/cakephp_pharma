<div class="bar-content">
    <div class="content_center">
        <div class="mo_breadcrumb">
            <h1><?php echo __d('contest', 'Contests'); ?></h1>
            <?php if ($uid): ?>
                <a href="<?php echo $this->request->base ?>/contests/create" class="button button-action topButton button-mobi-top"><?php echo __d('contest', 'Create New Contest'); ?></a>
            <?php endif; ?>
        </div>
        <ul id="list-content" class="contest-content-list">
            <?php echo $this->element('lists/contests', array('is_view_more' => $is_view_more, 'url_more' => $url_more, 'contests' => $contests), array('plugin' => 'Contest')); ?>
        </ul>
    </div>
</div>