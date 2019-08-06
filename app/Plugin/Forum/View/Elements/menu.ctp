<div class="box2 filter_block">
    <div class="box_content">
        <ul class="list2 menu-list" id="browse">
            <li class="<?php echo (!empty($type) && $type == 'forum') ? 'current' : '';?>" id="browse_all"><a class="no-ajax" data-url="<?php echo $this->request->base?>/forums" href="<?php echo $this->request->base?>/forums"><?php echo __d('forum', 'Forums')?></a></li>
            <li class="<?php echo (!empty($type) && $type == 'topic') ? 'current' : '';?>" id="browse_all"><a class="no-ajax" data-url="<?php echo $this->request->base?>/forums/topic" href="<?php echo $this->request->base?>/forums/topic"><?php echo __d('forum', 'All Topic')?></a></li>
        </ul>
    </div>
</div>