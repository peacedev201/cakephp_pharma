<a class="menu_mobile_left topic-color" href="#" onclick="$('.menu_left').toggle()"><?php echo __('All News') ?></a>
<ul id="browse" class="menu_left topic-color">
    <li <?php if (empty($this->request->named['category_id'])): ?>class="current"<?php endif; ?> id="browse_all"><a class="json-view" data-url="<?php echo $this->request->base ?>/topics/browse/all" href="<?php echo $this->request->base ?>/topics"><?php echo __('All News') ?></a></li>
    <?php if (!empty($uid)): ?>
        <li id="my_topics"><a data-url="<?php echo $this->request->base ?>/topics/browse/my" href="#"><?php echo __('My News') ?></a></li>
        <li id="friend_topics"><a data-url="<?php echo $this->request->base ?>/topics/browse/friends" href="#"><?php echo __("Friends' News") ?></a></li>
    <?php endif; ?>
    <li class="separate"></li>
</ul>