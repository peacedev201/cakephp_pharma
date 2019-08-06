<a class="menu_mobile_left blog-color" href="#" onclick="$('.menu_left').toggle()"><?php echo __('All Entries') ?></a>
<ul class="menu_left blog-color" id="browse">
        <li class="current" id="browse_all"><a class="json-view" data-url="<?php echo $this->request->base?>/blogs/browse/all" href="<?php echo $this->request->base?>/blogs"><?php echo __( 'All Entries')?></a></li>
        <?php if (!empty($uid)): ?>
        <li><a class="json-view" data-url="<?php echo $this->request->base?>/blogs/browse/my" href="#"><?php echo __('My Entries')?></a></li>
        <li><a class="json-view" data-url="<?php echo $this->request->base?>/blogs/browse/friends" href="#"><?php echo __("Friends' Entries")?></a></li>
        <?php endif; ?>
</ul>